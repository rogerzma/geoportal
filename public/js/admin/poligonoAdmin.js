// INICIALIZACIÓN DEL MAPA
var map = L.map('map').setView(
    [22.9011, -102.6581],
    15
);

L.tileLayer(
    'https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}&key=TU_API_KEY',
    {
        attribution: '&copy; Google Maps'
    }
).addTo(map);

// Grupo que contiene los polígonos registrados
// y el polígono nuevo que se esté dibujando.
var drawnItems = new L.FeatureGroup();

map.addLayer(drawnItems);


// COORDENADAS EN TIEMPO REAL
map.on('mousemove', function (event) {
    const latitud =
        event.latlng.lat.toFixed(6);

    const longitud =
        event.latlng.lng.toFixed(6);

    const elementoCoordenadas =
        document.getElementById('lat-lng');

    if (elementoCoordenadas) {
        elementoCoordenadas.textContent =
            `Lat: ${latitud}, Lng: ${longitud}`;
    }
});

// BUSCADOR POR COORDENADAS

let marcadorBusquedaCoordenadas = null;

const inputBuscadorCoordenadas =
    document.getElementById(
        'buscador-coordenadas'
    );

const botonBuscarCoordenadas =
    document.getElementById(
        'btn-buscar-coordenadas'
    );

const mensajeBusquedaCoordenadas =
    document.getElementById(
        'mensaje-busqueda-coordenadas'
    );

/**
 * Limpiar el mensaje mostrado debajo del buscador.
 */
function limpiarMensajeBusquedaCoordenadas() {
    if (!mensajeBusquedaCoordenadas) {
        return;
    }

    mensajeBusquedaCoordenadas.textContent = '';
    mensajeBusquedaCoordenadas.className = '';
}

/**
 * Mostrar mensaje del buscador.
 */
function mostrarMensajeBusquedaCoordenadas(
    mensaje,
    tipo = 'error'
) {
    if (!mensajeBusquedaCoordenadas) {
        return;
    }

    mensajeBusquedaCoordenadas.textContent =
        mensaje;

    mensajeBusquedaCoordenadas.className =
        tipo === 'exito'
            ? 'mensaje-coordenadas-exito'
            : 'mensaje-coordenadas-error';
}

/**
 * Buscar una ubicación utilizando el formato:
 *
 * latitud, longitud
 *
 * Ejemplo:
 * 22.7700, -102.5700
 */
function buscarPorCoordenadas() {
    limpiarMensajeBusquedaCoordenadas();

    if (!inputBuscadorCoordenadas) {
        return;
    }

    const valor =
        inputBuscadorCoordenadas
            .value
            .trim();

    if (!valor) {
        mostrarMensajeBusquedaCoordenadas(
            'Ingrese una latitud y una longitud.'
        );

        return;
    }

    const partes = valor
        .split(',')
        .map(function (parte) {
            return parte.trim();
        });

    if (partes.length !== 2) {
        mostrarMensajeBusquedaCoordenadas(
            'Utilice el formato latitud, longitud. Ejemplo: 22.7700, -102.5700.'
        );

        return;
    }

    const latitud =
        Number(partes[0]);

    const longitud =
        Number(partes[1]);

    if (
        !Number.isFinite(latitud) ||
        !Number.isFinite(longitud)
    ) {
        mostrarMensajeBusquedaCoordenadas(
            'La latitud y la longitud deben ser valores numéricos.'
        );

        return;
    }

    if (
        latitud < -90 ||
        latitud > 90
    ) {
        mostrarMensajeBusquedaCoordenadas(
            'La latitud debe encontrarse entre -90 y 90.'
        );

        return;
    }

    if (
        longitud < -180 ||
        longitud > 180
    ) {
        mostrarMensajeBusquedaCoordenadas(
            'La longitud debe encontrarse entre -180 y 180.'
        );

        return;
    }

    /*
     * Centrar el mapa sin eliminar
     * los polígonos cargados.
     */
    map.setView(
        [latitud, longitud],
        18
    );

    /*
     * Eliminar el marcador anterior.
     */
    if (marcadorBusquedaCoordenadas) {
        map.removeLayer(
            marcadorBusquedaCoordenadas
        );
    }

    marcadorBusquedaCoordenadas =
        L.circleMarker(
            [latitud, longitud],
            {
                radius: 8,
                color: '#9d2449',
                fillColor: '#9d2449',
                fillOpacity: 0.8,
                weight: 3
            }
        )
        .addTo(map)
        .bindPopup(`
            <strong>Coordenada buscada</strong>
            <br>
            Latitud: ${latitud.toFixed(6)}
            <br>
            Longitud: ${longitud.toFixed(6)}
        `)
        .openPopup();

    const elementoCoordenadas =
        document.getElementById('lat-lng');

    if (elementoCoordenadas) {
        elementoCoordenadas.textContent =
            `Lat: ${latitud.toFixed(6)}, ` +
            `Lng: ${longitud.toFixed(6)}`;
    }

    mostrarMensajeBusquedaCoordenadas(
        'Coordenadas localizadas correctamente.',
        'exito'
    );
}

