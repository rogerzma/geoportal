@extends('layouts.appGOB')

@section('title', 'Mapa Admin')

@section('content')

<!-- Contenedor de la barra de navegación -->
<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('admin') }}">Administrador</a></li>
        <li class="active">Vista general del mapa</li>
    </ol>
</div>

<!-- Contenedor principal -->
<div class="container">

    <div class="row">

        <!-- Columna principal -->
        <div class="col-md-9">

            <h2>Vista general de los polígonos</h2>
            <hr class="red">

            <div id="alertContainer" class="alert-position"></div>

            <p>
                Número de hectáreas intervenidas:
                <span id="hectareas-totales">--</span>
            </p>

            <div id="toggle-tabla-cultivos-wrap">
                <button class="btn btn-primary" type="button" id="toggle-tabla-cultivos">Ocultar cultivos</button>
            </div>

            <div id="tabla-cultivos-section">

                <label for="buscador-cultivos">Buscar cultivo:</label>

                <input class="form-control" type="text" id="buscador-cultivos" placeholder="Escribe el nombre del cultivo">

                <div class="table-responsive card-map-container" id="tabla-up-wrapper">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="background:#009933; color:#FFF;">Cultivo</th>
                                <th style="background:#009933; color:#FFF;">No. de hectáreas</th>
                                <th style="background:#009933; color:#FFF;">Visualizar</th>
                            </tr>
                        </thead>

                        <tbody id="tabla-cultivos-body">
                            <!-- Datos dinámicos -->
                        </tbody>
                    </table>

                    <div id="paginacion-cultivos" class="text-center"></div>
                </div>

            </div>

            <div style="margin-top:12px;">
                <p>Buscar por coordenadas:</p>
            </div>

            <div class="row" id="buscador-coordenadas-row">

                <div class="col-sm-6" id="buscador-coordenadas-wrapper">
                    <input class="form-control" type="text" id="buscador-coordenadas" placeholder="Ejemplo: 22.7700, -102.5700">
                </div>

                <div class="col-sm-6">
                    <button id="btn-buscar-coordenadas" class="btn btn-success" type="button">Buscar</button>
                </div>

            </div>

        </div>

        <!-- Menú lateral -->
        <div class="col-md-3">
            <div class="list-group">
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Inicio</a>
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('unidades-produccion-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Unidades de producción</a>
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('administrar-usuarios-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Usuarios</a>
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('administrar-cultivos-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Cultivos</a>
            </div>
        </div>

    </div>

    <!-- El texto y el mapa quedan fuera del row de 9 + 3 columnas -->
    <p class="texto-seleccion-poligono">
        A continuación, seleccione un polígono para ver su información.
    </p>

    <div class="card-map-container">
        <div class="map-wrapper">

            <div id="map"></div>

            <div id="coordinates">
                <strong>Coordenadas:</strong>
                <div id="lat-lng">Lat: --, Lng: --</div>
            </div>

        </div>
    </div>

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
                        <label for="nombre_capturista" class="form-label">Nombre del capturista</label>
                        <input type="text" class="form-control" id="nombre_capturista" name="nombre_capturista" required>
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

@endsection

@push('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="{{ asset('js/admin/mapaAdmin.js') }}?v={{ time() }}"></script>

@endpush