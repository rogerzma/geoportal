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
};
const COLOR_DEFAULT = '#3388ff';

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}

const CULTIVOS_ORDENADOS = Object.keys(COLORES_CULTIVO)
    .sort((a, b) => a.localeCompare(b, 'es', { sensitivity: 'base' }));

let poligonosGlobal = [];
let poligonosLayerGroup = L.layerGroup().addTo(map);

let cultivosTablaCompleta = [];
let cultivosFiltrados = [];
let cultivosSeleccionados = new Set();
let paginaActual = 1;
let marcadorBusquedaCoordenadas = null;
const cultivosPorPagina = 5;

function normalizarTexto(texto) {
    return (texto || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}

function mostrarPoligonosPorCultivo() {
    poligonosLayerGroup.clearLayers();

    poligonosGlobal.forEach(poligono => {
        if (!cultivosSeleccionados.has(poligono.cultivo)) return;

        let coords = [];
        try {
            coords = JSON.parse(poligono.coordenadas).map(coord => [coord.lat, coord.lng]);
        } catch (e) {
            return;
        }

        const color = colorPorCultivo(poligono.cultivo);
        const polygon = L.polygon(coords, {
            color: color,
            fillOpacity: 0.7,
            weight: 2
        }).addTo(poligonosLayerGroup);

        polygon.bindPopup(`
            <div class="popup-poligono">
                <strong>Nombre:</strong> ${poligono.nombre}<br>
                <strong>Cultivo:</strong> ${poligono.cultivo}<br>
                <strong>Fecha de creación:</strong> ${poligono.fecha_creacion}<br>
            </div>
        `);
    });
}

function renderPaginacionCultivos() {
    const paginacion = document.getElementById('paginacion-cultivos');
    if (!paginacion) return;

    const totalPaginas = Math.ceil(cultivosFiltrados.length / cultivosPorPagina);
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
        btnAnterior.addEventListener('click', function (e) {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                renderTablaCultivosPaginada();
            }
        });
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener('click', function (e) {
            e.preventDefault();
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaCultivosPaginada();
            }
        });
    }

    document.querySelectorAll('.page-num-cultivos').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pagina = Number(this.getAttribute('data-pagina'));
            if (pagina !== paginaActual) {
                paginaActual = pagina;
                renderTablaCultivosPaginada();
            }
        });
    });
}

function renderTablaCultivosPaginada() {
    const tabla = document.getElementById('tabla-cultivos-body');
    if (!tabla) return;

    tabla.innerHTML = '';

    if (cultivosFiltrados.length === 0) {
        tabla.innerHTML = '<tr><td colspan="3" class="text-center">Sin resultados</td></tr>';
        renderPaginacionCultivos();
        return;
    }

    const inicio = (paginaActual - 1) * cultivosPorPagina;
    const fin = inicio + cultivosPorPagina;
    const cultivosPagina = cultivosFiltrados.slice(inicio, fin);

    cultivosPagina.forEach(({ cultivo, hectareas, color }) => {
        const idCheck = `check-cultivo-${cultivo.replace(/\s+/g, '-').toLowerCase()}`;
        const checked = cultivosSeleccionados.has(cultivo) ? 'checked' : '';

        tabla.innerHTML += `
            <tr>
                <td><span style="color:${color}; font-weight:bold;">${cultivo}</span></td>
                <td>${hectareas}</td>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-cultivo" id="${idCheck}" value="${cultivo}" ${checked}>
                </td>
            </tr>
        `;
    });

    document.querySelectorAll('.check-cultivo').forEach(chk => {
        chk.addEventListener('change', function () {
            if (this.checked) {
                cultivosSeleccionados.add(this.value);
            } else {
                cultivosSeleccionados.delete(this.value);
            }
            mostrarPoligonosPorCultivo();
        });
    });

    renderPaginacionCultivos();
}

function aplicarFiltroCultivos() {
    const input = document.getElementById('buscador-cultivos');
    const termino = normalizarTexto(input ? input.value : '');

    cultivosFiltrados = cultivosTablaCompleta.filter(item =>
        normalizarTexto(item.cultivo).includes(termino)
    );

    const totalPaginas = Math.max(1, Math.ceil(cultivosFiltrados.length / cultivosPorPagina));
    if (paginaActual > totalPaginas) {
        paginaActual = totalPaginas;
    }

    renderTablaCultivosPaginada();
}

