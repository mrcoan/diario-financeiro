<?php

$host = 'localhost';
$user = 'root';
$db = 'diario_de_financas';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");
}