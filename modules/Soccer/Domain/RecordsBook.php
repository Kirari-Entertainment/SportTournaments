<?php namespace App\Soccer\Domain;

use App\Soccer\Domain\Team\Team;
use App\Soccer\Domain\Player\Player;
use App\Soccer\Domain\Player\TeamMembership;
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
}