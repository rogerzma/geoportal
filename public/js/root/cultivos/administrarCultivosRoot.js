$(document).ready(function () {
    // CSRF para todas las peticiones AJAX
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
     * Convierte diferentes valores recibidos desde Laravel
     * a un booleano real de JavaScript.
     */
    function estaActivo(valor) {
        return (
            valor === true ||
            valor === 1 ||
            valor === '1'
        );
    }

    /**
     * Valida el código hexadecimal antes de usarlo
     * como color dentro del atributo style.
     */
    function obtenerColorSeguro(color) {
        const valor = String(color || '').trim();

        const esHexadecimal = /^#[0-9A-Fa-f]{6}$/.test(valor);

        return esHexadecimal
            ? valor
            : '#000000';
    }

    /**
     * Escapar contenido para evitar insertar HTML
     * recibido desde la base de datos.
     */
    function escaparHtml(valor) {
        return $('<div>')
            .text(valor ?? '')
            .html();
    }

    /**
     * Obtiene los cultivos que coinciden con
     * el contenido del buscador.
     */
    function obtenerCultivosFiltrados() {
        const filtro = (
            $('#buscador-cultivos').val() || ''
        )
            .trim()
            .toLowerCase();

        if (!filtro) {
            return cultivosGlobal;
        }

        return cultivosGlobal.filter(function (cultivo) {
            const activo = estaActivo(cultivo.activo);

            const estadoTexto = activo
                ? 'activo sí si'
                : 'inactivo no';

            const valoresBusqueda = [
                cultivo.nombre,
                cultivo.nombre_cientifico,
                cultivo.categoria,
                cultivo.color,
                estadoTexto
            ];

            return valoresBusqueda.some(function (valor) {
                return String(valor || '')
                    .toLowerCase()
                    .includes(filtro);
            });
        });
    }

    /**
     * Obtener los cultivos desde Laravel.
     */
    function cargarCultivos() {
        $.ajax({
            url: urlCultivosData,
            method: 'GET',
            dataType: 'json',

            success: function (response) {
                /*
                 * Permite recibir:
                 *
                 * 1. Un arreglo directo:
                 *    [{...}, {...}]
                 *
                 * 2. Una respuesta con propiedad data:
                 *    { data: [{...}, {...}] }
                 */
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

                    $('#mensaje-vacio')
                        .show()
                        .html(`
                            <div class="alert alert-info">
                                No existen cultivos registrados.
                            </div>
                        `);

                    $('#paginacion-cultivos').empty();

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
            cultivosFiltrados.length /
            cultivosPorPagina
        );

        /*
         * Evita que la página actual quede fuera del rango
         * después de una búsqueda o actualización.
         */
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
            (paginaActual - 1) *
            cultivosPorPagina;

        const fin =
            inicio + cultivosPorPagina;

        const cultivosPagina =
            cultivosFiltrados.slice(inicio, fin);

        let filas = '';

        cultivosPagina.forEach(function (cultivo) {
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
                            href="${urlEditarCultivoBase}/${cultivo.id}"
                            class="glyphicon glyphicon-pencil"
                            title="Modificar cultivo"
                        ></a>

                        ${
                            activo
                                ? `
                                    <a
                                        href="/root/modificar-cultivo/${cultivo.id}"
                                        title="Modificar unidad de producción"
                                        aria-label="Modificar unidad de producción"
                                        style="
                                            text-decoration: none;
                                            margin-right: 12px;
                                        "
                                    >
                                        <span class="bootstrap-icons" aria-hidden="true">
                                            <i class="bi bi-pencil"></i>
                                        </span>
                                    </a>
                                    <a
                                        href="#"
                                        class="btn-eliminar-cultivo"
                                        data-id="${cultivo.id}"
                                        title="Eliminar cultivo"
                                        aria-label="Eliminar cultivo"
                                        style="
                                            color: #ff0000;
                                            text-decoration: none;
                                        "
                                    >
                                        <span class="bootstrap-icons" aria-hidden="true">
                                            <i class="bi bi-trash"></i>
                                        </span>
                                </a>
                                `
                                
                                : ''}
                    </td>
                </tr>
            `;
        });

        $('#tbody-cultivos').html(filas);

        renderPaginacionCultivos();
    }

    /**
     * Renderiza una paginación compacta:
     *
     * Primera página
     * Páginas cercanas
     * Última página
     */
    function renderPaginacionCultivos() {
        const cultivosFiltrados =
            obtenerCultivosFiltrados();

        const totalPaginas = Math.ceil(
            cultivosFiltrados.length /
            cultivosPorPagina
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

        // Primera página
        html += crearBotonPagina(1);

        // Puntos suspensivos del lado izquierdo
        if (
            paginaActual >
            ventanaPaginas + 2
        ) {
            html += `
                <li class="disabled">
                    <span>…</span>
                </li>
            `;
        }

        // Rango central
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

        // Puntos suspensivos del lado derecho
        if (
            paginaActual <
            totalPaginas -
            ventanaPaginas -
            1
        ) {
            html += `
                <li class="disabled">
                    <span>…</span>
                </li>
            `;
        }

        // Última página
        if (totalPaginas > 1) {
            html += crearBotonPagina(
                totalPaginas
            );
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
     * Crea un botón numérico de la paginación.
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
                >
                    ${pagina}
                </a>
            </li>
        `;
    }

    /**
     * Ir a la página anterior.
     */
    $(document).on(
        'click',
        '#cultivos-anterior',
        function (event) {
            event.preventDefault();

            if (
                $(this)
                    .parent()
                    .hasClass('disabled')
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
     * Ir a la página siguiente.
     */
    $(document).on(
        'click',
        '#cultivos-siguiente',
        function (event) {
            event.preventDefault();

            if (
                $(this)
                    .parent()
                    .hasClass('disabled')
            ) {
                return;
            }

            const cultivosFiltrados =
                obtenerCultivosFiltrados();

            const totalPaginas = Math.ceil(
                cultivosFiltrados.length /
                cultivosPorPagina
            );

            if (
                paginaActual <
                totalPaginas
            ) {
                paginaActual++;
                renderTablaCultivos();
            }
        }
    );

    /**
     * Cambiar a una página específica.
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
     * Buscar cultivos mientras el usuario escribe.
     */
    $('#buscador-cultivos').on(
        'input',
        function () {
            paginaActual = 1;
            renderTablaCultivos();
        }
    );

    /**
     * Abrir el modal de confirmación.
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
                !Number.isInteger(
                    cultivoIdParaDesactivar
                ) ||
                cultivoIdParaDesactivar <= 0
            ) {
                cultivoIdParaDesactivar = null;
                return;
            }

            /*
             * Este elemento es opcional.
             * Si no existe en el modal, no provoca error.
             */
            $('#nombre-cultivo-eliminar').text(
                nombreCultivo
            );

            $('#modalEliminarCultivo')
                .modal('show');
        }
    );

    /**
     * Limpiar el cultivo seleccionado cuando
     * el modal sea cerrado.
     */
    $('#modalEliminarCultivo').on(
        'hidden.bs.modal',
        function () {
            cultivoIdParaDesactivar = null;

            $('#nombre-cultivo-eliminar')
                .text('');
        }
    );

    /**
     * Desactivar el cultivo.
     */
    $('#btnConfirmarEliminarCultivo').on(
        'click',
        function () {
            if (
                !cultivoIdParaDesactivar
            ) {
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

                success: function (response) {
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

                    alert(mensaje);
                },

                complete: function () {
                    $boton
                        .prop('disabled', false)
                        .text('Desactivar');
                }
            });
        }
    );

    // Primera carga
    cargarCultivos();
});