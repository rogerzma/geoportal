// Inicializar el mapa en Zacatecas
var map = L.map('map').setView([22.9011, -102.6581], 15);
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
    attribution: '&copy; Google Maps'
}).addTo(map);


// Lógica para buscar coordenadas desde el buscador definido en welcome.blade.php
function buscarCoordenadasEnMapa() {
    const input = document.getElementById('buscador-coordenadas');
    if (!input) return;
    const valor = input.value.trim();
    // Expresión regular para lat, lng
    const regex = /^(-?\d{1,2}(?:\.\d+)?)[,\s]+(-?\d{1,3}(?:\.\d+)?)$/;
    const match = valor.match(regex);
    if (match) {
        const lat = parseFloat(match[1]);
        const lng = parseFloat(match[2]);
        if (!isNaN(lat) && !isNaN(lng)) {
            map.setView([lat, lng], 16);
            L.marker([lat, lng])
                .addTo(map)
                .bindPopup(`<span style='font-size:18px;'><b>Lat:</b> ${lat}, <b>Lng:</b> ${lng}</span>`)
                .openPopup();
        }
    } else {
        // Opcional: mostrar mensaje de error si el formato es incorrecto
        alert('Por favor ingresa coordenadas válidas en el formato: latitud, longitud');
    }
}

// Asignar evento al botón de buscar
function asignarEventoBuscadorCoordenadas() {
    const btn = document.getElementById('btn-buscar-coordenadas');
    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            buscarCoordenadasEnMapa();
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', asignarEventoBuscadorCoordenadas);
} else {
    asignarEventoBuscadorCoordenadas();
}

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
    'Fresa':    '#FF0D3D', // rosa fuerte
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
    'Sin cultivo': '#000000' // negro
};

const COLOR_DEFAULT = '#3388ff';

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}

const CULTIVOS_ORDENADOS = Object.keys(COLORES_CULTIVO)
    .sort((a, b) => a.localeCompare(b, 'es', { sensitivity: 'base' }));

// Variables globales para polígonos y cultivos
let poligonosGlobal = [];
let poligonosLayerGroup = L.layerGroup().addTo(map);

function cargarScriptExterno(src) {
    return new Promise((resolve, reject) => {
        const existente = document.querySelector(`script[data-src="${src}"]`);
        if (existente) {
            if (existente.dataset.loaded === 'true') {
                resolve();
                return;
            }
            existente.addEventListener('load', () => resolve(), { once: true });
            existente.addEventListener('error', () => reject(new Error(`No se pudo cargar ${src}`)), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.dataset.src = src;
        script.addEventListener('load', function() {
            this.dataset.loaded = 'true';
            resolve();
        }, { once: true });
        script.addEventListener('error', () => reject(new Error(`No se pudo cargar ${src}`)), { once: true });
        document.head.appendChild(script);
    });
}

function crearFeatureGeoJsonDesdePoligono(poligono) {
    let coordsOriginales;
    try {
        coordsOriginales = JSON.parse(poligono.coordenadas);
    } catch (e) {
        return null;
    }

    if (!Array.isArray(coordsOriginales) || coordsOriginales.length < 3) {
        return null;
    }

    const anillo = coordsOriginales
        .filter(p => p && Number.isFinite(Number(p.lat)) && Number.isFinite(Number(p.lng)))
        .map(p => [Number(p.lng), Number(p.lat)]);

    if (anillo.length < 3) {
        return null;
    }

    const primero = anillo[0];
    const ultimo = anillo[anillo.length - 1];
    if (primero[0] !== ultimo[0] || primero[1] !== ultimo[1]) {
        anillo.push([primero[0], primero[1]]);
    }

    return {
        type: 'Feature',
        properties: {
            id: poligono.id ?? null,
            nombre: poligono.nombre ?? '',
            cultivo: poligono.cultivo ?? '',
            fecha: poligono.fecha_creacion ?? '',
            up_id: poligono.up_id ?? null,
            user_id: poligono.user_id ?? null
        },
        geometry: {
            type: 'Polygon',
            coordinates: [anillo]
        }
    };
}

function obtenerFeatureCollectionSeleccionActual() {
    const checksActivos = Array.from(document.querySelectorAll('.check-cultivo:checked')).map(c => c.value);
    const aplicarFiltro = checksActivos.length > 0;

    const features = poligonosGlobal
        .filter(poligono => !aplicarFiltro || checksActivos.includes(poligono.cultivo))
        .map(crearFeatureGeoJsonDesdePoligono)
        .filter(Boolean);

    return {
        type: 'FeatureCollection',
        features
    };
}

async function descargarPoligonosShape() {
    try {
        if (!Array.isArray(poligonosGlobal) || poligonosGlobal.length === 0) {
            mostrarAlerta('No hay polígonos disponibles para exportar.', 'warning');
            return;
        }

        const featureCollection = obtenerFeatureCollectionSeleccionActual();
        if (!featureCollection.features.length) {
            mostrarAlerta('No hay geometrías válidas para exportar a Shapefile.', 'warning');
            return;
        }

        await cargarScriptExterno('https://cdn.jsdelivr.net/npm/@mapbox/shp-write@0.4.3/shpwrite.min.js');

        if (!window.shpwrite || typeof window.shpwrite.download !== 'function') {
            throw new Error('La librería de exportación SHP no está disponible.');
        }

        window.shpwrite.download(featureCollection, {
            folder: 'geoportal',
            types: {
                polygon: 'poligonos'
            }
        });

        mostrarAlerta(`Descarga iniciada: ${featureCollection.features.length} polígono(s).`, 'success');
    } catch (error) {
        console.error('Error al exportar a Shapefile:', error);
        mostrarAlerta('No fue posible generar el archivo SHP. Revisa la consola para más detalle.', 'danger');
    }
}

function agregarBotonDescargaShape() {
    if (document.getElementById('btn-descargar-shape')) {
        return;
    }

    const ancla = document.getElementById('toggle-tabla-cultivos-wrap') || document.getElementById('buscador-coordenadas-row');
    if (!ancla) {
        return;
    }

    const boton = document.getElementById('btn-descargar-shape');
    if (boton) {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            descargarPoligonosShape();
        });
    }
}

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

