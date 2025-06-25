<?php

// Configuración simple de base de datos para Cotizador Yépez
class DatabaseConfig 
{
    public static $default = [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'cotizador_yepez',
        'port'     => 3306,
        'charset'  => 'utf8mb4'
    ];
    
    public static function connect() 
    {
        $config = self::$default;
        
        try {
            $connection = new mysqli(
                $config['hostname'],
                $config['username'], 
                $config['password'],
                $config['database'],
                $config['port']
            );
            
            if ($connection->connect_error) {
                throw new Exception('Error de conexión: ' . $connection->connect_error);
            }
            
            $connection->set_charset($config['charset']);
            return $connection;
            
        } catch (Exception $e) {
            throw new Exception('No se pudo conectar a la base de datos: ' . $e->getMessage());
        }
    }
}

// Función global para obtener conexión
if (!function_exists('getDatabase')) {
    function getDatabase() {
        return DatabaseConfig::connect();
    }
}

?>