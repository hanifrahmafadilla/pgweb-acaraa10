<?php
include 'koneksi.php';

function utmToLatLong($x, $y) {
    $utmZone = 49;
    $eccSquared = 0.00669438;
    $k0 = 0.9996;
    $a = 6378137;

    $x_val = $x - 500000;
    $y_val = $y - 10000000;

    $e1 = (1 - sqrt(1 - $eccSquared)) / (1 + sqrt(1 - $eccSquared));
    $M = $y_val / $k0;
    $mu = $M / ($a * (1 - $eccSquared / 4 - 3 * $eccSquared * $eccSquared / 64 - 5 * $eccSquared * $eccSquared * $eccSquared / 256));

    $phi1Rad = $mu + (3 * $e1 / 2 - 27 * $e1 * $e1 * $e1 / 32) * sin(2 * $mu) 
              + (21 * $e1 * $e1 / 16 - 55 * $e1 * $e1 * $e1 * $e1 / 32) * sin(4 * $mu)
              + (151 * $e1 * $e1 * $e1 / 96) * sin(6 * $mu);

    $N1 = $a / sqrt(1 - $eccSquared * sin($phi1Rad) * sin($phi1Rad));
    $T1 = tan($phi1Rad) * tan($phi1Rad);
    $C1 = ($eccSquared / (1 - $eccSquared)) * cos($phi1Rad) * cos($phi1Rad);
    $R1 = $a * (1 - $eccSquared) / pow(1 - $eccSquared * sin($phi1Rad) * sin($phi1Rad), 1.5);
    $D = $x_val / ($N1 * $k0);

    $lat = $phi1Rad - ($N1 * tan($phi1Rad) / $R1) * ($D * $D / 2 - (5 + 3 * $T1 + 10 * $C1 - 4 * $C1 * $C1 - 9 * ($eccSquared / (1 - $eccSquared))) * pow($D, 4) / 24 + (61 + 90 * $T1 + 298 * $C1 + 45 * $T1 * $T1 - 252 * ($eccSquared / (1 - $eccSquared)) - 3 * $C1 * $C1) * pow($D, 6) / 720);
    $lat = rad2deg($lat);

    $lng = ($D - (1 + 2 * $T1 + $C1) * pow($D, 3) / 6 + (5 - 2 * $C1 + 28 * $T1 - 3 * $C1 * $C1 + 8 * ($eccSquared / (1 - $eccSquared)) + 24 * $T1 * $T1) * pow($D, 5) / 120) / cos($phi1Rad);
    $lng = 6 * ($utmZone - 1) - 180 + 3 + rad2deg($lng);

    return array('lat' => $lat, 'lng' => $lng);
}

$sql = "SELECT * FROM cagar_budaya";
$result = $conn->query($sql);

$markers = array();
$rows_data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rows_data[] = $row;
        $coords = utmToLatLong($row['x'], $row['y']);
        $markers[] = array(
            'namobj' => $row['namobj'],
            'kategori' => $row['kategori'],
            'lat' => $coords['lat'],
            'lng' => $coords['lng']
        );
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Cagar Budaya Sleman</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Georgia', serif; background-color: #f4efe6; margin: 0; padding: 15px; color: #3e2723; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(62, 39, 35, 0.1); border-top: 5px solid #5d4037; margin-bottom: 15px; }
        .header h2 { margin: 0; font-family: 'Times New Roman', serif; color: #4e342e; }
        .btn-add { padding: 9px 16px; background: #6d4c41; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 4px; font-family: sans-serif; font-size: 13px; }
        .btn-add:hover { background: #4e342e; }
        .main-container { display: flex; gap: 15px; height: calc(100vh - 120px); }
        .map-box { flex: 1.2; background: #ffffff; border-radius: 8px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; }
        #map { width: 100%; height: 100%; border-radius: 6px; }
        .table-box { flex: 1; background: #ffffff; border-radius: 8px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #5d4037; color: #ffffff; font-family: sans-serif; font-size: 13px; padding: 10px; position: sticky; top: 0; }
        td { padding: 8px; border-bottom: 1px solid #d7ccc8; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background-color: #efebe9; }
        tr:hover { background-color: #d7ccc8; }
        .btn-edit { color: #8d6e63; font-weight: bold; text-decoration: none; font-family: sans-serif; }
        .btn-delete { color: #c62828; font-weight: bold; text-decoration: none; font-family: sans-serif; }
    </style>
</head>
<body>

<div class="header">
    <h2>🏛️ Persebaran Cagar Budaya Sleman</h2>
    <a href="input.php" class="btn-add">+ Tambah Data</a>
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
                    <th>Kategori</th>
                    <th>X (UTM)</th>
                    <th>Y (UTM)</th>
                    <th colspan="2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($rows_data as $row): ?>
                <tr>
                    <td style="text-align: left; font-weight: bold;"><?= $row['namobj'] ?></td>
                    <td><span style="background:#d7ccc8; padding:2px 6px; border-radius:4px;"><?= $row['kategori'] ?></span></td>
                    <td><?= $row['x'] ?></td>
                    <td><?= $row['y'] ?></td>
                    <td><a href="edit/index.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a></td>
                    <td><a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?');" class="btn-delete">Hapus</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="text-align:center; color:#795548;">Belum ada data cagar budaya.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-7.7705, 110.4895], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markersData = <?php echo json_encode($markers); ?>;

    markersData.forEach(function(item) {
        L.marker([item.lat, item.lng])
            .addTo(map)
            .bindPopup("<b>" + item.namobj + "</b><br>Kategori: " + item.kategori);
    });
</script>

</body>
</html>