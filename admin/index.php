<?php
// COTIZADOR YÉPEZ - VERSIÓN SIMPLE QUE FUNCIONA

// Configuración de base de datos
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cotizador_yepez';

// Conectar a base de datos
try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    $db_status = "✅ Base de datos conectada";
    $db_error = false;
    
} catch (Exception $e) {
    $db_status = "❌ Error: " . $e->getMessage();
    $db_error = true;
}

// Obtener ruta actual
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = str_replace('/cotizador-yepez/admin', '', $uri);
$uri = trim($uri, '/');

// Routing simple
if ($uri === 'api/test-db') {
    // Test de base de datos
    header('Content-Type: application/json');
    
    if ($db_error) {
        echo json_encode(['success' => false, 'message' => $db_status]);
        exit;
    }
    
    try {
        $result = $conn->query("SELECT codigo, aplicacion, precio_publico FROM baterias_lth LIMIT 3");
        $baterias = [];
        
        while ($row = $result->fetch_assoc()) {
            $baterias[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Conexión exitosa',
            'data' => $baterias,
            'total' => count($baterias)
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    
    exit;
}

// Página principal
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotizador Acumuladores Yépez</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cotizador-container { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            padding: 20px 0; 
        }
        .cotizador-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1); 
        }
        .header-logo { 
            background: #2c3e50; 
            color: white; 
            padding: 20px; 
            border-radius: 15px 15px 0 0; 
            text-align: center; 
        }
        .status-card {
            border-left: 4px solid #28a745;
            background-color: #f8f9fa;
            padding: 15px;
            margin: 15px 0;
        }
        .error-card {
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="cotizador-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="cotizador-card">
                        <div class="header-logo">
                            <h1><i class="fas fa-car-battery"></i> Acumuladores Yépez</h1>
                            <p class="mb-0">Sistema de Cotización - <?= date('d/m/Y H:i:s') ?></p>
                        </div>
                        
                        <div class="card-body p-4">
                            
                            <?php if (!$db_error): ?>
                            <div class="status-card">
                                <h4><i class="fas fa-check-circle text-success"></i> ¡Sistema Funcionando!</h4>
                                <p class="mb-1"><?= $db_status ?></p>
                                <p class="mb-1">✅ PHP <?= phpversion() ?> funcionando</p>
                                <p class="mb-1">✅ Apache corriendo correctamente</p>
                                <p class="mb-0">✅ Estructura del proyecto lista</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-database"></i> Pruebas del Sistema</h5>
                                    <button onclick="testDatabase()" class="btn btn-info mb-2">
                                        <i class="fas fa-test-tube"></i> Probar Base de Datos
                                    </button>
                                    <div id="db-result"></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5><i class="fas fa-search"></i> Búsqueda Rápida</h5>
                                    <form onsubmit="buscarBaterias(event)">
                                        <div class="mb-2">
                                            <select class="form-select" id="marca" required>
                                                <option value="">Seleccionar marca</option>
                                                <option value="Nissan">Nissan</option>
                                                <option value="Toyota">Toyota</option>
                                                <option value="Honda">Honda</option>
                                                <option value="Chevrolet">Chevrolet</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <select class="form-select" id="modelo" required>
                                                <option value="">Seleccionar modelo</option>
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <select class="form-select" id="anio" required>
                                                <option value="">Seleccionar año</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Buscar Baterías
                                        </button>
                                    </form>
                                    <div id="search-result"></div>
                                </div>
                            </div>
                            
                            <?php else: ?>
                            <div class="error-card">
                                <h4><i class="fas fa-exclamation-triangle text-danger"></i> Error de Conexión</h4>
                                <p><?= $db_status ?></p>
                                <p><strong>Verifica:</strong></p>
                                <ul>
                                    <li>XAMPP MySQL está ejecutándose</li>
                                    <li>Base de datos 'cotizador_yepez' existe</li>
                                    <li>Usuario 'root' sin contraseña</li>
                                </ul>
                            </div>
                            <?php endif; ?>
                            
                            <hr>
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Sistema MVP funcionando con PHP nativo + MySQL + Bootstrap
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Test de base de datos
        function testDatabase() {
            fetch('api/test-db')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('db-result').innerHTML = `
                            <div class="alert alert-success">
                                <strong>✅ ${data.message}</strong><br>
                                Baterías encontradas: ${data.total}<br>
                                <small>Primeras 3: ${data.data.map(b => b.codigo).join(', ')}</small>
                            </div>
                        `;
                    } else {
                        document.getElementById('db-result').innerHTML = `
                            <div class="alert alert-danger">
                                <strong>❌ Error:</strong> ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('db-result').innerHTML = `
                        <div class="alert alert-danger">
                            <strong>❌ Error de conexión:</strong> ${error.message}
                        </div>
                    `;
                });
        }
        
        // Modelos por marca
        const modelos = {
            'Nissan': ['Versa', 'Sentra', 'Altima', 'March', 'Kicks'],
            'Toyota': ['Corolla', 'Camry', 'Yaris', 'RAV4', 'Hilux'],
            'Honda': ['Civic', 'Accord', 'CR-V', 'HR-V', 'Fit'],
            'Chevrolet': ['Aveo', 'Sonic', 'Cruze', 'Malibu', 'Equinox']
        };
        
        // Cargar modelos
        document.getElementById('marca').addEventListener('change', function() {
            const marca = this.value;
            const modeloSelect = document.getElementById('modelo');
            const anioSelect = document.getElementById('anio');
            
            modeloSelect.innerHTML = '<option value="">Seleccionar modelo</option>';
            anioSelect.innerHTML = '<option value="">Seleccionar año</option>';
            
            if (marca && modelos[marca]) {
                modelos[marca].forEach(modelo => {
                    modeloSelect.innerHTML += `<option value="${modelo}">${modelo}</option>`;
                });
            }
        });
        
        // Cargar años
        document.getElementById('modelo').addEventListener('change', function() {
            const anioSelect = document.getElementById('anio');
            anioSelect.innerHTML = '<option value="">Seleccionar año</option>';
            
            if (this.value) {
                for (let año = 2025; año >= 2010; año--) {
                    anioSelect.innerHTML += `<option value="${año}">${año}</option>`;
                }
            }
        });
        
        // Buscar baterías
        function buscarBaterias(event) {
            event.preventDefault();
            
            const marca = document.getElementById('marca').value;
            const modelo = document.getElementById('modelo').value;
            const anio = document.getElementById('anio').value;
            
            document.getElementById('search-result').innerHTML = `
                <div class="alert alert-info mt-3">
                    <strong>🔍 Búsqueda:</strong> ${marca} ${modelo} ${anio}<br>
                    <small>Próximo paso: Conectar con procedimiento almacenado buscar_baterias_avanzado</small>
                </div>
            `;
        }
    </script>
</body>
</html>