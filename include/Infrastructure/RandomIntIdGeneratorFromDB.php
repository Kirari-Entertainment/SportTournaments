<?php namespace Robust\Boilerplate\Infrastructure;

use PDO;
use Robust\Boilerplate\IdGenerator;

class RandomIntIdGeneratorFromDB extends RepositoryFromDB implements IdGenerator {
    static $idTableName = 'id_generator_aleatory_spent';
    static $universalClassStorage = \stdClass::class;

    private $startMaxRangeDigits;

    /**
     * @param PDO|null $_dbConnection Permite elegir una base de datos MariaDB llevar el conteo de entidades persistentes.
     * @param int $startMaxRangeDigits Cantidad inicial de dígitos que tendrá el rango de identificadores.
     */
    public function __construct(PDO $_dbConnection = null, int $startMaxRangeDigits = 4) {
        parent::__construct($_dbConnection);
        $this->startMaxRangeDigits = $startMaxRangeDigits > 0 ? $startMaxRangeDigits : 4;

        // Crear tabla de control de identificadores
        $query = "CREATE TABLE IF NOT EXISTS ".static::$idTableName." (
            class         VARCHAR(255),
            id_taken      INT
        )";

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute();
    }

    public function nextUniversal() : int {
        return $this->nextForClass(static::$universalClassStorage);
    }

    public function nextForClass(string $className) : int {
        return $this->getNForClass(1, $className)[0];
    }

    public function getNForClass(int $n, string $className) : array {
        $className = stripslashes($className);
        $currentMaxId = pow(10, $this->startMaxRangeDigits) - 1;

        $assignedIds = [];

        $QUERY_TAKEN_IDS = "SELECT id_taken FROM ".static::$idTableName." WHERE class = '$className';";
        $QUERY_TAKE_ID = "INSERT INTO ".static::$idTableName."(class, id_taken) VALUES (:class, :id_taken)";

        try {
            $stmt = $this->dbConnection->prepare($QUERY_TAKEN_IDS);
            $stmt->execute();

            $takenIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        } catch (\Exception $th) {
            die($th->getMessage());
        }

        for ($i = 0; $i < $n; ++$i) {
            while (sizeof($takenIds) > $currentMaxId) $currentMaxId = pow(10, ++$this->startMaxRangeDigits) - 1;

            do $proposedId = rand(0, $currentMaxId);
            while (in_array($proposedId, $takenIds));

            $stmt = $this->dbConnection->prepare($QUERY_TAKE_ID);
            $stmt->bindParam(":class", $className, PDO::PARAM_STR);
            $stmt->bindParam(":id_taken", $proposedId, PDO::PARAM_INT);

            $stmt->execute();

            $assignedIds[] = $proposedId;
        }

        return $assignedIds;
    }

}