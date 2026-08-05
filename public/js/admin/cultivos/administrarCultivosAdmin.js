$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let cultivosGlobal = [];
    let paginaActual = 1;
    let cultivoIdParaDesactivar = null;

    const cultivosPorPagina = 5;
    const ventanaPaginas = 2;

    /**
     * Convierte el valor de activo recibido desde Laravel
     * a un booleano de JavaScript.
     */
    function estaActivo(valor) {
        return (
            valor === true ||
            valor === 1 ||
            valor === '1'
        );
    }

    /**
     * Valida que el color sea hexadecimal.
     */
    function obtenerColorSeguro(color) {
        const valor = String(color || '').trim();

        return /^#[0-9A-Fa-f]{6}$/.test(valor)
            ? valor
            : '#000000';
    }

    /**
     * Evita insertar HTML recibido desde la base de datos.
     */
    function escaparHtml(valor) {
        return $('<div>')
            .text(valor ?? '')
            .html();
    }

    /**
     * Normaliza texto para realizar búsquedas
     * sin distinguir mayúsculas ni acentos.
     */
    function normalizarTexto(valor) {
        return String(valor || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .trim();
    }

    /**
     * Devuelve los cultivos que coinciden con el buscador.
     */
    function obtenerCultivosFiltrados() {
        const filtro = normalizarTexto(
            $('#buscador-cultivos').val()
        );

        if (!filtro) {
            return cultivosGlobal;
        }

        return cultivosGlobal.filter(function (cultivo) {
            const activo = estaActivo(cultivo.activo);

            const estadoTexto = activo
                ? 'activo si'
                : 'inactivo no';

            const valoresBusqueda = [
                cultivo.nombre,
                cultivo.nombre_cientifico,
                cultivo.categoria,
                cultivo.color,
                estadoTexto
            ];

            return valoresBusqueda.some(function (valor) {
                return normalizarTexto(valor).includes(filtro);
            });
        });
    }

    /**
     * Obtiene los cultivos desde Laravel.
     */
    function cargarCultivos() {
        $.ajax({
            url: urlCultivosData,
            method: 'GET',
            dataType: 'json',

            success: function (response) {
                if (Array.isArray(response)) {
                    cultivosGlobal = response;
                } else if (
                    response &&
                    Array.isArray(response.data)
                ) {
                    cultivosGlobal = response.data;
                } else {
                    cultivosGlobal = [];
                }

                paginaActual = 1;

                if (cultivosGlobal.length === 0) {
                    $('#tabla-cultivos-wrapper').hide();
                    $('#paginacion-cultivos').empty();

                    $('#mensaje-vacio')
                        .show()
                        .html(`
                            <div class="alert alert-info">
                                No existen cultivos registrados.
                            </div>
                        `);

                    return;
                }

                $('#tabla-cultivos-wrapper').show();
                $('#mensaje-vacio').hide();

                renderTablaCultivos();
            },

            error: function (xhr) {
                console.error(
                    'Error al cargar los cultivos:',
                    xhr.responseText
                );

                $('#tabla-cultivos-wrapper').hide();
                $('#paginacion-cultivos').empty();

                $('#mensaje-vacio')
                    .show()
                    .html(`
                        <div class="alert alert-danger">
                            No fue posible cargar los cultivos.
                        </div>
                    `);
            }
        });
    }

    /**
     * Renderiza la página actual de la tabla.
     */
    function renderTablaCultivos() {
        const cultivosFiltrados =
            obtenerCultivosFiltrados();

        const totalPaginas = Math.ceil(
            cultivosFiltrados.length / cultivosPorPagina
        );

        if (
            totalPaginas > 0 &&
            paginaActual > totalPaginas
        ) {
            paginaActual = totalPaginas;
        }

        if (paginaActual < 1) {
            paginaActual = 1;
        }

        if (cultivosFiltrados.length === 0) {
            $('#tbody-cultivos').html(`
                <tr>
                    <td colspan="6" class="text-center">
                        No se encontraron cultivos relacionados
                        con la búsqueda.
                    </td>
                </tr>
            `);

            $('#paginacion-cultivos').empty();
            return;
        }

        const inicio =
            (paginaActual - 1) * cultivosPorPagina;

        const fin =
            inicio + cultivosPorPagina;

        const cultivosPagina =
            cultivosFiltrados.slice(inicio, fin);

        let filas = '';

        cultivosPagina.forEach(function (cultivo) {
            const id = Number(cultivo.id);

            const nombre =
                cultivo.nombre || '—';

            const nombreCientifico =
                cultivo.nombre_cientifico || '—';

            const categoria =
                cultivo.categoria || '—';

            const color =
                obtenerColorSeguro(cultivo.color);

            const activo =
                estaActivo(cultivo.activo);

            const activoTexto =
                activo ? 'Sí' : 'No';

            const activoClase =
                activo
                    ? 'label-success'
                    : 'label-default';

            filas += `
                <tr>
                    <td>
                        ${escaparHtml(nombre)}
                    </td>

                    <td>
                        ${escaparHtml(nombreCientifico)}
                    </td>

                    <td>
                        ${escaparHtml(categoria)}
                    </td>

                    <td>
                        <span
                            class="muestra-color"
                            style="
                                display:inline-block;
                                width:24px;
                                height:24px;
                                background-color:${color};
                                border:1px solid #777;
                                vertical-align:middle;
                                margin-right:8px;
                            "
                            aria-hidden="true"
                        ></span>

                        <span>
                            ${escaparHtml(color)}
                        </span>
                    </td>

                    <td>
                        <span class="label ${activoClase}">
                            ${activoTexto}
                        </span>
                    </td>

                    <td>
                        <a
                            href="${urlEditarCultivoBase}/${id}"
                            title="Modificar cultivo"
                            aria-label="Modificar cultivo"
                            style="
                                text-decoration:none;
                                margin-right:12px;
                            "
                        >
                            <span
                                class="bootstrap-icons"
                                aria-hidden="true"
                            >
                                <i class="bi bi-pencil"></i>
                            </span>
                        </a>

                        ${
                            activo
                                ? `
                                    <a
                                        href="#"
                                        class="btn-eliminar-cultivo"
                                        data-id="${id}"
                                        data-nombre="${escaparHtml(nombre)}"
                                        title="Desactivar cultivo"
                                        aria-label="Desactivar cultivo"
                                        style="
                                            color:#ff0000;
                                            text-decoration:none;
                                        "
                                    >
                                        <span
                                            class="bootstrap-icons"
                                            aria-hidden="true"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </span>
                                    </a>
                                `
                                : `
                                    <span
                                        class="text-muted"
                                        title="El cultivo ya está inactivo"
                                    >
                                        Inactivo
                                    </span>
                                `
                        }
                    </td>
                </tr>
            `;
        });

        $('#tbody-cultivos').html(filas);

        renderPaginacionCultivos();
    }

    /**
     * Genera un botón numérico de paginación.
     */
    function crearBotonPagina(pagina) {
        return `
            <li class="${
                paginaActual === pagina
                    ? 'active'
                    : ''
            }">
                <a
                    href="#"
                    class="pagina-cultivo"
                    data-pagina="${pagina}"
                    aria-label="Ir a la página ${pagina}"
                >
                    ${pagina}
                </a>
            </li>
        `;
    }

    /**
     * Renderiza paginación compacta.
     */
    function renderPaginacionCultivos() {
        const cultivosFiltrados =
            obtenerCultivosFiltrados();

        const totalPaginas = Math.ceil(
            cultivosFiltrados.length / cultivosPorPagina
        );

        if (totalPaginas <= 1) {
            $('#paginacion-cultivos').empty();
            return;
        }

        let html = `
            <nav aria-label="Paginación de cultivos">
                <ul class="pagination">

                    <li class="${
                        paginaActual === 1
                            ? 'disabled'
                            : ''
                    }">
                        <a
                            href="#"
                            id="cultivos-anterior"
                            aria-label="Página anterior"
                        >
                            &laquo;
                        </a>
                    </li>
        `;

        html += crearBotonPagina(1);

        if (paginaActual > ventanaPaginas + 2) {
            html += `
                <li class="disabled">
                    <span>…</span>
                </li>
            `;
        }

        const inicio = Math.max(
            2,
            paginaActual - ventanaPaginas
        );

        const fin = Math.min(
            totalPaginas - 1,
            paginaActual + ventanaPaginas
        );

        for (
            let pagina = inicio;
            pagina <= fin;
            pagina++
        ) {
            html += crearBotonPagina(pagina);
        }

        if (
            paginaActual <
            totalPaginas - ventanaPaginas - 1
        ) {
            html += `
                <li class="disabled">
                    <span>…</span>
                </li>
            `;
        }

        if (totalPaginas > 1) {
            html += crearBotonPagina(totalPaginas);
        }

        html += `
                    <li class="${
                        paginaActual === totalPaginas
                            ? 'disabled'
                            : ''
                    }">
                        <a
                            href="#"
                            id="cultivos-siguiente"
                            aria-label="Página siguiente"
                        >
                            &raquo;
                        </a>
                    </li>

                </ul>
            </nav>
        `;

        $('#paginacion-cultivos').html(html);
    }

    /**
     * Página anterior.
     */
    $(document).on(
        'click',
        '#cultivos-anterior',
        function (event) {
            event.preventDefault();

            if (
                $(this).parent().hasClass('disabled')
            ) {
                return;
            }

            if (paginaActual > 1) {
                paginaActual--;
                renderTablaCultivos();
            }
        }
    );

    /**
     * Página siguiente.
     */
    $(document).on(
        'click',
        '#cultivos-siguiente',
        function (event) {
            event.preventDefault();

            if (
                $(this).parent().hasClass('disabled')
            ) {
                return;
            }

            const totalPaginas = Math.ceil(
                obtenerCultivosFiltrados().length /
                cultivosPorPagina
            );

            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaCultivos();
            }
        }
    );

    /**
     * Página seleccionada.
     */
    $(document).on(
        'click',
        '.pagina-cultivo',
        function (event) {
            event.preventDefault();

            const nuevaPagina = Number(
                $(this).data('pagina')
            );

            if (
                Number.isInteger(nuevaPagina) &&
                nuevaPagina > 0
            ) {
                paginaActual = nuevaPagina;
                renderTablaCultivos();
            }
        }
    );

    /**
     * Buscador de cultivos.
     */
    $('#buscador-cultivos').on(
        'input',
        function () {
            paginaActual = 1;
            renderTablaCultivos();
        }
    );

    /**
     * Abre el modal de desactivación.
     */
    $(document).on(
        'click',
        '.btn-eliminar-cultivo',
        function (event) {
            event.preventDefault();

            cultivoIdParaDesactivar =
                Number($(this).data('id'));

            const nombreCultivo =
                $(this).data('nombre') || '';

            if (
                !Number.isInteger(cultivoIdParaDesactivar) ||
                cultivoIdParaDesactivar <= 0
            ) {
                cultivoIdParaDesactivar = null;
                return;
            }

            $('#nombre-cultivo-eliminar')
                .text(nombreCultivo);

            $('#modalEliminarCultivo')
                .modal('show');
        }
    );

    /**
     * Limpia la selección cuando se cierra el modal.
     */
    $('#modalEliminarCultivo').on(
        'hidden.bs.modal',
        function () {
            cultivoIdParaDesactivar = null;

            $('#nombre-cultivo-eliminar').text('');
        }
    );

    /**
     * Confirma la desactivación.
     */
    $('#btnConfirmarEliminarCultivo').on(
        'click',
        function () {
            if (!cultivoIdParaDesactivar) {
                return;
            }

            const $boton = $(this);

            $boton
                .prop('disabled', true)
                .text('Procesando...');

            $.ajax({
                url:
                    `${urlCultivosDestroyBase}/` +
                    cultivoIdParaDesactivar,

                method: 'DELETE',
                dataType: 'json',

                success: function () {
                    $('#modalEliminarCultivo')
                        .modal('hide');

                    cultivoIdParaDesactivar = null;

                    cargarCultivos();
                },

                error: function (xhr) {
                    console.error(
                        'Error al desactivar el cultivo:',
                        xhr.responseText
                    );

                    let mensaje =
                        'No fue posible desactivar el cultivo.';

                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {
                        mensaje =
                            xhr.responseJSON.message;
                    }

                    mostrarAlerta(mensaje, 'danger');
                },

                complete: function () {
                    $boton
                        .prop('disabled', false)
                        .text('Desactivar');
                }
            });
        }
    );

    /**
     * Muestra una alerta Bootstrap en pantalla.
     */
    function mostrarAlerta(mensaje, tipo = 'success') {
        let $contenedor = $('#alertContainer');

        if ($contenedor.length === 0) {
            $contenedor = $('<div>', {
                id: 'alertContainer'
            });

            $('.container').first().after($contenedor);
        }

        const $alerta = $(`
            <div
                class="alert alert-${tipo} alert-dismissible fade in"
                role="alert"
            >
                <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Cerrar"
                >
                    <span aria-hidden="true">&times;</span>
                </button>

                ${escaparHtml(mensaje)}
            </div>
        `);

        $contenedor.empty().append($alerta);

        window.setTimeout(function () {
            $alerta.alert('close');
        }, 5000);
    }

    cargarCultivos();
});