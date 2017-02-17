<?php
    $stats = new FSPA\Stats();
    $assoc_stats = $stats->fetchStats();
    $leagues = FSPA\Model\League::fetchAll();
    $players = FSPA\Model\Player::fetchAll();
?>
<h1>FSPA Stat-O-Matic</h1>

<p>No real plan here as of yet, just some tinkering.</p>

<h2>Association Stats</h2>

<p>Tracking the following items from <?php echo $assoc_stats['oldest_date'] ?> to <?php echo $assoc_stats['newest_date'] ?>:</p>

<ul>
    <li><?php echo $assoc_stats['leagues'] ?> Leagues</li>
    <li><?php echo $assoc_stats['seasons'] ?> Seasons</li>
    <li><?php echo $assoc_stats['players'] ?> Players</li>
    <li><?php echo $assoc_stats['machines'] ?> Machines across <?php echo $assoc_stats['locations'] ?> Locations</li>
    <li><?php echo $assoc_stats['meets'] ?> Meets</li>
    <li><?php echo $assoc_stats['groups'] ?> Groups</li>
    <li><?php echo $assoc_stats['games'] ?> Games</li>
    <li><?php echo $assoc_stats['scores'] ?> Scores</li>
</ul>

<p> A total of <b><?php echo number_format($assoc_stats['total_score']) ?></b> points have been scored by all players! Wow!</p>

<h2>Leagues</h2>

<ul>
<?php foreach ($leagues as $league) { ?>
    <li><a href="/league.php?leagueID=<?php echo $league->getID() ?>"><?php echo $league->getName() ?></a></li>
<?php } ?>
</ul>

<h2>Players</h2>

<ul>
<?php foreach ($players as $player) { ?>
    <li><a href="/player.php?playerID=<?php echo $player->getID() ?>"><?php echo $player->getName() ?></a></li>
<?php } ?>
</ul>
