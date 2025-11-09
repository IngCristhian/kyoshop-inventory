<?php
/**
 * MODELO PRODUCTO
 * Manejo de datos de productos en la base de datos
 */

require_once 'config/database.php';

class Producto {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Obtener todos los productos activos con paginación
     */
    public function obtenerTodos($pagina = 1, $limite = ITEMS_PER_PAGE, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        $condiciones = ['activo = 1'];
        $parametros = [];
        
        // Aplicar filtros
        if (!empty($filtros['categoria'])) {
            $condiciones[] = 'categoria = :categoria';
            $parametros['categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['tipo'])) {
            $condiciones[] = 'tipo = :tipo';
            $parametros['tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['ubicacion'])) {
            $condiciones[] = 'ubicacion = :ubicacion';
            $parametros['ubicacion'] = $filtros['ubicacion'];
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(nombre LIKE :busqueda OR descripcion LIKE :busqueda OR codigo_producto LIKE :busqueda)';
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
            $condiciones[] = 'stock <= 5';
        }
        
        $where = implode(' AND ', $condiciones);
        
        $sql = "SELECT * FROM productos 
                WHERE {$where} 
                ORDER BY fecha_actualizacion DESC 
                LIMIT :limite OFFSET :offset";
        
        $parametros['limite'] = $limite;
        $parametros['offset'] = $offset;
        
        return $this->db->fetchAll($sql, $parametros);
    }
    
    /**
     * Contar total de productos para paginación
     */
    public function contarTotal($filtros = []) {
        $condiciones = ['activo = 1'];
        $parametros = [];
        
        // Aplicar mismos filtros que en obtenerTodos
        if (!empty($filtros['categoria'])) {
            $condiciones[] = 'categoria = :categoria';
            $parametros['categoria'] = $filtros['categoria'];
        }

        if (!empty($filtros['tipo'])) {
            $condiciones[] = 'tipo = :tipo';
            $parametros['tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['ubicacion'])) {
            $condiciones[] = 'ubicacion = :ubicacion';
            $parametros['ubicacion'] = $filtros['ubicacion'];
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(nombre LIKE :busqueda OR descripcion LIKE :busqueda OR codigo_producto LIKE :busqueda)';
            $parametros['busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
            $condiciones[] = 'stock <= 5';
        }
        
        $where = implode(' AND ', $condiciones);
        
        $sql = "SELECT COUNT(*) as total FROM productos WHERE {$where}";
        $resultado = $this->db->fetch($sql, $parametros);
        return $resultado['total'];
    }
    
    /**
     * Obtener producto por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = :id AND activo = 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Obtener producto por código
     */
    public function obtenerPorCodigo($codigo) {
        $sql = "SELECT * FROM productos WHERE codigo_producto = :codigo AND activo = 1";
        return $this->db->fetch($sql, ['codigo' => $codigo]);
    }
    
    /**
     * Crear nuevo producto
     */
    public function crear($datos) {
        $sql = "INSERT INTO productos (
                    nombre, descripcion, precio, stock, imagen,
                    categoria, tipo, talla, color, ubicacion, codigo_producto
                ) VALUES (
                    :nombre, :descripcion, :precio, :stock, :imagen,
                    :categoria, :tipo, :talla, :color, :ubicacion, :codigo_producto
                )";

        return $this->db->insert($sql, [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'imagen' => $datos['imagen'] ?? null,
            'categoria' => $datos['categoria'],
            'tipo' => $datos['tipo'],
            'talla' => $datos['talla'],
            'color' => $datos['color'],
            'ubicacion' => $datos['ubicacion'],
            'codigo_producto' => $datos['codigo_producto']
        ]);
    }
    
    /**
     * Actualizar producto existente
     */
    public function actualizar($id, $datos) {
        $campos = [
            'nombre = :nombre',
            'descripcion = :descripcion',
            'precio = :precio',
            'stock = :stock',
            'categoria = :categoria',
            'tipo = :tipo',
            'talla = :talla',
            'color = :color',
            'ubicacion = :ubicacion',
            'codigo_producto = :codigo_producto',
            'fecha_actualizacion = CURRENT_TIMESTAMP'
        ];

        $parametros = [
            'id' => $id,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'categoria' => $datos['categoria'],
            'tipo' => $datos['tipo'],
            'talla' => $datos['talla'],
            'color' => $datos['color'],
            'ubicacion' => $datos['ubicacion'],
            'codigo_producto' => $datos['codigo_producto']
        ];
        
        // Solo actualizar imagen si se proporciona una nueva
        if (isset($datos['imagen']) && !empty($datos['imagen'])) {
            $campos[] = 'imagen = :imagen';
            $parametros['imagen'] = $datos['imagen'];
        }
        
        $sql = "UPDATE productos SET " . implode(', ', $campos) . " WHERE id = :id";
        
        return $this->db->execute($sql, $parametros);
    }
    
    /**
     * Eliminar producto (soft delete)
     */
    public function eliminar($id) {
        $sql = "UPDATE productos 
                SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }
    
    /**
     * Obtener todas las categorías únicas
     */
    public function obtenerCategorias() {
        $sql = "SELECT DISTINCT categoria FROM productos WHERE activo = 1 ORDER BY categoria";
        $resultado = $this->db->fetchAll($sql);
        return array_column($resultado, 'categoria');
    }
    
    /**
     * Obtener productos con stock bajo (<=5)
     */
    public function obtenerStockBajo() {
        $sql = "SELECT * FROM productos WHERE stock <= 5 AND activo = 1 ORDER BY stock ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Obtener estadísticas del inventario
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT 
                    COUNT(*) as total_productos,
                    SUM(stock) as total_stock,
                    COUNT(DISTINCT categoria) as total_categorias,
                    AVG(precio) as precio_promedio,
                    COUNT(CASE WHEN stock <= 5 THEN 1 END) as productos_bajo_stock,
                    SUM(precio * stock) as valor_total_inventario
                FROM productos 
                WHERE activo = 1";
        return $this->db->fetch($sql);
    }
    
    /**
     * Buscar productos
     */
    public function buscar($termino, $limite = 20) {
        $sql = "SELECT * FROM productos 
                WHERE activo = 1 
                AND (nombre LIKE :termino 
                     OR descripcion LIKE :termino 
                     OR codigo_producto LIKE :termino
                     OR categoria LIKE :termino
                     OR color LIKE :termino)
                ORDER BY nombre ASC 
                LIMIT :limite";
        
        return $this->db->fetchAll($sql, [
            'termino' => '%' . $termino . '%',
            'limite' => $limite
        ]);
    }
    
    /**
     * Validar datos del producto
     */
    public function validar($datos, $id = null) {
        $errores = [];
        
        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 255) {
            $errores[] = 'El nombre no puede exceder 255 caracteres';
        }
        
        // Validar precio
        if (empty($datos['precio']) || !is_numeric($datos['precio']) || $datos['precio'] <= 0) {
            $errores[] = 'El precio debe ser un número mayor a 0';
        }
        
        // Validar stock
        if (!isset($datos['stock']) || !is_numeric($datos['stock']) || $datos['stock'] < 0) {
            $errores[] = 'El stock debe ser un número mayor o igual a 0';
        }
        
        // Validar categoría
        if (empty($datos['categoria'])) {
            $errores[] = 'La categoría es requerida';
        }
        
        // Validar código único
        if (!empty($datos['codigo_producto'])) {
            $existente = $this->obtenerPorCodigo($datos['codigo_producto']);
            if ($existente && $existente['id'] != $id) {
                $errores[] = 'El código de producto ya existe';
            }
        }
        
        return $errores;
    }
}
?>