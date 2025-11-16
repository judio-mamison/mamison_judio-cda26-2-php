<?php

session_start();

if (isset($_SESSION['connected']) || $_SESSION['connected'] === true) {
    header("Location: private.php");
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];


$connectionString = "mysql:host=localhost:3306;dbname=cash;charset=utf8mb4";
$connectionOptions = [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($connectionString, 'root', '', $connectionOptions);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = ("SELECT email, role FROM users where email = :email AND password = :password ");
    $preq = $pdo->prepare($sql);
    $preq->execute(['email' => $email, 'password' => $password]);
    $user = $preq->fetch();
    if ($user != "" && $user['email'] === $email) {
        echo "<p>Login successful!</p>";
        $_SESSION['connected'] = true;
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $email = $_SESSION['email'];
        header("Location: private.php");
        exit();
    }
    else {
        echo "<script>alert('Login failed: Invalid email or password.'); window.location.href = 'index.php';</script>";
    }

} catch (PDOException $e) {
    print_r($e);
}
?>