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
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_mascota), 0) + 1 FROM mascotas");
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

    $insert = "INSERT INTO mascotas (
        id_proveedor, razon_social, nombre_comercial, rfc, telefono, correo, domicilio, ciudad, estado, codigo_postal,
        condicion_pago, tiempo_entrega, observaciones
    ) VALUES (
        :id_proveedor, :razon_social, :nombre_comercial, :rfc, :telefono, :correo, :domicilio, :ciudad, :estado, :codigo_postal,
        :condicion_pago, :tiempo_entrega, :observaciones,  1
    )";

    try {
        $stmt = $conexion->prepare($insert);
        $stmt->execute([
            ':id_proveedor'         => $id,
            ':razon_social'         => $datos['razon_social'],
            ':nombre_comercial'     => $datos['nombre_comercial'],
            ':rfc'                  => $datos['rfc'],
            ':telefono'             => $datos['telefono'],
            ':correo'               => $datos['correo'],
            ':domicilio'            => $datos['domicilio'],
            ':ciudad'               => $datos['ciudad'],
            ':estado'               => $datos['estado'],
            ':codigo_postal'        => $datos['codigo_postal' ],
            ':condicion_pago'       => $datos['condicion_pago'],
            ':tiempo_entrega'       => $datos['tiempo_entrega'],
            ':observaciones'        => $datos['observaciones']
        ]);

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