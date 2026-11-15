<?php

require_once __DIR__ . '/../models/Productos.php';

function limitePedido(array $producto): array {
    $existencia = (int) $producto['existencia'];
    $exist_minima = (int) $producto['existencia_minima'];
    $exist_maxima = (int) $producto['existencia_maxima'];

    $pedidoMinimo = 1;
    if ($existencia <= $exist_minima) {
        $pedidoMinimo = $exist_minima - $existencia + 1;
    }
    $pedidoMaximo = $exist_maxima > 0 ? max($exist_maxima - $existencia, 0) : PHP_INT_MAX;

    return ['minimo' => $pedidoMinimo, 'maximo' => $pedidoMaximo];
}

function validarCantidadesOrdenCompra(array $productos, array $cantidades): array {
    $errores = [];

    foreach ($productos as $id_prod => $p) {
        $actual = obtenerProductoPorId((int) $id_prod);
        if ($actual === null) {
            continue;
        }

        $cantidad = (int) ($cantidades[$id_prod] ?? 1);
        $existencia = (int) $actual['existencia'];
        $maxima = (int) $actual['existencia_maxima'];
        $minima = (int) $actual['existencia_minima'];
        $nombreProducto = $actual['nombre'];
        $limite = limitePedido($actual);
        $limiteMaximo = $limite['maximo'];

        if ($cantidad > $limite['maximo']) {
            if ($limite === 0) {
                $errores[$id_prod] = "El producto {$nombreProducto}, ya está en su existencia máxima ({$maxima}); no se puede pedir más.";
            } else {
                $errores[$id_prod] = "No se puede pedir {$cantidad} del producto {$nombreProducto}: tiene {$existencia} en existencia y el máximo es {$maxima}, así que solo puedes pedir hasta {$limiteMaximo}.";
            }
        }
    }

    return $errores;
}

?>
