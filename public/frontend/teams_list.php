<?php

use App\Soccer\Application\Tournaments\List\ListTournaments as TournamentsListManager;
use App\Soccer\Application\Teams\List\ListTeams as TeamsListManager;
use App\Soccer\Application\Tournaments\ListRegisteredTeams\ListRegisteredTeams;
use App\Soccer\Domain\RecordsBook;
use Robust\Boilerplate\Infrastructure\Provider;

$records = Provider::requestEntity(RecordsBook::class);

$tournamentsInteractor = new TournamentsListManager(
    $records
);

$teamsInteractor = new TeamsListManager(
    $records
);

$teamsInTournamentInteractor = new ListRegisteredTeams($records);

$teamsMembershipsInteractor = new \App\Soccer\Application\Tournaments\ListTeamMemberships\ListTeamMemberships($records);

$allTournaments = $tournamentsInteractor->execute();
$allTeams = $teamsInteractor->execute();

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
        <h1>All Data</h1>
    </header>
    <main>
        <section>
            <h2>Tournaments</h2>
            <?php foreach ($allTournaments as $tournament): ?>
                <article>
                    <header>
                        <h3><?= htmlspecialchars($tournament->name) ?></h3>
                    </header>
                    <strong>Tournament ID:</strong> <?= htmlspecialchars($tournament->id) ?>
                </article>
            <?php endforeach; ?>
        </section>

        <section>
            <h2>Teams</h2>
            <?php foreach ($allTeams as $team): ?>
                <article>
                    <header>
                        <h3><?= htmlspecialchars($team->name) ?></h3>
                    </header>
                    <strong>Team ID:</strong> <?= htmlspecialchars($team->id) ?>
                </article>
            <?php endforeach; ?>
        </section>


        <section>
            <h2>Teams registed for tournament</h2>
            <?php foreach ($allTournaments as $tournament): ?>
                <article>
                    <header>
                        <h3>
                        <?= htmlspecialchars($tournament->name) ?>
                        </h3>
                    </header>
                    <main>
                        <?php foreach ($teamsInTournamentInteractor->execute($tournament->id) as $team): ?>
                            <table>
                                <thead>
                                    <tr><th><?= htmlspecialchars($team->name) ?></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teamsMembershipsInteractor->execute($tournament->id, $team->id) as $player): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($player->playerName) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    </main>

                </article>
            <?php endforeach; ?>
        </section>

    </main>
</body>
</html>