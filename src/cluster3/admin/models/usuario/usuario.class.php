<?php
require_once __DIR__ . '/../sistema.class.php';

class Usuario extends Sistema {

    function create($data){
        $sql = "INSERT INTO usuarios (usuario, password, email) VALUES (:usuario, :password, :email)";
        $stmt = $this->db->prepare($sql);
        $this->bind($stmt, $data);
        $stmt->execute();
        $id = $this->db->lastInsertId();
        $this->syncLogin($data['email'] ?? '', $data['password'] ?? '');
        $this->syncRoles($data['email'] ?? '', $data['roles'] ?? []);
        $afectados = $stmt->rowCount();
    try{
        $sql = "INSERT INTO usuario_permiso (id_usuario, id_permiso) VALUES (:id_usuario, :id_permiso)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id_usuario", $id, PDO::PARAM_INT);
        $stmt->bindValue(":id_permiso", 1, PDO::PARAM_INT);
        $stmt->execute();
        $contenido = 'Bienvenido a Max Industries';
       if(!$this->enviarCorreo([$data['email']], 'Bienvenido a Max Industries', $contenido)){
        return ['Error' => 'No se pudo enviar el correo'];
       }
    }catch (PDOException $e){
        return ['Error' => 'No se pudo enviar el correo'];
    }
        return [
            'afectados' => $afectados,
            'id' => $id,
        ];
    }

    function getOne($id){
        if (!is_numeric($id)) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    function getAll(){
        return $this->db->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);
    }

    function update($id, $data){
        if (!is_numeric($id)) {
            return 0;
        }
        $anterior = $this->getOne($id);
        $sql = "UPDATE usuarios SET usuario = :usuario, password = :password, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $this->bind($stmt, $data);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $ok = $stmt->execute();
        $this->syncLogin($data['email'] ?? '', $data['password'] ?? '');
        $this->syncRoles($data['email'] ?? '', $data['roles'] ?? []);
        if ($ok && ($anterior['email'] ?? '') !== ($data['email'] ?? '')) {
            $this->enviarCorreo([$data['email']], 'Bienvenido a Max Industries', 'Bienvenido a Max Industries');
        }
        return $ok;
    }

    function getPermisos($correo = null){
        if ($correo !== null) {
            return parent::getPermisos($correo);
        }
        return $this->db->query("SELECT id_permiso, permiso FROM permiso ORDER BY permiso")->fetchAll(PDO::FETCH_ASSOC);
    }

    function getRolesAsignados($id){
        $usuario = $this->getOne($id);
        if (!$usuario || empty($usuario['email'])) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT ur.id_rol FROM usuario_rol ur JOIN usuario u ON u.id_usuario = ur.id_usuario WHERE u.correo = :correo");
        $stmt->bindValue(":correo", $usuario['email'], PDO::PARAM_STR);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    function syncRoles($email, $ids){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE correo = :correo");
        $stmt->bindValue(":correo", $email, PDO::PARAM_STR);
        $stmt->execute();
        $idLogin = $stmt->fetchColumn();
        if (!$idLogin) {
            return;
        }
        $stmt = $this->db->prepare("DELETE FROM usuario_rol WHERE id_usuario = :id");
        $stmt->bindValue(":id", $idLogin, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:id_usuario, :id_rol)");
        foreach ($ids as $idRol) {
            if (!is_numeric($idRol)) {
                continue;
            }
            $stmt->bindValue(":id_usuario", $idLogin, PDO::PARAM_INT);
            $stmt->bindValue(":id_rol", $idRol, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    function getPermisosUsuario($id){
        if (!is_numeric($id)) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT id_permiso FROM usuario_permiso WHERE id_usuario = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    function delete($id){
        if (!is_numeric($id)) {
            return 0;
        }
        $stmt = $this->db->prepare("DELETE FROM usuario_permiso WHERE id_usuario = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    function syncPermisos($idUsuario, $ids){
        if (!is_numeric($idUsuario)) {
            return;
        }
        $stmt = $this->db->prepare("DELETE FROM usuario_permiso WHERE id_usuario = :id");
        $stmt->bindValue(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("INSERT INTO usuario_permiso (id_usuario, id_permiso) VALUES (:id_usuario, :id_permiso)");
        foreach ($ids as $idPermiso) {
            if (!is_numeric($idPermiso)) {
                continue;
            }
            $stmt->bindValue(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(":id_permiso", $idPermiso, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    private function syncLogin($email, $password){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            return;
        }
        $hash = md5($password);
        $stmt = $this->db->prepare("SELECT id_usuario FROM usuario WHERE correo = :correo");
        $stmt->bindValue(":correo", $email, PDO::PARAM_STR);
        $stmt->execute();
        $id = $stmt->fetchColumn();
        if ($id) {
            $stmt = $this->db->prepare("UPDATE usuario SET contrasena = :contrasena WHERE id_usuario = :id");
            $stmt->bindValue(":contrasena", $hash, PDO::PARAM_STR);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO usuario (correo, contrasena) VALUES (:correo, :contrasena)");
        $stmt->bindValue(":correo", $email, PDO::PARAM_STR);
        $stmt->bindValue(":contrasena", $hash, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function bind($stmt, $data){
        foreach (['usuario', 'password', 'email'] as $campo) {
            $stmt->bindValue(":$campo", $data[$campo] ?? '', PDO::PARAM_STR);
        }
    }

    function guardarToken($correo, $token){
        try {
            if (filter_var($correo, FILTER_VALIDATE_EMAIL) && strlen($token) == 64) {
                $this->db->exec('ALTER TABLE usuario ADD COLUMN IF NOT EXISTS token VARCHAR(64) NULL');
                $stmt = $this->db->prepare('UPDATE usuario SET token = :token WHERE correo = :correo');
                $stmt->bindValue(':correo', $correo, PDO::PARAM_STR);
                $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    function validarToken($token){
        if (strlen($token) == 64) {
            $sql = 'SELECT id_usuario FROM usuario WHERE token = :token';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    function actualizarContrasena($token, $contrasena){
        $sql = 'SELECT correo FROM usuario WHERE token = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $correo = $stmt->fetchColumn();
        if (!$correo) {
            return false;
        }
        $sql = 'UPDATE usuario SET contrasena = :contrasena, token = NULL WHERE token = :token';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':contrasena', md5($contrasena), PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $stmt = $this->db->prepare('UPDATE usuarios SET password = :password WHERE email = :email');
        $stmt->bindValue(':password', $contrasena, PDO::PARAM_STR);
        $stmt->bindValue(':email', $correo, PDO::PARAM_STR);
        $stmt->execute();
        return true;
    }
}