if (botonBuscarCoordenadas) {
    botonBuscarCoordenadas.addEventListener(
        'click',
        buscarPorCoordenadas
    );
}

if (inputBuscadorCoordenadas) {
    inputBuscadorCoordenadas.addEventListener(
        'keydown',
        function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                buscarPorCoordenadas();
            }
        }
    );

    inputBuscadorCoordenadas.addEventListener(
        'input',
        limpiarMensajeBusquedaCoordenadas
    );
}
// COLORES DE CULTIVO

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

function colorPorCultivo(cultivo) {
    return COLORES_CULTIVO[cultivo]
        || '#000000';
}


// ========================================================
// ESTILO GRIS PARA POLÍGONOS REGISTRADOS
// ========================================================

const ESTILO_GRIS_REGISTRO = {
    color: '#666666',
    fillColor: '#808080',
    fillOpacity: 0.65,
    weight: 2
};

/**
 * Cambiar temporalmente a gris los polígonos
 * registrados mientras está activo el modo dibujo.
 */
function pintarRegistradosEnGris() {
    drawnItems.eachLayer(function (layer) {
        if (
            !layer._esRegistrado ||
            !layer.setStyle
        ) {
            return;
        }

        if (!layer._estiloOriginal) {
            layer._estiloOriginal = {
                color:
                    layer.options.color,

                fillColor:
                    layer.options.fillColor
                    || layer.options.color,

                fillOpacity:
                    layer.options.fillOpacity,

                weight:
                    layer.options.weight || 2
            };
        }

        layer.setStyle(
            ESTILO_GRIS_REGISTRO
        );
    });
}

/**
 * Recuperar el color original
 * de los polígonos registrados.
 */
function restaurarColorRegistrados() {
    drawnItems.eachLayer(function (layer) {
        if (
            !layer._esRegistrado ||
            !layer.setStyle ||
            !layer._estiloOriginal
        ) {
            return;
        }

        layer.setStyle(
            layer._estiloOriginal
        );
    });
}


// ========================================================
// PARÁMETRO DE LA UNIDAD DE PRODUCCIÓN
// ========================================================

function getUpIdFromUrl() {
    const parametros =
        new URLSearchParams(
            window.location.search
        );

    return parametros.get('up_id');
}


// ========================================================
// ESTADOS DE LAS HERRAMIENTAS
// ========================================================

let drawMode = false;
let drawControl = null;

let deleteMode = false;

let poligonoIdAEliminar = null;
let poligonoLayerAEliminar = null;

let drawnLayer = null;


// ========================================================
// ALERTAS: SOLO UNA VISIBLE
// ========================================================

let temporizadorAlerta = null;

/**
 * Mostrar una sola alerta.
 *
 * Si ya existe otra, se sustituye y no se acumula.
 */
