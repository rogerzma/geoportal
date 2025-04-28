<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geoportal Zacatecas - Mapa Satelital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
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
            <div class="icon-button" id="delete-parcela" title="Eliminar parcela">🗑️</div>
        </div>
    </div>

    <!-- Contenedor de coordenadas -->
    <div id="coordinates">
        <strong>Coordenadas:</strong>
        <div id="lat-lng">Lat: --, Lng: --</div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="{{ asset('js/mapa.js') }}"></script>
</body>
</html>