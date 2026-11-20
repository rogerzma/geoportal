// Inicializar el mapa en Zacatecas
var map = L.map('map').setView([22.9011, -102.6581], 15);

L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY', {
    attribution: '&copy; Google Maps'
}).addTo(map);


// Grupo de capas para polígonos
var drawnItems = new L.FeatureGroup();
map.addLayer(drawnItems);


// Grupo utilizado para mostrar u ocultar polígonos por cultivo
var poligonosLayerGroup = L.layerGroup().addTo(map);


// Mostrar coordenadas en tiempo real
map.on('mousemove', function (e) {
    var lat = e.latlng.lat.toFixed(6);
    var lng = e.latlng.lng.toFixed(6);

    var contenedorCoordenadas =
        document.getElementById('lat-lng');

    if (contenedorCoordenadas) {
        contenedorCoordenadas.textContent =
            `Lat: ${lat}, Lng: ${lng}`;
    }
});


// Genera un color para cada cultivo
const COLORES_CULTIVO = {
    'Alfalfa': '#00692C',
    'Algodon': '#EDEDED',
    'Ajo': '#FFE880',
    'Avena': '#B2BABB',
    'Cebada': '#A0522D',
    'Cebolla': '#D7BDE2',
    'Chile': '#1A6E0D',
    'Ciruela': '#7E57C2',
    'Durazno': '#FFB74D',
    'En descanso': '#7D7D7D',
    'Fresa': '#FF0D3D',
    'Frijol': '#57352B',
    'Guayaba': '#E91E63',
    'Maiz': '#F9A825',
    'Manzana': '#8BC34A',
    'Nogal': '#8B4513',
    'Nopal': '#388E3C',
    'Pepino': '#4CAF50',
    'Sorgo': '#8D5524',
    'Tomate': '#E53935',
    'Tomatillo': '#32CD32',
    'Trigo': '#F4D03F',
    'Uva': '#360869',
    'Zanahoria': '#FF9800',
    'Sin cultivo': '#000000'
};

const COLOR_DEFAULT = '#3388ff';

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo] || COLOR_DEFAULT;
}


// Variables para la tabla de cultivos
let poligonosGlobal = [];

let cultivosTablaCompleta = [];
let cultivosFiltrados = [];
let cultivosSeleccionados = new Set();

let paginaActual = 1;
let marcadorBusquedaCoordenadas = null;

const cultivosPorPagina = 5;
const ventanaPaginas = 2;


