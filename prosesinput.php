<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];

    $sql    = "INSERT INTO tbsiswa (nama, kelas) VALUES ('$nama', '$kelas')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: pageview.php");
        exit();
    } else {
        echo "Data gagal disimpan: " . mysqli_error($conn);
    }
}
?>