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

    <div class="row">
        <div class="col-sm-10 table-responsive" style="margin-bottom:2em;">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th colspan="1" style="background:#009933; color:#FFF;">Nombre</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Propietario</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Localidad</th>
                        <th colspan="1" style="background:#009933; color:#FFF;">Telefono</th>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
                </p>
        </div>
    </div>
</div>
