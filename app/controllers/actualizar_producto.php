<?php
require_once __DIR__ . '/../models/Productos.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_producto = $_POST['id_producto'];

    $archivo = $_FILES['imagen'] ?? null;

    if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
        // Se recibió una imagen nueva
        $imagen = $_FILES['imagen'] ?? null;
    } elseif ($archivo && $archivo['error'] === UPLOAD_ERR_NO_FILE) {
        // No eligió una imagen nueva
        require_once __DIR__ . '/../models/Productos.php';
        $imagen = Productos::obtenerimagen((int)($id_producto));

    }

    $datos = [
        'id'                    => $id_producto,
        'codigo'                => trim($_POST['codigo'] ?? ''),
        'nombre'                => trim($_POST['nombre'] ?? ''),
        'id_categoria'          => trim($_POST['id_categoria'] ?? ''),
        'id_proveedor'          => trim($_POST['id_proveedor'] ?? ''),
        'descripcion'           => trim($_POST['descripcion'] ?? ''),
        'contenido'             => trim($_POST['contenido'] ?? ''),
        'unidad_medida'         => trim($_POST['unidad_medida'] ?? ''),
        'precio_venta'          => trim($_POST['precio_venta'] ?? ''),
        'existencia'            => trim($_POST['existencia'] ?? ''),
        'existencia_minima'     => trim($_POST['existencia_minima'] ?? ''),
        'existencia_maxima'     => trim($_POST['existencia_maxima'] ?? ''),
        'fecha_ingreso'         => trim($_POST['fecha_ingreso'] ?? ''),
        'imagen'                => $imagen,
        'estado'                => trim($_POST['estado'] ?? '')
    ];

    validar_datos($datos,$id_producto);


} else {
    // Si intentan entrar directo a este archivo sin pasar por el formulario
    header("Location: agregar_producto.php");
    exit();
}

function validar_datos(array $datos, $id) {
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
        }else if ((float)$datos['precio_venta'] < 0) {
            $errores[] = "El precio de venta debe ser un valor mayor a 0.";
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
            if ($fechaIngresada > $fechaActual) {
                $errores[] = "Fecha de ingreso: No puede ser una fecha posterior a hoy o  anterior.";
            }
        } catch (Exception $e) {
            $errores[] = "Fecha de ingreso: Formato de fecha no válido.";
        }
    }

    
    //El estado es obligatorio
    if (!isset($datos['estado'])) {
        $errores[] = "El estado es obligatorio.";
    }else{
        $estado = $datos['estado'];
        $datos['estado'] = (int)$estado;
    }



    if (count($errores) > 0) {
        // se muestra errores de que campos no se llenaron
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;

        header("Location: ../views/editar_producto.php");
        exit;

    } else {
        //La imagen es obligatorio
        $foto = $datos['imagen'] ?? null;
    
        if (is_string($foto)) {
            if (trim($foto) === '') {
                $errores[] = "La imagen es obligatoria.";
            } elseif (strlen($foto) > 50) {
                $errores[] = "La imagen no puede tener más de 50 caracteres.";
            }
        } elseif (is_array($foto)) {
            $errorArchivo = $foto['error'] ?? null;
    
            if ($errorArchivo !== UPLOAD_ERR_OK) {
                $errores[] = "No se pudo subir la imagen. Código de error: ". htmlspecialchars((string)$errorArchivo);
            } else {
                require_once __DIR__ . '/../controllers/ImagenController.php';
                $resultadoImagen = ImagenController::subir($foto);
    
                if ($resultadoImagen['exito']) {
                    $rutaImagen = $resultadoImagen['ruta'];
    
                    if (is_string($rutaImagen) && $rutaImagen !== '') {
                        $datos['imagen'] = $rutaImagen;
    
                        if (strlen($rutaImagen) > 50) {
                            $errores[] = "La imagen no puede tener más de 50 caracteres.";
                        }
                    } else {
                        $errores[] = "No se obtuvo una ruta válida para la imagen.";
                    }
                } else {
                    $errores[] = "Error en la foto: " . $resultadoImagen['mensaje'];
                }
            }
        } else {
            $errores[] = "La imagen es obligatoria.";
        }



        if (count($errores) > 0) {
        // se muestra errores de que campos no se llenaron
        $_SESSION['errores'] = $errores;
        $_SESSION['old']     = $datos;

        header("Location: ../views/editar_producto.php");
        exit;
        }else{
            require_once __DIR__ . '/../models/Productos.php';
            $advert = Productos::ActualizarProducto($datos, $id);

            if (is_array($advert) && count($advert) > 0) {
                if ($advert[0] === "Error ID") {
                    $_SESSION['errores'] = ["El Id categoria (ID: " . htmlspecialchars((string)$datos['id_categoria']) . ") o el Id proveedor (ID: " . htmlspecialchars((string)$datos['id_proveedor']) . ")no se encontraron."];
                } else if($advert[0] === "Error al aguardar") {
                    $_SESSION['errores'] = ["Error al guardar en la base de datos: " . $advert[1]];
                }
                $_SESSION['old'] = $datos;

                header("Location: ../views/editar_producto.php");
                exit;
            } else {
                // Éxito: redirige a la lista de productos
                header('Location: ../views/inventario.php');
                exit;
            }
        }


    }
}