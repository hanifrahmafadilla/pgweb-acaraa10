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
            'id' => $row['id'],
            'namobj' => $row['namobj'],
            'klasifikasi' => $row['klasifikasi'],
            'kapanewon' => $row['kapanewon'],
            'alamat' => $row['alamat'],
            'lat' => (float)$row['latitude'],
            'lng' => (float)$row['longitude']
        );
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peta Desa Wisata Kabupaten Sleman</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f7f4; margin: 0; padding: 15px; color: #1b4332; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(45, 106, 79, 0.1); border-top: 5px solid #2d6a4f; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #1b4332; }
        .btn-add { padding: 9px 16px; background: #40916c; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 4px; font-size: 13px; }
        .btn-add:hover { background: #2d6a4f; }

        .main-container { display: flex; gap: 15px; height: calc(100vh - 190px); }
        .map-box { flex: 1; background: #ffffff; border-radius: 8px; padding: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; flex-direction: column; position: relative; }
        #map { width: 100%; height: 100%; border-radius: 6px; z-index: 1; }
        
        .filter-container { display: flex; gap: 10px; margin-bottom: 10px; position: relative; z-index: 1000; }
        .search-box { position: relative; flex: 1; }
        .search-box input { width: 100%; padding: 8px 35px 8px 12px; border: 1px solid #b7e4c7; border-radius: 4px; font-size: 13px; box-sizing: border-box; outline: none; }
        .search-box input:focus { border-color: #2d6a4f; }
        .clear-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #888; font-weight: bold; font-size: 14px; display: none; user-select: none; }
        .clear-btn:hover { color: #d90429; }

        .autocomplete-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #ffffff; border: 1px solid #b7e4c7; border-radius: 0 0 6px 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); max-height: 200px; overflow-y: auto; display: none; z-index: 2000; margin-top: 2px; }
        .suggestion-item { padding: 8px 12px; font-size: 13px; cursor: pointer; border-bottom: 1px solid #f0f7f4; color: #1b4332; display: flex; justify-content: space-between; align-items: center; }
        .suggestion-item:last-child { border-bottom: none; }
        .suggestion-item:hover { background-color: #d8f3dc; }
        .suggestion-badge { font-size: 10px; padding: 2px 6px; border-radius: 4px; background: #b7e4c7; color: #1b4332; font-weight: bold; }

        .filter-select { padding: 8px 12px; border: 1px solid #b7e4c7; border-radius: 4px; font-size: 13px; outline: none; background-color: #ffffff; color: #1b4332; font-weight: 500; cursor: pointer; }
        .filter-select:focus { border-color: #2d6a4f; }

        .table-box { flex: 1.4; background: #ffffff; border-radius: 8px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow-y: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #2d6a4f; color: #ffffff; font-size: 12px; padding: 10px; position: sticky; top: 0; }
        td { padding: 8px; border-bottom: 1px solid #d8f3dc; text-align: center; font-size: 12px; }
        tr:nth-child(even) { background-color: #f4fbf7; }
        tr:hover { background-color: #d8f3dc; }
        .btn-edit { color: #40916c; font-weight: bold; text-decoration: none; }
        .btn-delete { color: #d90429; font-weight: bold; text-decoration: none; }
        .badge { background: #b7e4c7; color: #1b4332; padding: 2px 6px; border-radius: 4px; font-weight: bold; }

        .footer-stats { display: flex; align-items: center; justify-content: space-around; background: #ffffff; margin-top: 15px; padding: 10px 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #d8f3dc; }
        .stat-pill { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #1b4332; }
        .stat-count { background: #e8f5e9; color: #2d6a4f; padding: 3px 10px; border-radius: 12px; font-weight: bold; font-size: 13px; border: 1px solid #b7e4c7; }
        .stat-pill.mandiri .stat-count { background: #e8f5e9; color: #2aad27; border-color: #2aad27; }
        .stat-pill.maju .stat-count { background: #fff8e1; color: #cb8427; border-color: #cb8427; }
        .stat-pill.berkembang .stat-count { background: #e3f2fd; color: #2a81cb; border-color: #2a81cb; }
        .stat-pill.rintisan .stat-count { background: #ffebee; color: #cb2b2b; border-color: #cb2b2b; }

        .legend { background: white; padding: 10px 12px; line-height: 18px; color: #333; border-radius: 5px; box-shadow: 0 0 15px rgba(0,0,0,0.2); font-size: 12px; }
        .legend h4 { margin: 0 0 5px; font-size: 13px; color: #1b4332; }
        .legend i { width: 12px; height: 12px; float: left; margin-right: 8px; opacity: 0.9; border-radius: 50%; }

        .btn-route { display: inline-block; margin-top: 8px; padding: 5px 10px; background-color: #2d6a4f; color: #ffffff !important; text-decoration: none; font-size: 11px; font-weight: bold; border-radius: 4px; }
        .btn-route:hover { background-color: #1b4332; }

        .btn-reset-view, .btn-location { background: white; border: 2px solid rgba(0,0,0,0.2); border-radius: 4px; padding: 6px 10px; cursor: pointer; font-weight: bold; font-size: 12px; color: #1b4332; box-shadow: 0 1px 5px rgba(0,0,0,0.4); margin-bottom: 4px; }
        .btn-reset-view:hover, .btn-location:hover { background-color: #f4f4f4; color: #2d6a4f; }
    </style>
</head>
<body>

<div class="header">
    <h2>🌿 Persebaran Desa Wisata Kabupaten Sleman</h2>
    <a href="input.php" class="btn-add">+ Tambah Desa Wisata</a>
</div>

<div class="main-container">
    <div class="map-box">
        <div class="filter-container">
            <div class="search-box">
                <input type="text" id="searchInput" onkeyup="applyFilters()" autocomplete="off" placeholder="🔍 Cari nama desa wisata di Sleman...">
                <span id="clearBtn" class="clear-btn" onclick="clearSearch()">✖</span>
                <div id="suggestionsBox" class="autocomplete-suggestions"></div>
            </div>
            <select id="filterKlasifikasi" class="filter-select" onchange="applyFilters()">
                <option value="ALL">-- Semua Klasifikasi --</option>
                <option value="Mandiri">Mandiri</option>
                <option value="Maju">Maju</option>
                <option value="Berkembang">Berkembang</option>
                <option value="Rintisan">Rintisan</option>
            </select>
        </div>
        <div id="map"></div>
    </div>

    <div class="table-box">
        <?php if (count($rows_data) > 0): ?>
        <table id="dataTable">
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
                <tr data-klasifikasi="<?= htmlspecialchars($row['klasifikasi']) ?>" data-namobj="<?= htmlspecialchars(strtolower($row['namobj'])) ?>">
                    <td style="text-align: left; font-weight: bold;"><?= htmlspecialchars($row['namobj']) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($row['klasifikasi']) ?></span></td>
                    <td><?= htmlspecialchars($row['kapanewon']) ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($row['alamat']) ?></td>
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

<div class="footer-stats">
    <div class="stat-pill">
        <span>Total Desa Wisata:</span>
        <span id="countTotal" class="stat-count">0</span>
    </div>
    <div class="stat-pill mandiri">
        <span>🟢 Mandiri:</span>
        <span id="countMandiri" class="stat-count">0</span>
    </div>
    <div class="stat-pill maju">
        <span>🟡 Maju:</span>
        <span id="countMaju" class="stat-count">0</span>
    </div>
    <div class="stat-pill berkembang">
        <span>🔵 Berkembang:</span>
        <span id="countBerkembang" class="stat-count">0</span>
    </div>
    <div class="stat-pill rintisan">
        <span>🔴 Rintisan:</span>
        <span id="countRintisan" class="stat-count">0</span>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var defaultCenter = [-7.7000, 110.3800];
    var defaultZoom = 11;
    var map = L.map('map').setView(defaultCenter, defaultZoom);

    var basemapOSM = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    });

    var basemapEsri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri'
    });

    var basemapGoogle = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: 'Map data &copy; Google'
    });

    basemapOSM.addTo(map);

    var markerGroup = L.layerGroup().addTo(map);
    var userLocationMarker = null;

    function createCustomIcon(colorUrl) {
        return L.icon({
            iconUrl: colorUrl,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
    }

    var redIcon    = createCustomIcon('https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png');
    var orangeIcon = createCustomIcon('https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png');
    var blueIcon   = createCustomIcon('https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png');
    var greenIcon  = createCustomIcon('https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png');

    var markersData = <?php echo json_encode($markers); ?>;
    var leafletMarkers = [];

    markersData.forEach(function(item) {
        var selectedIcon = redIcon;
        if (item.klasifikasi === 'Mandiri') {
            selectedIcon = greenIcon;
        } else if (item.klasifikasi === 'Maju') {
            selectedIcon = orangeIcon;
        } else if (item.klasifikasi === 'Berkembang') {
            selectedIcon = blueIcon;
        }

        var googleMapsUrl = "https://www.google.com/maps/dir/?api=1&destination=" + item.lat + "," + item.lng;
        
        var popupContent = "<b>" + item.namobj + "</b><br>" +
                           "Klasifikasi: " + item.klasifikasi + "<br>" +
                           "Kapanewon: " + item.kapanewon + "<br>" +
                           "Alamat: " + item.alamat + "<br>" +
                           "<a href='" + googleMapsUrl + "' target='_blank' class='btn-route'>🚗 Petunjuk Arah</a>";

        var m = L.marker([item.lat, item.lng], { icon: selectedIcon })
            .addTo(markerGroup)
            .bindPopup(popupContent);
        
        m.namobj = item.namobj;
        m.klasifikasi = item.klasifikasi;
        leafletMarkers.push(m);
    });

    function applyFilters() {
        var searchQuery = document.getElementById('searchInput').value.toLowerCase();
        var selectedKlasifikasi = document.getElementById('filterKlasifikasi').value;
        var clearBtn = document.getElementById('clearBtn');
        var suggestionsBox = document.getElementById('suggestionsBox');

        if (searchQuery.length > 0) {
            clearBtn.style.display = 'block';
        } else {
            clearBtn.style.display = 'none';
            suggestionsBox.style.display = 'none';
        }

        var countTotal = 0, countMandiri = 0, countMaju = 0, countBerkembang = 0, countRintisan = 0;
        var suggestionsHtml = '';
        var matchCount = 0;

        leafletMarkers.forEach(function(marker) {
            var matchSearch = searchQuery === "" || marker.namobj.toLowerCase().includes(searchQuery);
            var matchKlasifikasi = selectedKlasifikasi === "ALL" || marker.klasifikasi === selectedKlasifikasi;

            if (matchSearch && matchKlasifikasi) {
                if (!markerGroup.hasLayer(marker)) {
                    markerGroup.addLayer(marker);
                }
                
                countTotal++;
                if (marker.klasifikasi === 'Mandiri') countMandiri++;
                else if (marker.klasifikasi === 'Maju') countMaju++;
                else if (marker.klasifikasi === 'Berkembang') countBerkembang++;
                else if (marker.klasifikasi === 'Rintisan') countRintisan++;

                if (searchQuery.length > 0 && matchCount < 8) {
                    suggestionsHtml += '<div class="suggestion-item" onclick="selectSuggestion(\'' + marker.namobj.replace(/'/g, "\\'") + '\')">' +
                                       '<span>' + marker.namobj + '</span>' +
                                       '<span class="suggestion-badge">' + marker.klasifikasi + '</span>' +
                                       '</div>';
                    matchCount++;
                }
            } else {
                if (markerGroup.hasLayer(marker)) {
                    markerGroup.removeLayer(marker);
                }
            }
        });

        if (searchQuery.length > 0 && matchCount > 0) {
            suggestionsBox.innerHTML = suggestionsHtml;
            suggestionsBox.style.display = 'block';
        } else {
            suggestionsBox.style.display = 'none';
        }

        document.getElementById('countTotal').innerText = countTotal;
        document.getElementById('countMandiri').innerText = countMandiri;
        document.getElementById('countMaju').innerText = countMaju;
        document.getElementById('countBerkembang').innerText = countBerkembang;
        document.getElementById('countRintisan').innerText = countRintisan;

        var tableRows = document.querySelectorAll('#dataTable tbody tr');
        tableRows.forEach(function(row) {
            var rowKlasifikasi = row.getAttribute('data-klasifikasi');
            var rowNamobj = row.getAttribute('data-namobj');

            var matchSearch = searchQuery === "" || rowNamobj.includes(searchQuery);
            var matchKlasifikasi = selectedKlasifikasi === "ALL" || rowKlasifikasi === selectedKlasifikasi;

            if (matchSearch && matchKlasifikasi) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function selectSuggestion(name) {
        document.getElementById('searchInput').value = name;
        document.getElementById('suggestionsBox').style.display = 'none';
        
        leafletMarkers.forEach(function(marker) {
            if (marker.namobj.toLowerCase() === name.toLowerCase()) {
                map.flyTo(marker.getLatLng(), 15);
                marker.openPopup();
            }
        });

        applyFilters();
    }

    document.addEventListener('click', function(e) {
        var searchBox = document.querySelector('.search-box');
        if (searchBox && !searchBox.contains(e.target)) {
            document.getElementById('suggestionsBox').style.display = 'none';
        }
    });

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterKlasifikasi').value = 'ALL';
        document.getElementById('suggestionsBox').style.display = 'none';
        applyFilters();
        map.closePopup();
        map.flyTo(defaultCenter, defaultZoom);
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                if (userLocationMarker) {
                    map.removeLayer(userLocationMarker);
                }

                userLocationMarker = L.circleMarker([lat, lng], {
                    radius: 9,
                    fillColor: "#007bff",
                    color: "#ffffff",
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 0.8
                }).addTo(map).bindPopup("<b>📍 Lokasi Anda Saat Ini</b>").openPopup();

                map.flyTo([lat, lng], 14);
            }, function() {
                alert("Gagal mengunduh lokasi GPS kamu. Pastikan GPS browser diaktifkan.");
            });
        } else {
            alert("Browser kamu tidak mendukung geolokasi GPS.");
        }
    }

    applyFilters();

    var baseMaps = {
        "OpenStreetMap": basemapOSM,
        "Esri Imagery": basemapEsri,
        "Google Street": basemapGoogle
    };

    var overlayMaps = {
        "Desa Wisata": markerGroup
    };

    var layerControl = L.control.layers(baseMaps, overlayMaps, { collapsed: true }).addTo(map);
    var container = layerControl.getContainer();

    var toggleButton = container.querySelector('.leaflet-control-layers-toggle');
    if (toggleButton) {
        var newToggleButton = toggleButton.cloneNode(true);
        toggleButton.parentNode.replaceChild(newToggleButton, toggleButton);

        L.DomEvent.disableClickPropagation(container);

        newToggleButton.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (container.classList.contains('leaflet-control-layers-expanded')) {
                container.classList.remove('leaflet-control-layers-expanded');
            } else {
                container.classList.add('leaflet-control-layers-expanded');
            }
        });

        map.on('click', function() {
            container.classList.remove('leaflet-control-layers-expanded');
        });
    }

    L.control.scale({
        maxWidth: 150,
        metric: true,
        imperial: false,
        position: 'bottomright'
    }).addTo(map);

    var legend = L.control({ position: 'bottomleft' });
    legend.onAdd = function (map) {
        var div = L.DomUtil.create('div', 'legend');
        div.innerHTML += '<h4>Legenda Klasifikasi</h4>';
        div.innerHTML += '<i style="background: #2aad27;"></i> Mandiri<br>';
        div.innerHTML += '<i style="background: #cb8427;"></i> Maju<br>';
        div.innerHTML += '<i style="background: #2a81cb;"></i> Berkembang<br>';
        div.innerHTML += '<i style="background: #cb2b2b;"></i> Rintisan<br>';
        return div;
    };
    legend.addTo(map);

    var customControls = L.control({ position: 'bottomleft' });
    customControls.onAdd = function (map) {
        var div = L.DomUtil.create('div');
        div.innerHTML = '<button class="btn-location" onclick="getLocation()">📍 Lokasi Saya</button><br>' +
                        '<button class="btn-reset-view" onclick="clearSearch()">🎯 Reset View</button>';
        return div;
    };
    customControls.addTo(map);
</script>

</body>
</html>