<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>INIFAP C.E. ZACATECAS</title>
    
    <!-- CSS -->
    <link href="/favicon.ico" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">


    <!-- Respond.js soporte de media queries para Internet Explorer 8 -->
    <!-- ie8.js EventTarget para cada nodo en Internet Explorer 8 -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ie8/0.2.2/ie8.js"></script>
    <![endif]-->

    <nav class="navbar navbar-inverse sub-navbar navbar-fixed-top">
    <div class="container">
        <div class="row">
        <div class="collapse navbar-collapse" id="subenlaces">
            <ul class="nav navbar-nav navbar-right">
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/articulos">Blog</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/multimedia">Multimedia</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/prensa">Prensa</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/agenda">Agenda</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/acciones_y_programas">Acciones y programas</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/documentos">Documentos</a></li>
                <li class="landing-btn"><a href="https://vun.inifap.gob.mx/portalweb/_Transparencia">Transparencia</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/agricultura/es/#344">Contacto</a></li>
            </ul>
        </div>
        </div>
    </nav>
    
</head>

<body>

    <!-- Contenedor de la barra de navegación -->
    <div class="container">
        <ol class="breadcrumb top-buffer">
            <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
            <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
            <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
            <li><a href="{{ route('admin') }}">Administrador</a></li>
            <li><a href="{{ route('unidades-produccion-admin') }}">Administrar unidades de producción</a></li>
            <li class="active">Registrar poligonos</li>
        </ol>
    </div>

    <!-- Contenedor principal centrado -->
    <div class="container">
        <div class="row">
                <div class="col-md-9">
                    <h2>Registrar polígonos</h2>
                    <hr class="red">
                    <h3>{{ $unidadProduccion->nombre_up ?? 'Selecciona una UP' }}</h3>
                   <p>A continuación, seleccione las herramientas de dibujo para agregar o eliminar polígonos.</p>
                </div>
                <div class="col-md-3">
                    <div class="list-group">
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('unidades-produccion-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Unidades de producción</a>
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-usuarios-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
                    </div>
                </div>
            </div>
            <!-- Contenedor de alertas -->
            <div id="alertContainer" class="alert-position container mt-3"></div>
                <div class="row justify-content-end">
                    <div class="col-md-9">
                        
                        <p>Seleccione una parcela para ver sus datos o dibuje una nueva.</p>
                    </div>
                    <div class="col-md-11 card-map-container">
                        <div class="map-wrapper">
                            <!-- Mapa -->
                            <div class="flex-grow-1 p-3">
                                <div id="map">
                                    <div class="icon-container">
                                        <div class="icon-button" id="draw-poligono" title="Dibujar poligono">🖊️</div>
                                        <div class="icon-button" id="delete-poligono" title="Eliminar poligono">🗑️</div>
                                    </div>
                                </div>
                                
                            </div>
                            <div id="coordinates">
                                <strong>Coordenadas:</strong>
                                <div id="lat-lng">Lat: --, Lng: --</div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Modal para guardar datos de la parcela -->
    <div class="modal fade" id="parcelaModal" tabindex="1" aria-labelledby="parcelaModalLabel" aria-hidden="true">
        @php
            $unidadId = request()->query('up_id');
        @endphp
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="poligonoForm">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="parcelaModalLabel">Guardar datos de la parcela</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Solo visibles -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del polígono</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="cultivo" class="form-label">Cultivo</label>
                            <select class="form-control" name="cultivo" id="cultivo" required>
                                <option value="">Seleccione...</option>
                                <option value="Alfalfa">Alfalfa</option>
                                <option value="Algodon">Algodón</option>
                                <option value="Ajo">Ajo</option>
                                <option value="Avena">Avena</option>
                                <option value="Cebada">Cebada</option>
                                <option value="Cebolla">Cebolla</option>
                                <option value="Chile">Chile</option>
                                <option value="Ciruela">Ciruela</option>
                                <option value="Durazno">Durazno</option>
                                <option value="En descanso">En descanso</option>
                                <option value="Fresa">Fresa</option>
                                <option value="Frijol">Frijol</option>
                                <option value="Guayaba">Guayaba</option>
                                <option value="Maiz">Maiz</option>
                                <option value="Manzana">Manzana</option>
                                <option value="Nogal">Nogal</option>
                                <option value="Nopal">Nopal</option>
                                <option value="Pepino">Pepino</option>
                                <option value="Sorgo">Sorgo</option>
                                <option value="Tomate">Tomate</option>
                                <option value="Tomatillo">Tomatillo</option>
                                <option value="Trigo">Trigo</option>
                                <option value="Uva">Uva</option>
                                <option value="Zanahoria">Zanahoria</option>
                            </select>
                        </div>

                        <!-- Ocultos -->
                        <input type="hidden" id="geom" name="geom">
                        <input type="hidden" id="coordenadas" name="coordenadas">
                        <input type="hidden" id="fecha_creacion" name="fecha_creacion">
                        <input type="hidden" id="up_id" name="up_id" value="{{ $unidadId ?? 1 }}"> <!-- Si lo puedes pasar desde el backend -->
                        <input type="hidden" id="user_id" name="user_id" value="{{ auth()->user()->id }}">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar parcela</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de error-->
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

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="modalEliminarPoligono" tabindex="-1" aria-labelledby="modalEliminarPoligonoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminarPoligonoLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Seguro que deseas eliminar este polígono? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarPoligono">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log(typeof bootstrap.Modal); // Ahora sí debería ser "function"
    </script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="{{ asset('js/admin/poligonoAdmin.js') }}"></script>
</body>
</html>