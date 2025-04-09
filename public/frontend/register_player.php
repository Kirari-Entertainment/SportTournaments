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
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
        }
        .close {
            float: right;
            cursor: pointer;
            font-size: 1.5rem;
        }
        .player-actions {
            display: flex;
            gap: 10px;
        }
        #photoPreview img {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
        }
    </style>
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
                <option value="<?= htmlspecialchars($tournament->id) ?>" <?= $tournament->id == $tournamentId ? 'selected' : '' ?>>
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
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teamMemberships as $teamMembership): ?>
                <tr>
                    <td><?= htmlspecialchars($teamMembership->playerName) ?></td>
                    <td class="player-actions">
                        <button class="upload-photo-btn" data-player-id="<?= htmlspecialchars($teamMembership->playerId) ?>">Upload Photo</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Photo Upload Modal -->
    <div id="photoUploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Upload Player Photo</h2>
            <form id="photoUploadForm">
                <input type="hidden" id="playerIdInput" name="playerId">
                <label for="photoFile">Select Image</label>
                <input type="file" id="photoFile" accept="image/*" required>
                <div id="photoPreview"></div>
                <button type="button" id="uploadButton">Upload</button>
            </form>
        </div>
    </div>
</main>

<script>
    // Modal functionality
    const modal = document.getElementById('photoUploadModal');
    const closeBtn = document.getElementsByClassName('close')[0];
    const uploadButtons = document.getElementsByClassName('upload-photo-btn');
    const playerIdInput = document.getElementById('playerIdInput');
    const photoInput = document.getElementById('photoFile');
    const photoPreview = document.getElementById('photoPreview');
    const uploadButton = document.getElementById('uploadButton');
    
    // Open modal when upload button is clicked
    for (let btn of uploadButtons) {
        btn.addEventListener('click', function() {
            const playerId = this.dataset.playerId;
            playerIdInput.value = playerId;
            modal.style.display = 'block';
            photoPreview.innerHTML = '';
        });
    }
    
    // Close modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal if clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Preview selected image
    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.innerHTML = `<img src="${e.target.result}">`;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Handle binary upload
    uploadButton.addEventListener('click', function() {
        if (!photoInput.files || !photoInput.files[0]) {
            alert('Please select an image file');
            return;
        }
        
        const playerId = playerIdInput.value;
        const file = photoInput.files[0];
        
        // Read file as binary data
        const reader = new FileReader();
        reader.onload = function(e) {
            const binaryData = e.target.result;
            
            // Send binary data directly in request body
            fetch(`/soccer/player/${playerId}/picture`, {
                method: 'POST',
                headers: {
                    'Content-Type': file.type
                },
                body: binaryData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                alert('Photo uploaded successfully!');
                modal.style.display = 'none';
            })
            .catch(error => {
                console.error('Error uploading photo:', error);
                alert('Failed to upload photo. Please try again.');
            });
        };
        
        // Read the file as an ArrayBuffer (binary data)
        reader.readAsArrayBuffer(file);
    });
</script>
</body>
</html>