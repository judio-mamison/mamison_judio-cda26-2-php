<?php

session_start();

if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Private session</title>
</head>
<body>
    <button><a href="logout.php">Logout</a></button>
</body>
</html>