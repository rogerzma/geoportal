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
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('unidades-produccion') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Unidades de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-gob') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a>
            </div>
        </div>
    </div>
    

    <div class="row">
        <div class="col-sm-10 table-responsive" style="margin-bottom:2em;">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="1" style="background:#009933; color:#FFF;">Nombre</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Propietario</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Localidad</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Telefono</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Poligonos</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Opciones</th>
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
                <a href="{{'crear-up'}}" button class="btn btn-primary" type="button">Registrar Unidad de Producción</a>
                </p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/up.js') }}"></script>

@endsection