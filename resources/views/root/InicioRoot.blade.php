@extends('layouts.appGOB')

@section("title", "InicioRoot")

@section('content')

    <!-- Contenido -->

    <div class="container">
		<ol class="breadcrumb top-buffer">
			<li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
			<li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
			<li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
			<li class="active">Superusuario</li>
		</ol>
	</div>

    

    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h2>Menu Superusuario</h2>
                <hr class="red">
                <h4><p>Bienvenido al menú superusuario de geoportal INIFAP, seleccione las opciones que desee realizar:</p></h4>
            </div>
            <div class="col-md-3">
                <div class="list-group">
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('inicio') }}"><img src="images/templatemo_list.png" style="margin-right:10px;">Inicio</a>          
                    <a class="list-group-item" style="text-decoration: none;" data-toggle="modal" data-target="#modalDir"><img src="/images/templatemo_list.png" style="margin-right:10px;">Créditos</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('mapa-root') }}" class="menu-card">
                        <img src="/images/iconos/zacatecas.png" alt="">
                        <span>Mapa global de producción</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('unidades-produccion-root') }}" class="menu-card">
                        <img src="/images/iconos/up.png" alt="">
                        <span>Unidades de producción</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('administrar-cultivos-root') }}" class="menu-card">
                        <img src="/images/iconos/cultivos.png" alt="">
                        <span>Lista de cultivos</span>
                    </a>
                </div>
                <div class="col-md-3 col-sm-6">
                    <a href="{{ route('administrar-usuarios-root') }}" class="menu-card">
                        <img src="/images/iconos/usuarios.png" alt="">
                        <span>Administrar usuarios</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

<script>
    window.userRole = "{{ auth()->user()->role }}";
</script>
<!-- Tu archivo JS -->
<script src="{{ asset('js/root/upRoot.js') }}"></script>

@endsection