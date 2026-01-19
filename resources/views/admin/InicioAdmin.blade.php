@extends('layouts.appGOB')

@section("title", "InicioAdmin")

@section('content')

    <!-- Contenido -->

    <div class="container">
		<ol class="breadcrumb top-buffer">
			<li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
			<li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
			<li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
			<li class="active">Administrador</li>
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
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('inicio') }}"><img src="images/templatemo_list.png" style="margin-right:10px;">Inicio</a>          
                    <a class="list-group-item" style="text-decoration: none;" data-toggle="modal" data-target="#modalDir"><img src="/images/templatemo_list.png" style="margin-right:10px;">Créditos</a>    
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row">
                <h4><a href="{{ route('mapa-admin') }}">Vista general del geoportal</a><br></h4>
                <h4><a href="{{ route('unidades-produccion-admin') }}">Unidades de producción</a><br></h4>
                <h4><a href="{{ route('administrar-usuarios-admin') }}">Administrar usuarios</a><br></h4>
                <h4><a href="{{ route('registrar-usuarios-admin') }}">Dar de alta a usuarios</a><br><br></h4>
        </div>
        </div>
    </div>

<script>
    window.userRole = "{{ auth()->user()->role }}";
</script>
<!-- Tu archivo JS -->
<script src="{{ asset('js/admin/upAdmin.js') }}"></script>

@endsection