-- Base de Datos: Cotizador Acumuladores Yepez 
-- Fecha: 25/06/2025  1:58:56.20 
 
CREATE DATABASE IF NOT EXISTS cotizador_yepez; 
USE cotizador_yepez; 
 
-- Tabla de baterias LTH 
CREATE TABLE baterias_lth ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    codigo VARCHAR(20) NOT NULL, 
    aplicacion TEXT, 
    voltaje DECIMAL(4,2), 
    amperaje INT, 
    precio_publico DECIMAL(10,2), 
    precio_mayoreo DECIMAL(10,2), 
    activo BOOLEAN DEFAULT TRUE, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
); 
 
-- Tabla de ventas 
CREATE TABLE ventas ( 
    id INT PRIMARY KEY AUTO_INCREMENT, 
    codigo_venta VARCHAR(50) UNIQUE, 
    cliente_nombre VARCHAR(255), 
    cliente_telefono VARCHAR(20), 
    vehiculo_marca VARCHAR(100), 
    vehiculo_modelo VARCHAR(100), 
    vehiculo_anio INT, 
    bateria_codigo VARCHAR(20), 
    precio_final DECIMAL(10,2), 
    sucursal VARCHAR(100), 
    vendedor VARCHAR(255), 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
); 
