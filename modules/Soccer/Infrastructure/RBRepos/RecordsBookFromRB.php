<?php namespace App\Soccer\Infrastructure\RBRepos;

use App\Soccer\Domain\Game\Game;
use App\Soccer\Domain\Game\GameStatus;
use App\Soccer\Domain\Game\Goal;
use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\RecordsBook;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\TeamMembership;
use App\Soccer\Domain\Tournament\Tournament;
use DateTime;
use RedBeanPHP\OODBBean;
use Robust\Boilerplate\Infrastructure\RepositoryFromRB;
use R;

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
                if (empty($bean->sys_id_)) print_r($bean);
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
                    $this->parseFromBeanByEntity[Tournament::class]($bean->tournament),
                    $this->parseFromBeanByEntity[Team::class]($bean->team),
                    $this->parseFromBeanByEntity[Player::class]($bean->player),
                );
            }
        );

        $this->registerEntity(
            entityClass: Game::class,
            tableName: 'rbgame',

            parseToBean: function(Game $game, OODBBean &$bean) : void {
                $bean->sys_id_ = $game->getId();
                $bean->scheduledFor = $game->getScheduledFor()->format('Y-m-d H:i:s');
                
                $bean->tournament = static::findBeanBySystemId(
                    Tournament::class,
                    $game->getTournament()->getId()
                );

                $bean->teamA = static::findBeanBySystemId(
                    Team::class,
                    $game->getTeamA()->getId()
                );
                
                $bean->teamB = static::findBeanBySystemId(
                    Team::class,
                    $game->getTeamB()->getId()
                );
                
                $bean->status = $game->getStatus()->value;

                $teamAGoalsData = [];
                foreach ($game->getTeamAGoals() as $goal) {
                    $teamAGoalsData[] = [
                        'playerId' => $goal->getPlayer()->getId(),
                        'timestamp' => $goal->getScoredAt()->format('Y-m-d H:i:s')
                    ];
                }
                $teamBGoalsData = [];
                foreach ($game->getTeamBGoals() as $goal) {
                    $teamBGoalsData[] = [
                        'playerId' => $goal->getPlayer()->getId(),
                        'timestamp' => $goal->getScoredAt()->format('Y-m-d H:i:s')
                    ];
                }
                $bean->teamAGoals = json_encode($teamAGoalsData);
                $bean->teamBGoals = json_encode($teamBGoalsData);
            },

            parseFromBean: function(OODBBean $bean) : Game {
                $tournament = $this->parseFromBeanByEntity[Tournament::class]($bean->tournament);
                $teamA = $this->parseFromBeanByEntity[Team::class]($bean->teamA);
                $teamB = $this->parseFromBeanByEntity[Team::class]($bean->teamB);

                $teamAGoals = [];
                if (!empty($bean->teamAGoals)) {
                    $goalsData = json_decode($bean->teamAGoals, true);
                    foreach ($goalsData as $goalData) {
                        $player = $this->findPlayer($goalData['playerId']);
                        $teamAGoals[] = new Goal(
                            $player,
                            new \DateTime($goalData['timestamp'])
                        );
                    }
                }
                
                $teamBGoals = [];
                if (!empty($bean->team_b_goals)) {
                    $goalsData = json_decode($bean->team_b_goals, true);
                    foreach ($goalsData as $goalData) {
                        $player = $this->findPlayer($goalData['playerId']);
                        $teamBGoals[] = new Goal(
                            $player,
                            new \DateTime($goalData['timestamp'])
                        );
                    }
                }

                return Game::createFromHistoricalData(
                    $bean->sys_id_,
                    $tournament,
                    new DateTime($bean->scheduledFor),
                    $teamA,
                    $teamB,
                    GameStatus::from($bean->status),
                    $teamAGoals,
                    $teamBGoals
                );
            }
        );

        R::aliases([
            'tournament' => static::$tablesByEntity[Tournament::class],
            'team' => static::$tablesByEntity[Team::class],
            'player' => static::$tablesByEntity[Player::class],
            'team_a' => static::$tablesByEntity[Team::class],
            'team_b' => static::$tablesByEntity[Team::class],
        ]);
    }

    #region Team
    public function registerTeam(Team $team): void { $this->saveEntity($team); }
    public function retrieveAllTeams() : array { return $this->retrieveAllEntities(Team::class); }
    public function findTeam(string $id): ?Team { return $this->findEntityById(Team::class, $id); }
    public function updateTeam(Team $team): void { $this->updateEntity($team); }
    public function deleteTeam(string $id): void { $this->removeEntity(Team::class, $id); }

    #region Player
    public function registerPlayer(Player $player) : void { $this->saveEntity($player); }
    public function retrieveAllPlayers() : array {return $this->retrieveAllEntities(Player::class); }
    public function findPlayer(string $id): ?Player { return $this->findEntityById(Player::class, $id); }
    public function updatePlayer(Player $player): void { $this->updateEntity($player); }
    public function deletePlayer(string $id): void { $this->removeEntity(Player::class, $id); }

    #region Tournament
    public function registerTournament(Tournament $tournament): void { $this->saveEntity($tournament); }
    public function retrieveAllTournaments() : array { return $this->retrieveAllEntities(Tournament::class); }
    public function findTournament(string $id): ?Tournament { return $this->findEntityById(Tournament::class, $id); }
    public function updateTournament(Tournament $tournament): void { $this->updateEntity($tournament); }
    public function deleteTournament(string $id): void { $this->removeEntity(Tournament::class, $id); }

    #region TeamMembership
    public function registerTeamMembership(TeamMembership $teamMembership): void { $this->saveEntity($teamMembership); }
    public function retrieveAllTeamMemberships() : array { return $this->retrieveAllEntities(TeamMembership::class); }
    public function findTeamMembership(string $id): ?TeamMembership { return $this->findEntityById(TeamMembership::class, $id); }
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

        $tournamentBean = $this->findBeanBySystemId(Tournament::class, $tournamentId);

        $allTeamMembershipsBeans = R::findAll(
            self::$tablesByEntity[TeamMembership::class],
            "tournament_id = ?",
            [ $tournamentBean?->id ]
        );

        foreach ($allTeamMembershipsBeans as $teamMembershipBean) {
            $teamMemberships[] = $this->parseFromBeanByEntity[TeamMembership::class]($teamMembershipBean);
        }

        return $teamMemberships;
    }

    #region Game
    public function registerGame(Game $game): void { $this->saveEntity($game); }
    public function retrieveAllGames() : array { return $this->retrieveAllEntities(Game::class); }
    public function findGame(string $id): ?Game { return $this->findEntityById(Game::class, $id); }
    public function updateGame(Game $game): void { $this->updateEntity($game); }
    public function deleteGame(string $id): void { $this->removeEntity(Game::class, $id); }

    // Implement the method
    public function retrieveGamesByTournamentAndStatus(string $tournamentId, GameStatus $status): array {
        $games = [];
        
        $tournamentBean = $this->findBeanBySystemId(Tournament::class, $tournamentId);
        
        if ($tournamentBean) {
            $gamesBeans = R::find(
                self::$tablesByEntity[Game::class],
                "tournament_id = ? AND status = ?",
                [$tournamentBean->id, $status->value]
            );
            
            foreach ($gamesBeans as $gameBean) {
                $games[] = $this->parseFromBeanByEntity[Game::class]($gameBean);
            }
        }
        
        return $games;
    }

}