function configurarBuscadorCultivos() {
    const input = document.getElementById('buscador-cultivos');
    if (!input) return;

    input.addEventListener('input', function () {
        paginaActual = 1;
        aplicarFiltroCultivos();
    });
}

function configurarToggleTablaCultivos() {
    const boton = document.getElementById('toggle-tabla-cultivos');
    const seccionTabla = document.getElementById('tabla-cultivos-section');
    if (!boton || !seccionTabla) return;

    boton.addEventListener('click', function () {
        if (seccionTabla.style.display === 'none') {
            seccionTabla.style.display = '';
            boton.textContent = 'Ocultar tabla de cultivos';
        } else {
            seccionTabla.style.display = 'none';
            boton.textContent = 'Mostrar cultivos';
        }
    });
}

function buscarCoordenadasEnMapa() {
    const input = document.getElementById('buscador-coordenadas');
    if (!input) return;

    const valor = input.value.trim();
    const regex = /^(-?\d{1,2}(?:\.\d+)?)[,\s]+(-?\d{1,3}(?:\.\d+)?)$/;
    const match = valor.match(regex);

    if (!match) {
        mostrarAlerta('Ingresa coordenadas validas con formato: latitud, longitud', 'warning');
        return;
    }

    const lat = parseFloat(match[1]);
    const lng = parseFloat(match[2]);

    if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
        mostrarAlerta('Las coordenadas estan fuera de rango', 'warning');
        return;
    }

    map.setView([lat, lng], 16);

    if (marcadorBusquedaCoordenadas) {
        map.removeLayer(marcadorBusquedaCoordenadas);
    }

    marcadorBusquedaCoordenadas = L.marker([lat, lng])
        .addTo(map)
        .bindPopup(`<span style='font-size:16px;'><b>Lat:</b> ${lat}, <b>Lng:</b> ${lng}</span>`)
        .openPopup();
}

function configurarBuscadorCoordenadas() {
    const btn = document.getElementById('btn-buscar-coordenadas');
    const input = document.getElementById('buscador-coordenadas');

    if (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            buscarCoordenadasEnMapa();
        });
    }

    if (input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                buscarCoordenadasEnMapa();
            }
        });
    }
}

function cargarPoligonos() {
    fetch('/poligonos')
        .then(response => response.json())
        .then(poligonos => {
            poligonosGlobal = Array.isArray(poligonos) ? poligonos : [];

            if (poligonosGlobal.length === 0) {
                console.warn('No hay polígonos para mostrar');
                return;
            }

            let bounds = L.latLngBounds([]);
            poligonosGlobal.forEach(poligono => {
                try {
                    const coords = JSON.parse(poligono.coordenadas).map(c => [c.lat, c.lng]);
                    const polygonTemporal = L.polygon(coords);
                    bounds.extend(polygonTemporal.getBounds());
                } catch (e) {
                    console.warn('Polígono inválido', e);
                }
            });

            if (bounds.isValid()) {
                map.fitBounds(bounds, { padding: [30, 30] });
            }

            mostrarPoligonosPorCultivo();
        })
        .catch(error => {
            console.error('Error al cargar los polígonos:', error);
            mostrarAlerta('Error al cargar los polígonos.', 'danger');
        });
}

function cargarHectareasPorCultivo() {
    fetch('/api/poligonos/hectareas-por-cultivo')
        .then(response => response.json())
        .then(data => {
            cultivosTablaCompleta = [];

            CULTIVOS_ORDENADOS.forEach(cultivo => {
                const item = data.find(d => d.cultivo === cultivo);
                const hectareas = item ? Number(item.hectareas).toFixed(2) : '0.00';

                if (parseFloat(hectareas) > 0) {
                    cultivosTablaCompleta.push({
                        cultivo,
                        hectareas,
                        color: COLORES_CULTIVO[cultivo] || '#000'
                    });
                }
            });

            cultivosSeleccionados = new Set(cultivosTablaCompleta.map(item => item.cultivo));
            cultivosFiltrados = [...cultivosTablaCompleta];
            paginaActual = 1;

            renderTablaCultivosPaginada();
            mostrarPoligonosPorCultivo();
        })
        .catch(() => {
            const tabla = document.getElementById('tabla-cultivos-body');
            if (tabla) {
                tabla.innerHTML = '<tr><td colspan="3" class="text-center">--</td></tr>';
            }
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

configurarToggleTablaCultivos();
configurarBuscadorCultivos();
configurarBuscadorCoordenadas();

cargarHectareasPorCultivo();
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