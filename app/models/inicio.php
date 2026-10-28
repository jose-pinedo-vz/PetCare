<?php
    require_once __DIR__ . '/conexion_db.php';

    class inicio
    {
        public static function cantidadCitas($fecha)
        {
            try
            {
                $query = 'SELECT COUNT(id_consulta) AS citas FROM consulta_veterinaria WHERE fecha = ?';
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
                $query = 'SELECT COUNT(id_consulta) AS numCitasPagos FROM consulta_veterinaria WHERE estado_de_pago = "Pendiente";';
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
                $query = 'SELECT COUNT(id_mascota) AS numMascotas FROM mascotas';
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
                $query = 'SELECT COUNT(id_producto) as productoStockBajo FROM productos WHERE existencia <= existencia_maxima';
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
                $query = "SELECT consulta_veterinaria.fecha, consulta_veterinaria.motivo_de_consulta, consulta_veterinaria.atendido, clientes.nombre
                    FROM consulta_veterinaria, clientes WHERE fecha = ? AND consulta_veterinaria.id_cliente = clientes.id_cliente";
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