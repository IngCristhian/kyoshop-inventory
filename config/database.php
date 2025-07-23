<?php
/**
 * CONFIGURACIÓN DE BASE DE DATOS
 * Conexión MySQL para KyoShop Inventory System
 */

class Database {
    // Configuración para cPanel/hosting compartido
    private $host = 'localhost';          // Cambiar por tu host de cPanel
    private $db_name = 'kyoshop_inventory'; // Cambiar por tu nombre de BD
    private $username = 'root';           // Tu usuario de MySQL
    private $password = '0309';           // Tu contraseña de MySQL
    private $charset = 'utf8mb4';
    
    private $pdo;
    private static $instance = null;
    
    /**
     * Constructor privado para patrón Singleton
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Obtener instancia única de la base de datos
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establecer conexión con la base de datos
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            // En producción, loggear el error en lugar de mostrarlo
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * Ejecutar consulta preparada
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Error en consulta: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Obtener todos los registros
     */
    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Obtener un solo registro
     */
    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }
    
    /**
     * Insertar registro y obtener ID
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Actualizar/eliminar y obtener filas afectadas
     */
    public function execute($sql, $params = []) {
        return $this->query($sql, $params)->rowCount();
    }
}

// Función helper para obtener la conexión fácilmente
function getDB() {
    return Database::getInstance();
}
?>