// Normalizar texto para búsquedas
function normalizarTexto(texto) {
    return (texto || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim();
}


// Evitar insertar código HTML desde la base de datos
function escaparHtml(valor) {
    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}


// Obtener un color hexadecimal seguro
function obtenerColorSeguro(color) {
    const valor = String(color || '').trim();

    if (/^#[0-9A-Fa-f]{6}$/.test(valor)) {
        return valor;
    }

    return COLOR_DEFAULT;
}


// Convertir las coordenadas del polígono
function obtenerCoordenadasPoligono(poligono) {
    try {
        const coordenadas =
            typeof poligono.coordenadas === 'string'
                ? JSON.parse(poligono.coordenadas)
                : poligono.coordenadas;

        if (!Array.isArray(coordenadas)) {
            return [];
        }

        return coordenadas
            .map(function (coordenada) {
                return [
                    Number(coordenada.lat),
                    Number(coordenada.lng)
                ];
            })
            .filter(function (coordenada) {
                return (
                    Number.isFinite(coordenada[0]) &&
                    Number.isFinite(coordenada[1])
                );
            });

    } catch (error) {
        console.warn(
            'Coordenadas inválidas en el polígono:',
            poligono.id,
            error
        );

        return [];
    }
}


// Mostrar los polígonos seleccionados en la tabla
function mostrarPoligonosPorCultivo() {
    poligonosLayerGroup.clearLayers();

    poligonosGlobal.forEach(function (poligono) {
        const nombreCultivo =
            poligono.cultivo_catalogo?.nombre ||
            poligono.cultivo ||
            'Sin cultivo';

        if (!cultivosSeleccionados.has(nombreCultivo)) {
            return;
        }

        const coords =
            obtenerCoordenadasPoligono(poligono);

        if (coords.length < 3) {
            return;
        }

        const color =
            obtenerColorSeguro(
                poligono.cultivo_catalogo?.color ||
                colorPorCultivo(nombreCultivo)
            );

        const polygon = L.polygon(coords, {
            color: color,
            fillColor: color,
            fillOpacity: 0.7,
            weight: 2
        }).addTo(poligonosLayerGroup);

        const nombreVariante =
            poligono.variante_cultivo?.nombre || null;

        const lineaVariante = nombreVariante
            ? `
                <strong>Variante:</strong>
                ${escaparHtml(nombreVariante)}
                <br>
            `
            : '';

        const nombreUsuario =
            poligono.user?.name || null;

        const lineaUsuario = nombreUsuario
            ? `
                <strong>Registrado por:</strong>
                ${escaparHtml(nombreUsuario)}
                <br>
            `
            : '';

        polygon.bindPopup(`
            <div class="popup-poligono">
                <strong>Nombre:</strong>
                ${escaparHtml(poligono.nombre || 'Sin nombre')}
                <br>

                <strong>Cultivo:</strong>
                ${escaparHtml(nombreCultivo)}
                <br>

                ${lineaVariante}

                ${lineaUsuario}

                <strong>Fecha de creación:</strong>
                ${escaparHtml(
                    poligono.fecha_creacion ||
                    'No disponible'
                )}
            </div>
        `);
    });
}


// Cargar polígonos
function cargarPoligonos() {
    fetch('/poligonos')
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}`
                );
            }

            return response.json();
        })
        .then(function (poligonos) {
            poligonosGlobal =
                Array.isArray(poligonos)
                    ? poligonos
                    : [];

            if (poligonosGlobal.length === 0) {
                console.warn(
                    'No hay polígonos para mostrar'
                );

                mostrarPoligonosPorCultivo();
                return;
            }

            let bounds = L.latLngBounds([]);

            poligonosGlobal.forEach(function (poligono) {
                const coords =
                    obtenerCoordenadasPoligono(
                        poligono
                    );

                if (coords.length < 3) {
                    return;
                }

                const polygonTemporal =
                    L.polygon(coords);

                bounds.extend(
                    polygonTemporal.getBounds()
                );
            });

            if (bounds.isValid()) {
                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }

            mostrarPoligonosPorCultivo();
        })
        .catch(function (error) {
            console.error(
                'Error al cargar los polígonos:',
                error
            );

            mostrarAlerta(
                'Error al cargar los polígonos.',
                'danger'
            );
        });
}


// Renderizar tabla paginada
function renderTablaCultivosPaginada() {
    const tabla =
        document.getElementById(
            'tabla-cultivos-body'
        );

    if (!tabla) {
        return;
    }

    tabla.innerHTML = '';

    if (cultivosFiltrados.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="3" class="text-center">
                    No se encontraron cultivos.
                </td>
            </tr>
        `;

        renderPaginacionCultivos();
        return;
    }

    const totalPaginas = Math.ceil(
        cultivosFiltrados.length /
        cultivosPorPagina
    );

    if (paginaActual > totalPaginas) {
        paginaActual = totalPaginas;
    }

    if (paginaActual < 1) {
        paginaActual = 1;
    }

    const inicio =
        (paginaActual - 1) *
        cultivosPorPagina;

    const fin =
        inicio + cultivosPorPagina;

    const cultivosPagina =
        cultivosFiltrados.slice(inicio, fin);

    let filas = '';

    cultivosPagina.forEach(function (item) {
        const cultivo =
            item.cultivo || 'Sin cultivo';

        const hectareas =
            item.hectareas || '0.00';

        const color =
            obtenerColorSeguro(item.color);

        const idCheck =
            `check-cultivo-${normalizarTexto(cultivo)
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9-]/g, '')}`;

        const checked =
            cultivosSeleccionados.has(cultivo)
                ? 'checked'
                : '';

        filas += `
            <tr>
                <td>
                    <span
                        style="
                            color:${color};
                            font-weight:bold;
                        "
                    >
                        ${escaparHtml(cultivo)}
                    </span>
                </td>

                <td>
                    ${escaparHtml(hectareas)}
                </td>

                <td style="text-align:center;">
                    <input
                        type="checkbox"
                        class="check-cultivo"
                        id="${idCheck}"
                        value="${escaparHtml(cultivo)}"
                        ${checked}
                    >
                </td>
            </tr>
        `;
    });

    tabla.innerHTML = filas;

    document
        .querySelectorAll('.check-cultivo')
        .forEach(function (checkbox) {
            checkbox.addEventListener(
                'change',
                function () {
                    if (this.checked) {
                        cultivosSeleccionados.add(
                            this.value
                        );
                    } else {
                        cultivosSeleccionados.delete(
                            this.value
                        );
                    }

                    mostrarPoligonosPorCultivo();
                }
            );
        });

    renderPaginacionCultivos();
}


