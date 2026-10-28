<?php
include_once __DIR__ . '/models/rol/rol.class.php';
$app = new Rol();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
$datos = [];
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $app->create($_POST['data']);
            $roles = $app->getAll();
            include_once __DIR__ . '/templates/rol/index.html';
        } else {
            include_once __DIR__ . '/templates/rol/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $app->update($id, $_POST['data']);
            $roles = $app->getAll();
            include_once __DIR__ . '/templates/rol/index.html';
        } else {
            $datos = $app->getOne($id);
            include_once __DIR__ . '/templates/rol/_form.html';
        }
        break;
    case 'delete':
        $app->delete($id);
        $roles = $app->getAll();
        include_once __DIR__ . '/templates/rol/index.html';
        break;
    default:
        $roles = $app->getAll();
        include_once __DIR__ . '/templates/rol/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';
