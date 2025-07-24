// Inicializar el mapa en Zacatecas
var map = L.map('map').setView([22.9011, -102.6581], 15);
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
    attribution: '&copy; Google Maps'
}).addTo(map);

// Grupo de capas para parcelas
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Mostrar coordenadas en tiempo real
map.on('mousemove', function (e) {
    var lat = e.latlng.lat.toFixed(6);
    var lng = e.latlng.lng.toFixed(6);
    document.getElementById('lat-lng').textContent = `Lat: ${lat}, Lng: ${lng}`;
});

// Funcion para el color
function generarColorAleatorio() {
    const letras = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letras[Math.floor(Math.random() * 16)];
    }
    return color;
}

// Cargar parcelas
function cargarParcelas() {
    fetch('/api/parcelas')
        .then(response => response.json())
        .then(parcelas => {
            parcelas.forEach(parcela => {
                const coords = JSON.parse(parcela.coordenadas).map(coord => [coord.lat, coord.lng]);
                const color = generarColorAleatorio();
                const polygon = L.polygon(coords, {
                    color: color,
                    fillOpacity: 0.9
                }).addTo(map);

                polygon.bindPopup(`
                    <strong>Cultivo:</strong> ${parcela.cultivo}<br>
                    <strong>Productor:</strong> ${parcela.nombre_productor}<br>
                    <strong>Técnico:</strong> ${parcela.tecnico ? parcela.tecnico.nombre : 'N/A'}
                `);
            });
        })
        .catch(error => {
            console.error('Error al cargar las parcelas:', error);
            mostrarAlerta('Error al cargar las parcelas.', 'danger');
        });
}

cargarParcelas();

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
    const wktPolygon = `POLYGON((${coords}, ${latlngs[0].lng} ${latlngs[0].lat}))`;

    document.getElementById('geom').value = wktPolygon;
    document.getElementById('coordenadas').value = JSON.stringify(latlngs);

    $("#parcelaModal").modal("show");
});

// Activar dibujo
document.getElementById("draw-parcela").addEventListener("click", function () {
    var polygonOptions = {
        allowIntersection: false,
        showArea: true,
        shapeOptions: {
            fillOpacity: 1
        }
    };
    new L.Draw.Polygon(map, polygonOptions).enable();
});

// Modo de eliminación
let deleteMode = false;
document.getElementById("delete-parcela").addEventListener("click", function () {
    deleteMode = !deleteMode;
    if (deleteMode) {
        mostrarAlerta('Modo de eliminación activado. Haz clic en una parcela para eliminarla.', 'warning');
    } else {
        mostrarAlerta('Modo de eliminación desactivado.', 'info');
    }
});

// Mostrar alertas (reemplazo de toasts)
function mostrarAlerta(mensaje, tipo = 'success') {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${tipo} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    `;
    alertContainer.appendChild(alert);

    // Opcional: eliminar automáticamente después de 5 segundos
    setTimeout(() => {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500);
    }, 5000);
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
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(resp => resp.json())
        .then(json => {
            mostrarAlerta(json.message || 'Parcela guardada correctamente.', 'success');
            $("#parcelaModal").modal("hide");
            document.getElementById('parcelaForm').reset();
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al guardar la parcela.', 'danger');
        });
