<?php namespace Robust\Boilerplate\Infrastructure;

use RedBeanPHP\OODBBean;
use RedBeanPHP\RedException\SQL;
use Robust\Boilerplate\Infrastructure\InfrastructureException;
use Robust\Boilerplate\Infrastructure\RepositoryFromDB;

require_once 'rb-mysql.php';
use R;

/**
 * Base class for RedBean-based repository implementations.
 * Provides CRUD operations for entities using RedBean ORM.
 * 
 * @package Robust\Boilerplate\Infrastructure
 * 
 * @property array $tablesByEntity Mapping between entity classes and their database table names
 * @property array $parseToBeanByEntity Functions to parse entities into RedBean beans by entity class
 * @property array $parseFromBeanByEntity Functions to parse RedBean beans into entities by entity class
 * 
 * @method void saveEntity(object $entity) Saves a new entity to the database
 * @method object|null findEntityById(string $entityClass, int|string $id) Finds an entity by its ID
 * @method array retrieveAllEntities(string $entityClass) Retrieves all entities of a specific class
 * @method void updateEntity(object $entity) Updates an existing entity in the database
 * @method void removeEntity(string $entityClass, int|string $id) Removes an entity by its ID
 * @method static OODBBean findBeanBySystemId(string $entityClass, int|string $id) Finds a RedBean bean by system ID
 * 
 * @throws InfrastructureException When RedBean operations fail
 * 
 * @see SeriesCatalogFromRB For implementation example
 */
abstract class RepositoryFromRB extends RepositoryFromDB {
    protected static array $tablesByEntity = [];
    protected array $parseToBeanByEntity = [];
    protected array $parseFromBeanByEntity = [];

    /**
     * @param \PDO|null $dbConnection Allows to inject a PDO connection to RedBean.
     * If not provided, RedBean will create a new connection using the default configuration.
     */
    public function __construct(?\PDO $dbConnection = null) {
        parent::__construct($dbConnection);
        R::setup($this->dbConnection);
        R::freeze(false);
        
        // Force child classes to initialize their entities
        $this->initializeEntities();
    }

    // Abstract method that child classes must implement
    abstract protected function initializeEntities(): void;

    protected function registerEntity(
        string $entityClass,
        string $tableName,
        callable $parseToBean,
        callable $parseFromBean
    ): void {
        static::$tablesByEntity[$entityClass] = $tableName;
        $this->parseToBeanByEntity[$entityClass] = $parseToBean;
        $this->parseFromBeanByEntity[$entityClass] = $parseFromBean;
    }

    protected function saveEntity(object $entity): void {
        try {
            $entityBean = R::dispense(static::$tablesByEntity[get_class($entity)]);
            $this->parseToBeanByEntity[get_class($entity)]($entity, $entityBean);
            R::store($entityBean);

        } catch (SQL $e) {
            throw new InfrastructureException(
                message: "El controlador de RedBean podría requerir mantenimiento. {$e->getMessage()}",
                code: InfrastructureException::$INTERNAL_ERROR
            );
        }
    }

    protected function findEntityById(string $entityClass, int|string $id): ?object {
        $entityBean = static::findBeanBySystemId($entityClass, $id);

        return $entityBean ? $this->parseFromBeanByEntity[$entityClass]($entityBean) : null;
    }

    protected function retrieveAllEntities(string $entityClass) : array {
        $allEntitiesBeans = R::findAll(static::$tablesByEntity[$entityClass]);
        $allEntities = [];

        foreach ($allEntitiesBeans as $entityBean) {
            $allEntities[] = $this->parseFromBeanByEntity[$entityClass]($entityBean);
        }

        return $allEntities;
        
    }

    protected function updateEntity(object $entity): void {
        try {
            $entityBean = static::findBeanBySystemId(get_class($entity), $entity->getId());
            if ($entityBean) {
                $this->parseToBeanByEntity[get_class($entity)]($entity, $entityBean);
                R::store($entityBean);
            }

        } catch (SQL $e) {
            throw new InfrastructureException(
                message: "El controlador de RedBean podría requerir mantenimiento.",
                code: InfrastructureException::$INTERNAL_ERROR
            );
        }
    }

    protected function removeEntity(string $entityClass, int|string $id): void {
        $entityBean = static::findBeanBySystemId($entityClass, $id);
        if ($entityBean) R::trash($entityBean);
    }

    protected static function findBeanBySystemId(string $entityClass, int|string $id) : ?OODBBean {
        return R::findOne(static::$tablesByEntity[$entityClass], "sys_id_ = ?", [$id]);
    }
}