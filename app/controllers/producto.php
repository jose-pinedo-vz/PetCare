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
    }else if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['codigo'])) {
        $errores[] = "El codigo debe incluir letras, no solo números.";
    }

    //El codigo de barra es obligatorio
    if (empty($datos['codigo_barra'])) {
        $errores[] = "El codigo de barra es obligatorio.";
    }else if(!(int)$datos['codigo_barra']){
        $errores[] = "El codigo de barra debe ser un valor numerico.";
    }

    //El nombre es obligatorio
    if (empty($datos['nombre'])) {
        $errores[] = "El nombre es obligatorio.";
    }else if (strlen($datos['nombre']) > 50) {
        $errores[] = "El nombre no puede tener mas de 50 letras";
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
    }else if((int)$datos['descripcion']){
        $errores[] = "La descripcion debe ser un valor de texto.";
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
    }else if(!preg_match('/[0-9]/', $datos['unidad_medida'])){
        $errores[] = "La unidad de medida debe tener un valor numérico y la unidad de medida (Kg, Lt, etc.).";
    }

    //El precio de compra es obligatorio
    if (empty($datos['precio_compra'])) {
        $errores[] = "El precio de compra es obligatorio.";
    }else if(!(float)$datos['precio_compra']){
        $errores[] = "El precio de compra debe ser un valor numerico.";
    }else {
        $precioCompraLimpio = str_replace(',', '.', $datos['precio_compra'] ?? '');
        $datos['precio_compra'] = (float)$precioCompraLimpio;
        list(,$digitosDecimales) = explode('.', $datos['precio_compra'] . '.');
        if (strlen($digitosDecimales) > 2) {
            $errores[] = "El precio de compra no puede tener más de 2 decimales.";
        }
    }

    //El precio de venta es obligatorio
    if (empty($datos['precio_venta'])) {
        $errores[] = "El precio de venta es obligatorio.";
    }else if(!(float)$datos['precio_venta']){
        $errores[] = "El precio de venta debe ser un valor numerico.";
    }else {
        $precioVentaLimpio = str_replace(',', '.', $datos['precio_venta'] ?? '');
        $datos['precio_venta'] = (float)$precioVentaLimpio;
        list(,$digitosDecimales) = explode('.', $datos['precio_venta'] . '.');
        if (strlen($digitosDecimales) > 2) {
            $errores[] = "El precio de venta no puede tener más de 2 decimales.";
        }
    }
    //La existencia es obligatorio
    if (empty($datos['existencia'])) {
        $errores[] = "La existencia es obligatoria.";
    }else if(!(int)$datos['existencia']){
        $errores[] = "La existencia debe ser un valor numerico.";
    }else if ((int)$datos['existencia'] < 0) {
        $errores[] = "La existencia debe ser un valor mayor a 0.";
    }else{
        $datos['existencia']=(int)$datos['existencia'];
    }

    //La existencia minima es obligatorio
    if (empty($datos['existencia_minima'])) {
        $errores[] = "La existencia minima es obligatoria.";
    }else if(!(int)$datos['existencia_minima']){
        $errores[] = "La existencia minima debe ser un valor numerico.";
    }else if ((int)$datos['existencia_minima'] < 0) {
        $errores[] = "La existencia minima debe ser un valor mayor a 0.";
    }else{
        $datos['existencia_minima']=(int)$datos['existencia_minima'];
    }

    //La existencia maxima es obligatorio
    if (empty($datos['existencia_maxima'])) {
        $errores[] = "La existencia maxima es obligatoria.";
    }else if(!(int)$datos['existencia_maxima']){
        $errores[] = "La existencia maxima debe ser un valor numerico.";
    }else if ((int)$datos['existencia_maxima'] < 0) {
        $errores[] = "La existencia maxima debe ser un valor mayor a 0.";
    }else{
        $datos['existencia_maxima']=(int)$datos['existencia_maxima'];
    }

    //La fecha de ingreso es obligatorio
    if (empty($datos['fecha_ingreso'])) {
        $errores[] = "La fecha de ingreso es obligatoria.";
    }else {
        try {
            $fechaActual    = new DateTime('today');
            $fechaIngresada = new DateTime($datos['fecha_ingreso']);
            if ($fechaIngresada === $fechaActual) {
                $errores[] = "Fecha de ingreso: No puede ser una fecha posterior a hoy o  anterior.";
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de ingreso: Formato de fecha no válido.";
        }
    }

    //La fecha de caducidad es obligatorio
    if (empty($datos['fecha_caducidad'])) {
        $errores[] = "La fecha de caducidad es obligatoria.";
    }else {
        try {
            $fechaActual    = new DateTime('today');
            $fechaIngresada = new DateTime($datos['fecha_caducidad']);
            if ($fechaIngresada < $fechaActual) {
                $errores[] = "Fecha de caducidad: No puede ser una fecha anterior a hoy.";
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de caducidad: Formato de fecha no válido.";
        }
    }

    //El lote es obligatorio
    if (empty($datos['lote'])) {
        $errores[] = "El lote es obligatorio.";
    }else if (strlen($datos['lote']) > 20) {
        $errores[] = "El lote no puede tener mas de 20 caracteres.";
    }else if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['lote'])) {
        $errores[] = "El lote debe incluir letras, no solo números.";
    }

    //La ubicacion es obligatorio
    if (empty($datos['ubicacion'])) {
        $errores[] = "La ubicacion es obligatoria.";
    }else if (strlen($datos['ubicacion']) > 20) {
        $errores[] = "La ubicacion no puede tener mas de 20 caracteres.";
    }else if (!preg_match('/[a-zA-ZáéíóúÁÉÍÓÚñÑ]/u', $datos['ubicacion'])) {
        $errores[] = "La ubicacion debe incluir letras, no solo números.";
    }

    //La imagen es obligatorio
    if (empty($datos['imagen'])) {
        $errores[] = "La imagen es obligatoria.";
    }else if (strlen($datos['imagen']) > 50) {
        $errores[] = "La imagen no puede tener mas de 50 caracteres.";
    }else{
        require_once __DIR__ . '/../controllers/ImagenController.php';

        $resultadoImagen = null;
        $foto = $datos['fotografia'];

        if (is_array($foto) && !empty($foto['name']) && $foto['error'] !== UPLOAD_ERR_NO_FILE) {
            $resultadoImagen = ImagenController::subir($foto);
            if ($resultadoImagen['exito']) {
                $datos['fotografia'] = $resultadoImagen['ruta'];
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

    } else {
        require_once __DIR__ . '/../models/Producto.php';
        $advert = Productos::insertarProducto($datos);

        if (is_array($advert) && count($advert) > 0) {
            if ($advert[0] === "Error ID") {
                $_SESSION['errores'] = ["El Id categoria (ID: " . htmlspecialchars((string)$datos['id_categoria']) . ") o el Id proveedor (ID: " . htmlspecialchars((string)$datos['id_proveedor']) . ")no se encontraron."];
            } else if($advert[0] === "Error al aguardar") {
                $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
            }
            $_SESSION['old'] = $datos;

            header("Location: ../views/agregar_producto.php");
            exit;
        } else {
            // Éxito: redirige a la lista de productos
            header('Location: ../views/productos.php');
            exit;
        }


    }

}