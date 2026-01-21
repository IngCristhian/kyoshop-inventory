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
            $condiciones[] = '(nombre LIKE :busqueda1 OR descripcion LIKE :busqueda2 OR codigo_producto LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }

        if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
            $condiciones[] = 'stock <= 5';
        }

        // Filtro por stock mínimo
        if (isset($filtros['stock_minimo'])) {
            $condiciones[] = 'stock >= :stock_minimo';
            $parametros['stock_minimo'] = (int)$filtros['stock_minimo'];
        }

        $where = implode(' AND ', $condiciones);

        // Convertir límite y offset a enteros
        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT * FROM productos
                WHERE {$where}
                ORDER BY fecha_actualizacion DESC
                LIMIT {$limite} OFFSET {$offset}";

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
            $condiciones[] = '(nombre LIKE :busqueda1 OR descripcion LIKE :busqueda2 OR codigo_producto LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
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
                    nombre, descripcion, precio, stock, imagen, imagen_modelo,
                    categoria, tipo, talla, color, ubicacion, codigo_producto
                ) VALUES (
                    :nombre, :descripcion, :precio, :stock, :imagen, :imagen_modelo,
                    :categoria, :tipo, :talla, :color, :ubicacion, :codigo_producto
                )";

        return $this->db->insert($sql, [
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'],
            'precio' => $datos['precio'],
            'stock' => $datos['stock'],
            'imagen' => $datos['imagen'] ?? null,
            'imagen_modelo' => $datos['imagen_modelo'] ?? null,
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
            'ubicacion' => $datos['ubicacion'],
            'codigo_producto' => $datos['codigo_producto']
        ];

        // Actualizar talla solo si está presente en $datos
        if (array_key_exists('talla', $datos)) {
            $campos[] = 'talla = :talla';
            $parametros['talla'] = $datos['talla'];
        }

        // Actualizar color solo si está presente en $datos
        if (array_key_exists('color', $datos)) {
            $campos[] = 'color = :color';
            $parametros['color'] = $datos['color'];
        }

        // Actualizar imagen si está presente en $datos (incluso si es null para eliminar)
        if (array_key_exists('imagen', $datos)) {
            $campos[] = 'imagen = :imagen';
            $parametros['imagen'] = $datos['imagen'];
        }

        // Actualizar imagen_modelo si está presente en $datos (incluso si es null para eliminar)
        if (array_key_exists('imagen_modelo', $datos)) {
            $campos[] = 'imagen_modelo = :imagen_modelo';
            $parametros['imagen_modelo'] = $datos['imagen_modelo'];
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
                    SUM(precio * stock) as valor_total_inventario,
                    SUM(CASE WHEN tipo = 'Niño' THEN stock ELSE 0 END) as stock_nino,
                    SUM(CASE WHEN tipo = 'Mujer' THEN stock ELSE 0 END) as stock_mujer,
                    SUM(CASE WHEN tipo = 'Hombre' THEN stock ELSE 0 END) as stock_hombre
                FROM productos
                WHERE activo = 1";
        return $this->db->fetch($sql);
    }
    
    /**
     * Buscar productos
     */
    public function buscar($termino, $limite = 20) {
        $limite = (int)$limite;

        $sql = "SELECT * FROM productos
                WHERE activo = 1
                AND (nombre LIKE :termino
                     OR descripcion LIKE :termino
                     OR codigo_producto LIKE :termino
                     OR categoria LIKE :termino
                     OR color LIKE :termino)
                ORDER BY nombre ASC
                LIMIT {$limite}";

        return $this->db->fetchAll($sql, [
            'termino' => '%' . $termino . '%'
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

    /**
     * MÉTODOS DE SOPORTE PARA VARIANTES
     */

    /**
     * Verificar si este producto tiene variantes
     * @param int $id ID del producto
     * @return bool True si tiene variantes
     */
    public function tieneVariantes($id) {
        $sql = "SELECT COUNT(*) as total
                FROM productos
                WHERE producto_padre_id = :id AND activo = 1";
        $result = $this->db->fetch($sql, ['id' => $id]);
        return $result['total'] > 0;
    }

    /**
     * Obtener todas las variantes de un producto
     * @param int $id ID del producto padre
     * @return array Lista de variantes
     */
    public function obtenerVariantes($id) {
        $sql = "SELECT * FROM productos
                WHERE producto_padre_id = :id AND activo = 1
                ORDER BY talla, color";
        return $this->db->fetchAll($sql, ['id' => $id]);
    }

    /**
     * Obtener producto padre (si es variante)
     * @param int $id ID del producto variante
     * @return array|null Producto padre o null
     */
    public function obtenerProductoPadre($id) {
        $sql = "SELECT p2.*
                FROM productos p1
                INNER JOIN productos p2 ON p1.producto_padre_id = p2.id
                WHERE p1.id = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Obtener productos principales (sin padre) para el selector de agrupación
     * @return array Lista de productos principales
     */
    public function obtenerProductosPrincipales() {
        $sql = "SELECT * FROM productos
                WHERE producto_padre_id IS NULL
                AND activo = 1
                ORDER BY nombre";
        return $this->db->fetchAll($sql);
    }

    /**
     * Modificar obtenerTodos para excluir variantes opcionalmente
     * @param int $pagina Página actual
     * @param int $limite Productos por página
     * @param array $filtros Filtros de búsqueda
     * @param bool $incluirVariantes Si se incluyen variantes o solo productos principales
     * @return array Lista de productos
     */
    public function obtenerTodosSinVariantes($pagina = 1, $limite = ITEMS_PER_PAGE, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        $condiciones = ['activo = 1', 'producto_padre_id IS NULL'];
        $parametros = [];

        // Aplicar filtros (mismo código que obtenerTodos)
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
            $condiciones[] = '(nombre LIKE :busqueda1 OR descripcion LIKE :busqueda2 OR codigo_producto LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }

        if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
            $condiciones[] = 'stock <= 5';
        }

        if (isset($filtros['stock_minimo'])) {
            $condiciones[] = 'stock >= :stock_minimo';
            $parametros['stock_minimo'] = (int)$filtros['stock_minimo'];
        }

        $where = implode(' AND ', $condiciones);
        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT * FROM productos
                WHERE {$where}
                ORDER BY fecha_actualizacion DESC
                LIMIT {$limite} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $parametros);
    }
}
?>