// Crear botón de paginación
function crearBotonPagina(numeroPagina) {
    return `
        <li class="page-item ${
            paginaActual === numeroPagina
                ? 'active'
                : ''
        }">
            <a
                class="page-link pagina-cultivos"
                href="#"
                data-pagina="${numeroPagina}"
            >
                ${numeroPagina}
            </a>
        </li>
    `;
}


// Renderizar paginación
function renderPaginacionCultivos() {
    const paginacion =
        document.getElementById(
            'paginacion-cultivos'
        );

    if (!paginacion) {
        return;
    }

    const totalPaginas = Math.ceil(
        cultivosFiltrados.length /
        cultivosPorPagina
    );

    if (totalPaginas <= 1) {
        paginacion.innerHTML = '';
        return;
    }

    let paginacionHtml = `
        <nav aria-label="Paginación de cultivos">
            <ul class="pagination">

                <li class="page-item ${
                    paginaActual === 1
                        ? 'disabled'
                        : ''
                }">
                    <a
                        class="page-link"
                        href="#"
                        id="anterior-cultivos"
                    >
                        &laquo;
                    </a>
                </li>
    `;

    paginacionHtml += crearBotonPagina(1);

    if (
        paginaActual >
        ventanaPaginas + 2
    ) {
        paginacionHtml += `
            <li class="page-item disabled">
                <span class="page-link">…</span>
            </li>
        `;
    }

    const inicio = Math.max(
        2,
        paginaActual - ventanaPaginas
    );

    const fin = Math.min(
        totalPaginas - 1,
        paginaActual + ventanaPaginas
    );

    for (
        let pagina = inicio;
        pagina <= fin;
        pagina++
    ) {
        paginacionHtml +=
            crearBotonPagina(pagina);
    }

    if (
        paginaActual <
        totalPaginas - ventanaPaginas - 1
    ) {
        paginacionHtml += `
            <li class="page-item disabled">
                <span class="page-link">…</span>
            </li>
        `;
    }

    if (totalPaginas > 1) {
        paginacionHtml +=
            crearBotonPagina(totalPaginas);
    }

    paginacionHtml += `
                <li class="page-item ${
                    paginaActual === totalPaginas
                        ? 'disabled'
                        : ''
                }">
                    <a
                        class="page-link"
                        href="#"
                        id="siguiente-cultivos"
                    >
                        &raquo;
                    </a>
                </li>

            </ul>
        </nav>
    `;

    paginacion.innerHTML =
        paginacionHtml;

    const btnAnterior =
        document.getElementById(
            'anterior-cultivos'
        );

    const btnSiguiente =
        document.getElementById(
            'siguiente-cultivos'
        );

    if (btnAnterior) {
        btnAnterior.addEventListener(
            'click',
            function (e) {
                e.preventDefault();

                if (paginaActual > 1) {
                    paginaActual--;

                    renderTablaCultivosPaginada();
                }
            }
        );
    }

    if (btnSiguiente) {
        btnSiguiente.addEventListener(
            'click',
            function (e) {
                e.preventDefault();

                if (
                    paginaActual <
                    totalPaginas
                ) {
                    paginaActual++;

                    renderTablaCultivosPaginada();
                }
            }
        );
    }

    document
        .querySelectorAll('.pagina-cultivos')
        .forEach(function (boton) {
            boton.addEventListener(
                'click',
                function (e) {
                    e.preventDefault();

                    const pagina =
                        Number(
                            this.dataset.pagina
                        );

                    if (
                        Number.isInteger(pagina) &&
                        pagina > 0
                    ) {
                        paginaActual = pagina;

                        renderTablaCultivosPaginada();
                    }
                }
            );
        });
}


