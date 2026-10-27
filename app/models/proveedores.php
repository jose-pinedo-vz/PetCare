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

function eliminar(int $id)
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
// Obtiene los proveedores activos para mostrarlos en la vista
function obtenerProveedoresActivos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT id_proveedor, razon_social, nombre_comercial, rfc, telefono, correo,
                       domicilio, ciudad, estado, codigo_postal, condicion_pago,
                       tiempo_entrega, observaciones
                FROM proveedores
                WHERE esta_activo = 1
                ORDER BY id_proveedor DESC";

        $resultado = $conexion->query($sql);
        return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (Exception $e) {
        error_log("Error en obtenerProveedoresActivos: " . $e->getMessage());
        return [];
    }
}
//editar
function obtenerProveedorPorId(int $id): ?array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM proveedores WHERE id_proveedor = ?");
        $stmt->execute([$id]);
        $proveedor = $stmt->fetch(PDO::FETCH_ASSOC);
        return $proveedor ?: null;
    } catch (Exception $e) {
        error_log("Error en obtenerProveedorPorId: " . $e->getMessage());
        return null;
    }
}

// Actualiza los datos de un proveedor existente
function actualizarProveedor(int $id, array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        return ["Error BD", htmlspecialchars($e->getMessage())];
    }

    $sql = "UPDATE proveedores SET
        razon_social = :razon_social, nombre_comercial = :nombre_comercial, rfc = :rfc,
        telefono = :telefono, correo = :correo, domicilio = :domicilio, ciudad = :ciudad,
        estado = :estado, codigo_postal = :codigo_postal, condicion_pago = :condicion_pago,
        tiempo_entrega = :tiempo_entrega, observaciones = :observaciones
        WHERE id_proveedor = :id_proveedor";

    try {
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":razon_social",     $datos['razon_social'],     PDO::PARAM_STR);
        $stmt->bindParam(":nombre_comercial", $datos['nombre_comercial'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc",              $datos['rfc'],              PDO::PARAM_STR);
        $stmt->bindParam(":telefono",         $datos['telefono'],         PDO::PARAM_STR);
        $stmt->bindParam(":correo",           $datos['correo'],           PDO::PARAM_STR);
        $stmt->bindParam(":domicilio",        $datos['domicilio'],        PDO::PARAM_STR);
        $stmt->bindParam(":ciudad",           $datos['ciudad'],           PDO::PARAM_STR);
        $stmt->bindParam(":estado",           $datos['estado'],           PDO::PARAM_STR);
        $stmt->bindParam(":codigo_postal",    $datos['codigo_postal'],    PDO::PARAM_STR);
        $stmt->bindParam(":condicion_pago",   $datos['condicion_pago'],   PDO::PARAM_STR);
        $stmt->bindParam(":tiempo_entrega",   $datos['tiempo_entrega'],   PDO::PARAM_INT);
        $stmt->bindParam(":observaciones",    $datos['observaciones'],    PDO::PARAM_STR);
        $stmt->bindParam(":id_proveedor",     $id,                        PDO::PARAM_INT);
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return ["Error al Actualizar", htmlspecialchars($e->getMessage())];
    }
}

function existeRazonSocial($razon_social){
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT COUNT(*) FROM proveedores WHERE razon_social = :razon_social";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":razon_social", $razon_social, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log("Error en existeRazonSocial: " . $e->getMessage());
        return false;
    }
}

function existeRfc($rfc){
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT COUNT(*) FROM proveedores WHERE rfc = :rfc";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":rfc", $rfc, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log("Error en existeRfc: " . $e->getMessage());
        return false;
    }
}

function existeTelefono($telefono){
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT COUNT(*) FROM proveedores WHERE telefono = :telefono";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":telefono", $telefono, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log("Error en existeTelefono: " . $e->getMessage());
        return false;
    }
}

function existeCorreo($correo){
    try {
        $conexion = ConexionDB::obtenerConexion();
        $sql = "SELECT COUNT(*) FROM proveedores WHERE correo = :correo";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        error_log("Error en existeCorreo: " . $e->getMessage());
        return false;
    }
}
?>