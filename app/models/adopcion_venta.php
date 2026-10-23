<?php
if (file_exists(__DIR__ . '/conexion_db.php')) {
    require_once __DIR__ . '/conexion_db.php';
} elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    require_once __DIR__ . '/../../conexion_db.php';
}

function IDanimal(PDO $conexion): int {
    try {
        $stmt = $conexion->query("SELECT COALESCE(MAX(id_animal), 0) + 1 FROM animal_venta");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 1;
    }
}

function insertar(array $datos) {
    try {
        $conexion = ConexionDB::obtenerConexion();
    } catch (Exception $e) {
        return ["Error BD", htmlspecialchars($e->getMessage())];
    }

    $id = IDanimal($conexion);

    $insert = "INSERT INTO animal_venta (
        id_animal, especie, raza, sexo, fecha_de_nacimiento, color, peso,
        caracteristicas, fotografia, procedencia, fecha_de_ingreso,
        costo_de_adquisicion, precio_de_venta, estado, vacunas,
        desparasitacion, observaciones
    ) VALUES (
        :id_animal, :especie, :raza, :sexo, :fecha_de_nacimiento, :color, :peso,
        :caracteristicas, :fotografia, :procedencia, :fecha_de_ingreso,
        :costo_de_adquisicion, :precio_de_venta, :estado, :vacunas,
        :desparasitacion, :observaciones
    )";

    try {
        $stmt = $conexion->prepare($insert);
        $stmt->bindParam(":id_animal",            $id,                                  PDO::PARAM_INT);
        $stmt->bindParam(":especie",              $datos['especie'],                    PDO::PARAM_STR);
        $stmt->bindParam(":raza",                 $datos['raza'],                       PDO::PARAM_STR);
        $stmt->bindParam(":sexo",                 $datos['sexo'],                       PDO::PARAM_STR);
        $stmt->bindParam(":fecha_de_nacimiento",  $datos['fecha_de_nacimiento'],       PDO::PARAM_STR);
        $stmt->bindParam(":color",                $datos['color'],                      PDO::PARAM_STR);
        $stmt->bindParam(":peso",                 $datos['peso']);
        $stmt->bindParam(":caracteristicas",      $datos['caracteristicas'],           PDO::PARAM_STR);
        $stmt->bindParam(":fotografia",           $datos['fotografia'],                 PDO::PARAM_STR);
        $stmt->bindParam(":procedencia",          $datos['procedencia'],                PDO::PARAM_STR);
        $stmt->bindParam(":fecha_de_ingreso",     $datos['fecha_de_ingreso'],          PDO::PARAM_STR);
        $stmt->bindParam(":costo_de_adquisicion", $datos['costo_de_adquisicion']);
        $stmt->bindParam(":precio_de_venta",      $datos['precio_de_venta']);
        $stmt->bindParam(":estado",               $datos['estado'],                     PDO::PARAM_STR);
        $stmt->bindParam(":vacunas",              $datos['vacunas'],                    PDO::PARAM_STR);
        $stmt->bindParam(":desparasitacion",      $datos['desparasitacion'],           PDO::PARAM_STR);
        $stmt->bindParam(":observaciones",        $datos['observaciones'],             PDO::PARAM_STR);

        $stmt->execute();
        return [];
    } catch (PDOException $e) {
        return ["Error al Insertar", htmlspecialchars($e->getMessage())];
    }
}

function editar(array $datos)
{
    try
    {
        $conexion=ConexionDB::obtenerConexion();
    }
    catch (Exception $e)
    {
        return ["Error BD", htmlspecialchars($e->getMessage())];
    }
    $id=$datos['id_animal'] ?? null;
    
    if (!$id)
    {
        return ["Error", "ID de animal no especificado para la actualización."];
    }

    $update="UPDATE animal_venta SET
        especie=:especie,
        raza=:raza,
        sexo=:sexo,
        fecha_de_nacimiento=:fecha_de_nacimiento,
        color=:color,
        peso=:peso,
        caracteristicas=:caracteristicas,
        fotografia=:fotografia,
        procedencia=:procedencia,
        fecha_de_ingreso=:fecha_de_ingreso,
        costo_de_adquisicion=:costo_de_adquisicion,
        precio_de_venta=:precio_de_venta,
        estado=:estado,
        vacunas=:vacunas,
        desparasitacion=:desparasitacion,
        observaciones=:observaciones
    WHERE id_animal=:id_animal";

    try
    {
        $stmt=$conexion->prepare($update);
        $stmt->bindParam(":id_animal",            $id,                        PDO::PARAM_INT);
        $stmt->bindParam(":especie",              $datos['especie'],          PDO::PARAM_STR);
        $stmt->bindParam(":raza",                 $datos['raza'],             PDO::PARAM_STR);
        $stmt->bindParam(":sexo",                 $datos['sexo'],             PDO::PARAM_STR);
        $stmt->bindParam(":fecha_de_nacimiento",  $datos['fecha_de_nacimiento'],      PDO::PARAM_STR);
        $stmt->bindParam(":color",                $datos['color'],            PDO::PARAM_STR);
        $stmt->bindParam(":peso",                 $datos['peso']);
        $stmt->bindParam(":caracteristicas",      $datos['caracteristicas'],          PDO::PARAM_STR);
        $stmt->bindParam(":fotografia",           $datos['fotografia'],       PDO::PARAM_STR);
        $stmt->bindParam(":procedencia",          $datos['procedencia'],      PDO::PARAM_STR);
        $stmt->bindParam(":fecha_de_ingreso",     $datos['fecha_de_ingreso'],         PDO::PARAM_STR);
        $stmt->bindParam(":costo_de_adquisicion", $datos['costo_de_adquisicion']);
        $stmt->bindParam(":precio_de_venta",      $datos['precio_de_venta']);
        $stmt->bindParam(":estado",               $datos['estado'],           PDO::PARAM_STR);
        $stmt->bindParam(":vacunas",              $datos['vacunas'],          PDO::PARAM_STR);
        $stmt->bindParam(":desparasitacion",      $datos['desparasitacion'],          PDO::PARAM_STR);
        $stmt->bindParam(":observaciones",        $datos['observaciones'],            PDO::PARAM_STR);

        $stmt->execute();
        return [];
    }
    catch (PDOException $e)
    {
        return ["Error al Actualizar", htmlspecialchars($e->getMessage())];
    }
}

function obtenerTodos(): array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->query("SELECT * FROM animal_venta WHERE estado NOT IN ('Vendido', 'Adoptado') OR estado IS NULL ORDER BY id_animal DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
    catch (Exception $e)
    {
        return [];
    }
}

function obtenerPorId(int $id): ?array {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("SELECT * FROM animal_venta WHERE id_animal = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function eliminar(int $id): bool {
    try {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("DELETE FROM animal_venta WHERE id_animal = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        return false;
    }
}
function vendido(int $id, bool $esAdopcion)
{
    try
    {
        $conexion = ConexionDB::obtenerConexion();
        $stmt = $conexion->prepare("UPDATE animal_venta SET estado = ? WHERE id_animal = ?");
        $nuevoEstado = $esAdopcion ? 'Adoptado' : 'Vendido';
        $stmt->execute([$nuevoEstado, $id]);
        return true;    
    }
    catch (Exception $e)
    {
        return false;
    }
}
