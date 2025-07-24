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

// Botón hamburguesa: Desplazar mapa sin oscurecer
function toggleMenu() {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main-container");

    sidebar.classList.toggle("active");
}

// Función para mostrar un toast
function mostrarToast(mensaje, tipo = 'primary') {
    const toastContainer = document.getElementById('toastContainer');
    const toastTemplate = document.getElementById('toastMessage');

    // Clonar el template del toast
    const toast = toastTemplate.cloneNode(true);
    toast.classList.remove('text-bg-primary', 'text-bg-success', 'text-bg-danger');
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

// Función para abrir el modal de inicio de sesión
function abrirModalLogin() {
    const modal = new bootstrap.Modal(document.getElementById('loginModal'));
    modal.show();
}

// Manejar el inicio de sesión
document.getElementById('loginForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const data = {
        usuario: document.getElementById('usuario').value,
        contraseña: document.getElementById('contraseña').value
    };

    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        const loginError = document.getElementById('loginError');

        if (data.success) {
            window.location.href = '/administrador'; // Redirigir a la vista de administrador
        } else {
            // Mostrar el mensaje de error
            loginError.style.display = 'block';
            loginError.textContent = data.message || 'Nombre de usuario o contraseña incorrectos.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const loginError = document.getElementById('loginError');
        loginError.style.display = 'block';
        loginError.textContent = 'Ocurrió un error al iniciar sesión.';
    });
});