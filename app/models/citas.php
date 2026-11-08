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
        public function insertarCita(int $clave_cliente,int $mascota, int $servicio, int $empleado, string $estado_cita, 
                int $duracion_estimada, string $observaciones, string $estado, string $fecha, string $motivo, string $atendido): bool
        {
            try
            {
                // 11 columnas y exactamente 11 signos de interrogación (?)
                $query = "INSERT INTO citas (cliente, mascota, servicio, empleado, estado_cita, duracion_estimada, observacion, estado, fecha, motivo_consulta, atendido) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([
                    $clave_cliente,     
                    $mascota,           
                    $servicio,         
                    $empleado,          
                    $estado_cita,       
                    $duracion_estimada,
                    $observaciones,     
                    $estado,            
                    $fecha,             
                    $motivo,            
                    $atendido           
                ]);
                return true;
            }
            catch (Exception $e)
            {
            throw new Exception("Error al insertar la cita: ".$e->getMessage());
            return false;
            }
        }


        // rescatar la base de datos
        public function datosCitas():array
        {
            // $query2 = "SELECT nombre, apellido FROM clientes WHERE id_cliente = ?"
            $query = "SELECT c.id_cita, c.cliente, c.fecha, c.observacion, cl.nombre, cl.apellido, c.atendido, c.estado_cita
              FROM citas c
              INNER JOIN clientes cl ON c.cliente = cl.id_cliente";

            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([]);
            $datos = $stml->fetchAll(PDO::FETCH_ASSOC);

            return $datos;
        }

        // pasar la consulta de pendiente a realizapa pero pendiente de pago
        public function pendinteDepago($id) : bool
        {
            try
            {
                $query = "UPDATE citas SET atendido = 1 WHERE id_cita = ?";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$id]);
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function rescatarVeterinarios()
        {
            try
            {
                $query = "SELECT nombre, horario FROM `empleados` WHERE puesto LIKE 'Veterinario%' AND esta_activo = 1";
                $db = ConexionDB::obtenerConexion();
                $stml =$db->prepare($query);
                $stml->execute();
                return $stml->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        // cantidad de citas por fecha
        public static function cantidadCitasFecha($valor)
        {
            try
            {
                // Comparamos año, mes, día y hora:minuto, ignorando segundos o microsegundos
                $query = 'SELECT COUNT(*) AS total_citas FROM citas WHERE fecha = ? AND estado_cita = "pendiente"';
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$valor]);
                return $stml->fetchColumn();
            }
            catch (Exception $e)
            {
                return 0;
            }
        }

        public static function duracionCita($fecha)
        {
            try
            {
                $query = "SELECT duracion_estimada FROM citas WHERE fecha = ? AND estado_cita = 'pendiente'";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$fecha]);
                return $stml->fetchColumn();
            }
            catch (Exception $e)
            {
                return 0;
            }
        }

        // mostrar mascotas de un usuario
        public static function consultaMascota($id_cliente)
        {
            try
            {
                $query = "SELECT id_mascota, nombre FROM mascotas WHERE id_cliente = ? AND esta_activo = 0";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$id_cliente]);
                return $stml->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (Exception $e)
            {
                return [];

            }
        }

        public static function mostrarServiciosPorServicio($id_servicio)
        {
            try
            {
                $query = "SELECT empleados.id_empleado, CONCAT(empleados.nombre, ' ', empleados.apellido) AS nombre 
                  FROM empleados 
                  INNER JOIN servicios ON empleados.puesto = servicios.categoria 
                  WHERE servicios.id_servicio = ? AND servicios.estado = 1 AND empleados.esta_activo = 1";
                  
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$id_servicio]);
                return $stml->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (Exception $e)
            {
                return [];
            }
        }

        public static function mostrarServicios()
        {
            try
            {
                $query = "SELECT id_servicio, nombre FROM servicios WHERE estado = 1";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([]);
                return $stml->fetchAll(PDO::FETCH_ASSOC);

            }
            catch (Exception $e)
            {
                return [];
            }
        }

        public static function contarHorarios($fecha, $empleado)
        {
            $query = "SELECT COUNT(id_cita)
                    FROM citas
                    WHERE fecha = ? AND empleado = ?  
                    ";

            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$fecha, $empleado]);
            return (int)$stml->fetchColumn();
        }

        public static function cancelarCita($id_cita)
        {
            try
            {
                $query = "UPDATE citas 
                        SET estado_cita = 'cancelada'
                        WHERE id_cita = ?";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$id_cita]);
                $stml->fetchColumn();
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function consultarServicio(int $idServicio)
        {
            $query = "SELECT nombre FROM servicios WHERE id_servicio = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$idServicio]);
            return $stml->fetch(PDO::FETCH_ASSOC);
        }

        public static function consultarResponsable(int $idResponsable)
        {
            $query = "SELECT nombre, apellido FROM empleados WHERE id_empleado = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$idResponsable]);
            return $stml->fetch(PDO::FETCH_ASSOC);
        }

        public static function consultarMascota(int $idMascota)
        {
            $query = "SELECT nombre FROM mascotas WHERE id_mascota = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$idMascota]);
            return $stml->fetch(PDO::FETCH_ASSOC);
        }
    }
?>
