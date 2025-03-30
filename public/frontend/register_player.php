<?php


$recordsBook = \Robust\Boilerplate\Infrastructure\Provider::requestEntity(
    \App\Soccer\Domain\RecordsBook::class
);

$allTournament = (new \App\Soccer\Application\Tournaments\List\ListTournaments(
    $recordsBook
))->execute();

$tournamentId = $_GET['tournamentId'] ?? $allTournament[0]->id ?? null;

$teamsInTournament = $tournamentId ? (new \App\Soccer\Application\Tournaments\ListRegisteredTeams\ListRegisteredTeams(
    $recordsBook
))->execute($tournamentId) : [];

$teamId = $_GET['teamId'] ?? $teamsInTournament[0]->id ?? null;

$teamMemberships = $teamId ? (new \App\Soccer\Application\Tournaments\ListTeamMemberships\ListTeamMemberships(
    $recordsBook
))->execute($tournamentId, $teamId) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playerName = $_POST['playerName'];
    $playerLastName = $_POST['playerLastName'];

    (new \App\Soccer\Application\Tournaments\RegisterTeamMembership\RegisterTeamMembershipOfNewPlayer(
        \Robust\Boilerplate\Infrastructure\Provider::requestEntity(\Robust\Boilerplate\IdGenerator::class, ['type' => 'uuid']),
        $recordsBook
    ))->execute($tournamentId, $teamId, $playerName, $playerLastName);

    header('Location: ?tournamentId=' . $tournamentId . '&teamId=' . $teamId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css"
    >
    <title>All Teams</title>
</head>
<body>
<header>
    <h1>Teams Players' Registration for Tournament</h1>
</header>
<main>
    <form method="post">
        <label for="tournament">Tournament</label>
        <select name="tournament" id="tournament" onchange="location.href='?tournamentId=' + this.value">
            <?php foreach ($allTournament as $tournament): ?>
                <option value="<?= htmlspecialchars($tournament->id) ?>">
                    <?= htmlspecialchars($tournament->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="team">Team</label>
        <select name="team" id="team" onchange="location.href='?tournamentId=<?= htmlspecialchars($tournamentId) ?>&teamId=' + this.value">
            <?php foreach ($teamsInTournament as $team): ?>
                <option value="<?= htmlspecialchars($team->id) ?>" <?= $team->id == $teamId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="playerName">Player Name</label>
        <input type="text" name="playerName" id="playerName">
        <label for="playerLastName">Player Last Name</label>
        <input type="text" name="playerLastName" id="playerLastName">
        <button type="submit">Register Player</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Players in Team</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teamMemberships as $teamMembership): ?>
                <tr>
                    <td><?= htmlspecialchars($teamMembership->playerName) ?></td>
                </tr>
            <?php endforeach; ?>
    </table>
</main>
</body>
</html>