<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geoportal Zacatecas - Mapa Satelital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            transition: margin-left 0.4s ease-in-out;
        }

        #map {
            width: 100%;
            height: 590px;
            transition: margin-left 0.4s ease-in-out;
        }

        /* Sidebar fijo sin oscurecer */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #f4f4f4;
            position: fixed;
            left: -260px;
            top: 0;
            transition: left 0.4s ease-in-out;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            padding: 25px;
        }

        /* Ajustar el título del Geoportal */
        .navbar-brand {
            margin-left: 60px; /* Evita que el botón hamburguesa lo tape */
        }

        /* Ajustar la posición de los íconos en el menú */
        .sidebar {
            padding-top: 50px; /* Bajar los íconos dentro del menú */
        }

        .sidebar.active {
            left: 0;
        }

        /* Botón hamburguesa */
        .menu-btn {
            position: fixed;
            top: 10px;
            left: 15px;
            background-color: #198754;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 24px;
            z-index: 1000;
        }

        /* Contenedor de botones flotantes dentro del mapa */
        .icon-container {
            position: absolute;
            bottom: 10px;
            left: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
            pointer-events: auto;
        }

        .icon-button {
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.4);
            width: 50px;
            height: 50px;
            font-size: 16px;
        }

        #polygon-color {
            margin-bottom: 10px;
            width: 50%;
            height: 50px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Botón hamburguesa -->
    <button class="menu-btn" onclick="toggleMenu()">☰</button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <ul class="list-group">
            <li class="list-group-item">Capas</li>
            <li class="list-group-item">Búsquedas</li>
            <li class="list-group-item">Herramientas</li>
            <li class="list-group-item">Ayuda</li>
        </ul>
    </aside>

    <!-- Encabezado -->
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid d-flex justify-content-start">
            <a class="navbar-brand" href="#">Geoportal Zacatecas</a>
        </div>
    </nav>

    <!-- Mapa -->
    <div id="map">
        <!-- Selector de color y botones flotantes -->
        <div class="icon-container">
            <label for="polygon-color" style="margin-bottom: 5px;">Color del polígono:</label>
            <input type="color" id="polygon-color" value="#00aaff">
            <div class="icon-button" id="draw-parcela" title="Dibujar parcela">🖊️</div>
            <div class="icon-button" id="delete-parcela" title="Eliminar última parcela">🗑️</div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>

    <script>
        // Inicializar el mapa en Zacatecas
        var map = L.map('map').setView([22.775, -102.573], 12);
        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
            attribution: '&copy; Google Maps'
        }).addTo(map);

        // Grupo de capas para parcelas
        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Control de dibujo
        var drawControl = new L.Control.Draw({
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    shapeOptions: { color: '#00aaff' }
                },
                polyline: false,
                rectangle: false,
                circle: false,
                marker: false,
                circlemarker: false
            },
            edit: { featureGroup: drawnItems, remove: false }
        });
        map.addControl(drawControl);

        // Evento al finalizar dibujo
        map.on(L.Draw.Event.CREATED, function (e) {
            var newLayer = e.layer; // Nuevo polígono trazado
            var newPolygon = newLayer.toGeoJSON(); // Convertir a GeoJSON

            // Verificar intersección con polígonos existentes
            var intersects = false;
            drawnItems.eachLayer(function (layer) {
                var existingPolygon = layer.toGeoJSON();
                if (turf.intersect(existingPolygon, newPolygon)) {
                    intersects = true; // Hay intersección
                }
            });

            if (intersects) {
                alert("No se permite que las parcelas se sobrepongan.");
            } else {
                drawnItems.addLayer(newLayer); // Agregar el polígono si no hay intersección
            }
        });

        // Botón hamburguesa: Desplazar mapa sin oscurecer
        function toggleMenu() {
            document.getElementById("sidebar").classList.toggle("active");
            document.getElementById("map").style.marginLeft = document.getElementById("sidebar").classList.contains("active") ? "250px" : "0";
        }
    </script>

</body>
</html>