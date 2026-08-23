<?php

/* =========================
   CONFIG
========================= */
$host = "localhost";
$user = "yapjj";
$pass = "622215";
$db   = "vehicle_tracking";

$backupDir = __DIR__ . "/data_dump";

/* =========================
   CREATE FOLDER
========================= */
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

/* =========================
   STEP 1: SHOW CONFIRMATION
========================= */
if (!isset($_POST['confirm'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Export</title>
        <link href="styles.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100">
    <div class="container">
        <label class="text-3xl font-bold mb-8">Confirm Database Export?</label>
        <p>This will create a full SQL backup of the database.</p>

        <form method="post">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" class="btn btn-green">
                Yes, Export Now
            </button>
        </form>
        <p></p>
        <form action="forms.php">
            <button type="submit" class="btn btn-yellow">
                Cancel
            </button>
        </form>
    </div>
    </body>	
    </html>
    <?php
    exit;
}

/* =========================
   STEP 2: RUN DUMP
========================= */
$filename = $db . "_gmt_" . gmdate("Y-m-d_H-i-s") . ".sql";
$filepath = $backupDir . "/" . $filename;

$passEscaped = str_replace("'", "'\\''", $pass);

$command = "mysqldump -h " . escapeshellarg($host)
    . " -u " . escapeshellarg($user)
    . " -p'" . $passEscaped . "' "
    . escapeshellarg($db)
    . " > " . escapeshellarg($filepath);

exec($command, $output, $returnVar);

/* =========================
   STEP 3: SHOW RESULT
========================= */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Result</title>
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container">
    <?php if ($returnVar === 0): ?>
        <label class="text-3xl font-bold mb-8" style="color:green;">✔ Backup Successful</label>
        <p>File saved as: <?= htmlspecialchars($filename) ?> </p>
    <?php else: ?>
        <label class="text-3xl font-bold mb-8" style="color:red;">✖ Backup Failed</label>
        <p>Please check server permissions or mysqldump path.</p>
    <?php endif; ?>
    <br>
    <form action="forms.php">
        <button class="btn btn-red" type="submit">Back to Main Page</button>
    </form>
</div>
</body>
</html>
