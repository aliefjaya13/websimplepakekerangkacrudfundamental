<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Metode permintaan tidak diizinkan.');
}

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
$status = $_POST['status_kehadiran'] ?? '';
$allowedStatuses = ['Hadir', 'Tidak Hadir', 'Izin', 'Sakit'];

if (!$id || $id < 1 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(400);
    exit('ID siswa atau status kehadiran tidak valid.');
}

include 'config.php';

foreach (['status_kehadiran', 'terakhir_piket'] as $requiredColumn) {
    $columnResult = mysqli_query($conn, "SHOW COLUMNS FROM tbsiswa LIKE '$requiredColumn'");
    if (!$columnResult || mysqli_num_rows($columnResult) === 0) {
        http_response_code(503);
        header('Content-Type: text/plain; charset=utf-8');
        exit('Kolom status belum tersedia. Jalankan migration 2026_10_add_piket_attendance_columns.sql terlebih dahulu.');
    }
}

$studentQuery = mysqli_prepare($conn, 'SELECT id FROM tbsiswa WHERE id = ?');
if (!$studentQuery) {
    http_response_code(500);
    exit('Data siswa gagal diperiksa.');
}

mysqli_stmt_bind_param($studentQuery, 'i', $id);
mysqli_stmt_execute($studentQuery);
mysqli_stmt_store_result($studentQuery);
$studentExists = mysqli_stmt_num_rows($studentQuery) > 0;
mysqli_stmt_close($studentQuery);

if (!$studentExists) {
    http_response_code(404);
    exit('Data siswa tidak ditemukan.');
}

$updateQuery = mysqli_prepare(
    $conn,
    'UPDATE tbsiswa SET status_kehadiran = ?, terakhir_piket = NOW() WHERE id = ?'
);
if (!$updateQuery) {
    http_response_code(500);
    exit('Konfirmasi piket gagal disiapkan.');
}

mysqli_stmt_bind_param($updateQuery, 'si', $status, $id);
if (!mysqli_stmt_execute($updateQuery)) {
    error_log('Konfirmasi piket gagal: ' . mysqli_stmt_error($updateQuery));
    mysqli_stmt_close($updateQuery);
    http_response_code(500);
    exit('Konfirmasi piket gagal disimpan.');
}

mysqli_stmt_close($updateQuery);
mysqli_close($conn);

header('Location: pageview.php?konfirmasi=berhasil');
exit();