function cargarPoligonosTotales() {
    fetch('/api/poligonos/total')
        .then(response => response.json())
        .then(data => {
            document.getElementById('poligonos-totales').textContent = data.poligonos_totales ?? data.total_poligonos ?? '--';
        })
        .catch(() => {
            document.getElementById('poligonos-totales').textContent = '--';
        });
}

// --- Paginación de la tabla de cultivos ---
let cultivosPaginados = [];
let paginaActual = 1;
const cultivosPorPagina = 5;

function renderTablaCultivosPaginada() {
    const tabla = document.getElementById('tabla-cultivos-body');
    if (!tabla) return;
    tabla.innerHTML = '';
    const inicio = (paginaActual - 1) * cultivosPorPagina;
    const fin = inicio + cultivosPorPagina;
    const cultivosPagina = cultivosPaginados.slice(inicio, fin);
    cultivosPagina.forEach(({cultivo, hectareas, color}) => {
        const idCheck = `check-cultivo-${cultivo.replace(/\s+/g, '-').toLowerCase()}`;
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
    renderPaginacionCultivos();
    // Mostrar polígonos de los cultivos seleccionados
    const seleccionados = Array.from(document.querySelectorAll('.check-cultivo:checked')).map(c => c.value);
    mostrarPoligonosPorCultivo(seleccionados);
}

function renderPaginacionCultivos() {
    let paginacion = document.getElementById('paginacion-cultivos');
    if (!paginacion) {
        paginacion = document.createElement('div');
        paginacion.id = 'paginacion-cultivos';
        paginacion.className = 'text-center';
        const tablaWrapper = document.getElementById('tabla-up-wrapper');
        if (tablaWrapper) tablaWrapper.appendChild(paginacion);
    }

    const totalPaginas = Math.ceil(cultivosPaginados.length / cultivosPorPagina);
    if (totalPaginas <= 1) {
        paginacion.innerHTML = '';
        return;
    }

    let paginacionHtml = '<nav><ul class="pagination">';
    paginacionHtml += `<li class="page-item${paginaActual === 1 ? ' disabled' : ''}"><a class="page-link" href="#" id="anterior-cultivos">&laquo;</a></li>`;

    for (let i = 1; i <= totalPaginas; i++) {
        paginacionHtml += `<li class="page-item${paginaActual === i ? ' active' : ''}"><a class="page-link page-num-cultivos" href="#" data-pagina="${i}">${i}</a></li>`;
    }

    paginacionHtml += `<li class="page-item${paginaActual === totalPaginas ? ' disabled' : ''}"><a class="page-link" href="#" id="siguiente-cultivos">&raquo;</a></li>`;
    paginacionHtml += '</ul></nav>';
    paginacion.innerHTML = paginacionHtml;

    const btnAnterior = document.getElementById('anterior-cultivos');
    const btnSiguiente = document.getElementById('siguiente-cultivos');

    if (btnAnterior) {
        btnAnterior.addEventListener('click', function(e) {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                renderTablaCultivosPaginada();
            }
        });
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function(e) {
            e.preventDefault();
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaCultivosPaginada();
            }
        });
    }

    document.querySelectorAll('.page-num-cultivos').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const pagina = Number(this.getAttribute('data-pagina'));
            if (pagina !== paginaActual) {
                paginaActual = pagina;
                renderTablaCultivosPaginada();
            }
        });
    });
}

