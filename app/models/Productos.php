<?php
/*codigo aqui*/
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

function IDproducto(PDO $conexion): int {
    try {
        // Consultamos el ID máximo actual y le sumamos 1
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_producto), 0) + 1 FROM productos");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 1;
    }
}


function insertarProducto(array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        return ["Error BD",htmlspecialchars($e->getMessage())];
    }

    try {
        // Esta linea de codigo le indico que no guarde nada aun hasta no hacer la consulta de la segunda tabla de lotes, 
        //lo que aguarda los datos en un espacio de memoria temporal, y si hay un error en la segunda tabla no se guarda 
        //nada en la primera tabla ya que las dos tablas estan relacionadas
        $conexion->beginTransaction();

        $id = IDproducto($conexion);

        $insertProd = "INSERT INTO productos (
            id_producto, codigo, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
        ) VALUES (
            :id_producto, :codigo, :nombre, :id_categoria, :id_proveedor, :descripcion, :contenido,
            :unidad_medida, :precio_venta, :existencia, :existencia_minima, :existencia_maxima, :fecha_ingreso, :imagen, :estado
        )";

    

        $stmtProd = $conexion->prepare($insertProd);
        $stmtProd->bindParam(":id_producto",      $id,                         PDO::PARAM_INT);
        $stmtProd->bindParam(":codigo",           $datos['codigo'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":nombre",           $datos['nombre'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":id_categoria",     $datos['id_categoria'],      PDO::PARAM_INT);
        $stmtProd->bindParam(":id_proveedor",     $datos['id_proveedor'],      PDO::PARAM_INT);
        $stmtProd->bindParam(":descripcion",      $datos['descripcion'],       PDO::PARAM_STR);
        $stmtProd->bindParam(":contenido",        $datos['contenido'],         PDO::PARAM_STR);
        $stmtProd->bindParam(":unidad_medida",    $datos['unidad_medida'],     PDO::PARAM_STR);
        $stmtProd->bindParam(":precio_venta",     $datos['precio_venta'],      PDO::PARAM_STR);
        $stmtProd->bindParam(":existencia",       $datos['existencia'],        PDO::PARAM_INT);
        $stmtProd->bindParam(":existencia_minima",$datos['existencia_minima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":existencia_maxima",$datos['existencia_maxima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":fecha_ingreso",    $datos['fecha_ingreso'],     PDO::PARAM_STR);
        $stmtProd->bindParam(":imagen",           $datos['imagen'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":estado",           $datos['estado'],            PDO::PARAM_INT);
        
        
        $stmtProd->execute();

        $insertLote = "INSERT INTO lote_productos (
            id_producto, lote, fecha_caducidad, precio_compra, ubicacion, cantidad
        ) VALUES ( 
            :id_producto, :lote, :fecha_caducidad, :precio_compra,   :ubicacion, :cantidad
        )";

        $stmtLote = $conexion->prepare($insertLote);
        $stmtLote->bindParam(":id_producto",      $id,                         PDO::PARAM_INT);
        $stmtLote->bindParam(":lote",             $datos['lote'],              PDO::PARAM_STR);
        $stmtLote->bindParam(":fecha_caducidad",  $datos['fecha_caducidad'],   PDO::PARAM_STR);
        $stmtLote->bindParam(":precio_compra",    $datos['precio_compra'],     PDO::PARAM_STR);
        $stmtLote->bindParam(":ubicacion",        $datos['ubicacion'],         PDO::PARAM_STR);
        $stmtLote->bindParam(":cantidad",         $datos['existencia'],        PDO::PARAM_INT);

        $stmtLote->execute();

        // Si todo fue exitoso, confirmamos la transacción de guardasr los datos en ambas tablas
        $conexion->commit();

    }catch(PDOException $e){
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            return ["Error ID"];
        } else {
            return["Error al aguardar",htmlspecialchars($e->getMessage())];
        }
    }
}


// Funcion para obtener todos los productos
function obtenerDatosProductos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
                FROM productos
                ORDER BY id_producto DESC";

        $resultado = $conexion->query($sql);
        if (!$resultado) {
            return [];
        }

        return $resultado->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Error en obtenerDatosProductos: " . $e->getMessage());
        return [];
    }
}