function mostrarAlerta(
    mensaje,
    tipo = 'success'
) {
    const alertContainer =
        document.getElementById(
            'alertContainer'
        );

    if (!alertContainer) {
        return;
    }

    /*
     * Cancelar el temporizador anterior.
     */
    if (temporizadorAlerta) {
        clearTimeout(
            temporizadorAlerta
        );

        temporizadorAlerta = null;
    }

    /*
     * Reemplazar cualquier alerta previa.
     */
    alertContainer.innerHTML = `
        <div
            id="alerta-mapa"
            class="alert alert-${tipo} alert-dismissible fade show"
            role="alert"
        >
            <span>
                ${escaparHtmlAlerta(mensaje)}
            </span>

            <button
                type="button"
                class="btn-close"
                aria-label="Cerrar"
            ></button>
        </div>
    `;

    const alerta =
        document.getElementById(
            'alerta-mapa'
        );

    if (!alerta) {
        return;
    }

    const botonCerrar =
        alerta.querySelector(
            '.btn-close'
        );

    if (botonCerrar) {
        botonCerrar.addEventListener(
            'click',
            ocultarAlertaMapa
        );
    }

    /*
     * Ocultar automáticamente.
     */
    temporizadorAlerta = setTimeout(
        ocultarAlertaMapa,
        3000
    );
}

/**
 * Ocultar la alerta actual.
 */
function ocultarAlertaMapa() {
    const alertContainer =
        document.getElementById(
            'alertContainer'
        );

    const alerta =
        document.getElementById(
            'alerta-mapa'
        );

    if (!alertContainer) {
        return;
    }

    if (temporizadorAlerta) {
        clearTimeout(
            temporizadorAlerta
        );

        temporizadorAlerta = null;
    }

    if (!alerta) {
        alertContainer.innerHTML = '';
        return;
    }

    alerta.classList.remove('show');

    setTimeout(function () {
        alertContainer.innerHTML = '';
    }, 200);
}

/**
 * Evitar insertar HTML inesperado
 * en los mensajes.
 */
