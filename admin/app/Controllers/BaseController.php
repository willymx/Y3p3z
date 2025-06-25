<?php

namespace App\Controllers;

class BaseController 
{
    protected $request;
    protected $response;
    protected $db;
    
    public function __construct() 
    {
        $this->request = new \Request();
        $this->response = new \Response();
        
        // Conectar a base de datos
        require_once APPPATH . 'Config/Database.php';
        $this->db = getDatabase();
    }
    
    protected function view($viewFile, $data = []) 
    {
        // Extraer variables para la vista
        extract($data);
        
        // Buscar archivo de vista
        $viewPath = APPPATH . 'Views/' . $viewFile . '.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            // Vista no encontrada, mostrar cotizador básico
            $this->showBasicCotizador($data);
        }
    }
    
    private function showBasicCotizador($data = []) 
    {
        $title = $data['title'] ?? 'Cotizador Yépez';
        $fecha = $data['fecha'] ?? date('d/m/Y');
        $sucursales = $data['sucursales'] ?? ['Matriz Centro', 'Sucursal Norte', 'Sucursal Sur'];
        
        echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>$title</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css' rel='stylesheet'>
    <style>
        .cotizador-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; }
        .cotizador-card { background: white; border-radius: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
        .header-logo { background: #2c3e50; color: white; padding: 20px; border-radius: 15px 15px 0 0; text-align: center; }
        .resultado-item { border: 1px solid #e9ecef; border-radius: 8px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s; }
        .resultado-item:hover { border-color: #007bff; background-color: #f8f9fa; }
        .precio-badge { font-size: 1.2em; font-weight: bold; }
    </style>
</head>
<body>
    <div class='cotizador-container'>
        <div class='container'>
            <div class='row justify-content-center'>
                <div class='col-md-10'>
                    <div class='cotizador-card'>
                        <div class='header-logo'>
                            <h1><i class='fas fa-car-battery'></i> Acumuladores Yépez</h1>
                            <p class='mb-0'>Sistema de Cotización - $fecha</p>
                        </div>
                        
                        <div class='card-body p-4'>
                            <div class='alert alert-success'>
                                <h4><i class='fas fa-check-circle'></i> ¡Sistema Funcionando!</h4>
                                <p>✅ Base de datos conectada correctamente</p>
                                <p>✅ Controlador Cotizador cargado</p>
                                <p>✅ Configuración lista</p>
                                <hr>
                                <p><strong>Próximo paso:</strong> Crear la vista completa del cotizador</p>
                            </div>
                            
                            <!-- Formulario básico de prueba -->
                            <form id='formBusqueda' class='mb-4'>
                                <div class='row'>
                                    <div class='col-md-3'>
                                        <label class='form-label'>Marca</label>
                                        <select class='form-select' id='marca' name='marca' required>
                                            <option value=''>Seleccionar marca</option>
                                            <option value='Nissan'>Nissan</option>
                                            <option value='Toyota'>Toyota</option>
                                            <option value='Honda'>Honda</option>
                                            <option value='Chevrolet'>Chevrolet</option>
                                        </select>
                                    </div>
                                    <div class='col-md-3'>
                                        <label class='form-label'>Modelo</label>
                                        <select class='form-select' id='modelo' name='modelo' required>
                                            <option value=''>Seleccionar modelo</option>
                                        </select>
                                    </div>
                                    <div class='col-md-2'>
                                        <label class='form-label'>Año</label>
                                        <select class='form-select' id='anio' name='anio' required>
                                            <option value=''>Seleccionar año</option>
                                        </select>
                                    </div>
                                    <div class='col-md-2'>
                                        <label class='form-label'>Sucursal</label>
                                        <select class='form-select' id='sucursal' name='sucursal'>";
                                        
                                        foreach($sucursales as $sucursal) {
                                            echo "<option value='$sucursal'>$sucursal</option>";
                                        }
                                        
            echo "                </select>
                                    </div>
                                    <div class='col-md-2 d-flex align-items-end'>
                                        <button type='submit' class='btn btn-primary w-100'>
                                            <i class='fas fa-search'></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div id='resultados' style='display: none;'>
                                <h5><i class='fas fa-list'></i> Baterías Compatibles</h5>
                                <div id='lista-baterias'></div>
                            </div>
                            
                            <div id='loading' style='display: none;' class='text-center'>
                                <div class='spinner-border text-primary' role='status'>
                                    <span class='visually-hidden'>Buscando...</span>
                                </div>
                                <p class='mt-2'>Buscando baterías compatibles...</p>
                            </div>
                            
                            <div class='mt-4'>
                                <h5>🔗 Enlaces de prueba:</h5>
                                <p><a href='" . base_url('api/test-db') . "' class='btn btn-outline-info'>🗄️ Probar Base de Datos</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js'></script>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    
    <script>
        // Datos de prueba
        const modelos = {
            'Nissan': ['Versa', 'Sentra', 'Altima', 'March', 'Kicks'],
            'Toyota': ['Corolla', 'Camry', 'Yaris', 'RAV4', 'Hilux'],
            'Honda': ['Civic', 'Accord', 'CR-V', 'HR-V', 'Fit'],
            'Chevrolet': ['Aveo', 'Sonic', 'Cruze', 'Malibu', 'Equinox']
        };
        
        \$('#marca').change(function() {
            const marca = \$(this).val();
            \$('#modelo').empty().append('<option value=\"\">Seleccionar modelo</option>');
            \$('#anio').empty().append('<option value=\"\">Seleccionar año</option>');
            
            if (marca && modelos[marca]) {
                modelos[marca].forEach(modelo => {
                    \$('#modelo').append(`<option value=\"\${modelo}\">\${modelo}</option>`);
                });
            }
        });
        
        \$('#modelo').change(function() {
            \$('#anio').empty().append('<option value=\"\">Seleccionar año</option>');
            
            if (\$(this).val()) {
                for (let año = 2025; año >= 2010; año--) {
                    \$('#anio').append(`<option value=\"\${año}\">\${año}</option>`);
                }
            }
        });
        
        \$('#formBusqueda').submit(function(e) {
            e.preventDefault();
            alert('¡Formulario funcionando! Próximo paso: conectar con la base de datos real.');
        });
    </script>
</body>
</html>";
    }
}

?>