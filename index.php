<?php
try{
    $db = new mysqli('localhost', 'yapjj', '622215', 'vehicle_tracking');
    if ($db->connect_error) {throw new Exception($db->connect_error);}
} catch (Exception $e) {
    echo ("<h2>MySQL Server is offline.</h2><br><p>Check server status and try again.</p><br>");
    // echo $e;
    exit;
}


// Fetch records
$mileage_query = "SELECT * FROM MileageRecord";
$mileage_result = $db->query($mileage_query);

$fueling_query = "SELECT * FROM Fueling";
$fueling_result = $db->query($fueling_query);

$reset_trip_query = "SELECT * FROM ResetTrip";
$reset_trip_result = $db->query($reset_trip_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Tracking - Records</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container">
        <div>
            <label class="text-3xl font-bold mb-8">View Records</label>
        </div>

        <!-- Link to the forms page -->
        <div class="mb-5">
            <a href="forms.php" class="text-2xl text-blue-500 font-bold">Add New Records</a>
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

        <!-- Sorting Dropdown -->
        <div class="mb-4">
            <label for="sortDropdown" class="text-1xl">Sort by:</label>
            <select id="sortDropdown" class="columnSelect">
                <option value="date_desc">Date (Newest First)</option>
                <option value="date_asc">Date (Oldest First)</option>
                <option value="location_asc">Location (A-Z)</option>
                <option value="location_desc">Location (Z-A)</option>
                <!-- Add more sorting options here -->
            </select>
        </div>

        <!-- Tables Container -->
        <div id="tablesContainer">
            <!-- Mileage Records Table -->
            <div id="mileageTable" class="table-wrapper">
                <div class="search-header">
                    <h2 class="text-2xl font-semibold mb-4">Mileage Records</h2>
                    <div class="text-1xl">Search <input type="text" class="searchInput"/> within
                        <select class="columnSelect">
                            <option value="all">All Columns</option>
                            <option value="0">Date Time</option>
                            <option value="1">ODO (km)</option>
                            <option value="2">Trip (km)</option>
                            <option value="3">Avg (km/L)</option>
                            <option value="4">Range (km)</option>
                            <option value="5">Location</option>
                        </select>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date Time</th>
                            <th>ODO (km)</th>
                            <th>Trip (km)</th>
                            <th>AVG (km/L)</th>
                            <th>Range (km)</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $mileage_result->fetch_assoc()) { ?>
                            <tr>
                                <td data-time data-sort="<?php echo strtotime($row['date_time']); ?>">
                                    <?php $dateTime_M = new DateTime($row['date_time']); 
                                    echo htmlspecialchars($dateTime_M->format('Y-m-d H:i')); ?></td>
                                <td><?php echo htmlspecialchars($row['odo_km']); ?></td>
                                <td><?php echo htmlspecialchars($row['trip_km']); ?></td>
                                <td><?php echo htmlspecialchars($row['avg_km_l']); ?></td>
                                <td><?php echo htmlspecialchars($row['range_km']); ?></td>
                                <td data-location><?php echo htmlspecialchars($row['location']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Fueling Records Table -->
            <div id="fuelingTable" class="table-wrapper hidden">
                <div class="search-header">
                    <h2 class="text-2xl font-semibold mb-4">Fueling Records</h2>
                    <div class="text-1xl">Search <input type="text" class="searchInput"/> within
                        <select class="columnSelect">
                            <option value="all">All Columns</option>
                            <option value="0">Date Time</option>
                            <option value="1">Amount (RM)</option>
                            <option value="2">Liter</option>
                            <option value="3">Range After (km)</option>
                            <option value="4">Location</option>
                        </select>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date Time</th>
                            <th>Amount (RM)</th>
                            <th>Liter</th>
                            <th>Range After (km)</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $fueling_result->fetch_assoc()) { ?>
                            <tr>
                                <td data-time data-sort="<?php echo strtotime($row['date_time']); ?>">
                                    <?php $dateTime_F = new DateTime($row['date_time']); 
                                    echo htmlspecialchars($dateTime_F->format('Y-m-d H:i')); ?></td>
                                <td><?php echo htmlspecialchars($row['amount_rm']); ?></td>
                                <td><?php echo htmlspecialchars($row['liter']); ?></td>
                                <td><?php echo htmlspecialchars($row['range_after_km']); ?></td>
                                <td data-location><?php echo htmlspecialchars($row['location']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Reset Trip Records Table -->
            <div id="resetTripTable" class="table-wrapper hidden">
                <div class="search-header">
                    <h2 class="text-2xl font-semibold mb-4">Reset Trip Records</h2>
                    <div class="text-1xl">Search <input type="text" class="searchInput"/> within
                        <select class="columnSelect">
                            <option value="all">All Columns</option>
                            <option value="0">Date Time</option>
                            <option value="1">Mileage (km)</option>
                            <option value="2">Location</option>
                        </select>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date Time</th>
                            <th>Mileage (km)</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reset_trip_result->fetch_assoc()) { ?>
                            <tr>
                                <td data-time data-sort="<?php echo strtotime($row['date_time']); ?>">
                                    <?php $dateTime_R = new DateTime($row['date_time']); 
                                    echo htmlspecialchars($dateTime_R->format('Y-m-d H:i')); ?></td>
                                <td><?php echo htmlspecialchars($row['mileage_km']); ?></td>
                                <td data-location><?php echo htmlspecialchars($row['location']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mileageTable = document.getElementById('mileageTable');
            const fuelingTable = document.getElementById('fuelingTable');
            const resetTripTable = document.getElementById('resetTripTable');
            const sortDropdown = document.getElementById('sortDropdown');

            function showTable(tableToShow) {
                [mileageTable, fuelingTable, resetTripTable].forEach(table => {
                    table.classList.add('hidden');
                });
                tableToShow.classList.remove('hidden');
                applyCurrentSort();
            }

            document.getElementById('showMileageBtn').addEventListener('click', () => showTable(mileageTable));
            document.getElementById('showFuelingBtn').addEventListener('click', () => showTable(fuelingTable));
            document.getElementById('showResetTripBtn').addEventListener('click', () => showTable(resetTripTable));

            function sortTable(table) {
                const tbody = table.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const [field,direction] = sortDropdown.value.split('_');

                rows.sort((a, b) => {
                    if (field == 'date') {  
                        aValue = a.querySelector(`[data-time]`).dataset.sort || a.querySelector(`[data-time]`).textContent.trim();
                        bValue = b.querySelector(`[data-time]`).dataset.sort || b.querySelector(`[data-time]`).textContent.trim();
                    } else if (field == 'location') {  
                        aValue = a.querySelector(`[data-location]`).textContent.trim();
                        bValue = b.querySelector(`[data-location]`).textContent.trim();
                    }
                
                    if (direction == 'asc') {
                        return aValue.localeCompare(bValue, 'en', {numeric: true, sensitivity: 'base'});
                    } else {
                        return bValue.localeCompare(aValue, 'en', {numeric: true, sensitivity: 'base'});
                    }
                });

                rows.forEach(row => tbody.appendChild(row));
            }

            function applyCurrentSort() {
                const visibleTable = document.querySelector('.table-wrapper:not(.hidden) table');
                if (visibleTable) {sortTable(visibleTable);}
            }

            sortDropdown.addEventListener('change', applyCurrentSort);
            applyCurrentSort(); // Initial sort
        });

        document.querySelectorAll('.table-wrapper').forEach(container => {
            let searchInput = container.querySelector('.searchInput');
            let columnSelect = container.querySelector('.columnSelect');
            let tableBody = container.querySelector('tbody');

            function filterTable() {
                let searchValue = searchInput.value.toLowerCase();
                let selectedColumn = columnSelect.value;
                let rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    let cells = row.querySelectorAll('td');
                    let match = false;
                    if (selectedColumn === 'all') {
                        match = Array.from(cells).some(cell => cell.innerText.toLowerCase().includes(searchValue));
                    } else {
                        let colIndex = parseInt(selectedColumn, 10);
                        if (cells[colIndex] && cells[colIndex].innerText.toLowerCase().includes(searchValue)) {
                            match = true;
                        }
                    }
                    row.style.display = match ? '' : 'none';
                });
            }

            searchInput.addEventListener('keyup', filterTable);
            columnSelect.addEventListener('change', filterTable);
        });
    </script>
    </body>
</html>