function escaparHtmlAlerta(valor) {
    return String(valor ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}


// ========================================================
// MODO DIBUJO
// ========================================================

const botonDibujar =
    document.getElementById(
        'draw-poligono'
    );

if (botonDibujar) {
    botonDibujar.addEventListener(
        'click',
        function () {
            if (drawMode) {
                drawMode = false;

                if (drawControl) {
                    drawControl.disable();
                    drawControl = null;
                }

                restaurarColorRegistrados();

                map.getContainer().style.cursor =
                    '';

                mostrarAlerta(
                    'Modo dibujo desactivado.',
                    'info'
                );

                return;
            }

            /*
             * Desactivar modo eliminación
             * sin mostrar otra alerta intermedia.
             */
            deleteMode = false;

            poligonoIdAEliminar = null;
            poligonoLayerAEliminar = null;

            drawMode = true;

            const polygonOptions = {
                allowIntersection: false,
                showArea: true,

                shapeOptions: {
                    color: '#9d2449',
                    fillColor: '#9d2449',
                    fillOpacity: 0.45,
                    weight: 3
                }
            };

            drawControl =
                new L.Draw.Polygon(
                    map,
                    polygonOptions
                );

            drawControl.enable();

            pintarRegistradosEnGris();

            map.getContainer().style.cursor =
                'crosshair';

            mostrarAlerta(
                'Modo dibujo activado.',
                'info'
            );
        }
    );
}


// ========================================================
// MODO ELIMINACIÓN
// ========================================================

const botonEliminar =
    document.getElementById(
        'delete-poligono'
    );

if (botonEliminar) {
    botonEliminar.addEventListener(
        'click',
        function () {
            if (deleteMode) {
                deleteMode = false;

                map.getContainer().style.cursor =
                    '';

                poligonoIdAEliminar = null;
                poligonoLayerAEliminar = null;

                mostrarAlerta(
                    'Modo eliminación desactivado.',
                    'info'
                );

                return;
            }

            /*
             * Desactivar dibujo sin mostrar
             * una alerta adicional.
             */
            if (drawMode) {
                drawMode = false;

                if (drawControl) {
                    drawControl.disable();
                    drawControl = null;
                }

                restaurarColorRegistrados();
            }

            deleteMode = true;

            map.getContainer().style.cursor =
                'crosshair';

            mostrarAlerta(
                'Modo eliminación activado. Seleccione un polígono.',
                'warning'
            );
        }
    );
}


// ========================================================
// CARGAR POLÍGONOS DE UNA UP
// ========================================================


function cargarPoligonosPorUP(upId) {
    drawnItems.clearLayers();

    fetch(`/api/poligonos/up/${upId}`, {
        headers: {
            Accept: 'application/json'
        }
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    'No fue posible cargar los polígonos.'
                );
            }

            return response.json();
        })
        .then(function (poligonos) {
            let allCoords = [];

            poligonos.forEach(
                function (poligono) {
                    let coords;

                    try {
                        coords = JSON
                            .parse(
                                poligono.coordenadas
                            )
                            .map(
                                function (
                                    coordenada
                                ) {
                                    return [
                                        Number(
                                            coordenada.lat
                                        ),

                                        Number(
                                            coordenada.lng
                                        )
                                    ];
                                }
                            );
                    } catch (error) {
                        console.error(
                            'Coordenadas inválidas:',
                            poligono.id,
                            error
                        );

                        return;
                    }

                    allCoords =
                        allCoords.concat(coords);

                    const color =
                        poligono
                            .cultivo_catalogo
                            ?.color
                        || colorPorCultivo(
                            poligono.cultivo
                        );

                    const estiloOriginal = {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.9,
                        weight: 2
                    };

                    const polygon =
                        L.polygon(
                            coords,
                            estiloOriginal
                        )
                        .addTo(drawnItems);

                    polygon._esRegistrado = true;

                    polygon._estiloOriginal = {
                        ...estiloOriginal
                    };

                    const nombreCultivo =
                        poligono
                            .cultivo_catalogo
                            ?.nombre
                        || poligono.cultivo
                        || 'Sin cultivo';
                        
                    
                    const varianteCultivo =
                        poligono.variante_cultivo
                            ? poligono.variante_cultivo.nombre
                            : null;

                    const lineaVariante =
                        varianteCultivo
                            ? `
                                <strong>Variante:</strong>
                                ${escaparHtmlAlerta(varianteCultivo)}
                                <br>
                            `
                            : '';

                    polygon.bindPopup(`
                        <div class="popup-poligono">
                            <strong>Nombre:</strong>
                            ${escaparHtmlAlerta(
                                poligono.nombre
                            )}
                            <br>

                            <strong>Cultivo:</strong>
                            ${escaparHtmlAlerta(
                                nombreCultivo
                            )}
                            <br>
                            ${lineaVariante}

                            <strong>Fecha de creación:</strong>
                            ${escaparHtmlAlerta(
                                poligono.fecha_creacion
                            )}
                        </div>
                    `);

                    polygon.on(
                        'click',
                        function () {
                            if (!deleteMode) {
                                return;
                            }

                            poligonoIdAEliminar =
                                poligono.id;

                            poligonoLayerAEliminar =
                                polygon;

                            const modalElement =
                                document.getElementById(
                                    'modalEliminarPoligono'
                                );

                            const modal =
                                bootstrap.Modal
                                    .getOrCreateInstance(
                                        modalElement
                                    );

                            modal.show();
                        }
                    );
                }
            );

            if (drawMode) {
                pintarRegistradosEnGris();
            }

            if (allCoords.length > 0) {
                const bounds =
                    L.latLngBounds(allCoords);

                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }
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


// ========================================================
// CARGAR TODOS LOS POLÍGONOS
// ========================================================

function cargarPoligonos() {
    drawnItems.clearLayers();

    fetch('/api/poligonos', {
        headers: {
            Accept: 'application/json'
        }
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    'No fue posible cargar los polígonos.'
                );
            }

            return response.json();
        })
        .then(function (poligonos) {
            let allCoords = [];

            poligonos.forEach(
                function (poligono) {
                    let coords;

                    try {
                        coords = JSON
                            .parse(
                                poligono.coordenadas
                            )
                            .map(
                                function (
                                    coordenada
                                ) {
                                    return [
                                        Number(
                                            coordenada.lat
                                        ),

                                        Number(
                                            coordenada.lng
                                        )
                                    ];
                                }
                            );
                    } catch (error) {
                        console.error(
                            'Coordenadas inválidas:',
                            poligono.id,
                            error
                        );

                        return;
                    }

                    allCoords =
                        allCoords.concat(coords);

                    const color =
                        poligono
                            .cultivo_catalogo
                            ?.color
                        || colorPorCultivo(
                            poligono.cultivo
                        );

                    const estiloOriginal = {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.9,
                        weight: 2
                    };

                    const polygon =
                        L.polygon(
                            coords,
                            estiloOriginal
                        )
                        .addTo(drawnItems);

                    polygon._esRegistrado = true;

                    polygon._estiloOriginal = {
                        ...estiloOriginal
                    };

                    polygon.bindPopup(`
                        <strong>Nombre:</strong>
                        ${escaparHtmlAlerta(
                            poligono.nombre
                        )}
                        <br>

                        <strong>Cultivo:</strong>
                        ${escaparHtmlAlerta(
                            poligono.cultivo
                        )}
                        <br>

                        <strong>Unidad de producción:</strong>
                        ${escaparHtmlAlerta(
                            poligono.up_id
                        )}
                        <br>

                        <strong>Usuario:</strong>
                        ${escaparHtmlAlerta(
                            poligono.user
                                ? poligono.user.name
                                : 'N/A'
                        )}
                        <br>

                        <strong>Fecha de creación:</strong>
                        ${escaparHtmlAlerta(
                            poligono.fecha_creacion
                        )}
                    `);

                    polygon.on(
                        'click',
                        function () {
                            if (!deleteMode) {
                                return;
                            }

                            poligonoIdAEliminar =
                                poligono.id;

                            poligonoLayerAEliminar =
                                polygon;

                            const modalElement =
                                document.getElementById(
                                    'modalEliminarPoligono'
                                );

                            const modal =
                                bootstrap.Modal
                                    .getOrCreateInstance(
                                        modalElement
                                    );

                            modal.show();
                        }
                    );
                }
            );

            if (drawMode) {
                pintarRegistradosEnGris();
            }

            if (allCoords.length > 0) {
                const bounds =
                    L.latLngBounds(allCoords);

                map.fitBounds(bounds, {
                    padding: [30, 30]
                });
            }
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


// ========================================================
// CARGA INICIAL
// ========================================================

const upId = getUpIdFromUrl();

if (upId) {
    cargarPoligonosPorUP(upId);
} else {
    cargarPoligonos();
}


// =========================================
// CULTIVOS Y VARIANTES
// =========================================

const selectorCultivo =
    document.getElementById('cultivo_id');

const selectorVariante =
    document.getElementById('variante_cultivo_id');

function cargarVariantes() {

    if (!selectorCultivo || !selectorVariante) {
        return;
    }

    selectorVariante.innerHTML = '';

    const cultivo =
        window.cultivosCatalogo.find(function(c){
            return Number(c.id) === Number(selectorCultivo.value);
        });

    if (!cultivo) {

        selectorVariante.disabled = true;

        selectorVariante.innerHTML =
            '<option value="">Primero seleccione un cultivo</option>';

        return;
    }

    if (!cultivo.variantes.length) {

        selectorVariante.disabled = true;

        selectorVariante.innerHTML =
            '<option value="">No existen variantes</option>';

        return;
    }

    selectorVariante.disabled = false;

    selectorVariante.innerHTML =
        '<option value="">Seleccione...</option>';

    cultivo.variantes.forEach(function(v){

        selectorVariante.innerHTML += `
            <option value="${v.id}">
                ${v.nombre}
            </option>
        `;

    });

}

if(selectorCultivo){

    selectorCultivo.addEventListener(
        'change',
        cargarVariantes
    );

}

// ========================================================
// POLÍGONO DIBUJADO
// ========================================================

map.on(
    L.Draw.Event.CREATED,
    function (event) {
        if (drawnLayer) {
            drawnItems.removeLayer(
                drawnLayer
            );
        }

        drawnLayer = event.layer;

        drawnItems.addLayer(
            drawnLayer
        );

        const latlngs =
            drawnLayer.getLatLngs()[0];

        const coords = latlngs
            .map(function (coordenada) {
                return (
                    `${coordenada.lng} ` +
                    `${coordenada.lat}`
                );
            })
            .join(', ');

        const wktPolygon =
            `POLYGON((${coords}, ` +
            `${latlngs[0].lng} ` +
            `${latlngs[0].lat}))`;

        document
            .getElementById('geom')
            .value =
                wktPolygon;

        document
            .getElementById('coordenadas')
            .value =
                JSON.stringify(latlngs);

        document
            .getElementById(
                'fecha_creacion'
            )
            .value =
                new Date()
                    .toISOString()
                    .slice(0, 10);

        const modalElement =
            document.getElementById(
                'parcelaModal'
            );

        const modal =
            bootstrap.Modal
                .getOrCreateInstance(
                    modalElement
                );

        modal.show();
    }
);


// ========================================================
// CANCELAR REGISTRO DEL POLÍGONO
// ========================================================

const botonCancelarParcela =
    document.querySelector(
        '#parcelaModal .btn-secondary'
    );

if (botonCancelarParcela) {
    botonCancelarParcela.addEventListener(
        'click',
        function () {
            const modalElement =
                document.getElementById(
                    'parcelaModal'
                );

            const modal =
                bootstrap.Modal
                    .getInstance(
                        modalElement
                    );

            if (modal) {
                modal.hide();
            }

            if (drawnLayer) {
                drawnItems.removeLayer(
                    drawnLayer
                );

                drawnLayer = null;
            }

            /*
             * Reactivar el controlador actual,
             * no crear uno nuevo sin referencia.
             */
            if (drawMode) {
                const polygonOptions = {
                    allowIntersection: false,
                    showArea: true,

                    shapeOptions: {
                        color: '#9d2449',
                        fillColor: '#9d2449',
                        fillOpacity: 0.45,
                        weight: 3
                    }
                };

                drawControl =
                    new L.Draw.Polygon(
                        map,
                        polygonOptions
                    );

                drawControl.enable();

                mostrarAlerta(
                    'Modo dibujo activado nuevamente.',
                    'info'
                );
            }
        }
    );
}


// ========================================================
// CANCELAR ELIMINACIÓN
// ========================================================

const botonCancelarEliminacion =
    document.querySelector(
        '#modalEliminarPoligono .btn-secondary'
    );

if (botonCancelarEliminacion) {
    botonCancelarEliminacion.addEventListener(
        'click',
        function () {
            const modalElement =
                document.getElementById(
                    'modalEliminarPoligono'
                );

            const modal =
                bootstrap.Modal
                    .getInstance(
                        modalElement
                    );

            if (modal) {
                modal.hide();
            }

            deleteMode = true;

            map.getContainer().style.cursor =
                'crosshair';

            poligonoIdAEliminar = null;
            poligonoLayerAEliminar = null;

            mostrarAlerta(
                'Modo eliminación continúa activo.',
                'info'
            );
        }
    );
}


// ========================================================
// CONFIRMAR ELIMINACIÓN
// ========================================================

const botonConfirmarEliminar =
    document.getElementById(
        'btnConfirmarEliminarPoligono'
    );

if (botonConfirmarEliminar) {
    botonConfirmarEliminar.addEventListener(
        'click',
        function () {
            if (!poligonoIdAEliminar) {
                return;
            }

            fetch(
                `/api/poligonos/${poligonoIdAEliminar}`,
                {
                    method: 'DELETE',

                    headers: {
                        Accept:
                            'application/json'
                    }
                }
            )
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error(
                            'No fue posible eliminar el polígono.'
                        );
                    }

                    return response.json();
                })
                .then(function (json) {
                    const modalElement =
                        document.getElementById(
                            'modalEliminarPoligono'
                        );

                    const modal =
                        bootstrap.Modal
                            .getInstance(
                                modalElement
                            );

                    if (modal) {
                        modal.hide();
                    }

                    mostrarAlerta(
                        json.message,
                        'success'
                    );

                    if (
                        poligonoLayerAEliminar
                    ) {
                        drawnItems.removeLayer(
                            poligonoLayerAEliminar
                        );
                    }

                    poligonoIdAEliminar = null;
                    poligonoLayerAEliminar = null;
                    deleteMode = false;

                    map.getContainer().style.cursor =
                        '';
                })
                .catch(function (error) {
                    console.error(
                        'Error al eliminar:',
                        error
                    );

                    mostrarAlerta(
                        'Error al eliminar el polígono.',
                        'danger'
                    );
                });
        }
    );
}


