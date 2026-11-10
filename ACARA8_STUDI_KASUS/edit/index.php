<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Cagar Budaya</title>
    <style>
        body { font-family: 'Georgia', serif; background-color: #f4efe6; margin: 0; padding: 40px 0; color: #3e2723; }
        .form-card { width: 420px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(62, 39, 35, 0.15); border-top: 5px solid #8d6e63; }
        h2 { font-family: 'Times New Roman', serif; color: #4e342e; text-align: center; border-bottom: 2px dashed #8d6e63; padding-bottom: 10px; margin-top: 0; }
        label { font-family: sans-serif; font-size: 13px; font-weight: bold; color: #5d4037; display: block; margin-top: 12px; }
        input[type=text] { width: 100%; padding: 9px; margin-top: 4px; border: 1px solid #bcaaa4; border-radius: 4px; box-sizing: border-box; background-color: #efebe9; font-size: 14px; }
        input[type=text]:focus { border-color: #5d4037; outline: none; background-color: #ffffff; }
        input[type=submit] { width: 100%; background: #8d6e63; color: white; border: none; padding: 11px; margin-top: 20px; border-radius: 4px; font-weight: bold; cursor: pointer; font-family: sans-serif; font-size: 14px; transition: 0.3s; }
        input[type=submit]:hover { background: #5d4037; }
        .btn-back { display: block; text-align: center; margin-top: 15px; color: #8d6e63; text-decoration: none; font-family: sans-serif; font-size: 13px; }
        .btn-back:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-card">
    <h2>✏️ Edit Cagar Budaya</h2>

    <?php
    include '../koneksi.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM cagar_budaya WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<form action='edit.php' onsubmit='return validateForm()' method='post'>";
            while($row = $result->fetch_assoc()) {
                echo "<input type='hidden' name='id' value='".$row['id']."'>";
                
                echo "<label>Nama Cagar Budaya:</label>";
                echo "<input type='text' id='namobj' name='namobj' value='".$row['namobj']."' required>";
                
                echo "<label>Kategori:</label>";
                echo "<input type='text' id='kategori' name='kategori' value='".$row['kategori']."' required>";
                
                echo "<label>Koordinat X (UTM):</label>";
                echo "<input type='text' id='x' name='x' value='".$row['x']."' required>";
                
                echo "<label>Koordinat Y (UTM):</label>";
                echo "<input type='text' id='y' name='y' value='".$row['y']."' required>";
                
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
    <p id="informasi" style="color: #c62828; margin-top: 15px; font-weight: bold; text-align: center; font-family: sans-serif; font-size: 13px;"></p>
</div>

<script>
function validateForm() {
    let x = document.getElementById("x").value;
    if (isNaN(x)) {
        document.getElementById("informasi").innerHTML = "Koordinat X harus berupa angka!";
        event.preventDefault();
    }
}
</script>

</body>
</html>