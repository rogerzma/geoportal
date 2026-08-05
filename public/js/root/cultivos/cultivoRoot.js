$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const $nombre = $('#nombre_cultivo');
    const $nombreCientifico = $('#nombre_cientifico');
    const $categoria = $('#categoria');
    const $activo = $('#activo');
    const $colorHex = $('#color');
    const $colorPicker = $('#color_picker');

    const $varianteSi = $('#variante_si');
    const $varianteNo = $('#variante_no');
    const $contenedorVariantes = $('#contenedor-variantes');
    const $listaVariantes = $('#lista-variantes');

    const $botonRegistrar = $('#validateReportButton');

    function restaurarBotonRegistro() {
        $botonRegistrar
            .prop('disabled', false)
            .text('Registrar cultivo');
    }

    restaurarBotonRegistro();

    $(window).on('pageshow', function (event) {
        restaurarBotonRegistro();
    });

    const HEX_COMPLETO = /^#[0-9A-Fa-f]{6}$/;
    const HEX_PARCIAL = /^#?[0-9A-Fa-f]{0,6}$/;

    function normalizarHex(valor) {
        if (!valor) {
            return '';
        }

        let limpio = valor.trim();

        if (!limpio.startsWith('#')) {
            limpio = `#${limpio}`;
        }

        if (!HEX_PARCIAL.test(limpio)) {
            return null;
        }

        return limpio.toUpperCase();
    }

    function sincronizarDesdePicker() {
        const hex = ($colorPicker.val() || '#000000').toUpperCase();
        $colorHex.val(hex);
    }

    function sincronizarDesdeHexInput() {
        const normalizado = normalizarHex($colorHex.val());

        if (normalizado === null) {
            return;
        }

        $colorHex.val(normalizado);

        if (HEX_COMPLETO.test(normalizado)) {
            $colorPicker.val(normalizado);
        }
    }

    $colorPicker.on('input change', sincronizarDesdePicker);

    $colorHex.on('input', sincronizarDesdeHexInput);

    $colorHex.on('blur', function () {
        const normalizado = normalizarHex($colorHex.val());

        if (
            normalizado === null ||
            !HEX_COMPLETO.test(normalizado)
        ) {
            sincronizarDesdePicker();
            return;
        }

        $colorHex.val(normalizado);
        $colorPicker.val(normalizado);
    });

    sincronizarDesdePicker();

    /**
     * Crear un renglón para una variante.
     *
     * @param {string} valor
     */
    function crearFilaVariante(valor = '') {
		const fila = `
			<div class="fila-variante">
				<input
					type="text"
					class="form-control input-variante"
					placeholder="Ingrese el nombre de la variante"
				>

				<button type="button"
						class="btn btn-success btn-agregar-variante"
						title="Agregar variante">
					<span class="glyphicon glyphicon-plus"></span>
				</button>

				<button type="button"
						class="btn btn-danger btn-quitar-variante"
						title="Eliminar variante">
					<span class="glyphicon glyphicon-trash"></span>
				</button>
			</div>
		`;

		$listaVariantes.append(fila);
		actualizarBotonesQuitar();
	}

    /**
     * Mostrar el botón quitar solamente cuando hay más de una fila.
     */
    function actualizarBotonesQuitar() {
        const totalFilas = $('.fila-variante').length;

        if (totalFilas <= 1) {
            $('.btn-quitar-variante').hide();
        } else {
            $('.btn-quitar-variante').show();
        }
    }

    /**
     * Limpia todos los campos de variantes.
     */
    function limpiarVariantes() {
        $listaVariantes.empty();
    }

    /**
     * Evita insertar caracteres como HTML dentro del value.
     */
    function escaparHtml(valor) {
        return $('<div>').text(valor).html();
    }

    /**
     * Cuando se selecciona Sí:
     * - se muestra el contenedor;
     * - se crea una primera fila si todavía no existe.
     */
    $varianteSi.on('change', function () {
        if (!this.checked) {
            return;
        }

        $contenedorVariantes.show();

        if ($('.fila-variante').length === 0) {
            crearFilaVariante();
        }
    });

    /**
     * Cuando se selecciona No:
     * - se oculta el contenedor;
     * - se eliminan todos los valores.
     */
    $varianteNo.on('change', function () {
        if (!this.checked) {
            return;
        }

        limpiarVariantes();
        $contenedorVariantes.hide();
    });

    /**
     * Agregar una variante.
     */
    $(document).on(
        'click',
        '.btn-agregar-variante',
        function () {
            crearFilaVariante();

            $('.input-variante').last().focus();
        }
    );

    /**
     * Eliminar una variante.
     *
     * Nunca permite dejar cero filas mientras esté seleccionado Sí.
     */
    $(document).on(
        'click',
        '.btn-quitar-variante',
        function () {
            const totalFilas = $('.fila-variante').length;

            if (totalFilas <= 1) {
                return;
            }

            $(this).closest('.fila-variante').remove();
            actualizarBotonesQuitar();
        }
    );

    /**
     * Obtener variantes escritas.
     */
    function obtenerVariantes() {
        const variantes = [];

        $('.input-variante').each(function () {
            const nombreVariante = ($(this).val() || '').trim();

            if (nombreVariante) {
                variantes.push(nombreVariante);
            }
        });

        return variantes;
    }

    /**
     * Eliminar variantes duplicadas, ignorando mayúsculas.
     */
    function obtenerVariantesSinDuplicados() {
        const variantes = obtenerVariantes();
        const nombresRegistrados = new Set();

        return variantes.filter(function (variante) {
            const nombreNormalizado = variante.toLowerCase();

            if (nombresRegistrados.has(nombreNormalizado)) {
                return false;
            }

            nombresRegistrados.add(nombreNormalizado);
            return true;
        });
    }

    $botonRegistrar.on('click', function () {
        const nombre = ($nombre.val() || '').trim();

        const nombreCientifico =
            ($nombreCientifico.val() || '').trim();

        const categoria = $categoria.val();
        const activo = $activo.val();

        const color =
            (($colorHex.val() || '').trim()).toUpperCase();

        const tieneVariantes =
            $('input[name="tiene_variantes"]:checked').val();

        const variantes = obtenerVariantesSinDuplicados();

        if (
            !nombre ||
            !categoria ||
            activo === null ||
            activo === '' ||
            !HEX_COMPLETO.test(color) ||
            !tieneVariantes
        ) {
            mostrarError(
                'Complete todos los campos obligatorios.'
            );
            return;
        }

        if (tieneVariantes === 'si') {
            const totalCampos = $('.input-variante').length;

            if (totalCampos === 0 || variantes.length === 0) {
                mostrarError(
                    'Agregue al menos una variante del cultivo.'
                );
                return;
            }

            let hayCampoVacio = false;

            $('.input-variante').each(function () {
                if (!($(this).val() || '').trim()) {
                    hayCampoVacio = true;
                }
            });

            if (hayCampoVacio) {
                mostrarError(
                    'No deje renglones de variantes vacíos.'
                );
                return;
            }
        }

        $('#emptyFieldsAlert').hide();
        $botonRegistrar
            .prop('disabled', true)
            .text('Registrando...');

        $.ajax({
            url: urlCultivosStore,
            method: 'POST',
            data: {
                nombre,
                nombre_cientifico: nombreCientifico || null,
                categoria,
                color,
                activo,
                variantes
            },

            success: function (response) {
                console.log(response.message);

                window.location.href = urlCultivosRedirect;
            },

            error: function (xhr) {
                console.error(
                    'Error al registrar el cultivo:',
                    xhr.responseText
                );

                let mensaje = 'No fue posible registrar el cultivo.';

                if (xhr.responseJSON?.errors) {
                    mensaje = Object.values(xhr.responseJSON.errors)
                        .flat()[0];
                } else if (xhr.responseJSON?.message) {
                    mensaje = xhr.responseJSON.message;
                }

                $('#emptyFieldsAlert')
                    .text(mensaje)
                    .show();

                $('#errorModalLabel')
                    .text('Error al crear el cultivo');

                $('#errorModal .modal-body p')
                    .text(mensaje);

                $('#errorModal').modal('show');
            },

            complete: function () {
                // Se ejecuta tanto si fue exitosa como si ocurrió un error
                $botonRegistrar
                    .prop('disabled', false)
                    .text('Registrar cultivo');
            }
        });
    });

    function mostrarError(mensaje) {
        $('#emptyFieldsAlert')
            .text(mensaje)
            .show();

        $('#errorModalLabel')
            .text('Error al crear el cultivo');

        $('#errorModal .modal-body p')
            .text(mensaje);

        $('#errorModal').modal('show');
    }
});