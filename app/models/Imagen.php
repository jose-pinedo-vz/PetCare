<?php

require_once __DIR__ . '/conexion_db.php';

class Imagen
{
    // Actualiza la ruta de la fotografía de una mascota existente en la base de datos.
    public static function asociarAMascota(int $id_mascota, string $ruta): bool
    {
        try {
            $pdo = ConexionDB::obtenerConexion();
            $stmt = $pdo->prepare("UPDATE mascotas SET fotografia = :foto WHERE id_mascota = :id");
            return $stmt->execute([
                ':foto' => $ruta,
                ':id'   => $id_mascota
            ]);
        } catch (Exception $e) {
            // Si la base de datos no está disponible o no existe el registro, registramos el error
            error_log("Error al asociar imagen a mascota: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guarda la imagen en la tabla de imagenes
     */
    public static function registrarEnTabla(string $ruta, ?int $id_mascota = null): bool
    {
        try {
            $pdo = ConexionDB::obtenerConexion();
            // Intenta insertar en tabla imagenes si existe
            $stmt = $pdo->prepare("INSERT INTO imagenes (id_mascota, ruta, fecha_subida) VALUES (:id_mascota, :ruta, NOW())");
            return $stmt->execute([
                ':id_mascota' => $id_mascota,
                ':ruta'       => $ruta
            ]);
        } catch (Exception $e) {
            error_log("Tabla imagenes no disponible: " . $e->getMessage());
            return false;
        }
    }
}