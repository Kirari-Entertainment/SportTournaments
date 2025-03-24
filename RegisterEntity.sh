#!/usr/bin/env bash

# Ensure running with bash
if [ -z "$BASH_VERSION" ]; then
    echo "This script requires bash to run"
    exit 1
fi

create_domain_entity() {
    local entity_name=$1
    local entity_dir="modules/Series/Domain/${entity_name}"
    mkdir -p "$entity_dir"
    
    cat <<EOL > "$entity_dir/${entity_name}.php"
<?php namespace Kirari\Series\Domain\\${entity_name};

/**
 * Class ${entity_name}
 * 
 * @package Kirari\Series\Domain\\${entity_name}
 */
class ${entity_name} {
    /**
     * @param string \$id
     * @param string \$name
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly string \$id,
        private string \$name
    ) {
        if (empty(\$name)) {
            throw new \InvalidArgumentException("Name cannot be empty");
        }
    }

    public function getId(): string {
        return \$this->id;
    }

    public function getName(): string {
        return \$this->name;
    }

    public function setName(string \$name): void {
        if (empty(\$name)) {
            throw new \InvalidArgumentException("Name cannot be empty");
        }
        \$this->name = \$name;
    }
}
EOL
}

create_repository_interface() {
    local entity_name=$1
    local entity_dir="modules/Series/Domain/${entity_name}"
    
    cat <<EOL > "$entity_dir/${entity_name}Repository.php"
<?php namespace Kirari\Series\Domain\\${entity_name};

/**
 * Interface ${entity_name}Repository
 * 
 * @package Kirari\Series\Domain\\${entity_name}
 */
interface ${entity_name}Repository {
    /**
     * @param ${entity_name} \$${entity_name,,}
     * @return void
     */
    public function add${entity_name}(${entity_name} \$${entity_name,,}): void;

    /**
     * @param string \$id
     * @return ${entity_name}|null
     */
    public function find${entity_name}ById(string \$id): ?${entity_name};

    /**
     * @return array<${entity_name}>
     */
    public function retrieveAll${entity_name}s(): array;

    /**
     * @param ${entity_name} \$${entity_name,,}
     * @return void
     */
    public function update${entity_name}(${entity_name} \$${entity_name,,}): void;

    /**
     * @param string \$id
     * @return void
     */
    public function remove${entity_name}(string \$id): void;
}
EOL
}

create_repository_implementation() {
    local entity_name=$1
    local repo_dir="modules/Series/Infrastructure/RBRepos/${entity_name}"
    mkdir -p "$repo_dir"
    
    cat <<EOL > "$repo_dir/${entity_name}RepositoryFromRB.php"
<?php namespace Kirari\Series\Infrastructure\RBRepos\\${entity_name};

use Kirari\Series\Domain\\${entity_name}\\${entity_name};
use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\Infrastructure\RepositoryFromRB;
use RedBeanPHP\OODBBean;

class ${entity_name}RepositoryFromRB extends RepositoryFromRB implements ${entity_name}Repository {
    protected static array \$tablesByEntity = [
        ${entity_name}::class => "rb${entity_name,,}"
    ];

    public function __construct(\PDO \$dbConnection = null) {
        parent::__construct(\$dbConnection);
        
        \$this->registerEntity(
            ${entity_name}::class,
            "rb${entity_name,,}",
            function(${entity_name} \$entity, OODBBean &\$bean): void {
                \$bean->sys_id_ = \$entity->getId();
                \$bean->name = \$entity->getName();
            },
            function(OODBBean \$bean): ${entity_name} {
                return new ${entity_name}(
                    \$bean->sys_id_,
                    \$bean->name
                );
            }
        );
    }

    public function add${entity_name}(${entity_name} \$${entity_name,,}): void {
        \$this->saveEntity(\$${entity_name,,});
    }

    public function find${entity_name}ById(string \$id): ?${entity_name} {
        return \$this->findEntityById(${entity_name}::class, \$id);
    }

    public function retrieveAll${entity_name}s(): array {
        return \$this->retrieveAllEntities(${entity_name}::class);
    }

    public function update${entity_name}(${entity_name} \$${entity_name,,}): void {
        \$this->updateEntity(\$${entity_name,,});
    }

    public function remove${entity_name}(string \$id): void {
        \$this->removeEntity(${entity_name}::class, \$id);
    }
}
EOL
}


create_api_controller() {
    local entity_name=$1
    local controller_dir="modules/Series/Infrastructure/APIControllers"
    mkdir -p "$controller_dir"
    
    cat <<EOL > "${controller_dir}/${entity_name}Controller.php"
<?php namespace Kirari\Series\Infrastructure\APIControllers;

use Kirari\Series\Application\Add${entity_name}\Manager as Add${entity_name}Manager;
use Kirari\Series\Application\Get${entity_name}Details\Manager as Get${entity_name}Manager;
use Kirari\Series\Application\Get${entity_name}Details\\${entity_name}Details;
use Kirari\Series\Application\Update${entity_name}\Manager as Update${entity_name}Manager;
use Kirari\Series\Application\Delete${entity_name}\Manager as Delete${entity_name}Manager;
use Kirari\Series\Application\List${entity_name}\Manager as List${entity_name}Manager;
use Kirari\Series\Application\List${entity_name}\\${entity_name}List;

use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Auth\Roles;
use Robust\Boilerplate\HTTP\API\DefaultController;
use Robust\Boilerplate\HTTP\RCODES;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\Infrastructure\Provider;

class ${entity_name}Controller extends DefaultController {
    public static function register(): void {
        static::executeAuthenticated(
            managedUseCase: fn() => (new Add${entity_name}Manager(
                Provider::requestEntity(IdGenerator::class),
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'string' => RCODES::Created ],

            authorizedRoles: [
                Roles::Administrator,
                Roles::Manager
            ]
        );
    }

    public static function index(): void {
        static::executeAsGuest(
            managedUseCase: fn() => (new List${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(),

            resultCodes: [ ${entity_name}List::class => RCODES::OK ]
        );
    }

    public static function show(string \$id): void {
        static::executeAsGuest(
            managedUseCase: fn() => (new Get${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id),

            resultCodes: [ ${entity_name}Details::class => RCODES::OK ]
        );
    }

    public static function update(string \$id): void {
        static::executeAuthenticated(
            managedUseCase: fn() => (new Update${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id, ...static::parseJsonInputFromCurrentRequest()),

            resultCodes: [ 'void' => RCODES::NoContent ],

            authorizedRoles: [
                Roles::Administrator,
                Roles::Manager
            ]
        );
    }

    public static function delete(string \$id): void {
        static::executeAuthenticated(
            managedUseCase: fn() => (new Delete${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id),

            resultCodes: [ 'void' => RCODES::NoContent ],

            authorizedRoles: [
                Roles::Administrator,
                Roles::Manager
            ]
        );
    }
}
EOL
}

create_cli_controller() {
    local entity_name=$1
    local controller_dir="modules/Series/Infrastructure/CLIControllers"
    mkdir -p "$controller_dir"
    
    cat <<EOL > "${controller_dir}/${entity_name}Controller.php"
<?php namespace Kirari\Series\Infrastructure\CLIControllers;

use Kirari\Series\Application\Add${entity_name}\Manager as Add${entity_name}Manager;
use Kirari\Series\Application\Get${entity_name}Details\Manager as Get${entity_name}Manager;
use Kirari\Series\Application\Update${entity_name}\Manager as Update${entity_name}Manager;
use Kirari\Series\Application\Delete${entity_name}\Manager as Delete${entity_name}Manager;
use Kirari\Series\Application\List${entity_name}\Manager as List${entity_name}Manager;

use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\CLI\BaseController;
use Robust\Boilerplate\Infrastructure\Provider;

class ${entity_name}Controller extends BaseController {
    public static function register(): void {
        global \$moduleMatched;
        \$moduleMatched = true;
        \$options = static::parseOptions();

        try {
            \$new${entity_name}Id = (new Add${entity_name}Manager(
                Provider::requestEntity(IdGenerator::class),
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(...\$options);

            echo "${entity_name} added successfully with ID: \$new${entity_name}Id\n";

        } catch (\Exception \$e) {
            echo "Error: {\$e->getMessage()}\n";
        }
    }

    public static function list(): void {
        global \$moduleMatched;
        \$moduleMatched = true;

        try {
            \$list = (new List${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute();

            foreach (\$list as \$item) {
                echo "\$item->id | \$item->name\n";
            }

        } catch (\Exception \$e) {
            echo "Error: {\$e->getMessage()}\n";
        }
    }

    public static function show(): void {
        global \$moduleMatched;
        \$moduleMatched = true;

        \$id = static::parseOptions()['id'] ?? static::parsePositionalParams()[0] ?? null;

        if (!\$id) {
            \$id = readline("${entity_name} ID: ");
        }

        try {
            \$details = (new Get${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id);

            echo "\n\t\$details->name\n";

        } catch (\Exception \$e) {
            echo "Error: {\$e->getMessage()}\n";
        }
    }

    public static function update(): void {
        global \$moduleMatched;
        \$moduleMatched = true;

        \$id = static::parseOptions()['id'] ?? static::parsePositionalParams()[0] ?? null;

        if (!\$id) {
            \$id = readline("${entity_name} ID: ");
        }

        if (!\$options = static::parseOptions()) exit("Nothing to update.\n");

        try {
            (new Update${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id, ...\$options);

            echo "${entity_name} updated successfully.\n";

        } catch (\Exception \$e) {
            echo "Error: {\$e->getMessage()}\n";
        }
    }

    public static function delete(): void {
        global \$moduleMatched;
        \$moduleMatched = true;

        \$id = static::parseOptions()['id'] ?? static::parsePositionalParams()[0] ?? null;

        if (!\$id) {
            \$id = readline("${entity_name} ID: ");
        }

        try {
            (new Delete${entity_name}Manager(
                Provider::requestEntity(${entity_name}Repository::class)
            ))->execute(\$id);

            echo "${entity_name} deleted successfully.\n";

        } catch (\Exception \$e) {
            echo "Error: {\$e->getMessage()}\n";
        }
    }

    public static function unknown(): void {
        echo "Usage: ${entity_name,,} [add|ls|show|update|delete] [options]\n";
    }
}
EOL
}

create_use_case_managers() {
    local entity_name=$1

    # Create use case directories and their contents
    create_add_use_case "$entity_name"
    create_get_use_case "$entity_name"
    create_update_use_case "$entity_name"
    create_delete_use_case "$entity_name"
    create_list_use_case "$entity_name"
}

create_add_use_case() {
    local entity_name=$1
    local use_case_dir="modules/Series/Application/Add${entity_name}"
    mkdir -p "$use_case_dir"
    
    cat <<EOL > "${use_case_dir}/Manager.php"
<?php namespace Kirari\Series\Application\Add${entity_name};

use Kirari\Series\Domain\\${entity_name}\\${entity_name};
use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\IdGenerator;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class Manager {
    public function __construct(
        private IdGenerator \$idGenerator,
        private ${entity_name}Repository \$repository
    ) { }

    public function execute(string \$name): string {
        try {
            \$id = \$this->idGenerator->nextForClass(${entity_name}::class);
            \$entity = new ${entity_name}(\$id, \$name);
            \$this->repository->add${entity_name}(\$entity);
            return \$id;
            
        } catch (\InvalidArgumentException \$e) {
            throw new UseCaseException(
                message: \$e->getMessage(),
                code: UseCaseException::\$INVALID_PARAMETER
            );
        }
    }
}
EOL
}

create_get_use_case() {
    local entity_name=$1
    local use_case_dir="modules/Series/Application/Get${entity_name}Details"
    mkdir -p "$use_case_dir"
    
    # Create DTO
    cat <<EOL > "${use_case_dir}/${entity_name}Details.php"
<?php namespace Kirari\Series\Application\Get${entity_name}Details;

readonly class ${entity_name}Details {
    public function __construct(
        public string \$id,
        public string \$name
    ) { }
}
EOL

    # Create Manager
    cat <<EOL > "${use_case_dir}/Manager.php"
<?php namespace Kirari\Series\Application\Get${entity_name}Details;

use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class Manager {
    public function __construct(
        private ${entity_name}Repository \$repository
    ) { }

    public function execute(string \$id): ${entity_name}Details {
        if (!\$entity = \$this->repository->find${entity_name}ById(\$id)) {
            throw new UseCaseException(
                message: '${entity_name} not found',
                code: UseCaseException::\$ENTITY_NOT_FOUND
            );
        }

        return new ${entity_name}Details(
            \$entity->getId(),
            \$entity->getName()
        );
    }
}
EOL
}

create_update_use_case() {
    local entity_name=$1
    local use_case_dir="modules/Series/Application/Update${entity_name}"
    mkdir -p "$use_case_dir"
    
    cat <<EOL > "${use_case_dir}/Manager.php"
<?php namespace Kirari\Series\Application\Update${entity_name};

use Kirari\Series\Domain\\${entity_name}\\${entity_name};
use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class Manager {
    public function __construct(
        private ${entity_name}Repository \$repository
    ) { }

    public function execute(
        string \$id,
        string \$name
    ): void {
        if (!\$entity = \$this->repository->find${entity_name}ById(\$id)) {
            throw new UseCaseException(
                message: '${entity_name} not found',
                code: UseCaseException::\$ENTITY_NOT_FOUND
            );
        }

        try {
            \$updatedEntity = new ${entity_name}(\$id, \$name);
            \$this->repository->update${entity_name}(\$updatedEntity);
            
        } catch (\InvalidArgumentException \$e) {
            throw new UseCaseException(
                message: \$e->getMessage(),
                code: UseCaseException::\$INVALID_PARAMETER
            );
        }
    }
}
EOL
}

create_delete_use_case() {
    local entity_name=$1
    local use_case_dir="modules/Series/Application/Delete${entity_name}"
    mkdir -p "$use_case_dir"
    
    cat <<EOL > "${use_case_dir}/Manager.php"
<?php namespace Kirari\Series\Application\Delete${entity_name};

use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;
use Robust\Boilerplate\UseCase\UseCaseException;

readonly class Manager {
    public function __construct(
        private ${entity_name}Repository \$repository
    ) { }

    public function execute(string \$id): void {
        if (!\$this->repository->find${entity_name}ById(\$id)) {
            throw new UseCaseException(
                message: '${entity_name} not found',
                code: UseCaseException::\$ENTITY_NOT_FOUND
            );
        }

        \$this->repository->remove${entity_name}(\$id);
    }
}
EOL
}

create_list_use_case() {
    local entity_name=$1
    local use_case_dir="modules/Series/Application/List${entity_name}"
    mkdir -p "$use_case_dir"
    
    # Create List DTO
    cat <<EOL > "${use_case_dir}/${entity_name}List.php"
<?php namespace Kirari\Series\Application\List${entity_name};

use Robust\Boilerplate\UseCase\TypedArrayDTO;

class ${entity_name}List extends TypedArrayDTO {
    public function offsetSet(\$offset, \$value): void {
        if (\$value instanceof ${entity_name}ListEntry) {
            parent::offsetSet(\$offset, \$value);
        } else {
            throw new \InvalidArgumentException('Value must be a ${entity_name}ListEntry');
        }
    }

    public function offsetGet(\$offset): ${entity_name}ListEntry {
        return parent::offsetGet(\$offset);
    }

    public function current(): ${entity_name}ListEntry {
        return parent::current();
    }
}

readonly class ${entity_name}ListEntry {
    public function __construct(
        public string \$id,
        public string \$name
    ) { }
}
EOL

    # Create Manager
    cat <<EOL > "${use_case_dir}/Manager.php"
<?php namespace Kirari\Series\Application\List${entity_name};

use Kirari\Series\Domain\\${entity_name}\\${entity_name}Repository;

readonly class Manager {
    public function __construct(
        private ${entity_name}Repository \$repository
    ) { }

    public function execute(): ${entity_name}List {
        \$entities = \$this->repository->retrieveAll${entity_name}s();
        \$list = new ${entity_name}List();

        foreach (\$entities as \$entity) {
            \$list[] = new ${entity_name}ListEntry(
                \$entity->getId(),
                \$entity->getName()
            );
        }

        return \$list;
    }
}
EOL
}

# Add functions to parse and inject into existing files

inject_repository_interface() {
    local entity_name=$1
    local repo_file=$2

    # Check if file exists
    if [ ! -f "$repo_file" ]; then
        echo "Repository interface file not found: $repo_file"
        exit 1
    fi

    # Add new methods before the last closing brace
    sed -i "$ i \    public function add${entity_name}(${entity_name} \$${entity_name,,}): void;\n\
    public function find${entity_name}ById(string \$id): ?${entity_name};\n\
    public function retrieveAll${entity_name}s(): array;\n\
    public function update${entity_name}(${entity_name} \$${entity_name,,}): void;\n\
    public function remove${entity_name}(string \$id): void;\n" "$repo_file"
}

inject_repository_implementation() {
    local entity_name=$1 
    local impl_file=$2

    if [ ! -f "$impl_file" ]; then
        echo "Repository implementation file not found: $impl_file"
        exit 1
    fi

    # Add table mapping to tablesByEntity array
    sed -i "/protected static array \$tablesByEntity = \[/a \        ${entity_name}::class => \"rb${entity_name,,}\"," "$impl_file"

    # Add entity registration in constructor
    sed -i "/parent::__construct/a \\\n        \$this->registerEntity(\n            ${entity_name}::class,\n            \"rb${entity_name,,}\",\n            function(${entity_name} \$entity, OODBBean &\$bean): void {\n                \$bean->sys_id_ = \$entity->getId();\n                \$bean->name = \$entity->getName();\n            },\n            function(OODBBean \$bean): ${entity_name} {\n                return new ${entity_name}(\n                    \$bean->sys_id_,\n                    \$bean->name\n                );\n            }\n        );" "$impl_file"

    # Add CRUD methods before the last closing brace
    sed -i "$ i \    public function add${entity_name}(${entity_name} \$${entity_name,,}): void {\n\
        \$this->saveEntity(\$${entity_name,,});\n\
    }\n\n\
    public function find${entity_name}ById(string \$id): ?${entity_name} {\n\
        return \$this->findEntityById(${entity_name}::class, \$id);\n\
    }\n\n\
    public function retrieveAll${entity_name}s(): array {\n\
        return \$this->retrieveAllEntities(${entity_name}::class);\n\
    }\n\n\
    public function update${entity_name}(${entity_name} \$${entity_name,,}): void {\n\
        \$this->updateEntity(\$${entity_name,,});\n\
    }\n\n\
    public function remove${entity_name}(string \$id): void {\n\
        \$this->removeEntity(${entity_name}::class, \$id);\n\
    }\n" "$impl_file"
}

# Update main execution to handle repository parameter

# Main script execution
if [ -z "$1" ]; then
    echo "Usage: $0 <EntityName> [--repository=<repository_interface_path>] [--implementation=<repository_implementation_path>]"
    exit 1
fi

entity_name=$1
repo_interface=""
repo_implementation=""

# Parse command line arguments
shift
while (( "$#" )); do
    case "$1" in
        --repository=*)
            repo_interface="${1#*=}"
            shift
            ;;
        --implementation=*)
            repo_implementation="${1#*=}"
            shift
            ;;
        *)
            echo "Unknown parameter: $1"
            exit 1
            ;;
    esac
done

# Create domain entity
create_domain_entity "$entity_name"

# Handle repository creation/injection
if [ -n "$repo_interface" ] && [ -n "$repo_implementation" ]; then
    echo "Adding $entity_name to existing repository..."
    inject_repository_interface "$entity_name" "$repo_interface"
    inject_repository_implementation "$entity_name" "$repo_implementation"
else
    echo "Creating new repository for $entity_name..."
    create_repository_interface "$entity_name"
    create_repository_implementation "$entity_name"
fi

# Create use cases and controllers
create_use_case_managers "$entity_name"
create_api_controller "$entity_name"
create_cli_controller "$entity_name"

echo "Generated entity $entity_name with CRUD operations"

exit 0