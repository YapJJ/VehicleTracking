<?php
$message = "";
$message_type = "";
$sql_result_html = "";

try{
    $db = new mysqli('localhost', 'yapjj', '622215', 'vehicle_tracking');
    if ($db->connect_error) {throw new Exception($db->connect_error);}
} catch (Exception $e) {
    echo ("<h2>MySQL Server is offline.</h2><br><p>Check server status and try again.</p><br>");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_mileage'])) {
        $stmt = $db->prepare("INSERT INTO MileageRecord (date_time, odo_km, trip_km, avg_km_l, range_km, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siddis", $_POST['datetime'], $_POST['odo'], $_POST['trip'], $_POST['avg'], $_POST['range'], $_POST['location']);

        if ($stmt->execute()) {
            $message = "Mileage record added successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

    } elseif (isset($_POST['add_fueling'])) {
        $stmt = $db->prepare("INSERT INTO Fueling (date_time, amount_rm, liter, range_b4_km, range_after_km, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sddiss", $_POST['date_time'], $_POST['amount'], $_POST['liter'], $_POST['range_before'], $_POST['range_after'], $_POST['location']);

        if ($stmt->execute()) {
            $message = "Fueling record added successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

    } elseif (isset($_POST['add_reset_trip'])) {
        $stmt = $db->prepare("INSERT INTO ResetTrip (date_time, mileage_km, location) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $_POST['date_time'], $_POST['mileage'], $_POST['location']);

        if ($stmt->execute()) {
            $message = "Reset trip record added successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }

    } elseif (isset($_POST['run_sql'])) {
        $sql = trim($_POST['sql_query']);

        // Remove trailing semicolon & Check for multiple queries
        $sql = trim($sql);
        $sql = rtrim($sql, ';');
        if (strpos($sql, ';') !== false) {
            $message = "Multiple queries are not allowed.";
            $message_type = "error";
        } else {
            // Block dangerous keywords
            $blocked = ['drop', 'truncate', 'alter', 'grant', 'revoke'];
            foreach ($blocked as $word) {
                if (stripos($sql, $word) !== false) {
                    $message = "Query contains restricted keyword: $word";
                    $message_type = "error";
                    break;
                }
            }

            if (empty($message)) {
                $isSelect = preg_match('/^\s*(select|with)\b/i', $sql);
                if ($isSelect) {
                    // STEP 1: detect LIMIT at end of query
                    if (preg_match('/\blimit\s+(\d+)(\s*,\s*\d+|\s+offset\s+\d+)?\s*$/i', $sql, $match)) {
                        $limitValue = (int)$match[1];

                        // STEP 2: clamp value
                        $finalLimit = min($limitValue, 50);

                        // STEP 3: replace ONLY first number inside LIMIT clause
                        $sql = preg_replace('/\blimit\s+\d+/i', 'LIMIT ' . $finalLimit,$sql);
                    } else {
                        // no limit → append default
                        $sql .= " LIMIT 50";
                    }
                }

                try{
                    $result = $db->query($sql);
                    if ($result === false) { 
                        throw new Exception($db->error); 
                    } elseif ($result instanceof mysqli_result) { // SELECT-like result set
                        $message = "Query executed successfully: " . htmlspecialchars($sql) . "<br>Rows returned: " . $result->num_rows;
                        $message_type = "success";
                        $sql_result_html .= "<div class='mb-2 font-semibold'>{$message}</div>";

                        if ($result->num_rows > 0) {
                            $sql_result_html .= "<table border='1' cellpadding='5'><tr>";
                            while ($field = $result->fetch_field()) {
                                $sql_result_html .= "<th>{$field->name}</th>";
                            }
                            $sql_result_html .= "</tr>";
                            while ($row = $result->fetch_assoc()) {
                                $sql_result_html .= "<tr>";
                                foreach ($row as $cell) {$sql_result_html .= "<td>{$cell}</td>";}
                                $sql_result_html .= "</tr>";
                            }
                            $sql_result_html .= "</table>";
                        }
                        $result->free();
                    } else { // INSERT / UPDATE / DELETE success (no result set)
                        $message = "Query executed successfully: " . htmlspecialchars($sql) . "<br>Affected rows: " . $db->affected_rows;
                        $message_type = "success";
                        $sql_result_html .= "<div class='mb-2 font-semibold'>{$message}</div>";
                    }
                } catch (Exception $e) {
                    $message = "Error: " . htmlspecialchars($sql) . "<br>" . $e->getMessage();
                    $message_type = "error";
                    $sql_result_html .= "<div class='mb-2 font-semibold'>{$message}</div>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Tracking - Forms</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container">

    <!-- GLOBAL MESSAGE -->
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded 
            <?= $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <label class="text-3xl font-bold mb-8">Add New Records</label>

    <div class="mb-5">
        <a href="index.php" class="text-2xl text-blue-500 font-bold">View Records</a>
    </div>

    <!-- Buttons -->
    <div class="flex space-x-4 mb-8">
        <button id="showMileageBtn" class="btn btn-blue">Mileage</button>
        <button id="showFuelingBtn" class="btn btn-green">Fueling</button>
        <button id="showResetTripBtn" class="btn btn-yellow">Reset Trip</button>
        <button id="showSQLBtn" class="btn btn-red">SQL Console</button>
    </div>

    <div id="formsContainer">
        <!-- Add Mileage Record Form -->
        <div id="mileageForm" class="hidden">
            <h2 class="text-2xl font-semibold mb-4">Add Mileage Record</h2>
            <form action="" method="post" class="bg-white shadow rounded p-6 mb-4">
                <div class="mb-4">
                    <label class="form-label" for="datetime">Date Time</label>
                    <input class="form-input" id="datetime" name="datetime" type="datetime-local" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="odo">ODO (km)</label>
                    <input class="form-input" id="odo" name="odo" type="number" step="1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="trip">Trip (km)</label>
                    <input class="form-input" id="trip" name="trip" type="number" step="0.1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="avg">AVG (km/L)</label>
                    <input class="form-input" id="avg" name="avg" type="number" step="0.1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="range">Range (km)</label>
                    <input class="form-input" id="range" name="range" type="number" step="1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="location">Location</label>
                    <input class="form-input" id="location_mileage" name="location" type="text" required>
                </div>
                <div>
                    <button class="btn btn-blue" type="submit" name="add_mileage">
                        Add Mileage Record
                    </button>
                </div>
            </form>
        </div>

        <!-- Add Fueling Record Form -->
        <div id="fuelingForm" class="hidden">
            <h2 class="text-2xl font-semibold mb-4">Add Fueling Record</h2>
            <form action="" method="post" class="bg-white shadow rounded p-6 mb-4">
                <div class="mb-4">
                    <label class="form-label" for="date_time">Date Time</label>
                    <input class="form-input" id="date_time" name="date_time" type="datetime-local" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="amount">Amount (RM)</label>
                    <input class="form-input" id="amount" name="amount" type="number" step="0.01" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="liter">Liter</label>
                    <input class="form-input" id="liter" name="liter" type="number" step="0.001" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="range_before">Range Before (km)</label>
                    <input class="form-input" id="range_before" name="range_before" type="number" step="1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="range_after">Range After (km)</label>
                    <input class="form-input" id="range_after" name="range_after" type="number" step="1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="location">Location</label>
                    <input class="form-input" id="location_fueling" name="location" type="text" required>
                </div>
                <div>
                    <button class="btn btn-blue" type="submit" name="add_fueling">
                        Add Fueling Record
                    </button>
                </div>
            </form>
        </div>

        <!-- Add Reset Trip Record Form -->
        <div id="resetTripForm" class="hidden">
            <h2 class="text-2xl font-semibold mb-4">Add Reset Trip Record</h2>
            <form action="" method="post" class="bg-white shadow rounded p-6 mb-4">
                <div class="mb-4">
                    <label class="form-label" for="date_time">Date Time</label>
                    <input class="form-input" id="date_time" name="date_time" type="datetime-local" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="mileage">Mileage (km)</label>
                    <input class="form-input" id="mileage" name="mileage" type="number" step="0.1" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="location">Location</label>
                    <input class="form-input" id="location_reset" name="location" type="text" required>
                </div>
                <div>
                    <button class="btn btn-blue" type="submit" name="add_reset_trip">
                        Add Reset Trip Record
                    </button>
                </div>
            </form>
        </div>


        <!-- SQL -->
        <div id="sqlForm" class="hidden">
            <form method="post" class="bg-white shadow rounded p-6 mb-4">
                <textarea name="sql_query" class="sql-textarea" placeholder="Enter SQL query..." required></textarea>
                <button type="submit" name="run_sql" class="btn btn-red">Run Query</button>
            </form>
            <!-- MySQL Dump -->
            <form action="export.php" method="post" style="display:inline;">
                <button type="submit" class="btn btn-red">Export Database</button>
            </form>

            <?php if (!empty($sql_result_html)): ?>
                <div class="mt-4 p-4 bg-gray-100 rounded">
                    <?= $sql_result_html ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mileageForm = document.getElementById('mileageForm');
    const fuelingForm = document.getElementById('fuelingForm');
    const resetTripForm = document.getElementById('resetTripForm');
    const sqlForm = document.getElementById('sqlForm');

    document.getElementById('showMileageBtn').onclick = () => {
        mileageForm.style.display = 'block';
        fuelingForm.style.display = 'none';
        resetTripForm.style.display = 'none';
        sqlForm.style.display = 'none';
    };

    document.getElementById('showFuelingBtn').onclick = () => {
        mileageForm.style.display = 'none';
        fuelingForm.style.display = 'block';
        resetTripForm.style.display = 'none';
        sqlForm.style.display = 'none';
    };

    document.getElementById('showResetTripBtn').onclick = () => {
        mileageForm.style.display = 'none';
        fuelingForm.style.display = 'none';
        resetTripForm.style.display = 'block';
        sqlForm.style.display = 'none';
    };

    document.getElementById('showSQLBtn').onclick = () => {
        mileageForm.style.display = 'none';
        fuelingForm.style.display = 'none';
        resetTripForm.style.display = 'none';
        sqlForm.style.display = 'block';
    };
});
</script>
</body>
</html>
