<?php
    $league = FSPA\Model\League::fetchByID($_GET['leagueID']);
    $stats = new FSPA\Stats();
    $league_stats = $stats->fetchStats([ 'leagueID' => $_GET['leagueID'] ]);

    # If we didn't match a League, then this is an invalid leagueID
    if ($league_stats['leagues'] != 1) {
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
    <h2>League Stats for <?php echo $league->getName() ?></h2>

    <p>Tracking the following items from <?php echo $league_stats['oldest_date'] ?> to <?php echo $league_stats['newest_date'] ?>:</p>

    <ul>
        <li><?php echo $league_stats['seasons'] ?> Seasons</li>
        <li><?php echo $league_stats['players'] ?> Players</li>
        <li><?php echo $league_stats['machines'] ?> Machines across <?php echo $league_stats['locations'] ?> Locations</li>
        <li><?php echo $league_stats['meets'] ?> Meets</li>
        <li><?php echo $league_stats['groups'] ?> Groups</li>
        <li><?php echo $league_stats['games'] ?> Games</li>
        <li><?php echo $league_stats['scores'] ?> Scores</li>
    </ul>

    <p> A total of <b><?php echo number_format($league_stats['total_score']) ?></b> points have been scored by all players in this league! Wow!</p>
</body>
</html>

