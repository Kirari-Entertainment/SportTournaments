<?php namespace Robust\Boilerplate\Infrastructure;

use Robust\Boilerplate\IdGenerator;

/**
 * Genera identificadores únicos utilizando UUIDs sin librerías externas
 */
class UuidGenerator implements IdGenerator {

    public function nextUniversal() : string {
        return $this->nextForClass('universal');
    }

    public function nextForClass(string $className) : string {
        return $this->generateUuid();
    }

    public function getNForClass(int $n, string $className) : array {
        $uuids = [];
        for ($i = 0; $i < $n; $i++) {
            $uuids[] = $this->nextForClass($className);
        }
        return $uuids;
    }

    private function generateUuid() : string {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
