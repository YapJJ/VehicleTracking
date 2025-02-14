<?php
try{
    $db = new mysqli('localhost', 'yapjj', '622215', 'vehicle_tracking');
    if ($db->connect_error) {throw new Exception($db->connect_error);}
} catch (Exception $e) {
    echo ("<h2>MySQL Server is offline.</h2><br><p>Check server status and try again.</p><br>");
    // echo $e;
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_mileage'])) {
        $datetime = $_POST['datetime'];
        $odo = $_POST['odo'];
        $trip = $_POST['trip'];
        $avg = $_POST['avg'];
        $range = $_POST['range'];
        $location = $_POST['location'];
        
        $stmt = $db->prepare("INSERT INTO MileageRecord (date_time, odo_km, trip_km, avg_km_l, range_km, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siddis", $datetime, $odo, $trip, $avg, $range, $location);
        $stmt->execute();
    } elseif (isset($_POST['add_fueling'])) {
        $date_time = $_POST['date_time'];
        $amount = $_POST['amount'];
        $liter = $_POST['liter'];
        $range_before = $_POST['range_before'];
        $range_after = $_POST['range_after'];
        $location = $_POST['location'];
        
        $stmt = $db->prepare("INSERT INTO Fueling (date_time, amount_rm, liter, range_b4_km, range_after_km, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sddiss", $date_time, $amount, $liter, $range_before, $range_after, $location);
        $stmt->execute();
    } elseif (isset($_POST['add_reset_trip'])) {
        $date_time = $_POST['date_time'];
        $mileage = $_POST['mileage'];
        $location = $_POST['location'];
        
        $stmt = $db->prepare("INSERT INTO ResetTrip (date_time, mileage_km, location) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $date_time, $mileage, $location);
        $stmt->execute();
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
        <div>
            <label class="text-3xl font-bold mb-8">Add New Records</label>
        </div>

        <!-- Link to the tables page -->
        <div class="mb-5">
            <a href="index.php" class="text-2xl text-blue-500 font-bold">View Records</a>
        </div>

        <!-- AJAX Buttons -->
        <div class="flex space-x-4 mb-8">
            <button id="showMileageBtn" class="btn btn-blue">
                Mileage
            </button>
            <button id="showFuelingBtn" class="btn btn-green">
                Fueling
            </button>
            <button id="showResetTripBtn" class="btn btn-yellow">
                Reset Trip
            </button>
        </div>

        <!-- Forms Container -->
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
                        <input class="form-input" id="location" name="location" type="text" required>
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
                        <input class="form-input" id="location" name="location" type="text" required>
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
                        <input class="form-input" id="location" name="location" type="text" required>
                    </div>
                    <div>
                        <button class="btn btn-blue" type="submit" name="add_reset_trip">
                            Add Reset Trip Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mileageForm = document.getElementById('mileageForm');
            const fuelingForm = document.getElementById('fuelingForm');
            const resetTripForm = document.getElementById('resetTripForm');

            document.getElementById('showMileageBtn').addEventListener('click', function() {
                mileageForm.style.display = 'block';
                fuelingForm.style.display = 'none';
                resetTripForm.style.display = 'none';
            });

            document.getElementById('showFuelingBtn').addEventListener('click', function() {
                mileageForm.style.display = 'none';
                fuelingForm.style.display = 'block';
                resetTripForm.style.display = 'none';
            });

            document.getElementById('showResetTripBtn').addEventListener('click', function() {
                mileageForm.style.display = 'none';
                fuelingForm.style.display = 'none';
                resetTripForm.style.display = 'block';
            });
        });
    </script>
</body>
</html>