<?php
declare(strict_types=1);

require_once __DIR__ . '/conexion_db.php';

class Categoria
{
    public static function obtenerCategoriasActivas(): array
    {
        try {
            $conexion = ConexionDB::obtenerConexion();
            $sql = "SELECT id_categoria, nombre, descripcion, estado, servicio
                    FROM categoria_productos
                    WHERE estado = 'Activo'
                    ORDER BY id_categoria DESC";

            $resultado = $conexion->query($sql);
            return $resultado ? $resultado->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            error_log("Error en obtenerCategoriasActivas: " . $e->getMessage());
            return [];
        }
    }

    public static function existeNombre(string $nombre): bool
    {
        $conexion = ConexionDB::obtenerConexion();
        $consulta = $conexion->prepare(
            "SELECT 1 FROM categoria_productos WHERE LOWER(nombre) = LOWER(?) LIMIT 1"
        );
        $consulta->execute([$nombre]);

        return $consulta->fetchColumn() !== false;
    }

    public static function insertarCategoria(array $datos): array
    {
        $conexion = ConexionDB::obtenerConexion();

        $consultaId = $conexion->query(
            "SELECT COALESCE(MAX(id_categoria), 0) + 1 FROM categoria_productos"
        );
        $id = (int)$consultaId->fetchColumn();

        $insertCategoria = "INSERT INTO categoria_productos (
            id_categoria, nombre, descripcion, estado, servicio
        ) VALUES (
            :id_categoria, :nombre, :descripcion, :estado, :servicio
        )";

        $consulta = $conexion->prepare($insertCategoria);
        $servicio = 0;
        $consulta->bindParam(':id_categoria', $id,                   PDO::PARAM_INT);
        $consulta->bindParam(':nombre',       $datos['nombre'],      PDO::PARAM_STR);
        $consulta->bindParam(':descripcion',  $datos['descripcion'], PDO::PARAM_STR);
        $consulta->bindParam(':estado',       $datos['estado'],      PDO::PARAM_STR);
        $consulta->bindParam(':servicio',     $servicio,             PDO::PARAM_INT);
        $consulta->execute();

        return [
            'id_categoria' => $id,
            'nombre'       => $datos['nombre'],
            'estado'       => $datos['estado'],
        ];
    }
}