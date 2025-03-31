<?php namespace Robust\Boilerplate\Infrastructure;

use App\Soccer\Domain\Player\ProfPicRegistry;
use App\Soccer\Domain\RecordsBook;
use App\Soccer\Infrastructure\ImageCatalogs\ProfPicRegistryAtFs;
use App\Soccer\Infrastructure\RBRepos\RecordsBookFromRB;
use Robust\Auth\CredentialManager;
use Robust\Auth\CredentialManagerFromDB;
use Robust\Auth\CredentialManagerFromDBAndJWT;
use Robust\Boilerplate\IdGenerator;

define ('providersList', [
    IdGenerator::class => function(string $origin = 'local', string $type = 'incremental'/*random*/) {
        if ($type == 'uuid') return UuidGenerator::class;

        if ($origin == 'local') {
            if ($type == 'incremental') return IntIdGeneratorFromDB::class;
            else return RandomIntIdGeneratorFromDB::class;

        } else /* if ($origin == 'cloud') */ {

        }
    },

    CredentialManager::class => function(string $userType = 'machine') {
        if ($userType == 'machine') return CredentialManagerFromDBAndJWT::class;
        else /* if ($userType = 'human') */ return CredentialManagerFromDB::class;
    },

    RecordsBook::class => function() {
        return RecordsBookFromRB::class;
    },

    ProfPicRegistry::class => function() {
        return ProfPicRegistryAtFs::class;
    }

]);
class Provider {
    public static function requestEntity(
        string $type,
        ?array $params = null
    ) : object {
        if (!array_key_exists($type, providersList)) throw new InfrastructureException("Service $type not registered");
        if ($params) return new (providersList[$type](...$params));
        return new (providersList[$type]());
    }
}
