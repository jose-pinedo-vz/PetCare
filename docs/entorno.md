# Guía de Variables de Entorno (.env)

Cada integrante del equipo trabaja con configuraciones distintas en su computadora (por ejemplo, usuarios, puertos o contraseñas de XAMPP). Para no subir contraseñas privadas a GitHub y garantizar que el código funcione en cualquier máquina, utilizamos **variables de entorno**.

---

## ¿Para qué sirve el archivo `.env`?

1. **Seguridad:** Mantiene las credenciales de la base de datos fuera del historial de commits de Git.
2. **Portabilidad:** Permite que el código PHP sea exactamente el mismo para todos, mientras que la configuración de conexión se adapta a la computadora de cada quien.

---

## La Estrategia de los 3 Archivos

| Archivo | Ubicación | ¿Se sube a GitHub? | Función |
| :--- | :--- | :---: | :--- |
| **`.env`** | Raíz (`PETCARE/.env`) | **NUNCA** | Guarda las contraseñas reales de tu 

---

## Paso a Paso para Configurar tu Entorno Local

Cada integrante debe realizar estos pasos en su computadora al descargar el proyecto por primera vez:

1. Abre la raíz del proyecto (`PETCARE/`).
2. Crea un archivo **`.env`** (debe conservar el punto al inicio).
3. Abre `.env` en tu editor de código.
4. Modifica las variables según tu instalación local de MySQL:

```env
# Configuración local de Base de Datos
DB_HOST=localhost
DB_USER=root
DB_PASS=tu_contraseña_aqui
DB_NAME=petcare_db
DB_PORT=3306