// ========================================================
// GUARDAR POLÍGONO
// ========================================================

const formularioPoligono =
    document.getElementById(
        'poligonoForm'
    );

if (formularioPoligono) {
    formularioPoligono.addEventListener(
        'submit',
        function (event) {
            event.preventDefault();

            const campoCultivoId =
                document.getElementById(
                    'cultivo_id'
                );

            const campoVarianteId =
                document.getElementById(
                    'variante_cultivo_id'
                );

            const campoCultivoTexto =
                document.getElementById(
                    'cultivo'
                );

            const data = {
                nombre:
                    document
                        .getElementById(
                            'nombre'
                        )
                        .value
                        .trim(),

                coordenadas:
                    document
                        .getElementById(
                            'coordenadas'
                        )
                        .value,

                geom:
                    document
                        .getElementById(
                            'geom'
                        )
                        .value
                        .trim(),

                fecha_creacion:
                    document
                        .getElementById(
                            'fecha_creacion'
                        )
                        .value
                        .trim(),

                up_id:
                    Number(
                        document
                            .getElementById(
                                'up_id'
                            )
                            .value
                    ),

                user_id:
                    Number(
                        document
                            .getElementById(
                                'user_id'
                            )
                            .value
                    )
            };

            /*
             * Compatible temporalmente con:
             *
             * 1. Selector nuevo cultivo_id.
             * 2. Selector anterior cultivo.
             */
            if (campoCultivoId) {
                data.cultivo_id =
                    Number(
                        campoCultivoId.value
                    );

                data.variante_cultivo_id =
                    campoVarianteId &&
                    campoVarianteId.value
                        ? Number(
                            campoVarianteId.value
                        )
                        : null;
            } else if (campoCultivoTexto) {
                data.cultivo =
                    campoCultivoTexto
                        .value
                        .trim();
            }

            const submitBtn =
                formularioPoligono
                    .querySelector(
                        'button[type="submit"]'
                    );

            submitBtn.disabled = true;
            submitBtn.textContent =
                'Guardando...';

            fetch('/api/poligonos', {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/json',

                    Accept:
                        'application/json'
                },

                body:
                    JSON.stringify(data)
            })
                .then(function (response) {
                    return response
                        .json()
                        .then(function (json) {
                            if (!response.ok) {
                                throw json;
                            }

                            return json;
                        });
                })
                .then(function (json) {
                    const modalElement =
                        document.getElementById(
                            'parcelaModal'
                        );

                    const modal =
                        bootstrap.Modal
                            .getInstance(
                                modalElement
                            );

                    if (modal) {
                        modal.hide();
                    }

                    mostrarAlerta(
                        json.message,
                        'success'
                    );

                    formularioPoligono.reset();

                    if (drawnLayer) {
                        drawnItems.removeLayer(
                            drawnLayer
                        );

                        drawnLayer = null;
                    }

                    submitBtn.disabled = false;
                    submitBtn.textContent =
                        'Guardar parcela';

                    setTimeout(function () {
                        if (json.refresh) {
                            window.location.reload();
                        }
                    }, 1800);
                })
                .catch(function (error) {
                    console.error(
                        'Error del backend:',
                        error
                    );

                    const mensaje =
                        error.message
                        || error.error
                        || obtenerPrimerErrorValidacion(
                            error.errors
                        )
                        || 'Error al guardar el polígono.';

                    mostrarAlerta(
                        mensaje,
                        'danger'
                    );

                    submitBtn.disabled = false;
                    submitBtn.textContent =
                        'Guardar parcela';
                });
        }
    );
}

/**
 * Obtener el primer mensaje de validación
 * devuelto por Laravel.
 */
function obtenerPrimerErrorValidacion(
    errores
) {
    if (
        !errores ||
        typeof errores !== 'object'
    ) {
        return null;
    }

    for (
        const mensajes
        of Object.values(errores)
    ) {
        if (
            Array.isArray(mensajes) &&
            mensajes.length > 0
        ) {
            return mensajes[0];
        }
    }

    return null;
}