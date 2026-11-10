<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Desa Wisata</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f7f4; margin: 0; padding: 30px 0; color: #1b4332; }
        .form-card { width: 420px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(45, 106, 79, 0.15); border-top: 5px solid #40916c; }
        h2 { color: #1b4332; text-align: center; border-bottom: 2px dashed #52b788; padding-bottom: 10px; margin-top: 0; }
        label { font-size: 13px; font-weight: bold; color: #2d6a4f; display: block; margin-top: 10px; }
        input[type=text], textarea { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #b7e4c7; border-radius: 4px; box-sizing: border-box; background-color: #f4fbf7; font-size: 13px; font-family: inherit; }
        input[type=submit] { width: 100%; background: #40916c; color: white; border: none; padding: 10px; margin-top: 18px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px; }
        input[type=submit]:hover { background: #2d6a4f; }
        .btn-back { display: block; text-align: center; margin-top: 12px; color: #40916c; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h2>✏️ Edit Desa Wisata</h2>

    <?php
    include '../koneksi.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM desa_wisata WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<form action='edit.php' method='post' onsubmit='return validateForm()'>";
            while($row = $result->fetch_assoc()) {
                echo "<input type='hidden' name='id' value='".$row['id']."'>";
                
                echo "<label>Nama Desa Wisata:</label>";
                echo "<input type='text' id='namobj' name='namobj' value='".$row['namobj']."' required>";
                
                echo "<label>Klasifikasi:</label>";
                echo "<input type='text' id='klasifikasi' name='klasifikasi' value='".$row['klasifikasi']."' required>";
                
                echo "<label>Kapanewon:</label>";
                echo "<input type='text' id='kapanewon' name='kapanewon' value='".$row['kapanewon']."' required>";
                
                echo "<label>Alamat Lengkap:</label>";
                echo "<textarea id='alamat' name='alamat' rows='3' required>".$row['alamat']."</textarea>";
                
                echo "<label>Latitude:</label>";
                echo "<input type='text' id='latitude' name='latitude' value='".$row['latitude']."' required>";
                
                echo "<label>Longitude:</label>";
                echo "<input type='text' id='longitude' name='longitude' value='".$row['longitude']."' required>";
                
                echo "<input type='submit' value='Simpan Perubahan'>";
            }
            echo "</form>";
        }
    } else {
        header("Location: ../index.php");
        exit();
    }
    $conn->close();
    ?>

    <a href="../index.php" class="btn-back">← Batal & Kembali</a>
    <p id="informasi" style="color: #d90429; margin-top: 12px; font-weight: bold; text-align: center; font-size: 13px;"></p>
</div>

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