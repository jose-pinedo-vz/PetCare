<?php
/*codigo aqui*/
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

function IDproveedor(PDO $conexion): int {
    try {
        // Consultamos el ID máximo actual y le sumamos 1
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_proveedor), 0) + 1 FROM proveedores");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 1;
    }
}


function insertar(array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        return ["Error BD",htmlspecialchars($e->getMessage())];
    }

    $id = IDproveedor($conexion);
    // Por defecto, el proveedor se considera activo al insertarlo
    $activo = 1;

    $insert = "INSERT INTO proveedores (
        id_proveedor, razon_social, nombre_comercial, rfc, telefono, correo, domicilio, ciudad, estado, codigo_postal,
        condicion_pago, tiempo_entrega, observaciones, esta_activo
    ) VALUES (
        :id_proveedor, :razon_social, :nombre_comercial, :rfc, :telefono, :correo, :domicilio, :ciudad, :estado, :codigo_postal,
        :condicion_pago, :tiempo_entrega, :observaciones,  :esta_activo
    )";

    try {
        $stmt = $conexion->prepare($insert);
        $stmt->bindParam(":id_proveedor",     $id,                        PDO::PARAM_INT);
        $stmt->bindParam(":razon_social",     $datos['razon_social'],     PDO::PARAM_STR);
        $stmt->bindParam(":nombre_comercial", $datos['nombre_comercial'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc",              $datos['rfc'],              PDO::PARAM_STR);
        $stmt->bindParam(":telefono",         $datos['telefono'],         PDO::PARAM_STR);
        $stmt->bindParam(":correo",           $datos['correo'],           PDO::PARAM_STR);
        $stmt->bindParam(":domicilio",        $datos['domicilio'],        PDO::PARAM_STR);
        $stmt->bindParam(":ciudad",           $datos['ciudad'],           PDO::PARAM_STR);
        $stmt->bindParam(":estado",           $datos['estado'],           PDO::PARAM_STR);
        $stmt->bindParam(":codigo_postal",    $datos['codigo_postal' ],   PDO::PARAM_STR);
        $stmt->bindParam(":condicion_pago",   $datos['condicion_pago'],   PDO::PARAM_STR);
        $stmt->bindParam(":tiempo_entrega",   $datos['tiempo_entrega'],   PDO::PARAM_INT);
        $stmt->bindParam(":observaciones",    $datos['observaciones'],    PDO::PARAM_STR);
        $stmt->bindParam(":esta_activo",      $activo,                    PDO::PARAM_INT);
        
        $stmt->execute();

    }catch(PDOException $e){
        return ["Error al Insertar",htmlspecialchars($e->getMessage())];
    }
}

function eliminar(int $id): bool
{
    try {
        $db = ConexionDB::obtenerConexion();
        $stmt = $db->prepare("UPDATE proveedores 
                              SET esta_activo = 0
                              WHERE id_proveedor = ? AND esta_activo = 1");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return htmlspecialchars($e->getMessage());
    }
}
?>