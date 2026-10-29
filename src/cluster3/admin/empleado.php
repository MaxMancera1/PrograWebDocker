<?php
// Controlador
include_once __DIR__ . '/models/empleado/empleado.class.php';
$app = new Empleado();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
if ($accion === 'reporte') {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="reporte-empleados.pdf"');
    echo $app->reportePdf();
    exit;
}
if ($accion === 'credencial') {
    $pdf = $app->credencialPdf($id);
    if ($pdf === null) {
        http_response_code(404);
        exit;
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="credencial-' . (int) $id . '.pdf"');
    echo $pdf;
    exit;
}
if ($accion === 'foto') {
    $foto = $app->getFoto($id);
    if ($foto === null || $foto === '') {
        http_response_code(404);
        exit;
    }
    header('Content-Type: image/jpeg');
    echo $foto;
    exit;
}
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create': 
        if (isset($_POST['enviar'])) {
            $datos = $app->create($_POST);
            if (isset($datos['error'])) {
                $error = $datos['error'];
            } else {
                $success = $datos['success'];
                
            }
            $empleados = $app->getAll();
            require_once __DIR__ . '/templates/empleado/index.html';
        }else{

            require_once __DIR__ . '/templates/empleado/_form.html';
        }
       
        break;
    
    case 'read':
        default:
        $empleados = $app->getAll();
        require_once __DIR__ . '/templates/empleado/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';