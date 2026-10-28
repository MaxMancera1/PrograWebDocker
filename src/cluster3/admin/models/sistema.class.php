<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/../vendor/autoload.php';
    class Sistema {
    var $db;
    function __construct() {
        //Constructor de la clase
        //DSN de la base de datos
        $this -> db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);

    }

    function cargarArchivo($nombre, $ruta){
        if (!isset($_FILES[$nombre]) || $_FILES[$nombre]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $archivo = $_FILES[$nombre];
        $nombreArchivo = basename($archivo['name']);
        $tipos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $ext = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        if (!in_array($archivo['type'], $tipos, true) || !in_array($ext, $extensions, true)) {
            return null;
        }
        $destinoDir = __DIR__ . '/' . $ruta;
        if (!is_dir($destinoDir)) {
            mkdir($destinoDir, 0777, true);
        }
        if ($nombreArchivo === '' || !move_uploaded_file($archivo['tmp_name'], $destinoDir . $nombreArchivo)) {
            return null;
        }
        return $nombreArchivo;
    }

    function login($correo, $contrasena){
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            return false;
        }
        $contrasena = md5($contrasena);
        $sql = "SELECT * FROM usuario WHERE correo = :correo AND contrasena = :contrasena";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        if($usuario){
            $_SESSION['correo'] = $correo;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['valido'] = true;
            try {
                $_SESSION['roles'] = $this->getRoles($correo);
            } catch (PDOException $e) {
                $_SESSION['roles'] = [];
            }
            return true;
        }
        $this->logout();
        return false;
    }

    function getRoles($correo){
        $sql = "Select r.rol From rol r
        JOIN usuario_rol ur ON r.id_rol = ur.id_rol
        JOIN usuario u ON ur.id_usuario = u.id_usuario
        WHERE u.correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($roles as $key => $value){
            $roles[$key] = $value['rol'];
        }
        return $roles;
    }

    function getPermisos($correo){
        $sql = "Select p.permiso From permiso p
        JOIN rol_permiso rp ON p.id_permiso = rp.id_permiso
        JOIN rol r ON rp.id_rol = r.id_rol
        JOIN usuario_rol ur ON r.id_rol = ur.id_rol
        JOIN usuario u ON ur.id_usuario = u.id_usuario
        WHERE u.correo = :correo";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
        $stmt->execute();
        $permisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach($permisos as $key => $value){
            $permisos[$key] = $value['permiso'];
        }
        return $permisos;
    }


    function logout(){
        unset($_SESSION['correo']);
        unset($_SESSION['id_usuario']);
        unset($_SESSION['valido']);
        unset($_SESSION['roles']);
        unset($_SESSION['permisos']);   
        session_destroy();
        return true;
    }

    function checarRol($rol){
    if(isset($_SESSION['roles']) && in_array($rol, $_SESSION['roles'])){
        return true;
    }
        $alerta['tipo'] = 'danger';
        $alerta['mensaje'] = 'No tienes permisos para acceder a esta página';
        $this->logout();
        require_once __DIR__ . '/../templates/login/login.html';
        exit;
    }

    function enviarCorreo($destinatarios, $asunto, $contenido, $adjunto = null){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->Host = SMTP_HOST;
        $mail->Port = SMTP_PORT;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        foreach($destinatarios as $destinatario){
            $mail->addAddress($destinatario);
        }
        $mail->setFrom(SMTP_FROM, 'Max Industries');
        $mail->Subject = $asunto;
        $mail->Body = $contenido;
        if(!$mail->send()){
            return false;
        }
        return true;

    }


}