// Cargar hectáreas por cultivo del técnico
function cargarHectareasPorCultivo() {
    fetch(
        '/poligonos/hectareas-por-cultivo-usuario'
    )
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}`
                );
            }

            return response.json();
        })
        .then(function (data) {
            const resultados =
                Array.isArray(data)
                    ? data
                    : [];

            cultivosTablaCompleta =
                resultados
                    .map(function (item) {
                        const cultivo =
                            item.cultivo ||
                            'Sin cultivo';

                        const hectareas =
                            Number(
                                item.hectareas || 0
                            );

                        return {
                            cultivo: cultivo,
                            hectareas:
                                hectareas.toFixed(2),

                            color:
                                obtenerColorSeguro(
                                    item.color ||
                                    colorPorCultivo(
                                        cultivo
                                    )
                                )
                        };
                    })
                    .filter(function (item) {
                        return (
                            Number(item.hectareas) >
                            0
                        );
                    })
                    .sort(function (a, b) {
                        return a.cultivo.localeCompare(
                            b.cultivo,
                            'es',
                            {
                                sensitivity: 'base'
                            }
                        );
                    });

            cultivosSeleccionados =
                new Set(
                    cultivosTablaCompleta.map(
                        function (item) {
                            return item.cultivo;
                        }
                    )
                );

            cultivosFiltrados = [
                ...cultivosTablaCompleta
            ];

            paginaActual = 1;

            renderTablaCultivosPaginada();
            mostrarPoligonosPorCultivo();
        })
        .catch(function (error) {
            console.error(
                'Error al cargar hectáreas por cultivo:',
                error
            );

            const tabla =
                document.getElementById(
                    'tabla-cultivos-body'
                );

            if (tabla) {
                tabla.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center">
                            No fue posible cargar los datos.
                        </td>
                    </tr>
                `;
            }

            const paginacion =
                document.getElementById(
                    'paginacion-cultivos'
                );

            if (paginacion) {
                paginacion.innerHTML = '';
            }
        });
}


// Buscar en la tabla de cultivos
function aplicarFiltroCultivos() {
    const input =
        document.getElementById(
            'buscador-cultivos'
        );

    const termino =
        normalizarTexto(
            input ? input.value : ''
        );

    cultivosFiltrados =
        cultivosTablaCompleta.filter(
            function (item) {
                return normalizarTexto(
                    item.cultivo
                ).includes(termino);
            }
        );

    paginaActual = 1;

    renderTablaCultivosPaginada();
}


// Configurar buscador de cultivos
function configurarBuscadorCultivos() {
    const input =
        document.getElementById(
            'buscador-cultivos'
        );

    if (!input) {
        return;
    }

    input.addEventListener(
        'input',
        aplicarFiltroCultivos
    );
}


// Mostrar u ocultar la tabla
function configurarToggleTablaCultivos() {
    const boton =
        document.getElementById(
            'toggle-tabla-cultivos'
        );

    const tabla =
        document.getElementById(
            'tabla-cultivos-section'
        );

    if (!boton || !tabla) {
        return;
    }

    boton.addEventListener(
        'click',
        function () {
            const estaOculta =
                window.getComputedStyle(
                    tabla
                ).display === 'none';

            if (estaOculta) {
                tabla.style.display = '';

                boton.textContent =
                    'Ocultar cultivos';
            } else {
                tabla.style.display =
                    'none';

                boton.textContent =
                    'Mostrar cultivos';
            }
        }
    );
}


