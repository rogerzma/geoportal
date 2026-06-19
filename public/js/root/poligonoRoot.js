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

// Genera un color para cada cultivo
const COLORES_CULTIVO = {
    'Alfalfa':  '#00692C', // verde oscuro
    'Algodon':  '#EDEDED', // blanco
    'Ajo':      '#FFE880', // amarillo claro
    'Avena':    '#B2BABB', // gris claro
    'Cebada':   '#A0522D', // marrón
    'Cebolla':  '#D7BDE2', // lila claro
    'Chile':    '#1A6E0D', // verde
    'Ciruela':  '#7E57C2', // violeta ciruela
    'Durazno':  '#FFB74D', // durazno
    'En descanso': '#7D7D7D', // gris
    'Fresa':    '#FF69B4', // rosa fuerte
    'Frijol':   '#57352B', // café
    'Guayaba':  '#E91E63', // rosa
    'Maiz':     '#F9A825', // amarillo
    'Manzana':  '#8BC34A', // verde manzana
    'Nogal':    '#8B4513', // marrón oscuro
    'Nopal':    '#388E3C', // verde nopal
    'Pepino':   '#4CAF50', // verde claro
    'Sorgo':    '#8D5524', // marrón oscuro
    'Tomate':   '#E53935', // rojo
    'Tomatillo': '#32CD32', // verde lima claro
    'Trigo':    '#F4D03F', // dorado
    'Uva':      '#360869', // morado
    'Zanahoria':'#FF9800', // naranja
};
function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}

// Obtener el parámetro up_id de la URL
function getUpIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('up_id');
}


// Modo de dibujo
let drawMode = false;
let drawControl = null;

// Activar dibujo
document.getElementById("draw-poligono").addEventListener("click", function () {
    if (drawMode) {
        // Si ya está activo, desactívalo
        drawMode = false;
        if (drawControl) {
            drawControl.disable();
            drawControl = null;
        }
        map.getContainer().style.cursor = "";
        mostrarAlerta('Modo dibujo desactivado.', 'info');
        return;
    }
    // Si estaba activo el modo eliminación, desactívalo aquí si lo necesitas
    // deleteMode = false; // (opcional)

    // Activa modo dibujo
    drawMode = true;
    const polygonOptions = {
        allowIntersection: false,
        showArea: true,
        shapeOptions: { fillOpacity: 1 }
    };
    drawControl = new L.Draw.Polygon(map, polygonOptions);
    drawControl.enable();
    mostrarAlerta('Modo dibujo activado.', 'info');
});

// Modo de eliminación
let deleteMode = false;
let poligonoIdAEliminar = null;
let poligonoLayerAEliminar = null;
let modoActivo = null; // 'dibujo' | 'eliminacion' | null

// Eliminar polígono
document.getElementById("delete-poligono").addEventListener("click", function () {
    if (deleteMode) {
        // Si ya está activo, desactívalo
        deleteMode = false;
        map.getContainer().style.cursor = "";
        poligonoIdAEliminar = null;
        poligonoLayerAEliminar = null;
        mostrarAlerta('Modo de eliminación desactivado.', 'info');
        return;
    }
    // Si estaba activo el modo dibujo, desactívalo
    if (drawMode) {
        drawMode = false;
        if (drawControl) {
            drawControl.disable();
            drawControl = null;
        }
    }
    // Activa modo eliminación
    deleteMode = true;
    map.getContainer().style.cursor = "crosshair";
    mostrarAlerta('Haz clic en un polígono para eliminarlo.', 'warning');
});


// Cargar poligonos de una unidad de producción específica
function cargarPoligonosPorUP(upId) {
    drawnItems.clearLayers(); // Limpia los polígonos previos
    fetch(`/api/poligonos/up/${upId}`)
        .then(response => response.json())
        .then(poligonos => {
            let allCoords = [];
            poligonos.forEach(poligono => {
                const coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
                allCoords = allCoords.concat(coords);

                const color = colorPorCultivo(poligono.cultivo);
                const polygon = L.polygon(coords, {
                    color: color,
                    fillOpacity: 0.9
                }).addTo(drawnItems);

                polygon.bindPopup(`
                    <div class="popup-poligono">
                        <strong>Nombre:</strong> ${poligono.nombre}<br>
                        <strong>Cultivo:</strong> ${poligono.cultivo}<br>
                        <strong>Fecha de creación:</strong> ${poligono.fecha_creacion}<br>
                    </div>
                `);

                // Evento para eliminar
                polygon.on('click', function (e) {
                    if (deleteMode) {
                        poligonoIdAEliminar = poligono.id;
                        $('#modalEliminarPoligono').modal('show');
                    }
                });
            });

            if (allCoords.length > 0) {
                const bounds = L.latLngBounds(allCoords);
                map.fitBounds(bounds);
            }
        })
        .catch(error => {
            console.error('Error al cargar los poligonos:', error);
            mostrarAlerta('Error al cargar los poligonos.', 'danger');
        });
}

