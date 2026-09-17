<?php
    declare(strict_types=1);
    require_once 'conexion_db.php';

    class ConexionesClientes 
    {
        public function consultarIdCliente(int $clave):bool
        {
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
    }
?>