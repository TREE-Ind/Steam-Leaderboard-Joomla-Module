<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


<div class="custom<?php echo $moduleclass_sfx; ?>" <?php if ($params->get('backgroundimage')) : ?> style="background-image:url(<?php echo $params->get('backgroundimage'); ?>)"<?php endif; ?> >

</div>

<?php
// ref: https://partner.steamgames.com/doc/webapi/ISteamLeaderboards
$cache_filename = "leaderboard_cache";
$default_leaderboard = "TotalBirdies";
if (!file_exists($cache_filename) || time() - filemtime($cache_filename) > 1)
{
    $max = 10; // how many displayed scores
    $key = $params->get('publisher_key'); // steam webapi key, to get it: https://partner.steamgames.com/doc/webapi_overview/auth#create_publisher_key
    $appid = $params->get('app_id'); // your game's app id
    $lb_name =  $params->get('lb_name');
    $lb_id = $params->get('lb_id');
	// the names and corresponding leaderboard IDs
	// get the leaderboard IDs with https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardsForGame/v2/
    $leaderboards = [ $lb_name => $lb_id ];

    foreach ($leaderboards as $name => $id)
    {
        // fetch scores
        $uri = "https://partner.steam-api.com/ISteamLeaderboards/GetLeaderboardEntries/v1/?key="
            .$key."&appid=".$appid."&rangestart=0&rangeend=".$max."&datarequest=0&leaderboardid=".$id;
        $data = file_get_contents($uri);
        $leaderboard = json_decode($data);
        $players = "";
        foreach ($leaderboard->leaderboardEntryInformation->leaderboardEntries as $entry)
            $players .= $entry->steamID.",";
        
        // fetch player names
        $uri = "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/?key=".$key."&steamids=".$players;
        $data = file_get_contents($uri);
        $players = json_decode($data);
        
        // add names to scores
        for ($i = 0; $i < $max; $i++)
        {
            $entry = $leaderboard->leaderboardEntryInformation->leaderboardEntries[$i];
            $personaname = "";
            foreach ($players->response->players as $player)
                if ($player->steamid == $entry->steamID)
                    $entry->name = $player->personaname;           
        }
        
        $results[$name] = $leaderboard->leaderboardEntryInformation->leaderboardEntries;
    }

    // cache the data because 
    file_put_contents($cache_filename, serialize($results));
}
else
{
    $results = unserialize(file_get_contents($cache_filename));
}

// display leaderboards
foreach ($results as $name => $leaderboard)
{
    $i = 1;
	
    $active = $name == $default_leaderboard ? "active" : "";
	// using bootstrap tables
    print '<div class="item '.$active.' table-responsive"><table class="table table-striped table-hover">';
    //print '<caption class="text-center">'.$name.'</caption>';
    print '<thead><tr><th class="text-right">Rank</th><th>Name</th><th>Score</th></tr></thead><tbody>';
    foreach ($leaderboard as $entry)
        printf('<tr><th class="text-right" scope="row">%s</th><td>%s</td><td>%s</td></tr>', $i++, $entry->name, $entry->score);
    print '</tbody></table></div>';
}
?>