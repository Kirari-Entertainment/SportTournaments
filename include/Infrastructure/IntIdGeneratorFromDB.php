<?php namespace Robust\Boilerplate\Infrastructure;
use Exception;
use PDO;
use Robust\Boilerplate\IdGenerator;
use stdClass;

/**
 * Genera identificadores únicos enteros consecutivos
 */
class IntIdGeneratorFromDB extends RepositoryFromDB implements IdGenerator {
    static $idTableName = 'id_generator_counters';
    static $universalClassStorage = stdClass::class;

    /**
     * @param PDO $_dbConnection Permite elegir una base de datos MariaDB llevar el conteo de entidades persistentes.
     */
    public function __construct(PDO $_dbConnection = null) {
        parent::__construct($_dbConnection);

        // Crear tabla de control de identificadores
        $query = "CREATE TABLE IF NOT EXISTS ".static::$idTableName." (
            class         VARCHAR(255),
            id_count      INT,
            PRIMARY KEY (class)
        )";

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute();
    }
    
    public function nextUniversal() : int {
        return $this->nextForClass(static::$universalClassStorage);
    }

    public function nextForClass(string $className) : int {
        $nextId = $this->reserveNForClass(1, $className);

        return $nextId;
    }

    public function getNForClass(int $n, string $className) : array {
        $assignedIds = [];
        $lastAssigned = $this->reserveNForClass($n, $className);

        // Funciona porque este generador solo asigna ID enteros consecutivos.
        for ($i=0; $i < $n; $i++) { 
            $assignedIds[] = $lastAssigned - $i;
        }

        return $assignedIds;
    }

    private function reserveNForClass(int $n, string $className) : int {
        $lastAssigned = null;

        // Crea la fila, inicializa el contador o lo hace avanzar
        $query = 
        "INSERT INTO ".static::$idTableName." (class, id_count)
            VALUES (
                '$className',
                $n
                )

            ON DUPLICATE KEY UPDATE
                id_count = (
                    SELECT id_count
                    ) + $n
                    
            RETURNING id_count";
        
        try {
            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute();
            
        } catch (Exception $th) {
            die($th->getMessage());
        }

        $lastAssigned = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastAssigned = $lastAssigned['id_count'];

        return $lastAssigned;
    }
}
