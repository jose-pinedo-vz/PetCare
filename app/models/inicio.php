<?php
    require_once __DIR__ . '/conexion_db.php';

    class inicio
    {
        public static function cantidadCitas($fecha)
        {
            try
            {
                $query = 'SELECT COUNT(id_cita) AS citas FROM citas WHERE fecha = ?';
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$fecha]);
                return $stml->fetchAll();
            } 
            catch (Exception $e)
            {
                return 0;
            }
        }

        public static function cantidadCitasPorCobrar()
        {
            try
            {
                $query = 'SELECT COUNT(id_cita) AS numCitasPagos FROM citas WHERE estado_cita = "atendida"';
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();
                return $stml->fetchAll();
            }
            catch (Exception $e)
            {
                return 0;
            }
        }

        public static function cantidaDeMascotas()
        {
            try
            {
                $query = 'SELECT COUNT(id_mascota) AS numMascotas FROM mascotas WHERE esta_activo = "1"';
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();
                return $stml->fetchAll();
            }
            catch (Exception $e)
            {
                return 0;
            }
        }

        public static function cantidadProductosStockBajo()
        {
            try
            {
                $query = "SELECT COUNT(p.id_producto) AS productoStockBajo
                          FROM productos p
                          LEFT JOIN (
                              SELECT id_producto, SUM(cantidad) AS existencia
                              FROM lote_productos
                              WHERE fecha_caducidad IS NULL OR fecha_caducidad >= CURDATE()
                              GROUP BY id_producto
                          ) l ON l.id_producto = p.id_producto
                          WHERE (p.estado = 1 OR p.estado = 'activo')
                            AND COALESCE(l.existencia, 0) <= p.existencia_minima";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();
                return $stml->fetchAll();
            }
            catch (Exception $e)
            {
                return [['productoStockBajo' => 0, 0 => 0]];
            }
        }

        public static function citasDeHoy($fecha)
        {
            try
            {
                $query = "SELECT citas.fecha, citas.observacion, citas.estado, clientes.nombre
                    FROM citas, clientes WHERE fecha = ? AND citas.cliente = clientes.id_cliente";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$fecha]);
                return $stml->fetchAll();
            }
            catch (Exception $e)
            {
                return [];
            }
        }
    }
?>