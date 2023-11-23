<?php
$pdo = new PDO('mysql:host=localhost;dbname=webappwizard', 'root', 'kU7~51ft7`aQ');
$email = "admin@example.com";
$password = "toor"; // Replace with your desired password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO admins (email, password) VALUES (:email, :password)");
$stmt->execute(['email' => $email, 'password' => $hashedPassword]);

echo "Admin account created successfully.";
?>
