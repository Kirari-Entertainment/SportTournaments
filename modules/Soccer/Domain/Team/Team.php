<?php namespace App\Soccer\Domain\Team;

class Team {
    public function __construct(
        private string $id,
        private string $name,
        private ?string $captainId = null
        ) {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('Id cannot be empty');
        }

        if (empty($this->name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }
    }

    public function getId() : string { return $this->id; }
    public function getName() : string { return $this->name; }
}