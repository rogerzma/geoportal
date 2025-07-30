@extends('layouts.appGOB')

@section("title", "UnidadesProduccion")

@section('content')

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li class="active">Administrar unidades de producción</li>
    </ol>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <h2>Lista de unidades de producción (UP)</h2>
            <hr class="red">
            <p>Aquí se muestran los cultivos registrados para las diferentes operaciones del sistema.</p>
        </div>
        <div class="col-md-3">
            <div class="list-group">
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-gob') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-usuarios') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
            <form method="POST" action="{{ route('logout') }}">
                  @csrf
                 <button type="submit" class="list-group-item" style="text-decoration: none;"><img src="/images/templatemo_list.png" style="margin-right:10px;">Cerrar sesión</button>
            </form> 
            </div>
        </div>
    </div>
    

    <div class="row">
        <!-- Contenedor de tabla -->
        <div class="col-sm-10 table-responsive" id="tabla-up-wrapper" style="margin-bottom:2em;">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="1" style="background:#009933; color:#FFF;">Nombre</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Localidad</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Responsable</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Teléfono</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Polígonos</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-up">
                    <!-- Datos dinámicos -->
                </tbody>
            </table>
        </div>

        <!-- Mensaje si no hay UPs -->
        <div class="col-sm-10" id="mensaje-vacio" style="display:none; margin-bottom:2em;">
            <div class="alert alert-info">
                No existen unidades de producción registradas.
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
                <p>
                <a href="{{'crear-up'}}" button class="btn btn-primary" type="button">Registrar Unidad de Producción</a>
                </p>
        </div>
    </div>
    <div class="modal fade" id="modalEliminarUP" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header" style="background:#990000; color:white;">
                <h4 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h4>
            </div>
            <div class="modal-body">
                La unidad de producción se eliminará definitivamente. ¿Desea continuar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="btn btn-danger">Eliminar</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/up.js') }}"></script>

@endsection