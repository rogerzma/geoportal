@extends('layouts.appGOB')

@section('title', 'Mapa UP')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('tecnico') }}">Técnico</a></li>
        <li><a href="{{ route('tecnico-up') }}">Administrar unidades de producción</a></li>
        <li class="active">Registrar polígonos</li>
    </ol>
</div>

<div class="container">

    <div class="row">

        <div class="col-md-9">

            <h2>Registrar polígonos</h2>
            <hr class="red">

            <h3>{{ $unidadProduccion->nombre_up ?? 'Seleccione una unidad de producción' }}</h3>

            <p>
                A continuación, seleccione las herramientas de dibujo para agregar
                o eliminar polígonos.
            </p>

            <div id="alertContainer" class="alert-position"></div>

            <div class="buscador-coordenadas-seccion">

                <label for="buscador-coordenadas">
                    Buscar por coordenadas:
                </label>

                <div class="row">

                    <div class="col-sm-7">
                        <input class="form-control" type="text" id="buscador-coordenadas" placeholder="Ejemplo: 22.7700, -102.5700">
                    </div>

                    <div class="col-sm-3">
                        <button id="btn-buscar-coordenadas" class="btn btn-success" type="button">Buscar</button>
                    </div>

                </div>

                <p id="mensaje-busqueda-coordenadas"></p>

            </div>

        </div>

        <div class="col-md-3">
            <div class="list-group">
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Inicio</a>
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('tecnico-up') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Unidades de producción</a>
                <a class="list-group-item" style="text-decoration:none;" href="{{ route('usuarios-tecnico') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Usuarios</a>
            </div>
        </div>

    </div>

    <p class="texto-instrucciones-mapa">
        Seleccione una parcela para ver sus datos o dibuje una nueva.
    </p>

    <div class="card-map-container">
        <div class="map-wrapper">

            <div id="map">
                <div class="icon-container">
                    <button type="button" class="icon-button" id="draw-poligono" title="Dibujar polígono" aria-label="Dibujar polígono">🖊️</button>
                    <button type="button" class="icon-button" id="delete-poligono" title="Eliminar polígono" aria-label="Eliminar polígono">🗑️</button>
                </div>
            </div>

            <div id="coordinates">
                <strong>Coordenadas:</strong>
                <div id="lat-lng">Lat: --, Lng: --</div>
            </div>

        </div>
    </div>

</div>

{{-- Modal para guardar la parcela --}}
<div class="modal fade" id="parcelaModal" tabindex="-1" aria-labelledby="parcelaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="poligonoForm">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="parcelaModalLabel">Guardar datos de la parcela</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del polígono</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="255" required>
                    </div>

                    <div class="mb-3">
                        <label for="cultivo_id" class="form-label">Cultivo</label>

                        <select class="form-control" name="cultivo_id" id="cultivo_id" required>
                            <option value="">Seleccione...</option>

                            @forelse (($cultivos ?? collect()) as $cultivo)
                                <option value="{{ $cultivo->id }}">{{ $cultivo->nombre }}</option>
                            @empty
                                <option value="" disabled>No existen cultivos activos registrados</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="variante_cultivo_id" class="form-label">Variante</label>

                        <select class="form-control" name="variante_cultivo_id" id="variante_cultivo_id" disabled>
                            <option value="">Primero seleccione un cultivo</option>
                        </select>

                        <small id="mensaje-variantes" class="text-muted"></small>
                    </div>

                    <input type="hidden" id="geom" name="geom">
                    <input type="hidden" id="coordenadas" name="coordenadas">
                    <input type="hidden" id="fecha_creacion" name="fecha_creacion">
                    <input type="hidden" id="up_id" name="up_id" value="{{ $unidadProduccion->id ?? request()->query('up_id') }}">
                    <input type="hidden" id="user_id" name="user_id" value="{{ auth()->id() }}">

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar parcela</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Modal de confirmación de eliminación --}}
<div class="modal fade" id="modalEliminarPoligono" tabindex="-1" aria-labelledby="modalEliminarPoligonoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarPoligonoLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p>¿Seguro que desea eliminar este polígono? Esta acción no se puede deshacer.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarPoligono">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>

        </div>
    </div>
</div>

@php
    $cultivosCatalogo = ($cultivos ?? collect())->map(function ($cultivo) {
        return [
            'id' => $cultivo->id,
            'nombre' => $cultivo->nombre,
            'color' => $cultivo->color,
            'variantes' => $cultivo->variantes->map(function ($variante) {
                return [
                    'id' => $variante->id,
                    'nombre' => $variante->nombre,
                ];
            })->values()->all(),
        ];
    })->values()->all();
@endphp

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
    window.cultivosCatalogo = {{ Illuminate\Support\Js::from($cultivosCatalogo) }};
</script>

<script src="{{ asset('js/tecnico/poligonoTecnico.js') }}?v={{ time() }}"></script>

@endpush