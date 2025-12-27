import json
import re
from pathlib import Path
from typing import Dict, List

from PyPDF2 import PdfReader

ROOT = Path(__file__).resolve().parents[1]
PDF_PATH = ROOT / "Final_Enhanced_Executive_Committee_Nomination_List.pdf"
ASPIRANTS_PATH = ROOT / "data" / "aspirants.json"
OUT_PATH = ROOT / "data" / "positions_mapping.json"

# Titles/honorifics commonly present in names
TITLES = {"DR", "ESQ", "MR", "MRS", "MS", "ENG", "REV", "HON", "PROF"}

# Broad set of possible position keywords (uppercased)
POSITION_KEYWORDS = [
    "PRESIDENT",
    "VICE PRESIDENT",
    "CHAIRPERSON",
    "VICE CHAIRPERSON",
    "GENERAL SECRETARY",
    "ASSISTANT SECRETARY",
    "SECRETARY",
    "TREASURER",
    "FINANCIAL SECRETARY",
    "ORGANIZING SECRETARY",
    "PUBLIC RELATIONS OFFICER",
    "PRO",
    "WELFARE OFFICER",
    "WOMEN ORGANIZER",
    "WOMEN'S ORGANIZER",
    "YOUTH ORGANIZER",
    "EXECUTIVE COMMITTEE MEMBER",
    "EXECUTIVE MEMBER",
    "EXTERNAL RELATIONS OFFICER",
]


def normalize_text(s: str) -> str:
    s = s.upper()
    s = re.sub(r"[\u2018\u2019\u201C\u201D]", '"', s)  # normalize quotes
    s = re.sub(r"[.,()\[\]{}]", " ", s)  # punctuation to space
    s = re.sub(r"\s+", " ", s).strip()
    return s


def normalize_name(name: str) -> str:
    n = normalize_text(name)
    tokens = [t for t in n.split() if t not in TITLES]
    return " ".join(tokens)


def load_aspirant_names() -> List[str]:
    data = json.loads(Path(ASPIRANTS_PATH).read_text(encoding="utf-8"))
    names = [item["name"] for item in data if isinstance(item, dict) and item.get("name")]
    return names


def extract_pdf_text() -> str:
    reader = PdfReader(str(PDF_PATH))
    text_parts = []
    for page in reader.pages:
        try:
            t = page.extract_text() or ""
        except Exception:
            t = ""
        text_parts.append(t)
    return "\n".join(text_parts)


def find_position_near(lines: List[str], idx: int) -> str:
    # Scan a window around the found name line for position keywords
    start = max(0, idx - 5)
    end = min(len(lines), idx + 6)
    window = lines[start:end]

    # Direct keyword match
    for line in window:
        for kw in POSITION_KEYWORDS:
            if kw in line:
                return kw

    # Common patterns like "Position: X" or "Nomination for X"
    for line in window:
        m = re.search(r"POSITION\s*[:\-]\s*([A-Z][A-Z ]+)", line)
        if m:
            return m.group(1).strip()
        m = re.search(r"NOMINATION\s+FOR\s+([A-Z][A-Z ]+)", line)
        if m:
            return m.group(1).strip()

    return "Unknown"


def build_positions_mapping(pdf_text: str, names: List[str]) -> Dict[str, str]:
    norm_lines = [normalize_text(l) for l in pdf_text.splitlines()]
    mapping: Dict[str, str] = {}

    # Pre-join lines for substring search as fallback
    joined = "\n".join(norm_lines)

    for original_name in names:
        nn = normalize_name(original_name)
        if not nn or nn.startswith("WHATSAPP "):
            mapping[original_name] = "Unknown"
            continue

        # Locate occurrence by finding the best matching line index (first occurrence)
        idx = -1
        for i, line in enumerate(norm_lines):
            if nn in line:
                idx = i
                break

        if idx == -1:
            # Fallback to substring search if line split lost context
            if nn in joined:
                # Approximate idx by position in text
                pos = joined.find(nn)
                # Convert position to a line index by counting newlines
                idx = joined.count("\n", 0, pos)

        if idx != -1:
            position = find_position_near(norm_lines, idx)
        else:
            position = "Unknown"

        mapping[original_name] = position

    return mapping


def main():
    if not PDF_PATH.exists():
        raise SystemExit(f"PDF not found: {PDF_PATH}")
    names = load_aspirant_names()
    pdf_text = extract_pdf_text()
    mapping = build_positions_mapping(pdf_text, names)

    OUT_PATH.write_text(json.dumps(mapping, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"✓ Wrote positions mapping for {len(mapping)} aspirants to {OUT_PATH}")
    known = sum(1 for v in mapping.values() if v != "Unknown")
    print(f"✓ Found positions: {known} known, {len(mapping)-known} unknown")


if __name__ == "__main__":
    main()
