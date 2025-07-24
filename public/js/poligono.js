// Inicializar el mapa en Zacatecas
var map = L.map('map').setView([22.9011, -102.6581], 15);
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
    attribution: '&copy; Google Maps'
}).addTo(map);

// Grupo de capas para poligonos
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

// Cargar poligonos
function cargarPoligonos() {
    fetch('/api/poligonos')
        .then(response => response.json())
        .then(poligonos => {
            poligonos.forEach(poligono => {
                const coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
                const color = generarColorAleatorio();
                const polygon = L.polygon(coords, {
                    color: color,
                    fillOpacity: 0.9
                }).addTo(map);

                polygon.bindPopup(`
                    <strong>Nombre:</strong> ${poligono.nombre}<br>
                    <strong>Cultivo:</strong> ${poligono.cultivo}<br>
                    <strong>Unidad de Producción:</strong> ${poligono.up_id}<br>
                    <strong>Usuario:</strong> ${poligono.user ? poligono.user.name : 'N/A'}<br>
                    <strong>Fecha de creación:</strong> ${poligono.fecha_creacion}
                `);
            });
        })
        .catch(error => {
            console.error('Error al cargar los poligonos:', error);
            mostrarAlerta('Error al cargar los poligonos.', 'danger');
        });
}

cargarPoligonos();

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

    $("#poligonoModal").modal("show");
});

// Activar dibujo
document.getElementById("draw-poligono").addEventListener("click", function () {
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
document.getElementById("delete-poligono").addEventListener("click", function () {
    deleteMode = !deleteMode;
    if (deleteMode) {
        mostrarAlerta('Modo de eliminación activado. Haz clic en un polígono para eliminarlo.', 'warning');
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

// Guardar polígono
document.getElementById('poligonoForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const data = {
        nombre: document.getElementById('nombre').value,
        cultivo: document.getElementById('cultivo').value,
        coordenadas: document.getElementById('coordenadas').value,
        geom: document.getElementById('geom').value,
        fecha_creacion: document.getElementById('fecha_creacion').value,
        up_id: parseInt(document.getElementById('up_id').value),
        user_id: parseInt(document.getElementById('user_id').value)
    };

    fetch('/api/poligonos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(resp => resp.json())
        .then(json => {
            mostrarAlerta(json.message || 'Polígono guardado correctamente.', 'success');
            $("#poligonoModal").modal("hide");
            document.getElementById('poligonoForm').reset();
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarAlerta('Error al guardar el polígono.', 'danger');
        });
});