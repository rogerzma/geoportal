<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <button class="btn btn-sm btn-success m-3" onclick="abrirModalTecnico()">Nuevo técnico</button>
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
        <div id="map">
            <div class="icon-container">
                <label for="polygon-color" style="margin-bottom: 5px;">Color del polígono:</label>
                <input type="color" id="polygon-color" value="#00aaff">
                <div class="icon-button" id="draw-parcela" title="Dibujar parcela">🖊️</div>
                <div class="icon-button" id="delete-parcela" title="Eliminar parcela">🗑️</div>
            </div>
        </div>
    </div>
    

    <!-- Coordenadas -->
    <div id="coordinates">
        <strong>Coordenadas:</strong>
        <div id="lat-lng">Lat: --, Lng: --</div>
    </div>

    <!-- Modal para guardar datos de la parcela -->
    <div class="modal fade" id="parcelaModal" tabindex="-1" aria-labelledby="parcelaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <form id="parcelaForm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="parcelaModalLabel">Guardar datos de la parcela</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="geom" name="geom">
                <input type="hidden" id="coordenadas" name="coordenadas">
    
                <div class="mb-3">
                    <label for="cultivo" class="form-label">Tipo de cultivo</label>
                    <input type="text" class="form-control" id="cultivo" name="cultivo" required>
                </div>
                <div class="mb-3">
                    <label for="nombre_productor" class="form-label">Nombre del productor</label>
                    <input type="text" class="form-control" id="nombre_productor" name="nombre_productor" required>
                </div>
                <div class="mb-3">
                    <label for="tecnico_id" class="form-label">ID del técnico</label>
                    <input type="number" class="form-control" id="tecnico_id" name="tecnico_id" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar parcela</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Modal para agregar nuevo técnico -->
    <div class="modal fade" id="tecnicoModal" tabindex="-1" aria-labelledby="tecnicoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <form id="tecnicoForm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tecnicoModalLabel">Registrar nuevo técnico</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                <label for="nombre_tecnico" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre_tecnico" name="nombre" required>
                </div>
                <div class="mb-3">
                <label for="usuario_tecnico" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="usuario_tecnico" name="usuario" required>
                </div>
                <div class="mb-3">
                <label for="contrasena_tecnico" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contrasena_tecnico" name="contraseña" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar técnico</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </form>
        </div>
        </div>
    </div>
      
  
  
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
    <script src="{{ asset('js/mapa.js') }}"></script>
</body>

</html>