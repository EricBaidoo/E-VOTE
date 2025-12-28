<?php
/**
 * Re-import voter data from CSV and fix all formatting issues
 */

require_once 'includes/db_connect.php';

echo "<h2>Re-importing Voter Data from CSV</h2>";

$csvFile = 'LIST_OF_VOTERS-AMOSA_2025_ELECTIONS.csv';

if (!file_exists($csvFile)) {
    echo "<p style='color:red;'>Error: CSV file not found!</p>";
    exit;
}

// Read CSV with proper handling of line breaks in data
$content = file_get_contents($csvFile);

// Try different encodings
$encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252'];
$csvContent = null;

foreach ($encodings as $encoding) {
    if ($encoding !== 'UTF-8') {
        $converted = @iconv($encoding, 'UTF-8//IGNORE', $content);
        if ($converted !== false) {
            $csvContent = $converted;
            echo "<p>✓ Successfully read CSV using $encoding encoding</p>";
            break;
        }
    } else {
        $csvContent = $content;
        echo "<p>✓ Reading CSV with UTF-8 encoding</p>";
        break;
    }
}

if ($csvContent === null) {
    echo "<p style='color:red;'>Error: Could not read CSV file with any encoding</p>";
    exit;
}

// Parse CSV line by line
$lines = explode("\n", $csvContent);
$header = null;
$voters = [];
$currentRow = [];
$lineNum = 0;

foreach ($lines as $line) {
    $lineNum++;
    $line = trim($line);
    
    if (empty($line)) continue;
    
    // Parse CSV manually to handle quoted fields
    $fields = str_getcsv($line);
    
    if ($header === null) {
        $header = $fields;
        continue;
    }
    
    // If we have exactly 3 fields (Name, Phone, Email), it's a complete row
    if (count($fields) === 3) {
        // Save previous row if exists
        if (!empty($currentRow)) {
            $voters[] = $currentRow;
        }
        
        // Start new row
        $currentRow = [
            'name' => trim($fields[0]),
            'phone' => trim($fields[1]),
            'email' => trim($fields[2])
        ];
    } else {
        // This might be a continuation of email or other field
        foreach ($fields as $field) {
            $field = trim($field);
            if (!empty($field)) {
                // Append to email if it looks like an email part
                if (isset($currentRow['email'])) {
                    $currentRow['email'] .= $field;
                }
            }
        }
    }
}

// Don't forget the last row
if (!empty($currentRow)) {
    $voters[] = $currentRow;
}

echo "<p>✓ Parsed " . count($voters) . " voters from CSV</p>";

// Clean and update voters
$updated = 0;
$inserted = 0;
$errors = 0;

echo "<h3>Processing Voters...</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse:collapse;width:100%;font-size:12px;'>";
echo "<tr style='background:#2563eb;color:white;'>";
echo "<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th>";
echo "</tr>";

$id = 1;
foreach ($voters as $voter) {
    $name = trim($voter['name']);
    $email = trim($voter['email']);
    $phone = trim($voter['phone']);
    
    // Clean email: remove all whitespace and newlines
    $email = preg_replace('/\s+/', '', $email);
    $email = str_replace(["\n", "\r", "\t"], '', $email);
    $email = trim($email);
    
    // Clean phone
    $phone = preg_replace('/\s+/', '', $phone);
    $phone = trim($phone);
    
    // Skip if no name
    if (empty($name)) {
        continue;
    }
    
    // Check if voter exists
    $stmt = $conn->prepare("SELECT id FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc();
    $stmt->close();
    
    if ($exists) {
        // Update existing voter
        $stmt = $conn->prepare("UPDATE voters SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $phone, $id);
        
        if ($stmt->execute()) {
            echo "<tr style='background:#ffe;'>";
            echo "<td>{$id}</td>";
            echo "<td>" . htmlspecialchars($name) . "</td>";
            echo "<td>" . htmlspecialchars($email) . "</td>";
            echo "<td>" . htmlspecialchars($phone) . "</td>";
            echo "<td><span style='color:orange;'>✓ Updated</span></td>";
            echo "</tr>";
            $updated++;
        } else {
            echo "<tr style='background:#fee;'>";
            echo "<td>{$id}</td>";
            echo "<td colspan='4' style='color:red;'>Error updating: " . $conn->error . "</td>";
            echo "</tr>";
            $errors++;
        }
        $stmt->close();
    } else {
        // Insert new voter
        $stmt = $conn->prepare("INSERT INTO voters (id, name, email, phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $name, $email, $phone);
        
        if ($stmt->execute()) {
            echo "<tr style='background:#efe;'>";
            echo "<td>{$id}</td>";
            echo "<td>" . htmlspecialchars($name) . "</td>";
            echo "<td>" . htmlspecialchars($email) . "</td>";
            echo "<td>" . htmlspecialchars($phone) . "</td>";
            echo "<td><span style='color:green;'>✓ Inserted</span></td>";
            echo "</tr>";
            $inserted++;
        } else {
            echo "<tr style='background:#fee;'>";
            echo "<td>{$id}</td>";
            echo "<td colspan='4' style='color:red;'>Error inserting: " . $conn->error . "</td>";
            echo "</tr>";
            $errors++;
        }
        $stmt->close();
    }
    
    $id++;
}

echo "</table>";

echo "<h3 style='color:green;'>✓ Import Complete!</h3>";
echo "<ul>";
echo "<li><strong>New voters inserted:</strong> $inserted</li>";
echo "<li><strong>Existing voters updated:</strong> $updated</li>";
echo "<li><strong>Errors:</strong> $errors</li>";
echo "</ul>";

// Check for issues
$query = "SELECT COUNT(*) as count FROM voters WHERE (email = '' OR email IS NULL) AND (phone = '' OR phone IS NULL)";
$result = $conn->query($query);
$noContact = $result->fetch_assoc()['count'];

if ($noContact > 0) {
    echo "<div style='background:#fee;padding:15px;border:2px solid red;margin:10px 0;'>";
    echo "<h4 style='color:red;'>⚠️ Warning: $noContact voters have NO email or phone!</h4>";
    echo "<p>These voters cannot log in. They need contact info added manually.</p>";
    echo "</div>";
}

$conn->close();

echo "<br><p><a href='diagnose_voters.php'>Run Diagnostic</a> | <a href='admin/voters.php'>View Voters List</a></p>";
?>
