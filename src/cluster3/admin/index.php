<?php
include_once __DIR__ . '/models/sistema.class.php';
include_once __DIR__ . '/models/proveedor/proveedor.class.php';
$app = new Sistema();
$app->checarRol('Administrador', 'Empleado');
$dataProveedores = (new Proveedor())->getProveedoresProdcutos();

$filas = array_map(fn($p) => [$p['razon_social'], (int)$p['cantidad']], $dataProveedores);
usort($filas, fn($a, $b) => $b[1] <=> $a[1]);

$total    = array_sum(array_column($filas, 1));
$numProv  = count($filas);
$promedio = $numProv ? round($total / $numProv, 1) : 0;
$lider    = $filas[0] ?? ['—', 0];
$pctLider = $total ? round($lider[1] * 100 / $total) : 0;
$bajoProm = count(array_filter($filas, fn($f) => $f[1] < $promedio));

$dona  = array_slice($filas, 0, 5);
$otros = array_sum(array_column(array_slice($filas, 5), 1));
if ($otros > 0) { $dona[] = ['Otros', $otros]; }
$coloresDona = ['#FFC93C', '#FFFFFF', '#A8B8FF', '#7B93FF', '#D5DCFF', 'rgba(255,255,255,.28)'];

include_once __DIR__ . '/templates/admin/header.html';
include_once __DIR__ . '/templates/index.html';
include_once __DIR__ . '/templates/admin/footer.html';
