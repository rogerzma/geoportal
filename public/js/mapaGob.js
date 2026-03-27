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
    'Frijol':   '#57352B', // café
    'Chile':    '#1A6E0D', // verde
    'Maiz':     '#F9A825', // amarillo
    'Ajo':      '#FFE880', // amarillo claro
    'Tomate':   '#FF0000', // rojo
    'Avena':    '#B2BABB', // gris claro
    'Cebada':   '#A0522D', // marrón
    'Trigo':    '#F4D03F', // dorado
    'Sorgo':    '#8D5524', // marrón oscuro
    'Cebolla':  '#D7BDE2', // lila claro
    'Zanahoria':'#FF9800', // naranja
    'Pepino':   '#4CAF50', // verde claro
    'Guayaba':  '#E91E63', // rosa
    'Manzana':  '#8BC34A', // verde manzana
    'Durazno':  '#FFB74D', // durazno
    'Algodon':  '#FFFFFF', // blanco
    'Nopal':    '#388E3C', // verde nopal
};

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}

// Variables globales para polígonos y cultivos
let poligonosGlobal = [];
let poligonosLayerGroup = L.layerGroup().addTo(map);

// Cargar polígonos y guardarlos globalmente
function cargarPoligonos(callback) {
    fetch('/api/mapa-inicial')
        .then(response => response.json())
        .then(poligonos => {
            poligonosGlobal = poligonos;
            if (callback) callback();
        })
        .catch(error => {
            console.error('Error al cargar los polígonos:', error);
            mostrarAlerta('Error al cargar los polígonos.', 'danger');
        });
}

// Dibuja solo los polígonos de los cultivos seleccionados
function mostrarPoligonosPorCultivo(cultivosSeleccionados) {
    poligonosLayerGroup.clearLayers();
    poligonosGlobal.forEach(poligono => {
        if (!cultivosSeleccionados.includes(poligono.cultivo)) return;
        let coords = [];
        try {
            coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
        } catch (e) {
            return;
        }
        const color = colorPorCultivo(poligono.cultivo);
        L.polygon(coords, {
            color: color,
            fillOpacity: 0.7,
            weight: 2
        }).addTo(poligonosLayerGroup);
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

// Cargar hectáreas por cultivo y llenar la tabla
function cargarHectareasPorCultivo() {
    fetch('/api/poligonos/hectareas-por-cultivo')
        .then(response => response.json())
        .then(data => {
            const cultivos = ['Frijol', 'Chile', 'Maiz', 'Ajo', 'Tomate'];
            const tabla = document.getElementById('tabla-cultivos-body');
            if (!tabla) return;
            tabla.innerHTML = '';
            cultivos.forEach(cultivo => {
                const item = data.find(d => d.cultivo === cultivo);
                const hectareas = item ? Number(item.hectareas).toFixed(2) : '0.00';
                const idCheck = `check-cultivo-${cultivo}`;
                const color = COLORES_CULTIVO[cultivo] || '#000';
                tabla.innerHTML += `
                    <tr>
                        <td><span style="color:${color}; font-weight:bold;">${cultivo}</span></td>
                        <td id="hectareas-${cultivo}">${hectareas}</td>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check-cultivo" id="${idCheck}" value="${cultivo}" checked>
                        </td>
                    </tr>
                `;
            });

            // Evento para checkboxes
            document.querySelectorAll('.check-cultivo').forEach(chk => {
                chk.addEventListener('change', function() {
                    const seleccionados = Array.from(document.querySelectorAll('.check-cultivo:checked')).map(c => c.value);
                    mostrarPoligonosPorCultivo(seleccionados);
                });
            });

            // Mostrar todos los polígonos al inicio
            const seleccionados = cultivos;
            mostrarPoligonosPorCultivo(seleccionados);
        })
        .catch(() => {
            const tabla = document.getElementById('tabla-cultivos-body');
            if (tabla) tabla.innerHTML = '<tr><td colspan="3">--</td></tr>';
        });
}


// Cargar polígonos y luego tabla de cultivos
cargarPoligonos(() => {
    cargarHectareasPorCultivo();
});
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