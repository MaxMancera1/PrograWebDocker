<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


//Variables de configuración de la base de datos
define('DB_HOST', 'mariadb');
define('DB_USER', 'user');
define('DB_PASS', 'password');
define('DB_NAME', 'database');
define('DB_PORT', '3306');
define('DB_DRIVER', 'mysql');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'manceragarciamax@gmail.com');
define('SMTP_PASS', 'ycja djwh opkh lvco');
define('SMTP_FROM', 'manceragarciamax@gmail.com');
define('SMTP_FROM_NAME', 'Maximiliano Mancera Garcia');

?>
