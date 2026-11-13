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

function existeCodigoProducto(string $codigo): bool {
    $conexion = ConexionDB::obtenerConexion();
    $consulta = $conexion->prepare(
        "SELECT 1 FROM productos WHERE LOWER(codigo) = LOWER(?) LIMIT 1"
    );
    $consulta->execute([$codigo]);

    return $consulta->fetchColumn() !== false;
}


function insertarProducto(array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        error_log("Error al conectar para insertar producto: " . $e->getMessage());
        return ["Error al aguardar", "No fue posible conectar con la base de datos."];
    }

    try {
        $id = IDproducto($conexion);

        $insertProd = "INSERT INTO productos (
            id_producto, codigo, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
        ) VALUES (
            :id_producto, :codigo, :nombre, :id_categoria, :id_proveedor, :descripcion, :contenido,
            :unidad_medida, :precio_venta, :existencia_minima, :existencia_maxima, :fecha_ingreso, :imagen, :estado
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
        $stmtProd->bindParam(":existencia_minima",$datos['existencia_minima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":existencia_maxima",$datos['existencia_maxima'], PDO::PARAM_INT);
        $stmtProd->bindParam(":fecha_ingreso",    $datos['fecha_ingreso'],     PDO::PARAM_STR);
        $stmtProd->bindParam(":imagen",           $datos['imagen'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":estado",           $datos['estado'],            PDO::PARAM_INT);
        $stmtProd->execute();
    }catch(PDOException $e){
        if (isset($e->errorInfo[1]) && $e->errorInfo[1] === 1452) {
            return ["Error ID"];
        } else {
            error_log("Error al insertar producto: " . $e->getMessage());
            return ["Error al aguardar", "No fue posible guardar el producto."];
        }
    }
}


// Funcion para obtener todos los productos
function obtenerDatosProductos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.id_categoria, p.id_proveedor,
                       p.descripcion, p.contenido, p.unidad_medida, p.precio_venta,
                       COALESCE(l.existencia, 0) AS existencia,
                       p.existencia_minima, p.existencia_maxima, p.fecha_ingreso, p.imagen, p.estado
                FROM productos AS p
                LEFT JOIN (
                    SELECT id_producto, SUM(cantidad) AS existencia
                    FROM lote_productos
                    WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                    GROUP BY id_producto
                ) AS l ON l.id_producto = p.id_producto
                ORDER BY p.id_producto DESC";

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
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.id_categoria, p.id_proveedor,
                       p.descripcion, p.contenido, p.unidad_medida, p.precio_venta,
                       COALESCE(l.existencia, 0) AS existencia,
                       p.existencia_minima, p.existencia_maxima, p.fecha_ingreso, p.imagen, p.estado
                FROM productos AS p
                LEFT JOIN (
                    SELECT id_producto, SUM(cantidad) AS existencia
                    FROM lote_productos
                    WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                    GROUP BY id_producto
                ) AS l ON l.id_producto = p.id_producto
                WHERE p.estado = 1
                ORDER BY p.id_producto DESC";

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
        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.id_categoria, p.id_proveedor,
                       p.descripcion, p.contenido, p.unidad_medida, p.precio_venta,
                       COALESCE(l.existencia, 0) AS existencia,
                       p.existencia_minima, p.existencia_maxima, p.fecha_ingreso, p.imagen, p.estado
                FROM productos AS p
                LEFT JOIN (
                    SELECT id_producto, SUM(cantidad) AS existencia
                    FROM lote_productos
                    WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                    GROUP BY id_producto
                ) AS l ON l.id_producto = p.id_producto
                WHERE p.estado = 0
                ORDER BY p.id_producto DESC";

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
            SELECT p.id_producto, p.codigo, p.nombre,
                   COALESCE(l.existencia, 0) AS existencia, p.imagen
            FROM productos AS p
            LEFT JOIN (
                SELECT id_producto, SUM(cantidad) AS existencia
                FROM lote_productos
                WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                GROUP BY id_producto
            ) AS l ON l.id_producto = p.id_producto
            WHERE p.id_proveedor = ? AND p.estado = 1
            ORDER BY p.nombre
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
            SELECT p.id_producto, p.nombre, p.codigo,
                   COALESCE(l.existencia, 0) AS existencia,
                   p.existencia_minima, p.unidad_medida
            FROM productos AS p
            LEFT JOIN (
                SELECT id_producto, SUM(cantidad) AS existencia
                FROM lote_productos
                WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                GROUP BY id_producto
            ) AS l ON l.id_producto = p.id_producto
            WHERE p.id_proveedor = ?
              AND COALESCE(l.existencia, 0) <= p.existencia_minima
              AND p.estado = 1
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
        $stmtProd->bindParam(":descripcion",       $datos['descripcion'],      PDO::PARAM_STR);
        $stmtProd->bindParam(":contenido",         $datos['contenido'],       PDO::PARAM_STR);
        $stmtProd->bindParam(":unidad_medida",     $datos['unidad_medida'],    PDO::PARAM_STR);
        $stmtProd->bindParam(":precio_venta",      $datos['precio_venta'],     PDO::PARAM_STR);
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
    public static function existeCodigo(string $codigo): bool {
        return existeCodigoProducto($codigo);
    }

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
