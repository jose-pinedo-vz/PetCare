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
        // Por defecto, el producto se considera activo al insertarlo
        $activo = 1;

        $insertProd = "INSERT INTO productos (
            id_producto, codigo, codigo_barra, telefono, nombre, id_categoria, id_proveedor, descripcion, contenido,
            unidad_medida, precio_venta, existencia, existencia_minima, existencia_maxima, fecha_ingreso, imagen, estado
        ) VALUES (
            :id_producto, :codigo, :codigo_barra, :telefono, :nombre, :id_categoria, :id_proveedor, :descripcion, :contenido,
            :unidad_medida, :precio_venta, :existencia, :existencia_minima, :existencia_maxima, :fecha_ingreso, :imagen, :estado
        )";

    

        $stmtProd = $conexion->prepare($insertProd);
        $stmtProd->bindParam(":id_producto",      $id,                         PDO::PARAM_INT);
        $stmtProd->bindParam(":codigo",           $datos['codigo'],            PDO::PARAM_STR);
        $stmtProd->bindParam(":codigo_barra",     $datos['codigo_barra'],      PDO::PARAM_STR);
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
        $stmtProd->bindParam(":estado",           $activo,                     PDO::PARAM_STR);
        
        
        $stmtProd->execute();

        $insertLote = "INSERT INTO lote_productos (
            id_producto, fecha_caducidad,precio_compra, lote, ubicacion, cantidad
        ) VALUES ( 
            :id_producto, :fecha_caducidad, :precio_compra, :lote, :ubicacion, :cantidad
        )";

        $stmtLote = $conexion->prepare($insertLote);
        $stmtLote->bindParam(":id_producto",      $id,                         PDO::PARAM_INT);
        $stmtLote->bindParam(":fecha_caducidad",  $datos['fecha_caducidad'],   PDO::PARAM_STR);
        $stmtLote->bindParam(":precio_compra",    $datos['precio_compra'],     PDO::PARAM_STR);
        $stmtLote->bindParam(":lote",             $datos['lote'],              PDO::PARAM_STR);
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



function eliminar(int $id): bool
{
    try {
        $db = ConexionDB::obtenerConexion();
        $stmt = $db->prepare("UPDATE productos 
                              SET esta_activo = 0
                              WHERE id_producto = ? AND esta_activo = 1");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return htmlspecialchars($e->getMessage());
    }
}

class Productos {
    public static function insertarProducto(array $datos){
        return insertarProducto($datos);
    }
}

if (!class_exists('Productos', false)) {
    class_alias('Productos', 'Productos');
}
?>