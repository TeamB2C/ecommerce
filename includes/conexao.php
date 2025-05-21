<?php
$host = 'sql313.infinityfree.com';
$dbname = 'if0_39028034_ecommerce';
$username = 'if0_39028034';
$password = 'flexcorp2024';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}
?>
