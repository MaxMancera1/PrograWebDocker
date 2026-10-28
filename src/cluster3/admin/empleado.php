<?php
// Controlador
include_once __DIR__ . '/models/empleado/empleado.class.php';
$app = new Empleado();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
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