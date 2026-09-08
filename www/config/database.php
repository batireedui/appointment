<?php
/**
 * Өгөгдлийн сангийн холболт (PDO)
 * Хостинг дээрээ эдгээр утгуудыг өөрчилнө үү.
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'hospital_appointment');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('Өгөгдлийн сантай холбогдоход алдаа гарлаа: ' . $e->getMessage());
        }
    }
    return $pdo;
}
