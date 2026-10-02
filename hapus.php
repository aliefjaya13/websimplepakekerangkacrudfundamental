<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql    = "DELETE FROM tbsiswa WHERE id='$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        header("Location: pageview.php");
        exit();
    } else {
        echo "Data gagal dihapus: " . mysqli_error($conn);
    }
}
?>