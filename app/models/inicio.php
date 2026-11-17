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
                $query = 'SELECT COUNT(id_producto) as productoStockBajo FROM productos WHERE contenido <= existencia_minima';
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();
                return $stml->fetchAll();
            }
            catch (Exception $e)
            {

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