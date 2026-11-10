<!DOCTYPE html>
<html>
<head>
    <title>Tambah Cagar Budaya</title>
    <style>
        body { font-family: 'Georgia', serif; background-color: #f4efe6; margin: 0; padding: 40px 0; color: #3e2723; }
        .form-card { width: 420px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.15); border-top: 5px solid #5d4037; }
        h2 { font-family: 'Times New Roman', serif; color: #4e342e; text-align: center; border-bottom: 2px solid #8d6e63; padding-bottom: 10px; margin-top: 0; }
        label { font-family: sans-serif; font-size: 13px; font-weight: bold; color: #5d4037; display: block; margin-top: 12px; }
        input[type=text] { width: 100%; padding: 9px; margin-top: 4px; border: 1px solid #bcaaa4; border-radius: 4px; box-sizing: border-box; background-color: #faf8f5; font-size: 14px; }
        input[type=text]:focus { border-color: #5d4037; outline: none; background-color: #ffffff; }
        input[type=submit] { width: 100%; background: #6d4c41; color: white; border: none; padding: 11px; margin-top: 20px; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: sans-serif; font-size: 14px; transition: 0.3s; }
        input[type=submit]:hover { background: #4e342e; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #8d6e63; text-decoration: none; font-family: sans-serif; font-size: 13px; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-card">
    <h2>🏛️ Input Cagar Budaya</h2>
    
    <form action="input.php" method="post" onsubmit="return validateForm()">
        <label>Nama Objek Cagar Budaya:</label>
        <input type="text" id="namobj" name="namobj" placeholder="Contoh: Candi Banyunibo" required>
        
        <label>Kategori (Bangunan / Situs / Struktur):</label>
        <input type="text" id="kategori" name="kategori" placeholder="Contoh: Bangunan" required>
        
        <label>Koordinat X (UTM):</label>
        <input type="text" id="x" name="x" placeholder="Contoh: 444202.2308" required>
        
        <label>Koordinat Y (UTM):</label>
        <input type="text" id="y" name="y" placeholder="Contoh: 9140209.003" required>
        
        <input type="submit" name="submit" value="Simpan Cagar Budaya">
    </form>
    
    <a href="index.php" class="btn-back">← Kembali ke Halaman Utama</a>
    <p id="informasi" style="color: #c62828; margin-top: 10px; text-align: center; font-family: sans-serif; font-size: 13px;"></p>
</div>

<?php
if (isset($_POST['submit'])) {
    include 'koneksi.php';

    $namobj = $_POST['namobj'];
    $kategori = $_POST['kategori'];
    $x = $_POST['x'];
    $y = $_POST['y'];

    $sql = "INSERT INTO cagar_budaya (namobj, kategori, x, y) VALUES ('$namobj', '$kategori', $x, $y)";

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
    let x = document.getElementById("x").value;
    if (isNaN(x)) {
        document.getElementById("informasi").innerHTML = "Koordinat X harus berupa angka!";
        event.preventDefault();
        return false;
    }
    return true;
}
</script>

</body>
</html>