// Buscar coordenadas
function buscarCoordenadasEnMapa() {
    const input =
        document.getElementById(
            'buscador-coordenadas'
        );

    if (!input) {
        return;
    }

    const valor = input.value.trim();

    const regex =
        /^(-?\d{1,2}(?:\.\d+)?)[,\s]+(-?\d{1,3}(?:\.\d+)?)$/;

    const match = valor.match(regex);

    if (!match) {
        mostrarAlerta(
            'Ingresa coordenadas válidas con formato: latitud, longitud.',
            'warning'
        );

        return;
    }

    const lat =
        parseFloat(match[1]);

    const lng =
        parseFloat(match[2]);

    if (
        isNaN(lat) ||
        isNaN(lng) ||
        lat < -90 ||
        lat > 90 ||
        lng < -180 ||
        lng > 180
    ) {
        mostrarAlerta(
            'Las coordenadas están fuera de rango.',
            'warning'
        );

        return;
    }

    map.setView(
        [lat, lng],
        16
    );

    if (marcadorBusquedaCoordenadas) {
        map.removeLayer(
            marcadorBusquedaCoordenadas
        );
    }

    marcadorBusquedaCoordenadas =
        L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`
                <span style="font-size:16px;">
                    <b>Lat:</b> ${lat},
                    <b>Lng:</b> ${lng}
                </span>
            `)
            .openPopup();
}


// Configurar buscador de coordenadas
function configurarBuscadorCoordenadas() {
    const boton =
        document.getElementById(
            'btn-buscar-coordenadas'
        );

    const input =
        document.getElementById(
            'buscador-coordenadas'
        );

    if (boton) {
        boton.addEventListener(
            'click',
            function (e) {
                e.preventDefault();

                buscarCoordenadasEnMapa();
            }
        );
    }

    if (input) {
        input.addEventListener(
            'keydown',
            function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    buscarCoordenadasEnMapa();
                }
            }
        );
    }
}


// Cargar hectáreas totales
function cargarHectareasTotales() {
    fetch(
        '/poligonos/hectareas-totales-usuario'
    )
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    `Error HTTP ${response.status}`
                );
            }

            return response.json();
        })
        .then(function (data) {
            const elemento =
                document.getElementById(
                    'hectareas-totales'
                );

            if (elemento) {
                elemento.textContent =
                    data.hectareas_totales ??
                    '--';
            }
        })
        .catch(function () {
            const elemento =
                document.getElementById(
                    'hectareas-totales'
                );

            if (elemento) {
                elemento.textContent = '--';
            }
        });
}


// Mostrar alertas
function mostrarAlerta(mensaje, tipo = 'success') {
    let alertContainer =
        document.getElementById(
            'alertContainer'
        );

    if (!alertContainer) {
        alertContainer =
            document.createElement('div');

        alertContainer.id =
            'alertContainer';

        alertContainer.style.position =
            'fixed';

        alertContainer.style.top =
            '20px';

        alertContainer.style.right =
            '20px';

        alertContainer.style.zIndex =
            '9999';

        document.body.appendChild(
            alertContainer
        );
    }

    alertContainer.innerHTML = '';

    const alerta =
        document.createElement('div');

    alerta.className =
        `alert alert-${tipo} alert-dismissible fade show`;

    alerta.role = 'alert';

    alerta.innerHTML = `
        ${escaparHtml(mensaje)}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Cerrar"
        ></button>
    `;

    alertContainer.appendChild(alerta);

    setTimeout(function () {
        alerta.classList.remove('show');
        alerta.classList.add('fade');

        setTimeout(function () {
            alerta.remove();
        }, 500);
    }, 5000);
}


// Configurar componentes de la vista
configurarToggleTablaCultivos();
configurarBuscadorCultivos();
configurarBuscadorCoordenadas();


// Cargar información
cargarPoligonos();
cargarHectareasPorCultivo();
cargarHectareasTotales();


// Corregir el tamaño de Leaflet después de cargar la vista
setTimeout(function () {
    map.invalidateSize();
}, 300);