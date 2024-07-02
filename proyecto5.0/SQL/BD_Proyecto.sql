CREATE DATABASE IF NOT EXISTS proyecto;

USE proyecto;


-- Tabla de Empresas
CREATE TABLE IF NOT EXISTS Empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    pass VARCHAR(255) NOT NULL -- Cambiado a 'password' en lugar de 'pass'
);

-- Tabla de Trabajadores
CREATE TABLE IF NOT EXISTS Trabajadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT,
    dni VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    telefono VARCHAR(20),
    email VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    fecha_contratacion DATE,
    primer_login BOOLEAN DEFAULT TRUE,
    contraseña VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (empresa_id) REFERENCES Empresas(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Tabla de Fichajes
CREATE TABLE IF NOT EXISTS Fichajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trabajador_id INT,
    fecha DATE,
    hora_entrada TIME,
    hora_salida TIME,
    FOREIGN KEY (trabajador_id) REFERENCES Trabajadores(id) ON DELETE CASCADE ON UPDATE CASCADE
);
