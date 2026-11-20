$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $(
                'meta[name="csrf-token"]'
            ).attr('content')
        }
    });

    const $nombreCultivo = $('#nombre_cultivo');
    const $nombreCientifico = $('#nombre_cientifico');
    const $categoria = $('#categoria');
    const $color = $('#color');
    const $colorPicker = $('#color_picker');
    const $activo = $('#activo');
    const $contenedorVariantes = $('#contenedor-variantes');
    const $listaVariantes = $('#lista-variantes');
    const $botonActualizar = $('#btnActualizarCultivo');

    /**
     * Sincronizar selector de color con campo hexadecimal.
     */
    $colorPicker.on('input change', function () {
        $color.val(
            $(this).val().toUpperCase()
        );
    });

    /**
     * Sincronizar campo hexadecimal con selector de color.
     */
    $color.on('input', function () {
        let valor = $(this).val().trim();

        if (/^#[0-9A-Fa-f]{6}$/.test(valor)) {
            valor = valor.toUpperCase();

            $(this).val(valor);
            $colorPicker.val(valor);
        }
    });

    /**
     * Mostrar variantes.
     */
    $('#variante_si').on('change', function () {
        if (!$(this).is(':checked')) {
            return;
        }

        $contenedorVariantes.show();

        const totalFilas = $listaVariantes
            .find('.fila-variante')
            .length;

        if (totalFilas === 0) {
            agregarFilaVariante();
        }
    });

    /**
     * Ocultar variantes.
     */
    $('#variante_no').on('change', function () {
        if ($(this).is(':checked')) {
            $contenedorVariantes.hide();
        }
    });

    /**
     * Agregar una fila de variante.
     */
    $(document).on(
        'click',
        '.btn-agregar-variante',
        function () {
            agregarFilaVariante();
        }
    );

    /**
     * Quitar una fila de variante.
     */
    $(document).on(
        'click',
        '.btn-quitar-variante',
        function () {
            const $fila = $(this).closest(
                '.fila-variante'
            );

            const totalFilas = $listaVariantes
                .find('.fila-variante')
                .length;

            if (totalFilas > 1) {
                $fila.remove();
                return;
            }

            /*
             * Si solo hay una fila, se limpia.
             */
            $fila
                .find('.input-variante')
                .val('')
                .removeAttr('data-variante-id');
        }
    );

    /**
     * Actualizar cultivo.
     */
    $botonActualizar.on('click', function () {
        ocultarError();

        const nombre = $nombreCultivo
            .val()
            .trim();

        const nombreCientifico = $nombreCientifico
            .val()
            .trim();

        const categoria = $categoria.val();

        const color = $color
            .val()
            .trim()
            .toUpperCase();

        const activo = $activo.val();

        const tieneVariantes = $(
            'input[name="tiene_variantes"]:checked'
        ).val();

        if (
            !nombre ||
            !categoria ||
            !color ||
            activo === null ||
            activo === ''
        ) {
            mostrarError(
                'Complete todos los campos obligatorios.'
            );

            return;
        }

        if (!/^#[0-9A-Fa-f]{6}$/.test(color)) {
            mostrarError(
                'El color debe tener formato hexadecimal, por ejemplo #009933.'
            );

            return;
        }

        if (!tieneVariantes) {
            mostrarError(
                'Indique si el cultivo tiene variantes.'
            );

            return;
        }

        const variantes = [];

        if (tieneVariantes === 'si') {
            const nombresRegistrados = new Set();
            let hayDuplicados = false;

            $listaVariantes
                .find('.input-variante')
                .each(function () {
                    const nombreVariante = $(this)
                        .val()
                        .trim();

                    /*
                     * Ignorar campos vacíos.
                     */
                    if (!nombreVariante) {
                        return;
                    }

                    const clave = nombreVariante
                        .toLocaleLowerCase();

                    if (
                        nombresRegistrados.has(clave)
                    ) {
                        hayDuplicados = true;
                        return false;
                    }

                    nombresRegistrados.add(clave);
                    variantes.push(nombreVariante);
                });

            if (hayDuplicados) {
                mostrarError(
                    'No puede registrar variantes repetidas.'
                );

                return;
            }

            if (variantes.length === 0) {
                mostrarError(
                    'Agregue al menos una variante o seleccione que el cultivo no tiene variantes.'
                );

                return;
            }
        }

        const datos = {
            nombre: nombre,

            nombre_cientifico:
                nombreCientifico || null,

            categoria: categoria,

            color: color,

            activo: Number(activo),

            /*
             * Al seleccionar "No", se envía [].
             * El controlador eliminará las variantes.
             */
            variantes:
                tieneVariantes === 'si'
                    ? variantes
                    : []
        };

        actualizarCultivo(datos);
    });

    /**
     * Realizar petición al controlador.
     */
    function actualizarCultivo(datos) {
        $botonActualizar
            .prop('disabled', true)
            .text('Actualizando...');

        $.ajax({
            url: urlActualizarCultivo,

            method: 'PUT',

            contentType:
                'application/json; charset=UTF-8',

            dataType: 'json',

            data: JSON.stringify(datos),

            success: function (response) {
                /*
                 * Redirigir a la lista después
                 * de actualizar correctamente.
                 */
                window.location.href =
                    urlCultivosRedirect;
            },

            error: function (xhr) {
                console.error(
                    'Error al actualizar el cultivo:',
                    xhr.responseText
                );

                mostrarError(
                    obtenerMensajeError(xhr)
                );
            },

            complete: function () {
                $botonActualizar
                    .prop('disabled', false)
                    .text('Actualizar cultivo');
            }
        });
    }

    /**
     * Crear una nueva fila de variante.
     */
    function agregarFilaVariante() {
        const fila = `
            <div class="fila-variante">

                <input
                    type="text"
                    class="form-control input-variante"
                    placeholder="Ingrese el nombre de la variante"
                    maxlength="150"
                >

                <button
                    type="button"
                    class="btn btn-success btn-agregar-variante"
                    title="Agregar variante"
                >
                    <span class="bootstrap-icons">
                        <i class="bi bi-plus-lg"></i>
                    </span>
                </button>

                <button
                    type="button"
                    class="btn btn-danger btn-quitar-variante"
                    title="Eliminar variante"
                >
                    <span class="bootstrap-icons">
                        <i class="bi bi-trash"></i>
                    </span>
                </button>

            </div>
        `;

        $listaVariantes.append(fila);

        $listaVariantes
            .find('.input-variante')
            .last()
            .focus();
    }

    /**
     * Obtener mensaje de Laravel.
     */
    function obtenerMensajeError(xhr) {
        if (
            xhr.responseJSON &&
            xhr.responseJSON.errors
        ) {
            const mensajes = [];

            Object.values(
                xhr.responseJSON.errors
            ).forEach(function (erroresCampo) {
                if (Array.isArray(erroresCampo)) {
                    erroresCampo.forEach(
                        function (mensaje) {
                            mensajes.push(mensaje);
                        }
                    );
                }
            });

            if (mensajes.length > 0) {
                return mensajes.join('<br>');
            }
        }

        if (
            xhr.responseJSON &&
            xhr.responseJSON.message
        ) {
            return xhr.responseJSON.message;
        }

        return 'No fue posible actualizar el cultivo.';
    }

    /**
     * Mostrar modal de error.
     */
    function mostrarError(mensaje) {
        $('#errorModal .modal-body').html(`
            <div class="alert alert-danger">
                ${mensaje}
            </div>
        `);

        /*
         * Compatible con Bootstrap 5.
         */
        const modalElement =
            document.getElementById('errorModal');

        if (
            typeof bootstrap !== 'undefined' &&
            bootstrap.Modal
        ) {
            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );

            modal.show();
        } else {
            $('#errorModal').modal('show');
        }
    }

    function ocultarError() {
        $('#emptyFieldsAlert').hide();

        $('#errorModal .modal-body').html(`
            <p>
                Por favor, revise la información antes de continuar.
            </p>
        `);
    }
});