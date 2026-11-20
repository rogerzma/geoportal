@extends('layouts.appGOB')

@section("title", "Administrar Cultivos")

@section('content')

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
        <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
        <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
        <li><a href="{{ route('inicio') }}">Geoportal</a></li>
        <li><a href="{{ route('admin') }}">Administrador</a></li>
        <li class="active">Lista de cultivos</li>
    </ol>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <h2>Lista de cultivos</h2>
            <hr class="red">
            <p>Aquí se muestran los cultivos registrados para las diferentes operaciones del sistema.</p>
        </div>
        <div class="col-md-3">
            <div class="list-group">
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('mapa-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Mapa de producción</a>
            <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-usuarios-admin') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;">Usuarios</a> 
            </div>
        </div>
    </div>
    

    <div class="row">
        <div class="col-sm-10" style="margin-bottom: 12px;">
            <label for="buscador-cultivos">
                Buscar en cultivos
            </label>

            <input
                type="text"
                id="buscador-cultivos"
                class="form-control"
                placeholder="Escribe nombre, nombre científico, categoría, color o estado"
            >
        </div>
        <!-- Contenedor de tabla -->
        <div class="col-sm-10 table-responsive" id="tabla-cultivos-wrapper" style="margin-bottom:2em;">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="1" style="background:#009933; color:#FFF;">Nombre</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Nombre científico</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Categoría</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Color</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Activo</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Opciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-cultivos">
                    <!-- Datos dinámicos -->
                </tbody>
            </table>
            <div class = "row mt-4">
                <div class = "col-md-12 text-center">
                    <div id= "paginacion-cultivos"></div>
                </div>
            </div>

        <!-- Mensaje si no hay UPs -->
        <div class="col-sm-10" id="mensaje-vacio" style="display:none; margin-bottom:2em;">
            <div class="alert alert-info">
                No existen cultivos registrados.
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
                <p>
                <a href="{{ route('registrar-cultivos-admin') }}" class="btn btn-primary">
                    Registrar cultivo
                </a>
                </p>
        </div>
    </div>
    <div class="modal fade" id="modalEliminarCultivo" tabindex="-1" role="dialog" aria-labelledby="modalEliminarCultivoLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header" style="background:#990000; color:white;">
                <h4 class="modal-title" id="modalEliminarCultivoLabel">Confirmar eliminación</h4>
            </div>
            <div class="modal-body">
                El cultivo se eliminará definitivamente. ¿Desea continuar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarEliminarCultivo" class="btn btn-danger">Eliminar</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const urlCultivosData = "{{ route('admin.cultivos.data') }}";
    const urlCultivosDestroyBase = "{{ url('/admin/cultivos') }}";
    const urlEditarCultivoBase = "{{ url('/admin/modificar-cultivo') }}";
</script>
<script src="{{ asset('js/admin/cultivos/administrarCultivosAdmin.js') }}"></script>

@endsection