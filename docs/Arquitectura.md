# Arquitectura del Sistema (MVC)

Para el desarrollo de proyecto, seleccionamos la arquitectura Modelo-Vista-Controlador (MVC). Es un patrón clásico, organizado y eficiente para coordinar el trabajo en equipo sin mezclar responsabilidades.

## Estructura de Capas (`app/`)

* **Modelo (`app/models/`):**
  Es la capa encargada de interactuar directamente con el corazón del proyecto: la base de datos. Contiene las consultas SQL (`SELECT`, `INSERT`, `UPDATE`, `DELETE`) y la configuración de conexión (`conexion_db.php`).

* **Vista (`app/views/`):**
  Contiene los archivos de interfaz gráfica (HTML), las hojas de estilo (`css/`) y las imágenes del sistema (`img/`). Su función es mostrar la información y capturar los datos de los formularios. **Regla de oro: En la vista NO se escribe lógica de procesado.**

* **Controlador (`app/controllers/`):**
  Es el cerebro del sistema donde vive la lógica en PHP. Recibe los datos capturados por la vista, los procesa, los limpia/sanitiza y llama al modelo para guardarlos en la base de datos. Si la interfaz necesita datos, el controlador se los pide al modelo, los prepara y los entrega a la vista.

## Flujo de Información
`Usuario` ➔ `Vista (HTML)` ➔ `Controlador (PHP)` ➔ `Modelo (SQL)` ➔ `Base de Datos`

---
*Cualquier duda sobre la estructura de archivos puede consultarse directamente con los líderes del proyecto.*

## Para el correcto desarrollo se deven de nombrar correctamente los arvhicos

* **Archivos primarios** 
    Son archivos independientes o importantes, como podrai ser la maqueta de un apartado o una coneccion importante. La primera letra de un archivo de estos tiene que ser mayuscula

* **Archivos primarios** 
    Son archivos que estan para auxiliar a otro, como el css, sus nombres van en minuscula. 