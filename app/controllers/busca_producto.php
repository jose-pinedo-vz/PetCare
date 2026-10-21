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
    if ($_GET['accion']==='agregar')
    {
        $codigo=$_GET['codigo'] ?? '';
        $nombre=$_GET['nombre'] ?? '';
        $precio=(float)($_GET['precio'] ?? 0);
        $existencia=(int)$_GET['existencia'] ?? 0;

        // Verificar si el producto ya está en el carrito para sumar cantidad
        $encontrado=false;
        foreach ($_SESSION['carrito'] as &$item)
        {
            if ($item['codigo']===$codigo)
            {
                $item['cantidad']++;
                $encontrado=true;
                break;
            }
        }
        
        // Si no existe, lo agregamos como nuevo
        if (!$encontrado)
        {
            $_SESSION['carrito'][]=[
                'codigo'=>$codigo,
                'nombre'=>$nombre,
                'precio'=>$precio,
                'cantidad'=>1,
                'descuento'=>0,
                'existencia'=>$existencia
            ];
        }
        header("Location: ../views/Venta.php");
        exit();
    }

    if ($_GET['accion']==='eliminar' && isset($_GET['index']))
    {
        $index=$_GET['index'];
        if (isset($_SESSION['carrito'][$index]))
        {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito']=array_values($_SESSION['carrito']);
        }
        header("Location: ../views/Venta.php");
        exit();
    }
    if (isset($_GET['accion']) && $_GET['accion']==='actualizar')
    {
        if (isset($_POST['cantidades']) && is_array($_POST['cantidades']))
        {
            foreach ($_POST['cantidades'] as $index => $nuevaCantidad)
            {
                if (isset($_SESSION['carrito'][$index]))
                {
                    $stockDisponible=$_SESSION['carrito'][$index]['existencia'];
                    $cant=(int)$nuevaCantidad;
                    if ($cant<1)
                    {
                        $cant=1;
                    }
                    elseif($cant>$stockDisponible)
                    {
                        $cant=$stockDisponible;
                    }
                    $_SESSION['carrito'][$index]['cantidad']=$cant;
                }
            }
        }
        if (isset($_POST['descuentos']) && is_array($_POST['descuentos']))
        {
            foreach ($_POST['descuentos'] as $index => $nuevoDescuento)
            {
                if (isset($_SESSION['carrito'][$index]))
                {
                    $desc=(float)$nuevoDescuento;
                    if ($desc<0)
                    {
                        $desc=0;
                    }
                    elseif ($desc>100)
                    {
                        $desc=100;
                    }
                    $_SESSION['carrito'][$index]['descuento']=$desc;
                }
            }
        }
            header("Location: ../views/Venta.php");
            exit();
    }
    if (isset($_GET['accion']) && $_GET['accion']==='cancelar')
    {
        $_SESSION['carrito']=[];
        header("Location: ../views/Venta.php");
        exit();
    }
}

?>