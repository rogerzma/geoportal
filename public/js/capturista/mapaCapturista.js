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

// Genera un color para cada cultivo
const COLORES_CULTIVO = {
    'Frijol': '#57352B',   // cafe
    'Chile':  '#1A6E0D',   // verde
    'Maiz':   '#F9A825',   // amarillo
    'Ajo':    '#FFE880',    // amarillo claro
    'Tomate': '#FF0000', // rojo
};

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}

// Cargar polígonos
function cargarPoligonos() {
    fetch('/poligonos')
        .then(response => response.json())
        .then(poligonos => {

            if (!poligonos.length) {
                console.warn('No hay polígonos para mostrar');
                return;
            }

            let bounds = L.latLngBounds([]);

            poligonos.forEach(poligono => {
                try {
                    const coords = JSON.parse(poligono.coordenadas)
                        .map(c => [c.lat, c.lng]);

                    const color = colorPorCultivo(poligono.cultivo);
                    const polygon = L.polygon(coords, {
                        color: color,
                        fillOpacity: 0.7,
                        weight: 2
                    }).addTo(map);

                    bounds.extend(polygon.getBounds());

                } catch (e) {
                    console.warn('Polígono inválido', e);
                }
            });

            // 🔥 ESTO ES CLAVE
            map.fitBounds(bounds, { padding: [30, 30] });
        })
        .catch(error => {
            console.error('Error al cargar los polígonos:', error);
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