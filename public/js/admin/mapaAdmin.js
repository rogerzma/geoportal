// Inicializar el mapa en Zacatecas
var map = L.map('map').setView([22.9011, -102.6581], 15);
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
    attribution: '&copy; Google Maps'
}).addTo(map);

// Grupo de capas para polígonos
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);

// Mostrar coordenadas en tiempo real
map.on('mousemove', function (e) {
    var lat = e.latlng.lat.toFixed(6);
    var lng = e.latlng.lng.toFixed(6);
    document.getElementById('lat-lng').textContent = `Lat: ${lat}, Lng: ${lng}`;
});

// Función para el color
function generarColorAleatorio() {
    const letras = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letras[Math.floor(Math.random() * 16)];
    }
    return color;
}

// Cargar polígonos
function cargarPoligonos() {
    fetch('/api/poligonos')
        .then(response => response.json())
        .then(poligonos => {
            poligonos.forEach(poligono => {
                let coords = [];
                try {
                    coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
                } catch (e) {
                    return;
                }
                const color = generarColorAleatorio();
                // Crea el polígono y agrega un popup con la información
                const polygon = L.polygon(coords, {
                    color: color,
                    fillOpacity: 0.7,
                    weight: 2
                }).addTo(map);

                // Personaliza aquí la información que quieres mostrar
                let popupContent = `
                    <div class="popup-poligono">
                        <strong>Nombre:</strong> ${poligono.nombre}<br>
                        <strong>Cultivo:</strong> ${poligono.cultivo}<br>
                        <strong>Unidad de producción:</strong> ${poligono.unidad_produccion?.nombre_up ?? 'N/A'}<br>
                        <strong>Fecha de creación:</strong> ${poligono.fecha_creacion}<br>
                        <strong>Capturista:</strong> ${poligono.user?.name ?? 'N/A'}
                    </div>
                `;
                polygon.bindPopup(popupContent, { className: 'popup-poligono-leaflet' });
            });
        })
        .catch(error => {
            console.error('Error al cargar los polígonos:', error);
            mostrarAlerta('Error al cargar los polígonos.', 'danger');
        });
}

function cargarHectareasTotales() {
    fetch('/api/poligonos/hectareas-totales')
        .then(response => response.json())
        .then(data => {
            document.getElementById('hectareas-totales').textContent = data.hectareas_totales ?? '--';
        })
        .catch(() => {
            document.getElementById('hectareas-totales').textContent = '--';
        });
}

cargarPoligonos();
cargarHectareasTotales();

// Mostrar alertas (reemplazo de toasts)
function mostrarAlerta(mensaje, tipo = 'success') {
    let alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'alertContainer';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        document.body.appendChild(alertContainer);
    }
    const alert = document.createElement('div');
    alert.className = `alert alert-${tipo} alert-dismissible fade show`;
    alert.role = 'alert';
    alert.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    `;
    alertContainer.appendChild(alert);

    setTimeout(() => {
        alert.classList.remove('show');
        alert.classList.add('fade');
        setTimeout(() => alert.remove(), 500);
    }, 5000);
}