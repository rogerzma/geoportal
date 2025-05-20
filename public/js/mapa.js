
var map = L.map('map').setView([22.775, -102.573], 12);
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

// Control de dibujo
var drawControl = new L.Control.Draw({
    draw: {
        polygon: {
            allowIntersection: false,
            showArea: true,
            shapeOptions: { 
                color: '#00aaff',
                fillOpacity: 1
            }
        },
        polyline: false,
        rectangle: false,
        circle: false,
        marker: false,
        circlemarker: false
    },
    edit: { featureGroup: drawnItems, remove: false }
});
map.addControl(drawControl);

// Evento al finalizar dibujo
map.on(L.Draw.Event.CREATED, function (e) {
    var layer = e.layer;

    // Agregar evento de clic para eliminar el polígono
    layer.on('click', function () {
        if (deleteMode && confirm('¿Deseas eliminar esta parcela?')) {
            drawnItems.removeLayer(layer);
        }
    });

    drawnItems.addLayer(layer);
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
        alert('Modo de eliminación activado. Haz clic en una parcela para eliminarla.');
    } else {
        alert('Modo de eliminación desactivado.');
    }
});

// Botón hamburguesa: Desplazar mapa sin oscurecer
function toggleMenu() {
    document.getElementById("sidebar").classList.toggle("active");
    document.getElementById("map").style.marginLeft = document.getElementById("sidebar").classList.contains("active") ? "250px" : "0";
}
