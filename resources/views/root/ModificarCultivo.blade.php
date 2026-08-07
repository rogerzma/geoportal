@extends("layouts.appGOB")

@section("title", "Modificar cultivo")

@section("view-name", "ModificarCultivo")

@section("content")

<style>
    .fila-variante {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }

    .fila-variante .input-variante {
        flex: 1;
    }

    .btn-agregar-variante,
    .btn-quitar-variante {
        margin-left: 8px;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .btn-agregar-variante .glyphicon,
    .btn-quitar-variante .glyphicon {
        font-size: 14px;
    }

    .color-selector-group {
        width: 100%;
    }

    .color-preview-addon {
        padding: 4px 10px;
    }

    #color_picker {
        width: 42px;
        height: 26px;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
    }
</style>

@php
    $tieneVariantes = $cultivo->variantes->isNotEmpty();
@endphp

<div class="container">
    <ol class="breadcrumb top-buffer">
        <li>
            <a href="http://www.gob.mx">
                <i class="icon icon-home"></i>
            </a>
        </li>

        <li>
            <a href="http://www.gob.mx/inifap">
                Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias
            </a>
        </li>

        <li>
            <a href="http://zacatecas.inifap.gob.mx/">
                Inifap C.E. Zacatecas
            </a>
        </li>

        <li>
            <a href="{{ route('inicio') }}">
                Geoportal
            </a>
        </li>

        <li>
            <a href="{{ route('root') }}">
                Superusuario
            </a>
        </li>

        <li class="active">
            Modificar cultivo
        </li>
    </ol>
</div>

