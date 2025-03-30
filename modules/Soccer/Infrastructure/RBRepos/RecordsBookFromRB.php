<?php namespace App\Soccer\Infrastructure\RBRepos;

use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Player\TeamMembership;
use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\Tournament;
use DateTime;
use R;
use RedBeanPHP\OODBBean;
use Robust\Boilerplate\Infrastructure\RepositoryFromRB;

class RecordsBookFromRB extends RepositoryFromRB implements RecordsBook {
    protected function initializeEntities(): void {
        $this->registerEntity(
            entityClass: Team::class,
            tableName: 'rbteam',

            parseToBean: function(Team $entity, OODBBean &$bean) : void {
                $bean->sys_id_ = $entity->getId();
                $bean->name = $entity->getName();
            },

            parseFromBean: function(OODBBean $bean) : Team {
                return new Team(
                    $bean->sys_id_,
                    $bean->name
                );
            }
        );

        $this->registerEntity(
            entityClass: Player::class,
            tableName: 'rbplayer',

            parseToBean: function(Player $player, OODBBean &$bean) : void {
                $bean->sys_id_ = $player->getId();
                $bean->name = $player->getName();
                $bean->last_name = $player->getLastName();
            },

            parseFromBean: function(OODBBean $bean) : Player {
                return new Player(
                    $bean->sys_id_,
                    $bean->name,
                    $bean->last_name
                );
            }
        );

        $this->registerEntity(
            entityClass: Tournament::class,
            tableName: 'rbtournament',

            parseToBean: function(Tournament $tournament, OODBBean &$bean) : void {
                $bean->sys_id_ = $tournament->getId();
                $bean->name = $tournament->getName();
                $bean->description = $tournament->getDescription();
                $bean->startDate = $tournament->getStartDate()->format('Y-m-d H:i:s');
                $bean->endDate = $tournament->getEndDate()->format('Y-m-d H:i:s');
                $bean->inscriptionStartDate = $tournament->getInscriptionStartDate()->format('Y-m-d H:i:s');
                $bean->inscriptonEndDate = $tournament->getInscriptionEndDate()->format('Y-m-d H:i:s');

                foreach ($tournament->getRegisteredTeams() as $team) {
                    $bean->sharedRbteamList[] = static::findBeanBySystemId(
                        Team::class,
                        $team->getId()
                    );
                }
            },

            parseFromBean: function(OODBBean $bean) : Tournament {
                $alreadyRegisteredTeams = array_map(
                    function(OODBBean $teamBean) {
                        return $this->parseFromBeanByEntity[Team::class]($teamBean);
                    },
                    $bean->sharedRbteamList
                );

                return new Tournament(
                    $bean->sys_id_,
                    $bean->name,
                    $bean->description,
                    new DateTime($bean->startDate),
                    new DateTime($bean->endDate),
                    new DateTime($bean->inscriptionStartDate),
                    new DateTime($bean->inscriptonEndDate),
                    $alreadyRegisteredTeams
                );
            }
        );

        $this->registerEntity(
            entityClass: TeamMembership::class,
            tableName: 'rbteammembership',

            parseToBean: function(TeamMembership $teamMembership, OODBBean &$bean) : void {
                $bean->sys_id_ = $teamMembership->getId();
                $bean->player = static::findBeanBySystemId(
                    Player::class,
                    $teamMembership->getPlayer()->getId()
                );
                $bean->team = static::findBeanBySystemId(
                    Team::class,
                    $teamMembership->getTeam()->getId()
                );
                $bean->tournament = static::findBeanBySystemId(
                    Tournament::class,
                    $teamMembership->getTournament()->getId()
                );
            },

            parseFromBean: function(OODBBean $bean) : TeamMembership {
                return new TeamMembership(
                    $bean->sys_id_,
                    $this->parseFromBeanByEntity[Player::class]($bean->player),
                    $this->parseFromBeanByEntity[Team::class]($bean->team),
                    $this->parseFromBeanByEntity[Tournament::class]($bean->tournament)
                );
            }
        );
    }

    #region Team
    public function registerTeam(Team $team): void { $this->saveEntity($team); }
    public function retrieveAllTeams() : array { return $this->retrieveAllEntities(Team::class); }
    public function findTeam(string $id): Team { return $this->findEntityById(Team::class, $id); }
    public function updateTeam(Team $team): void { $this->updateEntity($team); }
    public function deleteTeam(string $id): void { $this->removeEntity(Team::class, $id); }

    #region Player
    public function registerPlayer(Player $player) : void { $this->saveEntity($player); }
    public function retrieveAllPlayers() : array {return $this->retrieveAllEntities(Player::class); }
    public function findPlayer(string $id): Player { return $this->findEntityById(Player::class, $id); }
    public function updatePlayer(Player $player): void { $this->updateEntity($player); }
    public function deletePlayer(string $id): void { $this->removeEntity(Player::class, $id); }

    #region Tournament
    public function registerTournament(Tournament $tournament): void { $this->saveEntity($tournament); }
    public function retrieveAllTournaments() : array { return $this->retrieveAllEntities(Tournament::class); }
    public function findTournament(string $id): Tournament { return $this->findEntityById(Tournament::class, $id); }
    public function updateTournament(Tournament $tournament): void { $this->updateEntity($tournament); }
    public function deleteTournament(string $id): void { $this->removeEntity(Tournament::class, $id); }

    #region TeamMembership
    public function registerTeamMembership(TeamMembership $teamMembership): void { $this->saveEntity($teamMembership); }
    public function retrieveAllTeamMemberships() : array { return $this->retrieveAllEntities(TeamMembership::class); }
    public function findTeamMembership(string $id): TeamMembership { return $this->findEntityById(TeamMembership::class, $id); }
    public function updateTeamMembership(TeamMembership $teamMembership): void { $this->updateEntity($teamMembership); }
    public function deleteTeamMembership(string $id): void { $this->removeEntity(TeamMembership::class, $id); }

    public function retrieveTeamMembershipsByPlayer(string $playerId): array {
        $teamMemberships = [];

        return $teamMemberships;
    }

    public function retrieveTeamMembershipsByTeam(string $teamId): array {
        $teamMemberships = [];

        return $teamMemberships;
    }

    public function retrieveTeamMembershipsByTournament(string $tournamentId): array {
        $teamMemberships = [];

        return $teamMemberships;
    }
}