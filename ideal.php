<?php
include('pokemontypes.php');

// Prepare list of sample replays
$samplereplays = [
    'https://replay.pokemonshowdown.com/gen61v1-949443390.log',
    'https://replay.pokemonshowdown.com/gen61v1-949441915.log',
    'https://replay.pokemonshowdown.com/gen61v1-949439807.log',
    'https://replay.pokemonshowdown.com/gen61v1-949438120.log',
    'https://replay.pokemonshowdown.com/gen61v1-956378962.log',
    'https://replay.pokemonshowdown.com/gen61v1-956381342.log',
    'https://replay.pokemonshowdown.com/gen61v1-956382087.log',
    'https://replay.pokemonshowdown.com/gen61v1-953917120.log',
    'https://replay.pokemonshowdown.com/gen61v1-953919346.log',
    'https://replay.pokemonshowdown.com/gen61v1-953920910.log',
    'https://replay.pokemonshowdown.com/smogtours-gen61v1-453581.log',
    'https://replay.pokemonshowdown.com/smogtours-gen61v1-453582.log',
    'https://replay.pokemonshowdown.com/smogtours-gen61v1-453585.log'
];

// Alt aliases to be scouted
$scoutedalts0 = ['SECTOR 7 dom', 'fadeonitdom', 'loopedupdom', 'G5 SCRAF dom'];
$scoutedalts = array_map(function($name) {
    return strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", $name));
}, $scoutedalts0);

// Handle submitted form data
$nums = range(0, 9);
$box = isset($_POST['statsbox']) ? htmlspecialchars($_POST['statsbox']) : '';
$verifiedreplays = [];

if (strlen($box) > 2) {
    $submitreplays = explode('https://', $box);
    foreach ($submitreplays as $value) {
        if (empty(trim($value))) continue;
        $clean = rtrim($value, ".\n\r");
        if (!str_ends_with($clean, ".log")) {
            $clean .= ".log";
        }
        array_push($verifiedreplays, 'https://' . $clean);
        echo 'https://' . $clean . "<br />";
    }
} else {
    echo "No replays provided.<br />";
}

// Type arrays
$allpokemon = [];
$allteams = [];
$nodupespokemon = [];
$typebuckets = [
    'fire' => [], 'water' => [], 'grass' => [], 'bug' => [], 'dark' => [], 'dragon' => [], 'electric' => [],
    'fairy' => [], 'fighting' => [], 'flying' => [], 'ghost' => [], 'ground' => [], 'ice' => [], 'normal' => [],
    'poison' => [], 'psychic' => [], 'rock' => [], 'steel' => []
];

function cleanmon($string) {
    if (strpos(substr($string, 1, 20), ',') !== false) {
        return substr($string, 3, strpos($string, ',') - 3);
    }
    return substr($string, 3, strpos(substr($string, 3), '|'));
}

function scoutreplay($replay) {
    global $scoutedalts, $allpokemon, $allteams, $typebuckets, $firetypes, $watertypes, $grasstypes, $groundtypes, $steeltypes, $fairytypes, $flyingtypes, $psychictypes, $darktypes, $dragontypes, $electrictypes, $fightingtypes, $ghosttypes, $icetypes, $normaltypes, $poisontypes, $rocktypes, $bugtypes;

    $html = htmlspecialchars(file_get_contents($replay));
    $sections = explode("|poke|", $html);
    $players = explode("|player|", $sections[0]);

    $playerone = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", substr($players[1], 3, strpos($players[1], '|') - 3)));
    $playertwo = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", substr($players[2], 3, strpos($players[2], '|') - 3)));

    $is1v1 = str_contains($replay, '1v1');

    $teamlist = array_map('cleanmon', array_slice($sections, 1, $is1v1 ? 3 : 6));
    $teamlist2 = array_map('cleanmon', array_slice($sections, $is1v1 ? 4 : 7, $is1v1 ? 3 : 6));

    $team = null;
    if (in_array($playerone, $scoutedalts)) {
        $team = $teamlist;
    } elseif (in_array($playertwo, $scoutedalts)) {
        $team = $teamlist2;
    } else {
        echo '<br />Missing alt match for replay: ' . $replay;
        return;
    }

    $allteams[] = $team;
    foreach ($team as $mon) {
        $allpokemon[] = $mon;
        foreach ($typebuckets as $type => &$bucket) {
            $typedata = $type . 'types';
            if (in_array($mon, $$typedata)) $bucket[] = $mon;
        }
    }
}

foreach ($verifiedreplays as $replay) scoutreplay($replay);

echo "Teams:<br />";
foreach ($allteams as $index => $team) {
    $link = substr($verifiedreplays[$index], 0, -4);
    echo "<a target='_blank' style='text-decoration:none; color:black;' href='$link'>" . implode(" / ", $team) . "</a><br />";
}

$countingfreq = array_count_values($allpokemon);
arsort($countingfreq);
$nodupespokemon = array_keys($countingfreq);

echo "<br />Number of Teams: " . count($allteams) . "<br />";
echo "Number of Pokemon: " . count($allpokemon) . "<br />";
echo "Number of Unique Pokemon: " . count($nodupespokemon) . "<br />";

foreach ($countingfreq as $mon => $count) {
    echo "<img src='https://www.smogon.com/forums//media/minisprites/" . strtolower($mon) . ".png' /> $mon: $count<br />";
}

echo "<br /><a href='index.php'><button>Back</button></a><br />";

function typedump($bucket, $label) {
    global $allpokemon;
    $freq = array_count_values($bucket);
    arsort($freq);
    $total = array_sum($freq);
    $percent = round(($total / count($allpokemon)) * 100, 2);
    echo "<br />$label Types Used: $total / " . count($allpokemon) . " ($percent%) - ";
    foreach ($freq as $mon => $count) {
        echo "<img src='https://www.smogon.com/forums//media/minisprites/" . strtolower($mon) . ".png' /> $mon ($count), ";
    }
}

foreach ($typebuckets as $type => $bucket) typedump($bucket, ucfirst($type));
?>
