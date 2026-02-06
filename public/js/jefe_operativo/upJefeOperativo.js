$(document).ready(function () {
    // 🛡 CSRF para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Paginación
    let unidadesGlobal = [];
    let paginaActual = 1;
    const unidadesPorPagina = 5;
    const ventanaPaginas = 2;

    // 🧩 Crear nueva UP
    $('#validateReportButton').on('click', function () {
        const nombre_up = $('#nombre_up').val();
        const responsable = $('#responsable').val();
        const localidad = $('#localidad').val();
        const telefono = $('#telefono').val();
        const capturista_id = $('#capturista').val();

        if (!nombre_up || !responsable || !localidad || !telefono) {
            $('#emptyFieldsAlert').show();
            return;
        }
        $('#emptyFieldsAlert').hide();

        $.ajax({
            url: '/unidades-produccion',
            method: 'POST',
            data: {
                nombre_up,
                responsable,
                localidad,
                telefono,
                capturista_id : capturista_id || null
            },
            success: function (response) {
                window.location.href = urlUnidadesProduccion;
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#errorModal').modal('show');
            }
        });
    });

    // 📦 Cargar tabla de UP
        function cargarUnidadesProduccion() {
        $.ajax({
            url: '/unidades-produccion',
            method: 'GET',
            success: function (response) {
                if (response.length === 0) {
                    $('#tabla-up-wrapper').hide();
                    $('#mensaje-vacio').show();
                } else {
                    $('#tabla-up-wrapper').show();
                    $('#mensaje-vacio').hide();

                    unidadesGlobal = response;
                    paginaActual = 1;
                    renderTablaUP();
                }
            },
            error: function (xhr) {
                console.error('Error al cargar las unidades de producción:', xhr.responseText);
                $('#tabla-up-wrapper').hide();
                $('#mensaje-vacio').show().html('<div class="alert alert-danger">No se pudieron cargar los datos.</div>');
            }
        });
    }

    // Tabla paginada
    function renderTablaUP(){
        let filas = '';
        const inicio = (paginaActual - 1) * unidadesPorPagina;
        const fin = inicio + unidadesPorPagina;
        const unidadesPagina = unidadesGlobal.slice(inicio, fin);

        unidadesPagina.forEach(function (up) {
            const capturista = up.capturista ? up.capturista.name : '—';
            filas += `
                <tr>
                    <td>${up.nombre_up}</td>
                    <td>${up.localidad}</td>
                    <td>${capturista}</td>
                    <td>${up.responsable}</td>
                    <td>${up.telefono}</td>
                    <td>
                        <a href="/jefe_operativo/up/poligonos?up_id=${up.id}" title="Ver mapa">
                             <img src="/images/map-icon.png" width="20" height="20" alt="Mapa">
                        </a>
                    </td>
                    <td>
                        <a href="/jefe_operativo/modificar-up/${up.id}" class="glyphicon glyphicon-pencil"></a>
                        <a href="#" class="glyphicon glyphicon-trash btn-eliminar-up" data-id="${up.id}" style="color: #ff0000;"></a>
                    </td>
                </tr>
            `;
        }); $('#tbody-up').html(filas);
        renderPaginacionUP();
    }

    // Renderizar paginación
    function renderPaginacionUP() {
        let totalPaginas = Math.ceil(unidadesGlobal.length / unidadesPorPagina);
        if (totalPaginas <= 1) {
            $('#paginacion-up').html('');
            return;
        }

        let paginacionHtml = '<nav><ul class="pagination justify-content-center">';

        // « Anterior
        paginacionHtml += `
            <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" id="up-anterior">&laquo;</a>
            </li>
        `;

        // Página 1
        paginacionHtml += `
            <li class="page-item ${paginaActual === 1 ? 'active' : ''}">
                <a class="page-link up-page-num" href="#" data-pagina="1">1</a>
            </li>
        `;

        // Ellipsis izquierda
        if (paginaActual > ventanaPaginas + 2) {
            paginacionHtml += `
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            `;
        }

        // Rango central
        let inicio = Math.max(2, paginaActual - ventanaPaginas);
        let fin = Math.min(totalPaginas - 1, paginaActual + ventanaPaginas);

        for (let i = inicio; i <= fin; i++) {
            paginacionHtml += `
                <li class="page-item ${paginaActual === i ? 'active' : ''}">
                    <a class="page-link up-page-num" href="#" data-pagina="${i}">
                        ${i}
                    </a>
                </li>
            `;
        }

        // Ellipsis derecha
        if (paginaActual < totalPaginas - (ventanaPaginas + 1)) {
            paginacionHtml += `
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            `;
        }

        // Última página
        paginacionHtml += `
            <li class="page-item ${paginaActual === totalPaginas ? 'active' : ''}">
                <a class="page-link up-page-num" href="#" data-pagina="${totalPaginas}">
                    ${totalPaginas}
                </a>
            </li>
        `;

        // » Siguiente
        paginacionHtml += `
            <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" id="up-siguiente">&raquo;</a>
            </li>
        `;

        paginacionHtml += '</ul></nav>';

        $('#paginacion-up').html(paginacionHtml);

        // Eventos
        $('#up-anterior').off().on('click', function (e) {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                renderTablaUP();
            }
        });

        $('#up-siguiente').off().on('click', function (e) {
            e.preventDefault();
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaUP();
            }
        });

        $('.up-page-num').off().on('click', function (e) {
            e.preventDefault();
            paginaActual = parseInt($(this).data('pagina'));
            renderTablaUP();
        });
    }

    cargarUnidadesProduccion();

    // 🗑️ Flujo para eliminar UP con modal de confirmación
    let upIdParaEliminar = null;

    $(document).on('click', '.btn-eliminar-up', function (e) {
        e.preventDefault();
        upIdParaEliminar = $(this).data('id'); // ← correcto
        $('#modalEliminarUP').modal('show');
    });

    $('#btnConfirmarEliminar').on('click', function () {
        if (!upIdParaEliminar) return;

        $.ajax({
            url: `/unidades-produccion/${upIdParaEliminar}`,
            method: 'DELETE',
            success: function (response) {
                $('#modalEliminarUP').modal('hide');
                upIdParaEliminar = null;
                cargarUnidadesProduccion();
            },
            error: function (xhr) {
                alert('Error al eliminar la unidad de producción: ' + xhr.responseText);
            }
        });
    });
});
