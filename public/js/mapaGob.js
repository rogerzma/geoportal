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
            });
        })
        .catch(error => {
            console.error('Error al cargar las parcelas:', error);
            mostrarAlerta('Error al cargar las parcelas.', 'danger');
        });
}

cargarParcelas();

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