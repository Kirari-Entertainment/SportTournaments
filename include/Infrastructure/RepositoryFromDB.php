<?php namespace Robust\Boilerplate\Infrastructure;
use PDO;
use const Robust\SysSettings\DB_HOST;
use const Robust\SysSettings\DB_NAME;
use const Robust\SysSettings\DB_PSWD;
use const Robust\SysSettings\DB_USER;

/******************************************************
 * Dependencias
 ******************************************************/

/**
 * Los repositorios que heredan de esta clase obtienen una conexión 
 * con la base de datos principal.
 */
abstract class RepositoryFromDB {
    // Base de datos principal
    protected ?PDO $dbConnection;


    /**
     * @param ?PDO $_dbConnection
     *          Permite utilizar una conexión de base de datos específica.
     *          Por defecto, se utilizará laa que indica la configuración del sistema.
     */
    public function __construct(?PDO $_dbConnection = null) {
        if ($_dbConnection !== null) {
            $this->dbConnection = $_dbConnection;

        } else {
            $this->dbConnection = new PDO(
                'mysql:host='. DB_HOST.
                ';dbname='. DB_NAME,
                DB_USER,
                DB_PSWD
            );
        }
    }
    
    
    public function __destruct() {
        $this->dbConnection = null;
    }
    
}