@extends('layouts.appGOB')

@section("title", "UnidadesProduccion")

@section('content')

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('admin') }}">Administrador</a></li>
        <li class="active">Administrar usuarios</li>
    </ol>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <h2>Lista de usuarios del sistema</h2>
            <hr class="red">
            <p>La siguiente lista muestra los diferentes usuarios registrados para el sistema de Geoportal.</p>
        </div>
        <div class="col-md-3">
            <div class="list-group">
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('unidades-produccion-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Unidades de producción</a>
        </div>
    </div>
    

    <div class="row">
        <div class="col-sm-10 table-responsive" style="margin-bottom:2em;">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="1" style="background:#990000; color:#FFF;">Nombre</th>
                        <th colspan="1" style="background:#990000; color:#FFF;">Correo</th>
                        <th colspan="1" style="background:#990000; color:#FFF;">Telefono</th>
                        <th colspan="1" style="background:#990000; color:#FFF;">Roles</th>
                        <th colspan="1" style="background:#990000; color:#FFF;">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-up">
                    <!-- Aquí se llenarán las filas dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
                <p>
                <a href="{{'registro-usuarios'}}" button class="btn btn-primary" type="button">Registrar usuario nuevo</a>
                </p>
        </div>
    </div>

    <div class="modal fade" id="modalEliminarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEliminarLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header" style="background:#990000; color:white;">
                <h4 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h4>
            </div>
            <div class="modal-body">
                El usuario se eliminará definitivamente. ¿Desea continuar?
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
<script src="{{ asset('js/admin/usuariosAdmin.js') }}"></script>

@endsection