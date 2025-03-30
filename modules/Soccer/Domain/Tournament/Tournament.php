<?php namespace App\Soccer\Domain\Tournament;

use App\Soccer\Domain\Team\Team;
use DateTime;

class Tournament {
    private array $registeredTeams = [];

    public function __construct(
        private string $id,
        private string $name,
        private string $description,
        private DateTime $startDate,
        private DateTime $endDate,
        private DateTime $inscriptionStartDate,
        private DateTime $inscriptionEndDate,
        ?array $alreadyRegisteredTeams = null
    ) {
        if (empty($this->id)) {
            throw new \InvalidArgumentException('ID cannot be empty');
        }

        if (empty($this->name)) {
            throw new \InvalidArgumentException('Name cannot be empty');
        }

        if (empty($this->description)) {
            throw new \InvalidArgumentException('Description cannot be empty');
        }

        if ($this->endDate < $this->startDate) {
            throw new \InvalidArgumentException('End date cannot be before start date');
        }

        if ($this->inscriptionEndDate < $this->inscriptionStartDate) {
            throw new \InvalidArgumentException('Inscription end date cannot be before inscription start date');
        }

        if ($alreadyRegisteredTeams) {
            foreach ($alreadyRegisteredTeams as $team) {
                if (!$team instanceof Team) {
                    throw new \InvalidArgumentException('Registered teams must be of type Team');
                }
            }
            $this->registeredTeams = $alreadyRegisteredTeams;
        }
    }

    public function getId() : string { return $this->id; }
    public function getName() : string { return $this->name; }
    public function getDescription() : string { return $this->description; }
    public function getStartDate() : DateTime { return $this->startDate; }
    public function getEndDate() : DateTime { return $this->endDate; }
    public function getInscriptionStartDate() : DateTime { return $this->inscriptionStartDate; }
    public function getInscriptionEndDate() : DateTime { return $this->inscriptionEndDate; }

    public function isOngoing() : bool {
        $now = new DateTime();
        return $this->startDate <= $now && $now <= $this->endDate;
    }

    public function isInscribable() : bool {
        $now = new DateTime();
        return $this->inscriptionStartDate <= $now && $now <= $this->inscriptionEndDate;
    }

    public function registerTeam(Team $team) : void {
        if ($this->isInscribable()) {
            $this->registeredTeams[] = $team;
        } else {
            throw new \InvalidArgumentException('Tournament is not inscribable');
        }
    }

    public function getRegisteredTeams() : array {
        return $this->registeredTeams;
    }
}