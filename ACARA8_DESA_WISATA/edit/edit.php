<?php
include '../koneksi.php';

$id = $_POST['id'];
$namobj = $_POST['namobj'];
$klasifikasi = $_POST['klasifikasi'];
$kapanewon = $_POST['kapanewon'];
$alamat = $_POST['alamat'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$sql = "UPDATE desa_wisata SET 
        namobj='$namobj', 
        klasifikasi='$klasifikasi', 
        kapanewon='$kapanewon', 
        alamat='$alamat', 
        latitude=$latitude, 
        longitude=$longitude 
        WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>