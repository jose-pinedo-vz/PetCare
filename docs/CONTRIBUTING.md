## Estándar de Mensajes de Commit (Conventional Commits)

Para mantener un historial claro, legible y estructurado en el proyecto **PetCare**, utilizamos la convención **Conventional Commits**. Todos los commits realizados en el repositorio deben seguir este formato.

### Estructura del mensaje
```text
<tipo>(<módulo_opcional>): <descripción corta en presente o infinitivo>

| Tipo | Propósito / Cuándo utilizarlo | Ejemplo |
| :--- | :--- | :--- |
| **`feat`** | Una nueva funcionalidad o característica para el usuario o sistema. | `feat(citas): agregar selector de horario disponible` |
| **`fix`** | Corrección de un error, fallo de lógica o bug en el código. | `fix(mascotas): corregir advertencia null en validar_mascota.php` |
| **`docs`** | Cambios exclusivamente en la documentación (`README.md`, comentarios). | `docs(readme): actualizar instrucciones de instalacion con Docker` |
| **`style`** | Cambios de formato visual o CSS que no alteran la lógica del código. | `style(ui): ajustar paleta de colores en estilos_base.css` |
| **`refactor`** | Reestructuración de código que no corrige errores ni añade funciones nuevas. | `refactor(modelos): simplificar consulta SQL en el modelo de empleados` |
| **`perf`** | Cambios de código orientados a mejorar el rendimiento o velocidad de ejecución. | `perf(queries): optimizar tiempo de carga en la lista de mascotas` |
| **`test`** | Añadir o corregir pruebas unitarias o de integración. | `test(auth): agregar pruebas unitarias para validacion de login` |
| **`build`** | Cambios que afectan el sistema de compilación o dependencias externas. | `build(deps): actualizar imagen base de PHP en el Dockerfile` |
| **`ci`** | Cambios en scripts o flujos de trabajo de automatización/despliegue. | `ci(docker): ajustar configuracion del contenedor Apache` |
| **`chore`** | Tareas rutinarias de mantenimiento que no modifican código de la app (`.gitignore`). | `chore(git): actualizar reglas en gitignore para la base de datos` |



## Enfoque Arquitectónico y Estándares de Código

Para el desarrollo del proyecto **PetCare**, se ha adoptado un modelo **MVC (Modelo-Vista-Controlador) híbrido**, diseñado para mantener la robustez y seguridad en la capa de datos, permitiendo al mismo tiempo agilidad en los controladores y vistas.

### Usar POO

### 1. Modelos (`/app/models/`)
* **Paradigma:** Programación Orientada a Objetos (POO) estricta a través de Clases.
* **Responsabilidad:** Gestionar exclusivamente la conexión a la base de datos, la ejecución de consultas SQL mediante sentencias preparadas y el manejo de los datos. No deben contener lógica de interfaz ni HTML.

### No usar POO

### 2. Controladores (`/app/controllers/`)
* **Paradigma:** Enfoque procedural estructurado mediante scripts independientes.
* **Responsabilidad:** Actuar como intermediarios entre la vista y el modelo. Reciben las peticiones del usuario, procesan la lógica de control, llaman a los modelos correspondientes y gestionan las redirecciones.

### 3. Vistas (`/app/views/`)
* **Paradigma:** Scripts procedurales enfocados puramente en la interfaz de usuario (HTML, CSS, JavaScript y componentes visuales).
* **Responsabilidad:** Mostrar la información de manera limpia y capturar datos mediante formularios. Queda prohibido realizar consultas directas a la base de datos desde los archivos de vistas.