<?php

require_once __DIR__ . '/models/usuario/usuario.class.php';
include_once __DIR__ . '/models/sistema.class.php';
$app = new Sistema();
$appUsuario = new Usuario();
$accion = $_GET['accion'] ?? 'login';
switch ($accion) {
case 'validar':
    if (isset($_POST['enviar'])) {
        $correo = $_POST['correo'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';
        if ($app->login($correo, $contrasena)) {
            header('Location: index.php');
            exit;
        }
        $alerta['tipo'] = 'danger';
        $alerta['mensaje'] = 'Correo o contraseña incorrectos';
    }
    require_once __DIR__ . '/templates/login/login.html';
    break;
case 'login':
    require_once __DIR__ . '/templates/login/login.html';
    break;

case 'logout':
    $app->logout();
    $alerta['tipo'] = 'success';
    $alerta['mensaje'] = 'Sesión cerrada correctamente';
    require_once __DIR__ . '/templates/login/login.html';
    break;
case 'recuperar_contrasena':
    $formulario = __DIR__ . '/templates/login/_form.html';
    require_once __DIR__ . '/templates/login/login.html';
    break;
case 'enviar_correo':
    if (isset($_POST['enviar'])) {
        $correo = $_POST['correo'] ?? '';
        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $token = md5(bin2hex(random_bytes(16)) . time()) . md5('Cruz Azul Campeon' . $correo);
            $url = 'http://' . $_SERVER['HTTP_HOST'] . '/cluster3/admin/login.php?accion=nueva&token=' . $token;
            $contenido = "Hola, has solicitado recuperar tu contraseña. Entra a este enlace para elegir una nueva:\n" . $url;
            if ($appUsuario->guardarToken($correo, $token)) {
                if ($appUsuario->enviarCorreo([$correo], 'Recuperación de contraseña', $contenido)) {
                    $alerta['tipo'] = 'success';
                    $alerta['mensaje'] = 'Se ha enviado un correo para recuperar tu contraseña';
                } else {
                    $alerta['tipo'] = 'danger';
                    $alerta['mensaje'] = 'No se pudo enviar el correo';
                }
            } else {
                $alerta['tipo'] = 'danger';
                $alerta['mensaje'] = 'Error al guardar el token';
            }
        } else {
            $alerta['tipo'] = 'danger';
            $alerta['mensaje'] = 'Correo no válido';
        }
    }
    $formulario = __DIR__ . '/templates/login/_form.html';
    require_once __DIR__ . '/templates/login/login.html';
    break;
case 'nueva':
    $token = $_GET['token'] ?? '';
    if (strlen($token) == 64 && $appUsuario->validarToken($token)) {
        $formulario = __DIR__ . '/templates/login/_form_password.html';
        require_once __DIR__ . '/templates/login/login.html';
        break;
    }
    $alerta['tipo'] = 'danger';
    $alerta['mensaje'] = 'Token invalido, genera un nuevo token';
    $formulario = __DIR__ . '/templates/login/_form.html';
    require_once __DIR__ . '/templates/login/login.html';
    break;
case 'restaurar':
    $token = $_POST['token'] ?? ($_GET['token'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $contrasena2 = $_POST['contrasena2'] ?? '';
    if (isset($_POST['enviar']) && $contrasena !== '' && $contrasena === $contrasena2 && strlen($token) == 64 && $appUsuario->validarToken($token) && $appUsuario->actualizarContrasena($token, $contrasena)) {
        $alerta['tipo'] = 'success';
        $alerta['mensaje'] = 'Contraseña actualizada';
        require_once __DIR__ . '/templates/login/login.html';
        break;
    }
    $alerta['tipo'] = 'danger';
    $alerta['mensaje'] = 'No se pudo actualizar la contraseña';
    $formulario = __DIR__ . '/templates/login/_form_password.html';
    require_once __DIR__ . '/templates/login/login.html';
    break;
}

