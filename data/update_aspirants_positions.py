import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
ASPIRANTS_PATH = ROOT / "data" / "aspirants.json"
MAPPING_PATH = ROOT / "data" / "positions_mapping.json"


def main():
    aspirants = json.loads(ASPIRANTS_PATH.read_text(encoding="utf-8"))
    mapping = json.loads(MAPPING_PATH.read_text(encoding="utf-8"))

    for item in aspirants:
        name = item.get("name")
        if name in mapping and mapping[name] and mapping[name] != "Unknown":
            item["position"] = mapping[name]
    out_path = ROOT / "data" / "aspirants.updated.json"
    out_path.write_text(json.dumps(aspirants, indent=2, ensure_ascii=False), encoding="utf-8")
    print(f"✓ Wrote updated aspirants to {out_path}")


if __name__ == "__main__":
    main()
