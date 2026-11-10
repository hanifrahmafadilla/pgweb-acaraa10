<?php
include '../koneksi.php';

$id = $_POST['id'];
$namobj = $_POST['namobj'];
$kategori = $_POST['kategori'];
$x = $_POST['x'];
$y = $_POST['y'];

$sql = "UPDATE cagar_budaya SET 
        namobj='$namobj', 
        kategori='$kategori', 
        x=$x, 
        y=$y 
        WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../index.php");
    exit();
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>