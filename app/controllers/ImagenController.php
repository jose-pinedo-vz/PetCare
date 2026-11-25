<?php

class ImagenController
{
    private static string $directorioBase = __DIR__ . '/../../uploads/mascotas/';

    private static array $tiposPermitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif'
    ];

    private static int $tamanioMaximo = 10485760/2; // 5 MB 

    public static function subir(?array $archivo, string $subdirectorio = 'mascotas', string $prefijo = 'pet_'): array
    {
        if (!$archivo || empty($archivo['name']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'exito' => false,
                'mensaje' => 'No se seleccionó ningún archivo de imagen.',
                'ruta' => null,
                'nombre_archivo' => null
            ];
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return [
                'exito' => false,
                'mensaje' => 'Error al transferir el archivo (código: ' . $archivo['error'] . ').',
                'ruta' => null,
                'nombre_archivo' => null
            ];
        }

        if ($archivo['size'] > self::$tamanioMaximo) {
            return [
                'exito' => false,
                'mensaje' => 'La imagen excede el tamaño máximo permitido (5 MB).',
                'ruta' => null,
                'nombre_archivo' => null
            ];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($tipoMime, self::$tiposPermitidos)) {
            return [
                'exito' => false,
                'mensaje' => 'Formato no permitido. Solo se aceptan imágenes JPG, PNG, WEBP o GIF.',
                'ruta' => null,
                'nombre_archivo' => null
            ];
        }

        $extension = self::$tiposPermitidos[$tipoMime];

        $subLimpio = trim(str_replace(['..', '\\'], ['', '/'], $subdirectorio), '/');
        $directorioDestino = __DIR__ . '/../../uploads/' . $subLimpio . '/';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        $nombreUnico = uniqid($prefijo, true) . '.' . $extension;
        $rutaDestinoFisica = $directorioDestino . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestinoFisica)) {
            return [
                'exito' => false,
                'mensaje' => 'No se pudo guardar la imagen en el servidor (permisos de carpeta).',
                'ruta' => null,
                'nombre_archivo' => null
            ];
        }

        $rutaRelativaBD = 'uploads/' . $subLimpio . '/' . $nombreUnico;

        return [
            'exito' => true,
            'mensaje' => 'se subio la imagen correctamente.',
            'ruta' => $rutaRelativaBD,
            'nombre_archivo' => $nombreUnico
        ];
    }

    public static function normalizarArchivosMultiples(?array $filesArray): array
    {
        $normalizados = [];
        if (!$filesArray || !isset($filesArray['name'])) {
            return $normalizados;
        }

        if (is_array($filesArray['name'])) {
            $total = count($filesArray['name']);
            for ($i = 0; $i < $total; $i++) {
                if (($filesArray['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $normalizados[] = [
                    'name'     => $filesArray['name'][$i],
                    'type'     => $filesArray['type'][$i] ?? '',
                    'tmp_name' => $filesArray['tmp_name'][$i],
                    'error'    => $filesArray['error'][$i],
                    'size'     => $filesArray['size'][$i] ?? 0,
                ];
            }
        } elseif (($filesArray['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $normalizados[] = $filesArray;
        }

        return $normalizados;
    }
}
