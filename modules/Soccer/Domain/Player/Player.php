<?php namespace App\Soccer\Domain\Player;

class Player {
    public function __construct(
        private string $id,
        private string $name,
        private string $lastName
    ) {}

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getLastName(): string { return $this->lastName; }
}