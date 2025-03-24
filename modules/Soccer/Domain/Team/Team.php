<?php namespace App\Soccer\Domain\Team;

class Team {
    private string $id;
    private string $name;

    public function __construct(string $id, string $name) {
        $this->id = $id;
        $this->name = $name;

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