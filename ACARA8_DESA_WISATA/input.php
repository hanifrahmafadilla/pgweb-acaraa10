<!DOCTYPE html>
<html>
<head>
    <title>Tambah Desa Wisata</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f7f4; margin: 0; padding: 30px 0; color: #1b4332; }
        .form-card { width: 420px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(45, 106, 79, 0.15); border-top: 5px solid #2d6a4f; }
        h2 { color: #1b4332; text-align: center; border-bottom: 2px solid #52b788; padding-bottom: 10px; margin-top: 0; }
        label { font-size: 13px; font-weight: bold; color: #2d6a4f; display: block; margin-top: 10px; }
        input[type=text], textarea { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #b7e4c7; border-radius: 4px; box-sizing: border-box; background-color: #f4fbf7; font-size: 13px; font-family: inherit; }
        input[type=submit] { width: 100%; background: #40916c; color: white; border: none; padding: 10px; margin-top: 18px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px; }
        input[type=submit]:hover { background: #2d6a4f; }
        .btn-back { display: block; text-align: center; margin-top: 12px; color: #40916c; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h2>🌿 Input Desa Wisata Cangkringan</h2>
    
    <form action="input.php" method="post" onsubmit="return validateForm()">
        <label>Nama Desa Wisata:</label>
        <input type="text" id="namobj" name="namobj" required>
        
        <label>Klasifikasi (Berkembang / Rintisan / Mandiri):</label>
        <input type="text" id="klasifikasi" name="klasifikasi" value="Berkembang" required>
        
        <label>Kapanewon:</label>
        <input type="text" id="kapanewon" name="kapanewon" value="Cangkringan" required>
        
        <label>Alamat Lengkap:</label>
        <textarea id="alamat" name="alamat" rows="3" placeholder="Contoh: Umbulharjo, Cangkringan, Sleman" required></textarea>
        
        <label>Latitude:</label>
        <input type="text" id="latitude" name="latitude" placeholder="-7.625411" required>
        
        <label>Longitude:</label>
        <input type="text" id="longitude" name="longitude" placeholder="110.448722" required>
        
        <input type="submit" name="submit" value="Simpan Desa Wisata">
    </form>
    
    <a href="index.php" class="btn-back">← Kembali ke Halaman Utama</a>
    <p id="informasi" style="color: #d90429; margin-top: 10px; text-align: center; font-size: 13px;"></p>
</div>

<?php
if (isset($_POST['submit'])) {
    include 'koneksi.php';

    $namobj = $_POST['namobj'];
    $klasifikasi = $_POST['klasifikasi'];
    $kapanewon = $_POST['kapanewon'];
    $alamat = $_POST['alamat'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $sql = "INSERT INTO desa_wisata (namobj, klasifikasi, kapanewon, alamat, latitude, longitude) VALUES ('$namobj', '$klasifikasi', '$kapanewon', '$alamat', $latitude, $longitude)";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<script>
function validateForm() {
    let lat = document.getElementById("latitude").value;
    let lng = document.getElementById("longitude").value;
    if (isNaN(lat) || isNaN(lng)) {
        document.getElementById("informasi").innerHTML = "Latitude dan Longitude harus berupa angka!";
        event.preventDefault();
        return false;
    }
    return true;
}
</script>
</body>
</html>