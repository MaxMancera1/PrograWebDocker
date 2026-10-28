<?php
// Controlador
include_once __DIR__ . '/models/proveedor/proveedor.class.php';
$app = new Proveedor();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset ($_POST['enviar'])) {
           $data =$_POST['data'];
           $datos = $app->create($data);
           $proveedores = $app->getAll();
           include_once __DIR__ . '/templates/proveedor/index.html';
        }else{
            include_once __DIR__ . '/templates/proveedor/_form.html';
        } 
        break;
    case 'update':
        if (isset ($_POST['enviar'])) {
            $data =$_POST['data'];
            $datos = $app->update($id,$data);
            $proveedores = $app->getAll();
            include_once __DIR__ . '/templates/proveedor/index.html';
         }else{
            $datos = $app->getOne($id);
            include_once __DIR__ . '/templates/proveedor/_form.html';
         } 
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $proveedores = $app->getAll();
        include_once __DIR__ . '/templates/proveedor/index.html';
        break;
    default:
        $proveedores = $app->getAll();
        include_once __DIR__ . '/templates/proveedor/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';
