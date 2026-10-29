<?php
if (session_status()===PHP_SESSION_NONE)
{
    session_start();
}
if (!isset($_SESSION['carrito']))
{
    $_SESSION['carrito']=[];
}
// Procesar acciones (agregar o eliminar)
if (isset($_GET['accion']))
{
    if (isset($_GET['accion']) && $_GET['accion']==='agregar_servicio')
    {
        $codigo=$_GET['codigo'] ?? '';
        $nombre=$_GET['nombre'] ?? '';
        $precio=(float)($_GET['precio'] ?? 0);
        $encontrado=false;
        foreach ($_SESSION['carrito'] as &$item)
        {
            if ($item['codigo']===$codigo && isset($item['es_servicio']))
            {
                $item['cantidad']++;
                $encontrado=true;
                break;
            }
        }
        
        if (!$encontrado)
        {
            $_SESSION['carrito'][]=[
                'codigo' => $codigo,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1,
                'descuento' => 0,
                'existencia' => 9999, 
                'es_servicio' => true  
            ];
        }
        
        header("Location: ../views/Venta.php");
        exit();
    }
}

?>