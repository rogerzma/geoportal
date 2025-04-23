<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geoportal Zacatecas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Contenedor del mapa */
        #map-container {
            width: 100%;
            height: 500px;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid d-flex justify-content-start">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                ☰
            </button>
            <a class="navbar-brand" href="#">Geoportal Zacatecas</a>
        </div>
    </nav>

    <!-- Barra lateral -->
    <div class="offcanvas offcanvas-start bg-light" id="sidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Menú</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="list-group">
                <li class="list-group-item">Capas</li>
                <li class="list-group-item">Búsquedas</li>
                <li class="list-group-item">Herramientas</li>
                <li class="list-group-item">Ayuda</li>
            </ul>
        </div>
    </div>

    <!-- Contenedor del mapa con iframe de QGIS -->
    <div id="map-container">
        <iframe src="{{ asset('qgis2web_2025_04_20-21_28_11_136464/index.html') }}"></iframe>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
