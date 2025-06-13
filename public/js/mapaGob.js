// Inicializar el mapa en Zacatecas
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

// Funcion para el color

function generarColorAleatorio() {
    const letras = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letras[Math.floor(Math.random() * 16)];
    }
    return color;
}

//Funcion de carga de parcelas
function cargarParcelas() {
    fetch('/api/parcelas')
        .then(response => response.json())
        .then(parcelas => {
            parcelas.forEach(parcela => {
                // Convertir las coordenadas de la parcela en un polígono
                const coords = JSON.parse(parcela.coordenadas).map(coord => [coord.lat, coord.lng]);
                // Generar un color aleatorio para la parcela
                const color = generarColorAleatorio();
                // Crear un polígono con las coordenadas 
                const polygon = L.polygon(coords, { 
                    color: color,
                    fillOpacity: 0.9
                }).addTo(map);
                
                // Agregar un popup con información de la parcela
                polygon.bindPopup(`
                    <strong>Cultivo:</strong> ${parcela.cultivo}<br>
                    <strong>Productor:</strong> ${parcela.nombre_productor}<br>
                    <strong>Técnico:</strong> ${parcela.tecnico ? parcela.tecnico.nombre : 'N/A'}
                `);
            });
        })
        .catch(error => {
            console.error('Error al cargar las parcelas:', error);
        });
}

// Llamar a la función para cargar las parcelas al cargar el mapa
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
    const wktPolygon = `POLYGON((${coords}, ${latlngs[0].lng} ${latlngs[0].lat}))`; // cerramos el polígono

    document.getElementById('geom').value = wktPolygon;
    document.getElementById('coordenadas').value = JSON.stringify(latlngs);

    const modal = new bootstrap.Modal(document.getElementById('parcelaModal'));
    console.log(document.getElementById('parcelaModal'));
    $("#parcelaModal").modal("show");
});

// Botón: Activar dibujo
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

function mostrarToast(mensaje, tipo = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    const toastTemplate = document.getElementById('toastMessage');

    // Clonar el template del toast
    const toast = toastTemplate.cloneNode(true);
    toast.classList.remove('text-bg-success', 'text-bg-danger');
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
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(resp => resp.json())
    .then(json => {
        mostrarToast(json.message || 'Parcela guardada correctamente', 'success');
        $("#parcelaModal").modal("hide"); // Cierra el modal al guardar correctamente
        document.getElementById('parcelaForm').reset();
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error al guardar la parcela', 'danger');
    });
});