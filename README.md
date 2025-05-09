# Sistema Escolar

Este es un sistema escolar diseñado para gestionar las operaciones académicas de una institución educativa. Permite a los estudiantes y administradores interactuar con la plataforma de manera eficiente y efectiva.

## Características
- **Gestión de Estudiantes y administradores**: Registrar, modificar y eliminar estudiantes y administradores del sistema.
- **Gestión de Necesidades del Alumno**: en la seccion alumno.php el alumno puede hacer las consultas de Actuliazar sus datos,Incribir materias, ver materias existentes,consultar hora de incripcion,consultar saturacion de inscripciones.
- **Gestión de Necesidades del Administrador**: en la sesion admin.php el administrador puede Registrar, modificar y eliminar estudiantes y administradores del sistema,crear materias y grupos para las licenciaturas existentes y asignar horas de inscripcon a los alumnos.
- **Inscripción de Estudiantes**: Inscribir a los estudiantes en las materias de acuerdo a su licenciatura.
- **Visualización de Usuarios**: Los administradores pueden generar un alumno nuevo y administrar a alguien del sistema.
- **Autenticación de Usuarios**: El sistema cuenta con un sistema de login para estudiantes y administradores.
## Estructura de Archivos
escolares/
│
├── README.md                        # Información del proyecto
│
├── php/                              # Archivos PHP que gestionan la lógica del servidor
│   ├── admin.php                     # Funciones y lógica para la gestión del administrador
│   ├── alumno.php                    # Funciones para gestionar alumnos
│   ├── conexion.php                  # Conexión a la base de datos
│   ├── dosificacion.php              # Gestión de dosificación de materias
│   ├── login.php                     # Lógica de inicio de sesión
│   ├── modificar_materias.php        # Lógica para modificar materias
│   ├── procesar_recuperacion.php     # Procesamiento de solicitudes de recuperación de contraseña
│   ├── ver_usuarios.php              # Funciones para insertar un nuevo usuario de acuerdo a su rol
│   ├── index.html                    # Página principal del proyecto login para iniciar el sistema
│   ├── recuperar_contraseña.php      # Funciones para recuperar contraseña
│   
│
├── js/                               # Archivos JavaScript para la lógica del cliente
│   ├── alumnos.js                    # Lógica para la gestión de alumnos
│
├── css/                              # Archivos CSS para los estilos del sitio
│   ├── actualizardatos.css            # Estilos para la página de actualización de datos
│   ├── admin.css                     # Estilos para el panel de administración
│   ├── alumnos.css                   # Estilos para la página de gestión de alumnos
│   ├── inscripcion.css               # Estilos para la seccion de inscripción
│   ├── inscrito.css                  # Estilos para la seccion de estudiantes inscritos
│   ├── saturacion.css                # Estilos para la página de saturación de materias
│   ├── styles.css                    # Estilos generales del sitio
│   └── styles1.css                   # Estilos adicionales o alternativos
│
├── images/                           # Imágenes utilizadas en el sitio
│   ├── logo.webp                     # Logo de la UNAM
│   ├── logo-FESC.ico                 # Icono del escudo de la FESC
│   ├── logo-FESC.png                 # Imagen del escudo de la FESC
│   ├── logo-unam-blanco.png          # Imagen del escudo de la UNAM blanco
│   └── user-avatar.png               # Imagen de icono de sesion
│
├── escolares.sql                     # Script SQL para crear la base de datos y tablas
│
└── .gitignore                        # Archivos y carpetas que Git debe ignorar


### Explicación de la Estructura

- **`README.md`**: Contiene información importante sobre el proyecto, cómo instalarlo, cómo usarlo y cómo contribuir.
- **`php`**: Contiene archivos php para usados para la logica en el proyecto.
- **`css`**: Contiene los estilos utilizados en cada una de las secciones del proyecto
- **`images`**: Contiene las imagenes usadas y referenciadas en el proyecto.
- **`js`**: Contiene scripts necesarios para llenados dinamicos o usos dinamicos.
- **`escolares.sql`**: Contiene el script SQL para crear la estructura de la base de datos.
- **`LICENSE`**: Archivo de licencia que indica las condiciones bajo las cuales se distribuye el proyecto.

## Instalación
### Requisitos previos
Asegúrate de tener instalados los siguientes programas:
- [XAMPP]https://www.apachefriends.org/es/index.html
- [Visual Stdio Code] https://visualstudio.microsoft.com/es/
o cualquier editor de codigo
-Importar el script de la base de datos que se encuentra en el proyecto para su funcionamiento correcto
### Clonar el repositorio
-Para poder usar el sistema localmente puesdes descargar el formato zip y clonarlo desde la terminal
```bash
git clone https://github.com/jesuuusjs/sist_escolar.git

## Conclusión

Este sistema escolar proporciona una solución eficiente para gestionar la inscripción, recuperación y administración de cursos y estudiantes. Está diseñado para ser fácil de usar y flexible, permitiendo a administradores y estudiantes interactuar de manera sencilla con la plataforma.

A lo largo del desarrollo de este proyecto, se han integrado funcionalidades clave que facilitan la administración de datos y la visualización de la información en tiempo real. Además, al ser un sistema web, se puede acceder desde cualquier dispositivo con un navegador, mejorando la accesibilidad.

### ¿Qué sigue?

- Continuar con la mejora de la seguridad del sistema.
- Implementar nuevas funcionalidades, como un sistema de calificaciones o reportes.
- Mejorar la experiencia de usuario con un diseño más intuitivo.

Si tienes ideas para mejorar el proyecto o deseas colaborar, no dudes en abrir un **issue** o enviar un **pull request**.

¡Gracias por tu interés en este proyecto!

