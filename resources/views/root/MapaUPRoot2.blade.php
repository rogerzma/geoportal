<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>INIFAP C.E. Zacatecas - Geoportal</title>

    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/images/favicon.ico" rel="shortcut icon">
    <link href="/favicon.ico" rel="shortcut icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">

    <style>
        #auth-bar {
            position: relative;
            z-index: 1030;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        #alertContainer:empty {
            display: none;
        }

        #alertContainer:not(:empty) {
            display: block;
            margin-top: 12px;
            margin-bottom: 12px;
        }

        .buscador-coordenadas-seccion {
            margin-top: 18px;
            margin-bottom: 12px;
        }

        .buscador-coordenadas-seccion label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        #buscador-coordenadas {
            height: 44px;
            font-size: 15px;
            border-radius: 7px;
        }

        #btn-buscar-coordenadas {
            min-width: 120px;
            height: 44px;
            padding: 0 18px;
            border-radius: 7px;
            font-weight: 600;
        }

        #mensaje-busqueda-coordenadas {
            min-height: 20px;
            margin-top: 6px;
            margin-bottom: 0;
            font-size: 14px;
        }

        .mensaje-coordenadas-error {
            color: #a94442;
        }

        .mensaje-coordenadas-exito {
            color: #31733c;
        }

        .texto-instrucciones-mapa {
            margin-top: 18px;
            margin-bottom: 12px;
            text-align: left;
        }

        .card-map-container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .map-wrapper {
            position: relative;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        #map {
            position: relative;
            width: 100%;
            height: 650px;
            margin: 0 0 45px 0;
            padding: 0;
            border: 1px solid #333333;
            z-index: 1;
        }

        .icon-container {
            position: absolute;
            left: 10px;
            bottom: 12px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .icon-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 5px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            font-size: 20px;
        }

        .icon-button:hover {
            background: #f3f3f3;
        }

        #coordinates {
            position: absolute;
            right: 12px;
            top: 70px;
            z-index: 1000;
            width: 235px;
            height: auto !important;
            min-height: 0 !important;
            padding: 10px 12px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
        }

        #coordinates strong {
            display: block;
            margin-bottom: 4px;
        }

        #lat-lng {
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        #mensaje-variantes {
            display: block;
            margin-top: 5px;
        }

        @media (max-width: 767px) {
            #btn-buscar-coordenadas {
                width: 100%;
                margin-top: 8px;
            }

            #map {
                height: 500px;
            }

            #coordinates {
                left: 12px;
                right: 12px;
                top: 70px;
                width: auto;
            }
        }
    </style>
</head>

<body>

<main class="page mb-5" style="text-align:left;">

    <nav class="navbar navbar-expand-md navbar-dark bg-light sub-navbar fixed-top">
        <div class="container">

            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#subNavBarDropdown" aria-controls="subNavBarDropdown" aria-expanded="false" aria-label="Mostrar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand sub-navbar" href="#"></a>

            <div class="collapse navbar-collapse" id="subNavBarDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/tramites" target="_self" title="Ir a trámites del gobierno">Trámites</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/articulos">Blog</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/multimedia">Multimedia</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/prensa">Prensa</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/agenda">Agenda</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/acciones_y_programas">Acciones y programas</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/documentos">Documentos</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://vun.inifap.gob.mx/portalweb/_Transparencia">Transparencia</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/agricultura/es/#344">Contacto</a></li>
                </ul>
            </div>

        </div>
    </nav>

    @auth
        <div class="container-fluid" id="auth-bar">
            <div class="d-flex justify-content-end align-items-center pe-4">

                <span class="me-3 fw-bold mb-0">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Cerrar sesión</button>
                </form>

            </div>
        </div>
    @endauth

    <div class="container">
        <ol class="breadcrumb top-buffer">
            <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
            <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
            <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
            <li><a href="{{ route('root') }}">Superusuario</a></li>
            <li><a href="{{ route('unidades-produccion-root') }}">Administrar unidades de producción</a></li>
            <li class="active">Registrar polígonos</li>
        </ol>
    </div>

    <div class="container">

        <div class="row">

            <div class="col-md-9">

                <h2>Registrar polígonos</h2>
                <hr class="red">

                <h3>{{ $unidadProduccion->nombre_up ?? 'Seleccione una unidad de producción' }}</h3>

                <p>A continuación, seleccione las herramientas de dibujo para agregar o eliminar polígonos.</p>

                <div id="alertContainer" class="alert-position"></div>

                <div class="buscador-coordenadas-seccion">

                    <label for="buscador-coordenadas">Buscar por coordenadas:</label>

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
                    <a class="list-group-item" style="text-decoration:none;" href="{{ route('root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Inicio</a>
                    <a class="list-group-item" style="text-decoration:none;" href="{{ route('unidades-produccion-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Unidades de producción</a>
                    <a class="list-group-item" style="text-decoration:none;" href="{{ route('administrar-usuarios-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Usuarios</a>
                </div>
            </div>

        </div>

        <p class="texto-instrucciones-mapa">Seleccione una parcela para ver sus datos o dibuje una nueva.</p>

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

</main>

@php
    $unidadId = request()->query('up_id');
@endphp

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
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
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
                    <input type="hidden" id="up_id" name="up_id" value="{{ $unidadId }}">
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

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-danger">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error al guardar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p>Por favor, revise los datos e intente de nuevo.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>

<script>
    window.cultivosCatalogo = {{ Illuminate\Support\Js::from($cultivosCatalogo) }};
</script>

<script src="{{ asset('js/root/poligonoRoot.js') }}?v={{ time() }}"></script>
<script src="https://framework-gb.cdn.gob.mx/gm/v3/assets/js/gobmx.js"></script>

</body>
</html>