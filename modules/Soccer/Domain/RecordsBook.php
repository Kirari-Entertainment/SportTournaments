<?php namespace App\Soccer\Domain;

use App\Soccer\Domain\Game\Game;
use App\Soccer\Domain\Game\GameStatus;
use App\Soccer\Domain\Game\Goal;
use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Tournament\TeamMembership;
use App\Soccer\Domain\Tournament\Tournament;

interface RecordsBook {
    public function registerTeam(Team $team) : void;
    public function retrieveAllTeams() : array;
    public function findTeam(string $id) : ?Team;
    public function updateTeam(Team $team) : void;
    public function deleteTeam(string $id) : void;

    public function registerPlayer(Player $player) : void;
    public function retrieveAllPlayers() : array;
    public function findPlayer(string $id) : ?Player;
    public function updatePlayer(Player $player) : void;
    public function deletePlayer(string $id) : void;

    public function registerTournament(Tournament $tournament) : void;
    public function retrieveAllTournaments() : array;
    public function findTournament(string $id) : ?Tournament;
    public function updateTournament(Tournament $tournament) : void;
    public function deleteTournament(string $id) : void;

    public function registerTeamMembership(TeamMembership $teamMembership) : void;
    public function retrieveAllTeamMemberships() : array;
    public function findTeamMembership(string $id) : ?TeamMembership;
    public function updateTeamMembership(TeamMembership $teamMembership) : void;
    public function deleteTeamMembership(string $id) : void;
    public function retrieveTeamMembershipsByPlayer(string $playerId) : array;
    public function retrieveTeamMembershipsByTeam(string $teamId) : array;
    public function retrieveTeamMembershipsByTournament(string $tournamentId) : array;

    public function registerGame(Game $game) : void;
    public function retrieveAllGames() : array;
    public function findGame(string $id) : ?Game;
    public function updateGame(Game $game) : void;
    public function deleteGame(string $id) : void;
    public function retrieveGamesByTournamentAndStatus(string $tournamentId, GameStatus $status): array;

    public function annotateGoal(Goal $goal) : void;
    public function retrieveAllGoals() : array;
    public function retrieveAllGoalsByGame(string $gameId);
    public function retrieveAllGoalsByPlayer(string $playerId);
    public function retrieveAllGoalsInTournament(string $tournamentId);
    public function retrieveAllGoalsByPlayerInTournament(string $playerId, string $tournamentId);
}