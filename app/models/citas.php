<?php
    declare(strict_types=1);
    // if (file_exists(__DIR__ . '/conexion_db.php')) {
    //     require_once __DIR__ . '/conexion_db.php';
    // } elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    //     require_once __DIR__ . '/../../conexion_db.php';
    // }

    require_once 'conexion_db.php';

    // nueva base de datos instalada
    class ConexionesClientes 
    {
        public function consultarIdCliente(int $clave):bool
        {
            // resivido una clave de un supuesto usuario consulto a la base de datos esa misma clave.

            // si la base de datos me retornauna tupla bacia o nula entonces ese usuario no existe, lo que quiere 
            // deceir que no tiene permiso para hacer una consulta
            
            // retorno true o false segun se el caso 

            try
            {
                $query = "SELECT id_cliente FROM clientes WHERE id_cliente = ?";
                $db = ConexionDB::obtenerConexion();
                $stmt = $db->prepare($query);
                $stmt->execute([$clave]);

                $datos = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($datos != null)
                {
                    return true;
                }
                else 
                {
                    return false;
                }    
                
            }
            catch (Exception $e)
            {
                throw new Exception("Error al consultar los productos".$e->getMessage());
                return false;
            }
        }


        // insercino natural a la base de datos
        public function insertarCita(int $clave_cliente,string $fecha,string $motivo):bool
        {
            try
            {
                $query = "INSERT INTO consulta_veterinaria (id_cliente, fecha, motivo_de_consulta, estado_de_pago) VALUES (?, ?, ?, ?)";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$clave_cliente, $fecha, $motivo, "Pendiente"]);
                return true;
            }
            catch (Exception $e)
            {
               throw new Exception("Error al consultar los productos".$e->getMessage());
               return false; 
            }
        }


        // rescatar la base de datos
        public function datosCitas():array
        {
            // $query2 = "SELECT nombre, apellido FROM clientes WHERE id_cliente = ?"
            $query = "SELECT c.id_cliente, c.fecha, c.motivo_de_consulta, cl.nombre, cl.apellido, c.atendido
              FROM consulta_veterinaria c
              INNER JOIN clientes cl ON c.id_cliente = cl.id_cliente";

            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute();
            $datos = $stml->fetchAll(PDO::FETCH_ASSOC);

            return $datos;
        }
    }
?>