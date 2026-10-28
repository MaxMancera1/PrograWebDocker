<?php
require_once __DIR__ . '/../sistema.class.php';

class Permiso extends Sistema {

    function create($data){
        $stmt = $this->db->prepare("INSERT INTO permiso (permiso) VALUES (:permiso)");
        $stmt->bindValue(":permiso", $data['permiso'] ?? '', PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    function getAll(){
        return $this->db->query("SELECT * FROM permiso ORDER BY permiso")->fetchAll(PDO::FETCH_ASSOC);
    }

    function getOne($id){
        if (!is_numeric($id)) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT * FROM permiso WHERE id_permiso = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    function update($id, $data){
        if (!is_numeric($id)) {
            return 0;
        }
        $stmt = $this->db->prepare("UPDATE permiso SET permiso = :permiso WHERE id_permiso = :id");
        $stmt->bindValue(":permiso", $data['permiso'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    function delete($id){
        if (!is_numeric($id)) {
            return 0;
        }
        $stmt = $this->db->prepare("DELETE FROM usuario_permiso WHERE id_permiso = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $this->db->prepare("DELETE FROM permiso WHERE id_permiso = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
