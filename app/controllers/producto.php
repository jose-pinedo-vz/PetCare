<?php
date_default_timezone_set('America/Mexico_City');

function validar_datos(array $datos) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $errores = [];

    // El codigo es obligatorio
    if (empty($datos['codigo'])) {
        $errores[] = "El codigo es obligatorio.";
    }else if (!preg_match('/^(?=.*[a-zA-ZñÑ])(?=.*\d)[a-zA-Z0-9\-_]+$/', $datos['codigo'])) {
        $errores[] = "El código debe contener al menos una letra, un número y sin caracteres especiales.";
    }

    //El nombre es obligatorio
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }else if (strlen($datos['nombre']) > 50) {
        $errores[] = "El nombre no puede tener mas de 50 letras";
    }else if (is_numeric($datos['nombre'])) {
        $errores[] = "El nombre no puede ser solo números.";
    }

    //El id_categoria es obligatorio
    if (empty($datos['id_categoria'])) {
        $errores[] = "El id de la categoria es obligatorio.";
    }else if(!(int)$datos['id_categoria']){
        $errores[] = "El id de la categoria debe ser un valor numerico.";
    }else if ((int)$datos['id_categoria'] < 1) {
        $errores[] = "El id de la categoria debe ser un valor mayor a 0.";
    }else{
        $datos['id_categoria']=(int)$datos['id_categoria'];
    }

    //EL id_proveedor es obligatorio
    if (empty($datos['id_proveedor'])) {
        $errores[] = "El id del proveedor es obligatorio.";
    }else if(!(int)$datos['id_proveedor']){
        $errores[] = "El id del proveedor debe ser un valor numerico."; 
    }else if ((int)$datos['id_proveedor'] < 1) {
        $errores[] = "El id del proveedor debe ser un valor mayor a 0.";
    }else{
        $datos['id_proveedor']=(int)$datos['id_proveedor'];
    }

    //La descripcion es obligatorio
    if (empty($datos['descripcion'])) {
        $errores[] = "La descripcion es obligatoria.";
    }else if(is_numeric($datos['descripcion'])){
        $errores[] = "La descripción no puede ser solo números.";
    }

    //El contenido es obligatorio
    if (empty($datos['contenido'])) {
        $errores[] = "El contenido es obligatorio.";
    }else if(!(float)$datos['contenido']){
        $errores[] = "El contenido debe ser un valor numerico.";
    }else {
        $contenidoLimpio = str_replace(',', '.', $datos['contenido'] ?? '');
        $datos['contenido'] = (float)$contenidoLimpio;
        if ((float)$datos['contenido'] < 0) {
            $errores[] = "El contenido debe ser un valor mayor a 0.";
        }else{
            $datos['contenido']=(float)$datos['contenido'];
        }
    }


    //La unidad de medida es obligatorio
    if (empty($datos['unidad_medida'])) {
        $errores[] = "La unidad de medida es obligatoria.";
    }else if (strlen($datos['unidad_medida']) > 5) {
        $errores[] = "La unidad de medida no puede tener mas de 5 caracteres";
    }

    //El precio de venta es obligatorio
    if ($datos['precio_venta'] === '') {
        $errores[] = "El precio de venta es obligatorio.";
    }else if (!is_numeric($datos['precio_venta']) || (float)$datos['precio_venta'] <= 0) {
        $errores[] = "El precio de venta debe ser un valor numerico.";
    }else {
        $datos['precio_venta'] = (float)$datos['precio_venta'];
        $partesPrecio = explode('.', (string)$datos['precio_venta']);
        if (isset($partesPrecio[1]) && strlen($partesPrecio[1]) > 2) {
            $errores[] = "El precio de venta no puede tener más de 2 decimales.";
        }
    }

    // Los límites de inventario pueden ser cero, pero deben ser enteros no negativos.
    if ($datos['existencia_minima'] === '') {
        $errores[] = "La existencia minima es obligatoria.";
    }else if (filter_var($datos['existencia_minima'], FILTER_VALIDATE_INT) === false) {
        $errores[] = "La existencia minima debe ser un valor numerico.";
    }else if ((int)$datos['existencia_minima'] < 0) {
        $errores[] = "La existencia minima debe ser un valor mayor a 0.";
    }else{
        $datos['existencia_minima']=(int)$datos['existencia_minima'];
    }

    if ($datos['existencia_maxima'] === '') {
        $errores[] = "La existencia maxima es obligatoria.";
    }else if (filter_var($datos['existencia_maxima'], FILTER_VALIDATE_INT) === false) {
        $errores[] = "La existencia maxima debe ser un valor numerico.";
    }else if ((int)$datos['existencia_maxima'] < 0) {
        $errores[] = "La existencia maxima debe ser un valor mayor a 0.";
    }else{
        $datos['existencia_maxima']=(int)$datos['existencia_maxima'];
    }

    if (!in_array((string)$datos['estado'], ['0', '1'], true)) {
        $errores[] = "El estado es obligatorio.";
    }else{
        $datos['estado'] = (int)$datos['estado'];
    }

    if (count($errores) > 0) {
        // se muestra errores de que campos no se llenaron
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;

        header("Location: ../views/agregar_producto.php");
        exit;

    } else {
        require_once __DIR__ . '/../models/Productos.php';
        $datos['fecha_ingreso'] = date('Y-m-d');

        try {
            if (Productos::existeCodigo($datos['codigo'])) {
                $_SESSION['errores'] = ["El código del producto ya está registrado."];
                $_SESSION['old'] = $datos;

                header("Location: ../views/agregar_producto.php");
                exit;
            }
        } catch (Throwable $e) {
            error_log("Error al validar el código del producto: " . $e->getMessage());
            $_SESSION['errores'] = ["No fue posible validar si el código del producto ya existe. Inténtalo nuevamente."];
            $_SESSION['old'] = $datos;

            header("Location: ../views/agregar_producto.php");
            exit;
        }

        //La imagen es obligatorio
        if (!is_array($datos['imagen'])
            || empty($datos['imagen']['name'])
            || $datos['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            $errores[] = "La imagen es obligatoria.";
        }else{
            require_once __DIR__ . '/../controllers/ImagenController.php';

            $foto = $datos['imagen'];
            if ($foto['error'] !== UPLOAD_ERR_OK) {
                $errores[] = "No fue posible recibir la imagen del producto.";
            } else {
                $resultadoImagen = ImagenController::subir($foto);
                if ($resultadoImagen['exito']) {
                    $datos['imagen'] = $resultadoImagen['ruta'];

                    if (strlen($datos['imagen']) > 50) {
                        $errores[] = "La imagen no puede tener mas de 50 caracteres.";
                    }
                } else {
                    $errores[] = "Error en la foto: " . $resultadoImagen['mensaje'];
                }
            }
        }

        if (count($errores) > 0) {
            // se muestra errores de que campos no se llenaron
            $_SESSION['errores'] = $errores;
            $_SESSION['old']     = $datos;

            header("Location: ../views/agregar_producto.php");
            exit;
        }else{
            $advert = Productos::insertarProducto($datos);

            if (is_array($advert) && count($advert) > 0) {
                if ($advert[0] === "Error ID") {
                    $_SESSION['errores'] = ["El Id categoria (ID: " . htmlspecialchars((string)$datos['id_categoria']) . ") o el Id proveedor (ID: " . htmlspecialchars((string)$datos['id_proveedor']) . ")no se encontraron."];
                } else {
                    $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
                }
                $_SESSION['old'] = $datos;

                header("Location: ../views/agregar_producto.php");
                exit;
            } else {
                // Éxito: redirige a la lista de productos
                header('Location: ../views/inventario.php');
                exit;
            }


    }
    }

}