// Funcion para obtener todos los productos ctivos
function obtenerDatosProductosActivos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
                FROM productos
                WHERE estado = 1
                ORDER BY id_producto DESC";

        $resultado = $conexion->query($sql);
        if (!$resultado) {
            return [];
        }

        return $resultado->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Error en obtenerDatosProductos: " . $e->getMessage());
        return [];
    }
}

//Obtenemos la imagen
function obtenerImagen($id) {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT  imagen
                FROM productos
                WHERE id_producto = ?";

        $resultado = $conexion->prepare($sql);
        $resultado->execute([$id]);
        
        $imagen = $resultado->fetchColumn();
        
        return $imagen !== false ? $imagen : null;
        
    } catch (Exception $e) {
        error_log("Error en obtenerImagen: " . $e->getMessage());
        return null;
    }
}


// Funcion para obtener todos los productos inactivos
function obtenerDatosProductosInactivos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
                FROM productos
                WHERE estado = 0
                ORDER BY id_producto DESC";

        $resultado = $conexion->query($sql);
        if (!$resultado) {
            return [];
        }

        return $resultado->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        error_log("Error en obtenerDatosProductos: " . $e->getMessage());
        return [];
    }
}

function obtenerProductoPorId(int $id): ?array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT * FROM productos WHERE id_producto = ?";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$id]);
        $producto = $consulta->fetch(PDO::FETCH_ASSOC);

        return $producto ?: null;
    } catch (Exception $e) {
        error_log("Error en obtenerMascotaPorId: " . $e->getMessage());
        return null;
    }
}

function obtenerNombreProveedor(int $id): ?string {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT nombre_comercial 
            FROM proveedores 
            WHERE id_proveedor = ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id]);
        $nombre = $consulta -> fetchColumn();

        return $nombre !== false ? $nombre : null;
    } catch (Exception $error) {
        error_log("Error al obterner el nombre del proveedor " . $error -> getMessage());
        return null;
    }
}

function obtenerCorreoProveedor(int $id): ?string {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT correo
            FROM proveedores
            WHERE id_proveedor = ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id]);

        $correo = $consulta -> fetchColumn();

        return $correo !== false ? $correo : null;

    } catch (Exception $error) {
        error_log("Error al obtener el correo del proveedor " . $error -> getMessage());
        return null;
    }
}

function obtenerProductosPorProveedor(int $id_proveedor): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT id_producto, codigo, nombre, existencia, imagen
            FROM productos
            WHERE id_proveedor = ? AND estado = 1
            ORDER BY nombre
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_proveedor]);

        return $consulta -> fetchAll(PDO::FETCH_ASSOC) ? : [];

    } catch (Exception $error) {
        error_log("Error al obtener los productos del proveedor " . $error -> getMessage());
        return [];
    }
}

function obtenerTiempoEntregaProveedor(int $id): int {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT tiempo_entrega 
            FROM proveedores
            WHERE id_proveedor = ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id]);

        $dias = $consulta -> fetchColumn();

        return $dias !== false ? (int) $dias : 0;
    } catch (Exception $error) {
        error_log("Error al obtener los dias de entrega " . $error -> getMessage());
        return 0;
    }
}

function obtenerPrecioCompra(int $id_producto): float {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT MAX(precio_compra) 
            FROM lote_productos 
            WHERE id_producto = ?
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_producto]);
        $precio = $consulta -> fetchColumn();

        return $precio !== false && $precio !== null ? (float) $precio : 0.0;
    } catch (Exception $error) {
        error_log("Error al obtener el precio " . $error->getMessage());
        return 0.0;
    }
}

function obtenerExistenciaMinima(int $id_proveedor): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "
            SELECT id_producto, nombre, codigo, existencia, existencia_minima, unidad_medida
            FROM productos
            WHERE id_proveedor = ? AND existencia <= existencia_minima
        ";
        $consulta = $conexion -> prepare($sql);
        $consulta -> execute([$id_proveedor]);

        $resultado = $consulta -> fetchAll(PDO::FETCH_ASSOC);

        if ($resultado) {
            return $resultado;
        } else {
            return [];
        }

    } catch (Exception $error) {
        error_log("Error en obtener la existencia minima de un producto " . $error -> getMessage());
        return [];
    }
}

