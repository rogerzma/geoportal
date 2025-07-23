// Control de dibujo
let drawnLayer = null;

map.on(L.Draw.Event.CREATED, function (event) {
    if (drawnLayer) {
        drawnItems.removeLayer(drawnLayer);
    }

    drawnLayer = event.layer;
    drawnItems.addLayer(drawnLayer);

    const latlngs = drawnLayer.getLatLngs()[0];
    const coords = latlngs.map(coord => `${coord.lng} ${coord.lat}`).join(', ');
    const wktPolygon = `POLYGON((${coords}, ${latlngs[0].lng} ${latlngs[0].lat}))`; // cerramos el polígono

    document.getElementById('geom').value = wktPolygon;
    document.getElementById('coordenadas').value = JSON.stringify(latlngs);

    const modal = new bootstrap.Modal(document.getElementById('parcelaModal'));
    modal.show();
});

// Botón: Activar dibujo
document.getElementById("draw-parcela").addEventListener("click", function () {
    var selectedColor = document.getElementById("polygon-color").value;

    var polygonOptions = {
        allowIntersection: false,
        showArea: true,
        shapeOptions: { 
            color: selectedColor,
            fillOpacity: 1
        }
    };

    new L.Draw.Polygon(map, polygonOptions).enable();
});

// Modo de eliminación
let deleteMode = false;
document.getElementById("delete-parcela").addEventListener("click", function () {
    deleteMode = !deleteMode; // Alternar el modo de eliminación
    if (deleteMode) {
        mostrarToast('Modo de eliminación activado. Haz clic en una parcela para eliminarla.', 'primary');
    } else {
        mostrarToast('Modo de eliminación desactivado.', 'primary');
    }
});

// Botón hamburguesa: Desplazar mapa sin oscurecer
function toggleMenu() {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main-container");

    sidebar.classList.toggle("active");
}

// Función para mostrar un toast
function mostrarToast(mensaje, tipo = 'primary') {
    const toastContainer = document.getElementById('toastContainer');
    const toastTemplate = document.getElementById('toastMessage');

    // Clonar el template del toast
    const toast = toastTemplate.cloneNode(true);
    toast.classList.remove('text-bg-primary', 'text-bg-success', 'text-bg-danger');
    toast.classList.add(`text-bg-${tipo}`); // Cambiar el color según el tipo
    toast.querySelector('.toast-body').textContent = mensaje;

    // Agregar el toast al contenedor
    toastContainer.appendChild(toast);

    // Inicializar el toast con Bootstrap
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();

    // Eliminar el toast del DOM después de que desaparezca
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

// Guardar parcela
document.getElementById('parcelaForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const data = {
        cultivo: document.getElementById('cultivo').value,
        coordenadas: document.getElementById('coordenadas').value,
        geom: document.getElementById('geom').value,
        nombre_productor: document.getElementById('nombre_productor').value,
        tecnico_id: parseInt(document.getElementById('tecnico_id').value)
    };

    fetch('/api/parcelas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(resp => resp.json())
    .then(json => {
        mostrarToast(json.message || 'Parcela guardada correctamente', 'success');
        bootstrap.Modal.getInstance(document.getElementById('parcelaModal')).hide();
        document.getElementById('parcelaForm').reset();
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al guardar la parcela', 'danger');
    });
});

// Guardar técnico
document.getElementById('tecnicoForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Evitar el envío tradicional del formulario

    // Capturar los datos del formulario
    const formData = new FormData(this);

    // Enviar los datos al servidor usando Fetch API
    fetch('/api/crear-tecnicos', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            mostrarToast(data.message, 'success'); // Mostrar mensaje de éxito
            this.reset(); // Limpiar el formulario
            bootstrap.Modal.getInstance(document.getElementById('tecnicoModal')).hide(); // Cerrar el modal
        } else if (data.error) {
            mostrarToast(data.error, 'danger'); // Mostrar mensaje de error
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Ocurrió un error al guardar el técnico.', 'danger');
    });
});

function abrirModalTecnico() {
    const modal = new bootstrap.Modal(document.getElementById('tecnicoModal'));
    modal.show();
}