function cargarHectareasPorCultivo() {
    fetch('/api/poligonos/hectareas-por-cultivo')
        .then(response => response.json())
        .then(data => {
            cultivosPaginados = [];
            CULTIVOS_ORDENADOS.forEach(cultivo => {
                const item = data.find(d => d.cultivo === cultivo);
                const hectareas = item ? Number(item.hectareas).toFixed(2) : '0.00';
                if (parseFloat(hectareas) > 0) {
                    cultivosPaginados.push({
                        cultivo,
                        hectareas,
                        color: COLORES_CULTIVO[cultivo] || '#000'
                    });
                }
            });
            paginaActual = 1;
            renderTablaCultivosPaginada();
        })
        .catch(() => {
            const tabla = document.getElementById('tabla-cultivos-body');
            if (tabla) tabla.innerHTML = '<tr><td colspan="3">--</td></tr>';
        });
}


// Función para mostrar/ocultar la tabla de cultivos como párrafo arriba de la tabla
function agregarToggleTablaCultivos() {
    const tablaWrapper = document.getElementById('tabla-up-wrapper');
    if (!tablaWrapper) return;
    let contenedorToggle = document.getElementById('toggle-tabla-cultivos-wrap');
    if (!contenedorToggle) {
        contenedorToggle = document.createElement('div');
        contenedorToggle.id = 'toggle-tabla-cultivos-wrap';
        contenedorToggle.className = 'col-sm-9';
        tablaWrapper.parentNode.insertBefore(contenedorToggle, tablaWrapper);
    }

    // Buscar si ya existe el botón
    let p = document.getElementById('toggle-tabla-cultivos');
    if (!p) {
        p = document.createElement('button');
        p.type = 'button';
        p.id = 'toggle-tabla-cultivos';
        p.innerHTML = '<span id="toggle-tabla-texto">Ocultar tabla de cultivos</span> <i id="toggle-tabla-icono" class="fas fa-eye-slash"></i>';
        contenedorToggle.appendChild(p);
    }

    // Mostrar la tabla por defecto
    tablaWrapper.style.display = '';
    document.getElementById('toggle-tabla-texto').textContent = 'Ocultar tabla de cultivos';
    const icono = document.getElementById('toggle-tabla-icono');
    if (icono) {
        icono.className = 'fas fa-eye-slash';
    }

    p.onclick = function(e) {
        e.preventDefault();
        if (tablaWrapper.style.display === 'none') {
            tablaWrapper.style.display = '';
            document.getElementById('toggle-tabla-texto').textContent = 'Ocultar tabla de cultivos';
            if (icono) icono.className = 'fas fa-eye-slash';
        } else {
            tablaWrapper.style.display = 'none';
            document.getElementById('toggle-tabla-texto').textContent = 'Mostrar cultivos';
            if (icono) icono.className = 'fas fa-eye';
        }
    };
}

// Cargar polígonos y luego tabla de cultivos
cargarPoligonos(() => {
    cargarHectareasPorCultivo();
    // Agregar el enlace después de que el DOM esté listo
    setTimeout(agregarToggleTablaCultivos, 300);
    setTimeout(agregarBotonDescargaShape, 350);
});
cargarHectareasTotales();
cargarPoligonosTotales();

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