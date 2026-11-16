<?php //Falta cambiar la consulta a atendido(1) si se inserto la ficha
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class FichaMedica
{
    public function insertar(array $datos): array
    {
        try{
            $db = ConexionDB::obtenerConexion(); //Se crea la conexion

            //Se insertan los datos en la tabla, los datos tienen su nombre pregrabado para controlar mejor la insersion
            $sql = "INSERT INTO ficha_medica
                    (id_consulta, fecha, id_mascota, id_veterinario, sintomas, temperatura, peso,
                     frecuencia_cardiaca, frecuencia_respiratoria, diagnostico, tratamiento,
                     medicamentos, dosis, indicaciones, estudios_asociados, proxima_cita,
                     observaciones, costo)
                    VALUES
                    (:id_consulta, NOW(), :id_mascota, :id_veterinario, :sintomas, :temperatura, :peso,
                     :frecuencia_cardiaca, :frecuencia_respiratoria, :diagnostico, :tratamiento,
                     :medicamentos, :dosis, :indicaciones, :estudios_asociados, :proxima_cita,
                     :observaciones, :costo)";

            //Se inserta bien 
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':id_consulta'             => $datos['id_consulta'],
                ':id_mascota'              => $datos['id_mascota'],
                ':id_veterinario'          => $datos['id_veterinario'],
                ':sintomas'                => $datos['sintomas'],
                ':temperatura'             => $datos['temperatura'],
                ':peso'                    => $datos['peso'],
                ':frecuencia_cardiaca'     => $datos['frecuencia_cardiaca'],
                ':frecuencia_respiratoria' => $datos['frecuencia_respiratoria'],
                ':diagnostico'             => $datos['diagnostico'],
                ':tratamiento'             => $datos['tratamiento'],
                ':medicamentos'            => $datos['medicamentos'],
                ':dosis'                   => $datos['dosis'],
                ':indicaciones'            => $datos['indicaciones'],
                ':estudios_asociados'      => $datos['estudios_asociados'],
                ':proxima_cita'            => $datos['proxima_cita'],
                ':observaciones'           => $datos['observaciones'],
                ':costo'                   => $datos['costo'],
            ]);
            $idFicha = (int)$db->lastInsertId();
            return ['exito' => true, 'id' => $datos['id_consulta'], 'id_ficha' => $idFicha, 'mensaje' => 'ficha medica registrada con éxito'];
        //Sisi dice que pos si con true y si no da false y el error
        }catch (Exception $e) {
            return ['exito' => false, 'id' => null, 'mensaje' => $e->getMessage()];
        }
    }

    public function guardarRadiografia(int $idConsulta, ?int $idFicha, ?int $idMascota, string $ruta): bool
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("INSERT INTO radiografias (id_consulta, id_ficha, id_mascota, ruta, fecha_subida) VALUES (:id_consulta, :id_ficha, :id_mascota, :ruta, NOW())");
            return $stmt->execute([
                ':id_consulta' => $idConsulta,
                ':id_ficha'    => $idFicha,
                ':id_mascota'  => $idMascota,
                ':ruta'        => $ruta
            ]);
        } catch (Exception $e) {
            error_log("Error al guardar radiografía en BD: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerRadiografias(int $idConsulta): array
    {
        try {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT * FROM radiografias WHERE id_consulta = :id ORDER BY id_radiografia ASC");
            $stmt->execute([':id' => $idConsulta]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            return [];

        }

    }

        

    public function datosConsulta(int $idConsulta): ?array
    {
        $db=ConexionDB::obtenerConexion();
        $stmt=$db->prepare("SELECT id_cita,cliente from citas where id_cita= :consulta and atendido = 0");
        $stmt->execute([':consulta'=>$idConsulta]);
        $datos=$stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        return $datos;
    }

    public function agarraMascotas(int $idCliente): ?array
    {
        $db=ConexionDB::obtenerConexion();
        $stmt=$db->prepare("SELECT id_mascota AS id, nombre From mascotas where id_cliente= :clientesito order by nombre");
        $stmt->execute([':clientesito'=>$idCliente]);
        $datos=$stmt->fetchAll(PDO::FETCH_ASSOC);

        return $datos;
    }

    public function obtenerVeterinarios(): array
    {
        $db = ConexionDB::obtenerConexion();
        $stmt = $db->query("SELECT id_empleado AS id, CONCAT(nombre, ' ', apellido) AS nombre FROM empleados WHERE puesto LIKE '%Veterinario%' AND esta_activo = 1 ORDER BY nombre ASC");
        $vets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($vets)) {
            $stmt = $db->query("SELECT id_empleado AS id, CONCAT(nombre, ' ', apellido) AS nombre FROM empleados WHERE puesto LIKE '%Veterinario%' ORDER BY nombre ASC");
            $vets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($vets)) {
            $stmt = $db->query("SELECT id_empleado AS id, CONCAT(nombre, ' ', apellido) AS nombre FROM empleados ORDER BY nombre ASC");
            $vets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $vets ?: [];
    }

    public function existeVeterinario(int $idVeterinario): bool
    {
        $db = ConexionDB::obtenerConexion();
        $stmt = $db->prepare("SELECT 1 FROM empleados WHERE id_empleado = :id");
        $stmt->execute([':id' => $idVeterinario]);
        return (bool)$stmt->fetchColumn();
    }

    public function obtenerEmpleadoDeUsuario(int $idUsuario): int
    {
        if ($idUsuario > 0) {
            $db = ConexionDB::obtenerConexion();
            $stmt = $db->prepare("SELECT empleado_asociado FROM usuarios WHERE id_usuario = :id");
            $stmt->execute([':id' => $idUsuario]);
            $empleado = $stmt->fetchColumn();
            if ($empleado && (int)$empleado > 0) {
                return (int)$empleado;
            }
        }

        $vets = $this->obtenerVeterinarios();
        if (!empty($vets)) {
            return (int)$vets[0]['id'];
        }

        return 1;
    }



    public function obtenerEmpleadoPorUsuario(int $idUsuario): ?int
    {
        $db = ConexionDB::obtenerConexion();

        $stmt = $db->prepare("SELECT empleado_asociado FROM usuarios WHERE id_usuario=:id_usuario");

        $stmt->execute([':id_usuario' => $idUsuario]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado || $resultado['empleado_asociado'] === null) {
            return null;
        }

        return (int)$resultado['empleado_asociado'];
    }

}