<div class="container">

    <div class="row">
        <div class="col-md-9">
            <h2>Modificar cultivo</h2>
            <hr class="red">

            <p>
                Modifique las características del cultivo seleccionado.
            </p>
        </div>

        <div class="col-md-3">
            <div class="list-group">

                <a
                    class="list-group-item"
                    style="text-decoration: none;"
                    href="{{ route('root') }}"
                >
                    <img
                        src="/images/templatemo_list.png"
                        style="margin-right: 10px;"
                        alt=""
                    >
                    Inicio
                </a>

                <a
                    class="list-group-item"
                    style="text-decoration: none;"
                    href="{{ route('mapa-root') }}"
                >
                    <img
                        src="/images/templatemo_list.png"
                        style="margin-right: 10px;"
                        alt=""
                    >
                    Mapa de producción
                </a>

                <a
                    class="list-group-item"
                    style="text-decoration: none;"
                    href="{{ route('administrar-usuarios-root') }}"
                >
                    <img
                        src="/images/templatemo_list.png"
                        style="margin-right: 10px;"
                        alt=""
                    >
                    Usuarios
                </a>

                <a
                    class="list-group-item"
                    style="text-decoration: none;"
                    href="{{ route('administrar-cultivos-root') }}"
                >
                    <img
                        src="/images/templatemo_list.png"
                        style="margin-right: 10px;"
                        alt=""
                    >
                    Cultivos
                </a>

            </div>
        </div>
    </div>

    <div
        class="alert alert-danger"
        id="emptyFieldsAlert"
        style="display: none;"
    >
        Campos vacíos.
    </div>

    <div class="row">

        <div class="col-md-4">
            <h4>Nombre del cultivo</h4>

            <input
                class="form-control"
                placeholder="Ingrese el nombre del cultivo"
                type="text"
                name="nombre"
                id="nombre_cultivo"
                maxlength="150"
                value="{{ old('nombre', $cultivo->nombre) }}"
            >
        </div>

        <div class="col-md-4">
            <h4>Nombre científico</h4>

            <input
                class="form-control"
                placeholder="Ingrese el nombre científico del cultivo"
                type="text"
                name="nombre_cientifico"
                id="nombre_cientifico"
                maxlength="150"
                value="{{ old('nombre_cientifico', $cultivo->nombre_cientifico) }}"
            >
        </div>

        <div class="col-md-4">
            <h4>Categoría</h4>

            <select
                class="form-control"
                name="categoria"
                id="categoria"
                required
            >
                <option value="" disabled>
                    Seleccione...
                </option>

                <option
                    value="Cereal"
                    {{ old('categoria', $cultivo->categoria) === 'Cereal' ? 'selected' : '' }}
                >
                    Cereal
                </option>

                <option
                    value="Leguminosa"
                    {{ old('categoria', $cultivo->categoria) === 'Leguminosa' ? 'selected' : '' }}
                >
                    Leguminosa
                </option>

                <option
                    value="Hortaliza"
                    {{ old('categoria', $cultivo->categoria) === 'Hortaliza' ? 'selected' : '' }}
                >
                    Hortaliza
                </option>

                <option
                    value="Frutal"
                    {{ old('categoria', $cultivo->categoria) === 'Frutal' ? 'selected' : '' }}
                >
                    Frutal
                </option>

                <option
                    value="Oleaginosa"
                    {{ old('categoria', $cultivo->categoria) === 'Oleaginosa' ? 'selected' : '' }}
                >
                    Oleaginosa
                </option>

                <option
                    value="Tuberculo"
                    {{ old('categoria', $cultivo->categoria) === 'Tuberculo' ? 'selected' : '' }}
                >
                    Tubérculo
                </option>

                <option
                    value="Forrajero"
                    {{ old('categoria', $cultivo->categoria) === 'Forrajero' ? 'selected' : '' }}
                >
                    Forrajero
                </option>

                <option
                    value="Forestal"
                    {{ old('categoria', $cultivo->categoria) === 'Forestal' ? 'selected' : '' }}
                >
                    Forestal
                </option>

                <option
                    value="Industrial"
                    {{ old('categoria', $cultivo->categoria) === 'Industrial' ? 'selected' : '' }}
                >
                    Industrial
                </option>

                <option
                    value="Otra"
                    {{ old('categoria', $cultivo->categoria) === 'Otra' ? 'selected' : '' }}
                >
                    Otra
                </option>
            </select>
        </div>

    </div>

    <div class="row" style="margin-top: 15px;">

        <div class="col-md-4">
            <h4>Color</h4>

            <div class="input-group color-selector-group">

                <span class="input-group-addon color-preview-addon">
                    <input
                        type="color"
                        id="color_picker"
                        value="{{ old('color', $cultivo->color ?? '#009933') }}"
                        title="Seleccione un color"
                    >
                </span>

                <input
                    type="text"
                    class="form-control"
                    name="color"
                    id="color"
                    value="{{ old('color', $cultivo->color ?? '#009933') }}"
                    maxlength="7"
                    placeholder="#009933"
                >

            </div>
        </div>

        <div class="col-md-4">
            <h4>¿Activo?</h4>

            <select
                class="form-control"
                name="activo"
                id="activo"
                required
            >
                <option value="" disabled>
                    Seleccione...
                </option>

                <option
                    value="1"
                    {{ old('activo', $cultivo->activo ? '1' : '0') === '1' ? 'selected' : '' }}
                >
                    Sí
                </option>

                <option
                    value="0"
                    {{ old('activo', $cultivo->activo ? '1' : '0') === '0' ? 'selected' : '' }}
                >
                    No
                </option>
            </select>
        </div>

    </div>

    <div class="row" style="margin-top: 20px;">

        <div class="col-md-8">
            <h4>¿El cultivo tiene variedades?</h4>

            <label class="radio-inline">
                <input
                    type="radio"
                    name="tiene_variantes"
                    id="variante_si"
                    value="si"
                    {{ $tieneVariantes ? 'checked' : '' }}
                >
                Sí
            </label>

            <label class="radio-inline">
                <input
                    type="radio"
                    name="tiene_variantes"
                    id="variante_no"
                    value="no"
                    {{ !$tieneVariantes ? 'checked' : '' }}
                >
                No
            </label>
        </div>

    </div>

    <div
        class="row"
        id="contenedor-variantes"
        style="{{ $tieneVariantes ? '' : 'display: none;' }} margin-top: 20px;"
    >
        <div class="col-md-5">

            <h4>Variedades</h4>

            <div id="lista-variantes">

                @foreach($cultivo->variantes as $variante)

                    <div class="fila-variante">

                        <input
                            type="text"
                            class="form-control input-variante"
                            placeholder="Ingrese el nombre de la variante"
                            maxlength="150"
                            value="{{ $variante->nombre }}"
                            data-variante-id="{{ $variante->id }}"
                        >

                        <button
                            type="button"
                            class="btn btn-success btn-agregar-variante"
                            title="Agregar variante"
                        >
                            <span class="glyphicon glyphicon-plus"></span>
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger btn-quitar-variante"
                            title="Eliminar variante"
                        >
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>

                    </div>

                @endforeach

            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 25px;">

        <div class="col-md-4">

            <button
                type="button"
                class="btn btn-primary"
                id="btnActualizarCultivo"
            >
                Actualizar cultivo
            </button>

            <a
                href="{{ route('administrar-cultivos-root') }}"
                class="btn btn-default"
                style="margin-left: 8px;"
            >
                Cancelar
            </a>

        </div>

    </div>

    <input
        type="hidden"
        id="cultivo_id"
        value="{{ $cultivo->id }}"
    >

    <input
        type="hidden"
        id="user_id"
        value="{{ Auth::id() }}"
    >

    <br>

    <div
        class="modal fade"
        id="errorModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="errorModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="errorModalLabel"
                    >
                        Error al modificar el cultivo
                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Cerrar"
                    >
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>

                </div>

                <div class="modal-body">
                    <p>
                        Por favor, revise la información antes de continuar.
                    </p>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const urlActualizarCultivo =
        "{{ route('actualizar.cultivo.root', ['id' => $cultivo->id]) }}";

    const urlCultivosRedirect =
        "{{ route('administrar-cultivos-root') }}";
</script>

<script src="{{ asset('js/root/cultivos/modificarCultivosRoot.js') }}"></script>

@endsection