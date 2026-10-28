<?php
// Modelo
require_once __DIR__ . '/../sistema.class.php';

class Producto extends Sistema {
    
    function create($data){
        $datos = [];
        $datos['foto'] = $this->cargarArchivo('foto', '../../img/uploads/');
        if (is_null($datos['foto'])) {
            return ['error' => 'Foto no encontrada'];
        }
        $sql = "INSERT INTO productos (producto, foto, precio, id_marca)
                VALUES (:producto, :foto, :precio, :id_marca)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
        $stmt->bindParam(":foto", $datos['foto'], PDO::PARAM_STR);
        $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
        $stmt->bindParam(":id_marca", $data['id_marca'], PDO::PARAM_INT);
        $stmt->execute();
        $datos = $datos;
        $datos['afectados'] = $stmt->rowCount();
        $datos['id'] = $this->db->lastInsertId();
        return $datos;

    }
    function getOne($id){
        $datos = [];
        if(is_numeric($id)){
            $sql = "SELECT * FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $datos;
    }
    function getAll(){
        $datos = [];
        $sql = "SELECT productos.*, marcas.marca
                FROM productos
                LEFT JOIN marcas ON productos.id_marca = marcas.id";
        $stmt = $this->db->query($sql);
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $datos;
    }
    function update($id, $data){
        $afectados = 0;
        if (is_numeric($id)){
            $foto = $this->cargarArchivo('foto', '../../img/uploads/');
            $intentoFoto = isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;
            if ($intentoFoto && is_null($foto)) {
                $actual = $this->getOne($id);
                $actual['error'] = $_FILES['foto']['error'] === UPLOAD_ERR_INI_SIZE
                    ? 'La imagen es demasiado grande'
                    : 'No se pudo guardar la imagen';
                return $actual;
            }
            if (is_null($foto)) {
                $actual = $this->getOne($id);
                $foto = $actual['foto'] ?? '';
            }
            $sql = "UPDATE productos SET producto = :producto, foto = :foto, precio = :precio, id_marca = :id_marca WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":producto", $data['producto'], PDO::PARAM_STR);
            $stmt->bindParam(":foto", $foto, PDO::PARAM_STR);
            $stmt->bindParam(":precio", $data['precio'], PDO::PARAM_STR);
            $stmt->bindParam(":id_marca", $data['id_marca'], PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;

    }
    function delete($id){
        $afectados = 0;
        if (is_numeric($id)){
            $sql = "DELETE FROM productos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $afectados = $stmt->execute();
        }
        return $afectados;
    }
}

?>
