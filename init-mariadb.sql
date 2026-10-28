-- ponytail: el entrypoint ya crea user@'%' via MYSQL_USER; solo aseguramos privilegios
GRANT ALL PRIVILEGES ON `database`.* TO 'user'@'%';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS proveedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  razon_social VARCHAR(255) NOT NULL,
  rfc VARCHAR(13) NOT NULL
);

INSERT INTO proveedores (razon_social, rfc) VALUES
  ('Proveedor Demo', 'XAXX010101000'),
  ('Distribuidora del Norte SA de CV', 'DIN850315ABC'),
  ('Comercializadora del Sur SA de CV', 'CDS920720XYZ');

CREATE TABLE IF NOT EXISTS marcas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  marca VARCHAR(255) NOT NULL,
  logo VARCHAR(255) NOT NULL,
  id_proveedor INT NULL
);

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  producto VARCHAR(255) NOT NULL,
  foto VARCHAR(255) NOT NULL,
  precio VARCHAR(255) NOT NULL,
  id_marca INT NOT NULL
);

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(150) NOT NULL
);

-- ponytail: la tabla ya existía con más columnas; DROP IF EXISTS evita fallar si se recrea
ALTER TABLE usuarios
  DROP COLUMN IF EXISTS nombre,
  DROP COLUMN IF EXISTS apellido,
  DROP COLUMN IF EXISTS telefono,
  DROP COLUMN IF EXISTS direccion,
  DROP COLUMN IF EXISTS ciudad,
  DROP COLUMN IF EXISTS estado,
  DROP COLUMN IF EXISTS codigo_postal,
  DROP COLUMN IF EXISTS pais;

INSERT INTO usuarios (usuario, password, email)
SELECT 'admin', 'admin123', 'ana@ejemplo.com'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE usuario = 'admin');

CREATE TABLE IF NOT EXISTS permiso (
  id_permiso INT AUTO_INCREMENT PRIMARY KEY,
  permiso VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS usuario_permiso (
  id_usuario INT NOT NULL,
  id_permiso INT NOT NULL,
  PRIMARY KEY (id_usuario, id_permiso)
);

INSERT INTO permiso (permiso)
SELECT seed.permiso FROM (
  SELECT 'Proveedores' AS permiso UNION ALL
  SELECT 'Marcas' UNION ALL
  SELECT 'Productos' UNION ALL
  SELECT 'Usuarios' UNION ALL
  SELECT 'Permisos' UNION ALL
  SELECT 'Roles'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM permiso);