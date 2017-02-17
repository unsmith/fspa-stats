<?php
    $player = FSPA\Model\Player::fetchByID($_GET['playerID']);
    $stats = new FSPA\Stats();
    $player_stats = $stats->fetchStats([ 'playerID' => $_GET['playerID'] ]);

    # If we didn't match a Player, then this is an invalid playerID
    if ($player_stats['players'] != 1) {
        header("Location: /");
        exit();
    }
?>
<html>
<head>
    <meta charset="utf-8" />
</head>
<body>
    <?php include("includes/menu.inc"); ?>
    <h2>Player Stats for <?php echo $player->getName() ?></h2>

    <p>Tracking the following items from <?php echo $player_stats['oldest_date'] ?> to <?php echo $player_stats['newest_date'] ?>:</p>

    <ul>
        <li><?php echo $player_stats['leagues'] ?> Leagues</li>
        <li><?php echo $player_stats['seasons'] ?> Seasons</li>
        <li><?php echo $player_stats['machines'] ?> Machines across <?php echo $player_stats['locations'] ?> Locations</li>
        <li><?php echo $player_stats['meets'] ?> Meets</li>
        <li><?php echo $player_stats['groups'] ?> Groups</li>
        <li><?php echo $player_stats['games'] ?> Games</li>
        <li><?php echo $player_stats['scores'] ?> Scores</li>
    </ul>

    <p> <?php echo $player->getFirstName() ?> has scored a total of <b><?php echo number_format($player_stats['total_score']) ?></b> points across all leagues! Wow!</p>
</body>
</html>

