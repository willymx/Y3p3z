<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Cotizador extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Cotizador Acumuladores Yépez',
            'fecha' => date('d/m/Y'),
            'sucursales' => ['Matriz Centro', 'Sucursal Norte', 'Sucursal Sur']
        ];
        
        return $this->view('cotizador/busqueda', $data);
    }

    public function testDatabase()
    {
        try {
            $sql = "SELECT codigo, aplicacion, precio_publico FROM baterias_lth LIMIT 5";
            $result = $this->db->query($sql);
            
            echo "<h2>🗄️ Test de Base de Datos</h2>";
            echo "<p><strong>✅ Conexión exitosa</strong></p>";
            echo "<p>Baterías encontradas: " . $result->num_rows . "</p>";
            
            if ($result->num_rows > 0) {
                echo "<h3>📋 Muestra de baterías:</h3>";
                echo "<table border='1' style='border-collapse: collapse; width: 100%; padding: 10px;'>";
                echo "<tr style='background: #f0f0f0;'><th style='padding: 8px;'>Código</th><th style='padding: 8px;'>Aplicación</th><th style='padding: 8px;'>Precio</th></tr>";
                
                while ($bateria = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td style='padding: 8px;'>" . htmlspecialchars($bateria['codigo']) . "</td>";
                    echo "<td style='padding: 8px;'>" . htmlspecialchars(substr($bateria['aplicacion'], 0, 50)) . "...</td>";
                    echo "<td style='padding: 8px;'>$" . number_format($bateria['precio_publico'], 2) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            echo "<br><p><a href='" . base_url() . "' class='btn btn-primary'>← Volver al cotizador</a></p>";
            
        } catch (Exception $e) {
            echo "<h2>❌ Error de Base de Datos</h2>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='" . base_url() . "'>← Volver al inicio</a></p>";
        }
    }
    
    public function buscar()
    {
        if ($this->request->isAJAX()) {
            $marca = $this->request->getPost('marca');
            $modelo = $this->request->getPost('modelo');
            $anio = $this->request->getPost('anio');
            $sucursal = $this->request->getPost('sucursal');

            try {
                // Usar el procedimiento almacenado
                $sucursal_id = $sucursal ? $this->getSucursalId($sucursal) : null;
                $sql = "CALL buscar_baterias_avanzado(?, ?, ?, ?)";
                
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("ssii", $marca, $modelo, $anio, $sucursal_id);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $baterias = [];
                
                while ($row = $result->fetch_assoc()) {
                    $baterias[] = $row;
                }
                
                $stmt->close();
                
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $baterias,
                    'total' => count($baterias)
                ]);
                
            } catch (Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al buscar baterías: ' . $e->getMessage()
                ]);
            }
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
    }

    public function getMarcas()
    {
        try {
            $sql = "SELECT DISTINCT marca FROM vehiculos_compatibilidad ORDER BY marca";
            $result = $this->db->query($sql);
            
            $marcas = [];
            while ($row = $result->fetch_assoc()) {
                $marcas[] = $row['marca'];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $marcas
            ]);
            
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function getSucursalId($nombre_sucursal)
    {
        try {
            $sql = "SELECT id FROM sucursales WHERE nombre = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $nombre_sucursal);
            $stmt->execute();
            
            $result = $stmt->get_result();
            $sucursal = $result->fetch_assoc();
            $stmt->close();
            
            return $sucursal ? $sucursal['id'] : null;
            
        } catch (Exception $e) {
            return null;
        }
    }
}

?>