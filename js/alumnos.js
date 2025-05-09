document.getElementById('mobileMenuBtn').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('show');
});

// Función para mostrar sección
function showSection(sectionId) {
    // Ocultar todas las secciones
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });

    // Mostrar la sección seleccionada
    document.getElementById(sectionId).style.display = 'block';

    // Actualizar menú activo
    document.querySelectorAll('.sidebar-menu li a').forEach(link => {
        link.classList.remove('active');
    });
    document.getElementById(`${sectionId.replace('-section', '-link')}`).classList.add('active');

    // Guardar sección actual en localStorage
    localStorage.setItem('currentSection', sectionId);

    // Ocultar menú en móvil después de seleccionar
    if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('show');
    }
}

// Event listeners para los enlaces del menú
document.getElementById('datos-link').addEventListener('click', function(e) {
    e.preventDefault();
    showSection('datos-section');
});

document.getElementById('inscripcion-link').addEventListener('click', function(e) {
    e.preventDefault();
    showSection('inscripcion-section');
});

document.getElementById('dosificacion-link').addEventListener('click', function(e) {
    e.preventDefault();
    showSection('dosificacion-section');
});

document.getElementById('saturacion-link').addEventListener('click', function(e) {
    e.preventDefault();
    showSection('saturacion-section');
});

// Función para confirmar cierre de sesión
function confirmarCierreSesion() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "¿Estás seguro de que deseas salir del sistema?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('currentSection'); // Limpiar sección al cerrar sesión
            window.location.href = 'index.html'; // Redirigir a la página de inicio de sesión
        }
    });
}

// Mostrar sección guardada o 'datos-section' por defecto
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar botón de menú en móvil
    if (window.innerWidth <= 768) {
        document.getElementById('mobileMenuBtn').style.display = 'block';
    }

    const savedSection = localStorage.getItem('currentSection') || 'datos-section';
    showSection(savedSection);
});

// Manejar redimensionamiento
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('mobileMenuBtn').style.display = 'none';
    } else {
        document.getElementById('mobileMenuBtn').style.display = 'block';
    }
});

        