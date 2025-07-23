<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geoportal Zacatecas - Mapa Satelital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
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
            <li class="list-group-item">Ayuda</li>
            <li class="list-group-item list-group-item-action" onclick="abrirModalLogin()">Iniciar sesión</li>
        </ul>
    </aside>

    <!-- Contenedor que se mueve -->
    <div id="main-container">
        <!-- Botón hamburguesa -->
        <button class="menu-btn" onclick="toggleMenu()">☰</button>
    
        <!-- Encabezado -->
        <nav class="navbar navbar-dark bg-success">
            <div class="container-fluid d-flex justify-content-start">
                <a class="navbar-brand" href="#">Geoportal Zacatecas</a>
            </div>
        </nav>
    
        <!-- Mapa -->
        <div id="map"></div>
    </div>

    <!-- Modal para iniciar sesión -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="loginForm">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div id="loginError" class="text-danger mb-3" style="display: none;">
                            Nombre de usuario o contraseña incorrectos.
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="contraseña" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="{{ asset('js/mapa.js') }}"></script>
    <script src="{{ asset('js/mapa_tecnico.js') }}"></script>
</body>
</html>