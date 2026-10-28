<?php
// Modelo
require_once __DIR__ . '/../sistema.class.php';

class Empleado extends Sistema {   
    function updateDatos($id){
    $datos = [];
    $datos['primer_apellido'] = $this->cargarArchivo('primer_apellido', '../../img/uploads/');
    if (is_null($datos['primer_apellido'])) {
        return ['error' => 'Primer Apellido no encontrado'];
    }
    $datos['segundo_apellido'] = $this->cargarArchivo('segundo_apellido', '../../img/uploads/');
    if (is_null($datos['segundo_apellido'])) {
        return ['error' => 'Segundo Apellido no encontrado'];
    }   
    $datos['rfc'] = $this->cargarArchivo('rfc', '../../img/uploads/');
    if (is_null($datos['rfc'])) {
        return ['error' => 'RFC no encontrado'];
    }
    $datos['correo'] = $this->cargarArchivo('correo', '../../img/uploads/');
    if (is_null($datos['correo'])) {
        return ['error' => 'Correo no encontrado'];
    }
    $datos['contraseña'] = $this->cargarArchivo('contraseña', '../../img/uploads/');
    if (is_null($datos['contraseña'])) {
        return ['error' => 'Contraseña no encontrado'];
    }
    $datos = [];
        $sql = "UPDATE empleados SET primer_apellido = :primer_apellido, segundo_apellido = :segundo_apellido WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam("nombre", $datos['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(":primer_apellido", $datos['primer_apellido'], PDO::PARAM_STR);
        $stmt->bindParam(":segundo_apellido", $datos['segundo_apellido'], PDO::PARAM_STR);
        $stmt->bindParam(":rfc", $datos['rfc'], PDO::PARAM_STR);
        $stmt->bindParam(":correo", $datos['correo'], PDO::PARAM_STR);
        $stmt->bindParam(":contraseña", $datos['contraseña'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $id;
        return $datos;

    }
        
    function create($data){
        $datos = [];
        
        $this->db->beginTransaction(); 
        try  {
            $sql = "INSERT INTO usuario (correo, contrasena) VALUES(:correo, :contrasena) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":correo", $data['correo'], PDO::PARAM_STR);
            $contrasena = md5($data['contrasena']);
            $stmt->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);
            $stmt->execute();
           

            $sql = "SELECT id_usuario FROM usuario WHERE correo = :correo";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":correo", $data['correo'], PDO::PARAM_STR);
            $stmt->execute();
            $idUsuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $idUsuario = $idUsuario['id_usuario'];
            

            $sql = "INSERT INTO usuario_rol (id_usuario, id_rol) VALUES(:id_usuario, :id_rol) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $idRol = 2;
            $stmt->bindParam(":id_rol", $idRol, PDO::PARAM_INT);
            $stmt->execute();
         

            $sql = "INSERT INTO empleado (id_usuario, nombre, primer_apellido, segundo_apellido, rfc) VALUES(:id_usuario, :nombre, :primer_apellido, :segundo_apellido, :rfc) ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_usuario", $idUsuario, PDO::PARAM_INT);
            $stmt->bindParam(":nombre", $data['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(":primer_apellido", $data['primer_apellido'], PDO::PARAM_STR);
            $stmt->bindParam(":segundo_apellido", $data['segundo_apellido'], PDO::PARAM_STR);
            $stmt->bindParam(":rfc", $data['rfc'], PDO::PARAM_STR);
            $stmt->execute();
          

            $this->db->commit();
            return ['success' => 'Empleado creado correctamente'];
        }
        catch (PDOException $e) {
            $this->db->rollBack();
            return ['error' => 'Error al crear el empleado'];
        }
        return ['error' => 'Error desconocido'];
    }
    
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM marcas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT empleado.*, usuario.correo AS correo
                FROM empleado
                LEFT JOIN usuario ON empleado.id_usuario = usuario.id_usuario";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $logo = $this->cargarArchivo('logo', '../../img/uploads/');
            $intentoLogo = isset($_FILES['logo']) && $_FILES['logo']['error'] !== UPLOAD_ERR_NO_FILE;
            if ($intentoLogo && is_null($logo)) {
                $actual = $this->getOne($id);
                $actual['error'] = $_FILES['logo']['error'] === UPLOAD_ERR_INI_SIZE
                    ? 'La imagen es demasiado grande'
                    : 'No se pudo guardar la imagen';
                return $actual;
            }
            if (is_null($logo)) {
                $actual = $this->getOne($id);
                $logo = $actual['logo'] ?? '';
            }
            $sql = "UPDATE marcas SET marca = :marca, logo = :logo, id_proveedor = :id_proveedor WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":marca", $data['marca'], PDO::PARAM_STR);
            $stmt->bindParam(":logo", $logo, PDO::PARAM_STR);
            $idProveedor = ($data['id_proveedor'] ?? '') === '' ? null : $data['id_proveedor'];
            $stmt->bindValue(":id_proveedor", $idProveedor, $idProveedor === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;

    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM marcas WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
