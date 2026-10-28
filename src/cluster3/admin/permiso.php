<?php
include_once __DIR__ . '/models/Permisos/permisos.class.php';
$app = new Permiso();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
$datos = [];
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $app->create($_POST['data']);
            $permisos = $app->getAll();
            include_once __DIR__ . '/templates/permiso/index.html';
        } else {
            include_once __DIR__ . '/templates/permiso/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $app->update($id, $_POST['data']);
            $permisos = $app->getAll();
            include_once __DIR__ . '/templates/permiso/index.html';
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/templates/permiso/_form.html';
        }
        break;
    case 'delete':
        $app->delete($id);
        $permisos = $app->getAll();
        include_once __DIR__ . '/templates/permiso/index.html';
        break;
    default:
        $permisos = $app->getAll();
        include_once __DIR__ . '/templates/permiso/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';
