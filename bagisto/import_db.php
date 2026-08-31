<?php

$host = '3.108.227.18';
$port = 3306;
$db   = 'bagisto';
$user = 'root';
$pass = 'wS&&@222!';
$file = 'e:/xampp/htdocs/himalayanwaterscience/bagisto/bagisto.sql';

echo "1. Connecting to MySQL Database at $host...\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ]);
    echo "SUCCESS: Connected successfully!\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

echo "2. Reading SQL file: $file ...\n";
if (!file_exists($file)) {
    die("Error: SQL file not found at $file\n");
}

$sql = file_get_contents($file);
echo "3. Importing database queries (this may take a few seconds)...\n";

try {
    $pdo->exec($sql);
    echo "\n🎉 SUCCESS: Database 'bagisto' imported successfully without any error!\n";
} catch (PDOException $e) {
    echo "\n❌ Import Error: " . $e->getMessage() . "\n";
}
