<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
class Bateria_model extends CI_Model { 
 
    public function buscar_compatibles($marca, $modelo, $anio) { 
        $sql = "CALL buscar_baterias_avanzado(?, ?, ?, NULL)"; 
        $query = $this->db->query($sql, array($marca, $modelo, $anio)); 
        return $query->result_array(); 
    } 
 
    public function obtener_todas() { 
        $this->db->where('disponible', TRUE); 
        return $this->db->get('baterias_lth')->result_array(); 
    } 
 
} 
