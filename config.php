<?php
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'dev';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'tbsiswa';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die('Koneksi ke database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
