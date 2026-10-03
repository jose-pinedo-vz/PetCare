<?php
declare(strict_types=1);

if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

class FichaMedica
{
    public function insertar(array $datos): bool
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
            return ['exito' => true, 'id' => $id, 'mensaje' => 'Cliente registrado con éxito'];
        //Sisi dice que pos si con true y si no da false y el error
        }catch (Exception $e) {
            return ['exito' => false, 'id' => null, 'mensaje' => $e->getMessage()];
        }
    }
}