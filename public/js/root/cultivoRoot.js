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

		if (normalizado === null || !HEX_COMPLETO.test(normalizado)) {
			sincronizarDesdePicker();
			return;
		}

		$colorHex.val(normalizado);
		$colorPicker.val(normalizado);
	});

	sincronizarDesdePicker();

	$('#validateReportButton').on('click', function () {
		const nombre = ($nombre.val() || '').trim();
		const nombre_cientifico = ($nombreCientifico.val() || '').trim();
		const categoria = $categoria.val();
		const activo = $activo.val();
		const color = (($colorHex.val() || '').trim()).toUpperCase();

		if (!nombre || !categoria || activo === null || activo === '' || !HEX_COMPLETO.test(color)) {
			$('#emptyFieldsAlert').show();
			$('#errorModal').modal('show');
			return;
		}

		$('#emptyFieldsAlert').hide();

		$.ajax({
			url: urlCultivosStore,
			method: 'POST',
			data: {
				nombre,
				nombre_cientifico: nombre_cientifico || null,
				categoria,
				color,
				activo
			},
			success: function () {
				window.location.href = urlCultivosRedirect;
			},
			error: function (xhr) {
				console.error(xhr.responseText);
				$('#errorModal').modal('show');
			}
		});
	});
});
