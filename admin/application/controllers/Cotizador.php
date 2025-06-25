<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
 
class Cotizador extends CI_Controller { 
 
    public function __construct() { 
        parent::__construct(); 
        $this->load->database(); 
        $this->load->model('Bateria_model'); 
        $this->load->helper('url'); 
    } 
 
    public function index() { 
        $data['title'] = 'Cotizador Acumuladores Yepez'; 
        $this->load->view('cotizador/busqueda', $data); 
    } 
 
    public function buscar() { 
        $marca = $this->input->post('marca'); 
        $modelo = $this->input->post('modelo'); 
        $anio = $this->input->post('anio'); 
 
        $baterias = $this->Bateria_model->buscar_compatibles($marca, $modelo, $anio); 
        echo json_encode($baterias); 
    } 
 
} 
