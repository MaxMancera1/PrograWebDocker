<?php
include_once __DIR__ . '/models/usuario/usuario.class.php';
include_once __DIR__ . '/models/rol/rol.class.php';
$app = new Usuario();
$rolesApp = new Rol();
$app->checarRol('Administrador');
$accion = $_GET['accion'] ?? 'read';
$id = $_GET['id'] ?? null;
$datos = [];
$rolesUsuario = [];
include_once __DIR__ . '/templates/admin/header.html';
switch ($accion) {
    case 'create':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $data['roles'] = $_POST['roles'] ?? [];
            $app->create($data);
            $usuarios = $app->getAll();
            include_once __DIR__ . '/templates/usuario/index.html';
        } else {
            $roles = $rolesApp->getAll();
            include_once __DIR__ . '/templates/usuario/_form.html';
        }
        break;
    case 'update':
        if (isset($_POST['enviar'])) {
            $data = $_POST['data'];
            $data['roles'] = $_POST['roles'] ?? [];
            $app->update($id, $data);
            $usuarios = $app->getAll();
            include_once __DIR__ . '/templates/usuario/index.html';
        } else {
            $datos = $app->getOne($id);
            $roles = $rolesApp->getAll();
            $rolesUsuario = $app->getRolesAsignados($id);
            include_once __DIR__ . '/templates/usuario/_form.html';
        }
        break;
    case 'delete':
        $app->delete($id);
        $usuarios = $app->getAll();
        include_once __DIR__ . '/templates/usuario/index.html';
        break;
    default:
        $usuarios = $app->getAll();
        include_once __DIR__ . '/templates/usuario/index.html';
}
include_once __DIR__ . '/templates/admin/footer.html';