function ActualizarProducto(array $datos, $id){
    try{
        $conexion = ConexionDB::obtenerConexion();
        $updateProd = "UPDATE productos SET 
            codigo = :codigo,
            nombre = :nombre,
            id_categoria = :id_categoria,
            id_proveedor = :id_proveedor,
            descripcion = :descripcion,
            contenido = :contenido,
            unidad_medida = :unidad_medida,
            precio_venta = :precio_venta,
            existencia = :existencia,
            existencia_minima = :existencia_minima,
            existencia_maxima = :existencia_maxima,
            fecha_ingreso = :fecha_ingreso,
            imagen = :imagen,
            estado = :estado
            WHERE id_producto = :id_producto";

        $stmtProd = $conexion->prepare($updateProd);
        $stmtProd->bindParam(":codigo",            $datos['codigo'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":nombre",            $datos['nombre'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":id_categoria",      $datos['id_categoria'],      PDO::PARAM_INT);
        $stmtProd->bindParam(":id_proveedor",      $datos['id_proveedor'],      PDO::PARAM_INT);
        $stmtProd->bindParam(":descripcion",       $datos['descripcion'],       PDO::PARAM_STR);
        $stmtProd->bindParam(":contenido",         $datos['contenido'],         PDO::PARAM_STR);
        $stmtProd->bindParam(":unidad_medida",     $datos['unidad_medida'],     PDO::PARAM_STR);
        $stmtProd->bindParam(":precio_venta",      $datos['precio_venta'],      PDO::PARAM_STR);
        $stmtProd->bindParam(":existencia",        $datos['existencia'],        PDO::PARAM_INT);
        $stmtProd->bindParam(":existencia_minima", $datos['existencia_minima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":existencia_maxima", $datos['existencia_maxima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":fecha_ingreso",     $datos['fecha_ingreso'],     PDO::PARAM_STR);
        $stmtProd->bindParam(":imagen",            $datos['imagen'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":estado",            $datos['estado'],            PDO::PARAM_INT);
        $stmtProd->bindParam(":id_producto",       $id,                         PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmtProd->execute();
    }catch(PDOException $e){
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            return ["Error ID"];
        } else {
            return["Error al aguardar",htmlspecialchars($e->getMessage())];
        }
    }
    }

function eliminar_producto(int $id): bool
{
    try {
        $db = ConexionDB::obtenerConexion();
        $stmt = $db->prepare("UPDATE productos 
                              SET estado = 0
                              WHERE id_producto = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return false;
    }
}

class Productos {
    public static function insertarProducto(array $datos){
        return insertarProducto($datos);
    }

    public static function obtenerDatosProductos(): array {
        return obtenerDatosProductos();
    }

    public static function obtenerDatosProductosActivos(): array {
        return obtenerDatosProductosActivos();
    }

    public static function obtenerDatosProductosInactivos(): array {
        return obtenerDatosProductosInactivos();
    }

    public static function obtenerProductoPorId(int $id): ?array{
        return obtenerProductoPorId($id);
    }

    public static function obtenerNombreProveedor(int $id): ?string {
        return obtenerNombreProveedor($id);
    }

    public static function obtenerCorreoProveedor(int $id): ?string {
        return obtenerCorreoProveedor($id);
    }

    public static function obtenerProductosPorProveedor(int $id_proveedor): array {
        return obtenerProductosPorProveedor($id_proveedor);
    }

    public static function obtenerTiempoEntregaProveedor(int $id): int {
        return obtenerTiempoEntregaProveedor($id);
    }

    public static function obtenerExistenciaMinima(int $id_proveedor) : array {
        return obtenerExistenciaMinima($id_proveedor);
    }

    public static function obtenerPrecioCompra(int $id_producto): float {
        return obtenerPrecioCompra($id_producto);
    }

    public static function ActualizarProducto(array $datos, $id){
        return ActualizarProducto($datos, $id);
    }

    public static function obtenerimagen(int $id){
        return obtenerImagen($id);
    }

    public static function eliminar_producto(int $id){
       return eliminar_producto($id);
    }
}

if (!class_exists('Productos', false)) {
    class_alias('Productos', 'Productos');
}
?>
