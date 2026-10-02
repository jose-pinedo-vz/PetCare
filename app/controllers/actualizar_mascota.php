<?php

// la direccion del archivo que conecta la base de datos
require __DIR__ . '/../models/conexion_db.php';

date_default_timezone_set('America/Mexico_City');

try {

    // asi se llama a un metodo 'obtenerConexion()' de la clase 'ConexionDB' para hacer la conexion
    $conexion = ConexionDB::obtenerConexion();

    // verifica si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // valida que el id sea un entero y si no llego o es invalido se va al if
        $idMascota = filter_input(INPUT_POST, 'id_mascota', FILTER_VALIDATE_INT);

        if ($idMascota === false || $idMascota === null) {
            echo "Error: no se recibio un ID de mascota valido";
            exit;
        }

        // Datos actuales de la mascota
        $consultaBusqueda = $conexion -> prepare(
            "
            SELECT *
            FROM mascotas
            WHERE id_mascota = ?
            "
        ); // Prepara la consulta
        $consultaBusqueda -> execute([$idMascota]); //ejecuta la consulta
        $datosActuales = $consultaBusqueda -> fetch(PDO::FETCH_ASSOC); // regresa un arreglo asociativo ['nombre' => 'Max', ...]

        // verifica que exita el id de la mascota
        if (!$datosActuales) {
            echo "Error: no existe una mascota con ese ID";
            exit;
        }

        // Campos editables "llegan como texto/numero desde $_POST "
        $camposEditables = [
            'nombre', 'especie', 'raza', 'sexo', 'edad', 'color', 'peso',
            'tamanio', 'id_cliente', 'id_veterinario',
            'alergias', 'enfermedades', 'medicamentos', 'condiciones_especiales',
            'temperamento', 'restricciones_para_manejo', 'vacunas',
            'ultima_desparasitacion', 'observaciones'
        ];

        // Si se hicieron cambios se usan, si no se quedan los anteriores
        // !empty() = para que el campo vacio coserve el valor anterior si lo tenia
        $valoresFinales = [];
        foreach ($camposEditables as $campo) {
            // $valoresFinales[$campo] = isset($_POST[$campo]) ? $_POST[$campo] : $datosActuales[$campo];
            $valoresFinales[$campo] = !empty($_POST[$campo]) ? $_POST[$campo] : $datosActuales[$campo];
        }

        // edad
        if (!empty($valoresFinales['edad'])) {
            try {
                $fechaActual = new DateTime('today');
                $fechaIngresada = new DateTime($valoresFinales['edad']);
                if ($fechaIngresada > $fechaActual) {
                    $msg = urlencode("La fecha de nacimiento no puede ser posterior a hoy.");
                    header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
                    // header('Location: ../views/editar_mascota.php');
                    // echo "Error: La fecha de edad no puede ser una fecha despues de hoy";
                    exit;
                }
            }
            catch (\Exception $error) {
                $msg = urlencode("Error: la fecha de edad no es valida");
                header("Location: ../views/editar_mascota.php?id_mascota={$idMascota}&error={$msg}");
                // header('Location: ../views/mascotas.php');
                // echo "Error: la fecha de edad no es valida";
                exit;
            }
        }

        // ultima desparasitacion
        if (!empty($valoresFinales['ultima_desparasitacion'])) {
            try {
                $fechaActual = new DateTime('today');
                $fechaIngresada = new DateTime($valoresFinales['ultima_desparasitacion']);
                if ($fechaIngresada > $fechaActual) {
                    header('Location: ../views/editar_mascota.php');
                    // echo "Error: La fecha de la ultima desparasitacion no puede ser una fecha despues de hoy";
                    exit;
                }
            }
            catch (\Exception $error) {
                header('Location: ../views/editar_mascota.php');
                // header('Location: ../views/mascotas.php');
                // echo "Error: la fecha de desparasitacion no es valida";
                exit;
            }
        }

        // la foto se agrega al final por si no se cambio (se envia por $_FILE)
        $valoresFinales['fotografia'] = $datosActuales['fotografia'];

        // si llego una foto nueva

        // se evalua si se subio un archivo nuevo o que tenga una archivo
        // !empty($_FILES['fotografia']['name'] = si el nombre del archivo no esta vacio
        // $_FILES['fotografia']['error'] !== UPLOAD_ERR_NO_FILE = el codigo de error es diferente a no se subio un archivo
        if (!empty($_FILES['fotografia']['name']) && $_FILES['fotografia']['error'] !== UPLOAD_ERR_NO_FILE) {

            // carga el 'filtro para subir imagenes' y la guarda si es correcta
            require_once __DIR__ . '/../controllers/ImagenController.php';
            $resultadoImagen = ImagenController::subir($_FILES['fotografia']);

            if ($resultadoImagen['exito']) {
                $valoresFinales['fotografia'] = $resultadoImagen['ruta'];
            }

            else {
                echo "Error en la foto: " . $resultadoImagen['mensaje'];
                exit;
            }
        }

        // agregamos fotografia al final de las columnas que se van a actualizar
        $camposEditables[] = 'fotografia';

        if (isset($_POST['activo'])) {
            $camposEditables[] = 'esta_activo';
            $valoresFinales['esta_activo'] = (int)$_POST['activo'];
        }

        // ( fn($c) => "$c = ?" ) = esto se puede considerar una funcion lambda de python
        // array_map (funcion, arreglo) = aplica la funcion a cada elemento del arreglo
        // implode (', ', resultado de array_map) = pega en una string el resultado de array_map y lo separa con una ,
        $sets = implode(', ', array_map( fn($camposEdi) => "$camposEdi = ?", $camposEditables) );

        // consulta
        $sqlActualizacion = "
            UPDATE mascotas
            SET $sets
            WHERE id_mascota = ?
        ";
        $sentenciaActualizacion = $conexion -> prepare($sqlActualizacion); // prepara la consulta escrita antes

        // array_values() = toma el arreglo le quita las llaves ej. 'nombre' por un valor enumerado desde 0 hasta el final
        // [... arreglo enumerado, el id de la mascota] = los ... desarman los valores uno por uno dentro de otro arreglo y al final agrega el id de la mascota
        $sentenciaActualizacion -> execute([...array_values($valoresFinales), $idMascota]);

        // echo "Informacion de mascota actualizada correctamente";
        header('Location: ../views/mascotas.php');
        exit;
    }
}

catch (\Throwable $error) {
    // \Throwable detecta Excepciones y Errores

    error_log("Error en actualizar_mascota.php: " . $error -> getMessage()); // esto no se muestra al usuario

    echo "Ocurrio un error al actualizar los datos. Intente de nuevo mas tarde";
}
