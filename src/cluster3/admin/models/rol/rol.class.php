<?php
require_once __DIR__ . '/../sistema.class.php';

class Rol extends Sistema {

    function create($data){
        $stmt = $this->db->prepare("INSERT INTO rol (rol) VALUES (:rol)");
        $stmt->bindValue(":rol", $data['rol'] ?? '', PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    function getAll(){
        return $this->db->query("SELECT * FROM rol ORDER BY rol")->fetchAll(PDO::FETCH_ASSOC);
    }

    function getOne($id){
        if (!is_numeric($id)) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT * FROM rol WHERE id_rol = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    function update($id, $data){
        if (!is_numeric($id)) {
            return 0;
        }
        $stmt = $this->db->prepare("UPDATE rol SET rol = :rol WHERE id_rol = :id");
        $stmt->bindValue(":rol", $data['rol'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    function delete($id){
        if (!is_numeric($id)) {
            return 0;
        }
        $stmt = $this->db->prepare("DELETE FROM usuario_rol WHERE id_rol = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("DELETE FROM rol WHERE id_rol = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