// Cargar todos los poligonos
function cargarPoligonos() {
    drawnItems.clearLayers(); // Limpia los polígonos previos
    fetch('/api/poligonos')
        .then(response => response.json())
        .then(poligonos => {
            let allCoords = [];
            poligonos.forEach(poligono => {
                const coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
                allCoords = allCoords.concat(coords);

                const color = generarColorAleatorio();
                const polygon = L.polygon(coords, {
                    color: color,
                    fillOpacity: 0.9
                }).addTo(drawnItems);

                polygon.bindPopup(`
                    <strong>Nombre:</strong> ${poligono.nombre}<br>
                    <strong>Cultivo:</strong> ${poligono.cultivo}<br>
                    <strong>Unidad de Producción:</strong> ${poligono.up_id}<br>
                    <strong>Usuario:</strong> ${poligono.user ? poligono.user.name : 'N/A'}<br>
                    <strong>Fecha de creación:</strong> ${poligono.fecha_creacion}
                `);

                // Evento para eliminar
                polygon.on('click', function (e) {
                    if (deleteMode) {
                        poligonoIdAEliminar = poligono.id;
                        poligonoLayerAEliminar = polygon;
                        const modal = new bootstrap.Modal(document.getElementById('modalEliminarPoligono'));
                        modal.show();
                    }
                });
            });

            if (allCoords.length > 0) {
                const bounds = L.latLngBounds(allCoords);
                map.fitBounds(bounds);
            }
        })
        .catch(error => {
            console.error('Error al cargar los poligonos:', error);
            mostrarAlerta('Error al cargar los poligonos.', 'danger');
        });
}

// Decidir qué función llamar según el parámetro up_id
const upId = getUpIdFromUrl();
if (upId) {
    cargarPoligonosPorUP(upId);
} else {
    cargarPoligonos();
}

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

    // Llenar campos ocultos
    document.getElementById('geom').value = wktPolygon;
    document.getElementById('coordenadas').value = JSON.stringify(latlngs);

    // Fecha actual
    document.getElementById('fecha_creacion').value = new Date().toISOString().slice(0, 10);

    $('#parcelaModal').modal('show');
});

// Cerrar el modal y eliminar el polígono no guardado si se cancela
document.querySelector('#parcelaModal .btn-secondary').addEventListener('click', function () {
    // Cierra el modal vía jQuery
    $('#parcelaModal').modal('hide');

    // Elimina el polígono si fue trazado
    if (drawnLayer) {
        drawnItems.removeLayer(drawnLayer);
        drawnLayer = null;
    }

    // Reactiva el modo dibujo automáticamente
    const polygonOptions = {
        allowIntersection: false,
        showArea: true,
        shapeOptions: {
            fillOpacity: 1
        }
    };
    new L.Draw.Polygon(map, polygonOptions).enable();

    // Opcional: alerta visual
    mostrarAlerta('Modo dibujo activado nuevamente.', 'info');
});

// Manejo del modal de eliminación
document.querySelector('#modalEliminarPoligono .btn-secondary').addEventListener('click', function () {
    // Cierra el modal
    $('#modalEliminarPoligono').modal('hide');

    // Mantén el modo eliminación activo
    deleteMode = true;
    map.getContainer().style.cursor = "crosshair";

    // Limpia variables de selección
    poligonoIdAEliminar = null;
    poligonoLayerAEliminar = null;

    // Alerta de retorno al modo activo
    mostrarAlerta('Modo eliminación reactivado.', 'info');
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

// Confirmar eliminación
document.getElementById('btnConfirmarEliminarPoligono').addEventListener('click', function () {
    if (!poligonoIdAEliminar) return;
    fetch(`/api/poligonos/${poligonoIdAEliminar}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(resp => resp.json())
    .then(json => {
        // Oculta el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarPoligono'));
        if (modal) modal.hide();

        mostrarAlerta('✅ ' + json.message, 'success');
        // Elimina el polígono del mapa
        if (poligonoLayerAEliminar) {
            drawnItems.removeLayer(poligonoLayerAEliminar);
        }
        // Recarga la página después de 2 segundos
        setTimeout(() => window.location.reload(), 2000);

        // Limpia variables
        poligonoIdAEliminar = null;
        poligonoLayerAEliminar = null;
        deleteMode = false;
        map.getContainer().style.cursor = "";
    })
    .catch(error => {
        mostrarAlerta('❌ Error al eliminar el polígono.', 'danger');
    });
});

document.getElementById('poligonoForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const data = {
        nombre: document.getElementById('nombre').value.trim(),
        cultivo: document.getElementById('cultivo').value.trim(),
        coordenadas: document.getElementById('coordenadas').value,
        geom: document.getElementById('geom').value.trim(),
        fecha_creacion: document.getElementById('fecha_creacion').value.trim(),
        up_id: parseInt(document.getElementById('up_id').value),
        user_id: parseInt(document.getElementById('user_id').value)
    };

    // Desactivar botón para prevenir doble clic
    const submitBtn = document.querySelector('#poligonoForm button[type="submit"]');
    submitBtn.disabled = true;

    fetch('/api/poligonos', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(resp => {
        if (!resp.ok) {
            return resp.json().then(errorJson => {
                throw errorJson;
            });
        }
        return resp.json();
        
    })
    .then(json => {
        const modalElement = document.getElementById('parcelaModal');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);

        if (modalInstance) {
            modalInstance.hide();
        }
        mostrarAlerta('✅ ' + json.message, 'success');

        // Limpiar formulario y capa dibujada
        document.getElementById('poligonoForm').reset();
        if (drawnLayer) {
            drawnItems.removeLayer(drawnLayer);
            drawnLayer = null;
        }

        // Opcional: espera un par de segundos para que el usuario vea la alerta
        setTimeout(() => {
            if (json.refresh) {
                window.location.reload(); // simula F5
            }
        }, 2000);

        // Rehabilitar botón
        submitBtn.disabled = false;
        
    })
    .catch(error => {
        console.log('Error del backend:', error);
        mostrarAlerta('❌ ' + (error.error || 'Error al guardar el polígono.'), 'danger');
        submitBtn.disabled = false;
    });
});