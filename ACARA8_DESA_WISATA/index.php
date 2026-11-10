<?php
include 'koneksi.php';

$sql = "SELECT * FROM desa_wisata";
$result = $conn->query($sql);

$markers = array();
$rows_data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rows_data[] = $row;
        $markers[] = array(
            'namobj' => $row['namobj'],
            'klasifikasi' => $row['klasifikasi'],
            'kapanewon' => $row['kapanewon'],
            'alamat' => $row['alamat'],
            'lat' => $row['latitude'],
            'lng' => $row['longitude']
        );
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Desa Wisata Cangkringan Sleman</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f7f4; margin: 0; padding: 15px; color: #1b4332; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(45, 106, 79, 0.1); border-top: 5px solid #2d6a4f; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1b4332; }
        .btn-add { padding: 9px 16px; background: #40916c; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 13px; }
        .btn-add:hover { background: #2d6a4f; }
        .main-container { display: flex; gap: 15px; height: calc(100vh - 120px); }
        .map-box { flex: 1; background: #ffffff; border-radius: 8px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        #map { width: 100%; height: 100%; border-radius: 6px; }
        .table-box { flex: 1.4; background: #ffffff; border-radius: 8px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #2d6a4f; color: #ffffff; font-size: 12px; padding: 10px; position: sticky; top: 0; }
        td { padding: 8px; border-bottom: 1px solid #d8f3dc; text-align: center; font-size: 12px; }
        tr:nth-child(even) { background-color: #f4fbf7; }
        tr:hover { background-color: #d8f3dc; }
        .btn-edit { color: #40916c; font-weight: bold; text-decoration: none; }
        .btn-delete { color: #d90429; font-weight: bold; text-decoration: none; }
        .badge { background: #b7e4c7; color: #1b4332; padding: 2px 6px; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>

<div class="header">
    <h2>🌿 Persebaran Desa Wisata Kapanewon Cangkringan</h2>
    <a href="input.php" class="btn-add">+ Tambah Desa Wisata</a>
</div>

<div class="main-container">
    <div class="map-box">
        <div id="map"></div>
    </div>

    <div class="table-box">
        <?php if (count($rows_data) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Objek</th>
                    <th>Klasifikasi</th>
                    <th>Kapanewon</th>
                    <th>Alamat</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th colspan="2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows_data as $row): ?>
                <tr>
                    <td style="text-align: left; font-weight: bold;"><?= $row['namobj'] ?></td>
                    <td><span class="badge"><?= $row['klasifikasi'] ?></span></td>
                    <td><?= $row['kapanewon'] ?></td>
                    <td style="text-align: left;"><?= $row['alamat'] ?></td>
                    <td><?= $row['latitude'] ?></td>
                    <td><?= $row['longitude'] ?></td>
                    <td><a href="edit/index.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a></td>
                    <td><a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?');" class="btn-delete">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; color:#2d6a4f;">Belum ada data desa wisata.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-7.6250, 110.4500], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markersData = <?php echo json_encode($markers); ?>;

    markersData.forEach(function(item) {
        L.marker([item.lat, item.lng])
            .addTo(map)
            .bindPopup("<b>" + item.namobj + "</b><br>Klasifikasi: " + item.klasifikasi + "<br>Kapanewon: " + item.kapanewon + "<br>Alamat: " + item.alamat);
    });
</script>

</body>
</html>