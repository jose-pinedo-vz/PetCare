<?php
    declare(strict_types=1);
    // if (file_exists(__DIR__ . '/conexion_db.php')) {
    //     require_once __DIR__ . '/conexion_db.php';
    // } elseif (file_exists(__DIR__ . '/../../conexion_db.php')) {
    //     require_once __DIR__ . '/../../conexion_db.php';
    // }

    //require_once 'conexion_db.php';
    require_once __DIR__ . '/conexion_db.php';

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
            $query = "SELECT c.id_cita, c.cliente, c.fecha, c.observacion, cl.nombre, cl.apellido, c.atendido, c.estado_cita, c.servicio, c.empleado, c.estado
              FROM citas c
              INNER JOIN clientes cl ON c.cliente = cl.id_cliente WHERE c.estado_cita IN ('pendiente', 'atendida', 'cancelada')
              ORDER BY fecha ASC";

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
                $query = "UPDATE citas SET atendido = 1, estado_cita = 'atendida', estado = 'atendida' WHERE id_cita = ?";
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

        // mostrar mascotas de un usuario
        public static function consultaMascota($id_cliente)
        {
            try
            {
                $query = "SELECT id_mascota, nombre FROM mascotas WHERE id_cliente = ? AND esta_activo = 1";
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

        public static function noAsistioCita($id_cita)
        {
            try
            {
                $query = "UPDATE citas
                    SET estado_cita = 'cancelada',  estado = 'No asistio'
                    WHERE id_cita = ?";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$id_cita]);
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

        public static function obtenerResponsable(int $cita)
        {
            $query = "SELECT empleado FROM citas WHERE id_cita = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$cita]);
            return $stml->fetch(PDO::FETCH_ASSOC);
        }

        public static function actualizarCita(int $Idcita, string $fecha)
        {
            try
            {
                $query = "UPDATE citas SET fecha = ? WHERE id_cita = ?";
                $db = ConexionDB::obtenerConexion();
                $stmt = $db->prepare($query);
                return $stmt->execute([$fecha, $Idcita]);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function consultarServicioPorCategoria(string $servicio)
        {
            $query = "SELECT categoria FROM servicios WHERE id_servicio = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$servicio]);
            return $stml->fetch(PDO::FETCH_ASSOC);
        }

        public static function consultarEmpleadoPorPuesto($puesto)
        {
            try
            {
                $query = "SELECT empleados.id_empleado, CONCAT(empleados.nombre, ' ', empleados.apellido) AS nombre 
                  FROM empleados 
                  WHERE puesto = ? AND esta_activo = 1
                  ";
                  
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$puesto]);
                return $stml->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (Exception $e)
            {
                return [];
            }
        }

        public static function consultarServicioPorConsulta($id_consulta)
        {
            $query = "SELECT servicio FROM citas WHERE id_cita = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$id_consulta]);
            return $stml->fetchColumn();
        }

        public static function consultarResponsablePorConsulta($id_consulta)
        {
            $query = "SELECT empleado FROM citas WHERE id_cita = ?";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$id_consulta]);
            return $stml->fetchColumn();
        }

        public static function insertarDesparacitacion($id_cita, $TipoDesparacitacion, $ProductoUtilizado, $ProximaCita, $Docies, $empleado, $Observaciones)
        {
            try
            {
                $query = "INSERT INTO servicio_desparasitacion (id_cita, tipo, producto_utilizado, proxima_aplicacion, dosis, veterinario, observaciones) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                return $stml->execute([$id_cita, $TipoDesparacitacion, $ProductoUtilizado, $ProximaCita, $Docies, $empleado, $Observaciones]);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function insertarCorteUnias($id_cita, $tipo_servicio, $precio, $observaciones, $comportamiento)
        {
            try
            {
                $query = "INSERT INTO servicio_corte_unas (id_cita, tipo_servicio, precio, observaciones, comportamiento, estado) 
                        VALUES (?, ?, ?, ?, ?, 1)";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                
                // Mandamos los 5 parámetros restantes (ya sin el estado)
                return $stml->execute([$id_cita, $tipo_servicio, $precio, $observaciones, $comportamiento]);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function marcarCitaAtendida($id_cita)
        {
            try
            {
                $query = "UPDATE citas SET estado_cita = 'atendida' WHERE id_cita = ?";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                return $stml->execute([$id_cita]);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public static function insertarCortePelo($id_cita, $tipo_corte, $longitud_corte, $corte_higienico, $corte_cara, $corte_patas, $cepillado, $deslanado, $accesorios, $precio, $observaciones)
        {
            try
            {
                $query = "INSERT INTO servicio_corte_pelo (id_cita, tipo_corte, longitud_corte, corte_higienico, corte_cara, corte_patas, cepillado, deslanado,  
                precio, observaciones) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                return $stml->execute([
                    $id_cita, 
                    $tipo_corte, 
                    $longitud_corte, 
                    $corte_higienico, 
                    $corte_cara, 
                    $corte_patas, 
                    $cepillado, 
                    $deslanado, 
                    //$accesorios, 
                    $precio, 
                    $observaciones
                ]);
            }
            catch (Exception $e)
            {
                exit();
                return false;
            }
        }

        public static function insertarBano($id_cita, $tipo_bano, $tipo_shampoo, $tratamiento_adicional, $cepillado, $secado, $precio, $estado, $observaciones, $condiciones_recibidas, $condiciones_entrega)
        {
            try {
                $query = "INSERT INTO servicio_bano (id_cita, tipo_bano, tipo_shampoo, tratamiento_adicional, cepillado, secado, precio, estado, observaciones, condiciones_recibidas, condiciones_entrega) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                return $stml->execute([
                    $id_cita, 
                    $tipo_bano, 
                    $tipo_shampoo, 
                    $tratamiento_adicional, 
                    $cepillado, 
                    $secado, 
                    $precio, 
                    $estado, 
                    $observaciones, 
                    $condiciones_recibidas, 
                    $condiciones_entrega
                ]);
            } 
            catch (Exception $e) 
            {

                return false;
            }
        }

        public static function consutltarDuracinoServicio($idServicio)
        {
            try
            {
                $query = "SELECT duracion FROM servicios WHERE id_servicio = ?";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$idServicio]);
                return $stml->fetchColumn();
            }
            catch (Exception $e)
            {
                return null;
            }
        }

        public static function actualizarCitaObservacino(int $Idcita, string $observacion)
        {
            try
            {
                $query = "UPDATE citas SET observacion = ? WHERE id_cita = ?";
                $db = ConexionDB::obtenerConexion();
                $stmt = $db->prepare($query);
                return $stmt->execute([$observacion, $Idcita]);
            }
            catch (Exception $e)
            {
                return false;
            }
        }

        public function hayMasCitas($idResponsable, $fecha)
        {
            try
            {
                $query = "SELECT * FROM citas WHERE empleado = ? and fecha = ? AND estado_cita != 'cancelada'";
                $db = ConexionDB::obtenerConexion();
                $stmt = $db->prepare($query);
                $stmt->execute([$idResponsable, $fecha]);
                return (bool) $stmt->fetchColumn();
            }
            catch (Exception $e)
            {
                return false;
            }   
        }
        // 

        public static function citasDelDia(string $fecha, int $empleado)
        {
            $query = "SELECT TIME(fecha) AS hora, duracion_estimada
                    FROM citas
                    WHERE empleado = ?
                        AND fecha >= ? AND fecha < DATE_ADD(?, INTERVAL 1 DAY)
                        AND estado_cita != 'cancelada'";
            $db = ConexionDB::obtenerConexion();
            $stml = $db->prepare($query);
            $stml->execute([$empleado, $fecha, $fecha]);

            $mapa = [];
            foreach ($stml->fetchAll(PDO::FETCH_ASSOC) as $f) {
                $mapa[$f['hora']] = (int)$f['duracion_estimada'];   
            }
            return $mapa;
        }

        public static function serviciosIdYNombreModel()
        {
            try
            {
                $query = "SELECT id_servicio, nombre FROM servicios";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();                                  

                $mapa = [];
                foreach ($stml->fetchAll(PDO::FETCH_ASSOC) as $f) {
                    $mapa[$f['id_servicio']] = $f['nombre'];        
                }
                return $mapa;
            }
            catch (Exception $e)
            {
                return [];
            }
        }

        public static function productosDesparasitantes()
        {
            try
            {
                $conexion = ConexionDB::obtenerConexion();   // FALTABA
                $query = "SELECT p.id_producto, p.nombre
                        FROM productos p
                        INNER JOIN categoria_productos c ON p.id_categoria = c.id_categoria
                        WHERE c.nombre = 'Desparasitante' AND c.estado = 'Activo'";
                $stml = $conexion->prepare($query);
                $stml->execute();
                return $stml->fetchAll(PDO::FETCH_ASSOC);
            }
            catch (Throwable $e)                          
            {
                return [];
            }
        }

        public static function ReponsableIdNombre()
        {
            try
            {
                //$query = "SELECT id_empleado, nombre FROM empleados";
                $query = "SELECT id_empleado, nombre, apellido FROM empleados";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute();                                  

                $mapa = [];
                foreach ($stml->fetchAll(PDO::FETCH_ASSOC) as $f) {
                    $mapa[$f['id_empleado']] = $f['nombre'] . ' ' . $f['apellido'];     
                }
                return $mapa;
            }
            catch (Exception $e)
            {
                return [];
            }
        }

        public static function empleadoDelUsuario(string $usuario): string
        {
            try
            {
                $query = "SELECT CONCAT(e.nombre, ' ', e.apellido) AS completo
                        FROM usuarios u
                        INNER JOIN empleados e ON e.id_empleado = u.empleado_asociado
                        WHERE u.usuario = ?
                        LIMIT 1";
                $db = ConexionDB::obtenerConexion();
                $stml = $db->prepare($query);
                $stml->execute([$usuario]);
                return (string)$stml->fetchColumn();  
            }
            catch (Exception $e)
            {
                return '';
            }
        }
    }
?>
