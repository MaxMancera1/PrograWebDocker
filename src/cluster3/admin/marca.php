<?php
// Controlador
include_once __DIR__ . '/models/marca/marca.class.php';
include_once __DIR__ . '/models/proveedor/proveedor.class.php';
$app = new Marca();
$app->checarRol('Administrador');
$proveedoresApp = new Proveedor();
$proveedores = $proveedoresApp->getAll();
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset ($_POST['enviar'])) {
           $data =$_POST['data'];
           $datos = $app->create($data);
           if (isset($datos['error'])) {
               include_once __DIR__ . '/templates/marca/_form.html';
           } else {
               $marcas = $app->getAll();
               include_once __DIR__ . '/templates/marca/index.html';
           }
        }else{
            include_once __DIR__ . '/templates/marca/_form.html';
        } 
        break;
    case 'update':
        if (isset ($_POST['enviar'])) {
            $data =$_POST['data'];
            $datos = $app->update($id,$data);
            if (is_array($datos) && isset($datos['error'])) {
                include_once __DIR__ . '/templates/marca/_form.html';
            } else {
                $marcas = $app->getAll();
                include_once __DIR__ . '/templates/marca/index.html';
            }
         }else{
            $datos = $app->getOne($id);
            include_once __DIR__ . '/templates/marca/_form.html';
         } 
        break;
    case 'delete':
        $afectados = $app->delete($id);
        $marcas = $app->getAll();
        include_once __DIR__ . '/templates/marca/index.html';
        break;
    default:
        $marcas = $app->getAll();
        include_once __DIR__ . '/templates/marca/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';
