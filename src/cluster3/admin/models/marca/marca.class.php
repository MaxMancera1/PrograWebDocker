<?php
// Modelo
require_once __DIR__ . '/../sistema.class.php';

class Marca extends Sistema {   
    function updateLogo($id){

    $datos = [];
        $datos['logo'] = $this->cargarArchivo('logo', '../../img/uploads/');
        if (is_null($datos['logo'])) {
            return ['error' => 'Logo no encontrado'];
        }
        $sql = "UPDATE marcas SET logo = :logo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":logo", $datos['logo'], PDO::PARAM_STR);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $id;
        return $datos;

    }
        
    function create($data){
        $datos = [];
        $datos['logo'] = $this->cargarArchivo('logo', '../../img/uploads/');
        if (is_null($datos['logo'])) {
            return ['error' => 'Logo no encontrado'];
        }
        $sql = "INSERT INTO marcas (marca, logo, id_proveedor)
                VALUES (:marca, :logo, :id_proveedor)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":marca", $data['marca'], PDO::PARAM_STR);
        $stmt->bindParam(":logo", $datos['logo'], PDO::PARAM_STR);
        $idProveedor = ($data['id_proveedor'] ?? '') === '' ? null : $data['id_proveedor'];
        $stmt->bindValue(":id_proveedor", $idProveedor, $idProveedor === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->execute();
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;
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
        $sql = "SELECT marcas.*, proveedores.razon_social AS proveedor
                FROM marcas
                LEFT JOIN proveedores ON marcas.id_proveedor = proveedores.id";
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
