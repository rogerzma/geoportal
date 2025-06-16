@extends('layouts.appGOB')

@section("title", "InicioAdmin")

@section('content')

    <!-- Contenido -->

    <div class="container">
		<ol class="breadcrumb top-buffer">
			<li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
			<li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
			<li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
			<li class="active">Geoportal</li>
		</ol>
	</div>

    <div class="container">
        <div class="row">
        <div class="col-md-9">
            <h2>Menu Administrador</h2>
            <hr class="red">
            <h4><p>Bienvenido al menú administrador de geoportal INIFAP, seleccione las opciones que desee realizar:</p></h4>
        </div>
        <div class="col-md-3">
            <div class="list-group">
                <a class="list-group-item" style="text-decoration: none;" href="{{ route('inicio') }}"><img src="images/templatemo_list.png" style="margin-right:10px;">Inicio</a>            </div>
        </div>
        </div>

        <div class="col-md-9">
            <div class="row">
                <h4><a href="{{ route('ranchos') }}">Administrar ranchos</a><br></h4>
                <h4><a href="#">Administrar usuarios</a><br></h4>
                <h4><a href="#">Dar de alta a usuarios</a><br><br></h4>
        </div>
        </div>
    </div>


@endsection
