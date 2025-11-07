@extends('layouts.appGOB')

@section("title", "InicioTecnico")

@section('content')

    <!-- Contenido -->

    <div class="container">
		<ol class="breadcrumb top-buffer">
			<li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
			<li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
			<li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
            <li class="active">Tecnico</li>
		</ol>
	</div>

    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h2>Menu Técnico</h2>
                <hr class="red">
                <h4><p>Bienvenido al menú técnico de geoportal INIFAP, seleccione las opciones que desee realizar:</p></h4>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('inicio') }}"><img src="images/templatemo_list.png" style="margin-right:10px;">Inicio</a>          
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="list-group-item" style="text-decoration: none;"><img src="images/templatemo_list.png" style="margin-right:10px;">Cerrar sesión</button>
                    </form>     
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row">
                <h4><a href="{{ route('mapa-tecnico') }}">Vista general del geoportal</a><br></h4>
                <h4><a href="{{ route('tecnico-up') }}">Unidades de producción</a><br></h4>
                <h4><a href="{{ route('usuarios-tecnico') }}">Administrar usuarios</a><br></h4>
                <h4><a href="{{ route('registrar-usuarios-tecnico') }}">Dar de alta a usuarios</a><br><br></h4>
        </div>
        </div>
    </div>


@endsection