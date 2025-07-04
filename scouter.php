<?php
include('pokemontypes.php');
header("content-type: text/plain");

// Sample replay list for fallback/testing
$samplereplays = ['https://replay.pokemonshowdown.com/gen41v1-1103500393.log'];

$verifiedreplays = [];
$scoutedalts = [];
$scoutedaltsExact = [];
$opponents = [];
$uniqueopponents = [];
$addedopponents = [];

$tab = "\t";

function fixstr($samplestr) {
    $samplestr = preg_replace("/[^a-zA-Z0-9]+/", "", $samplestr);
    return strtolower($samplestr);
}

function urlmon($string) {
    $key1 = str_replace('-*', '', $string);
    $key2 = str_replace(' ', '-', $key1);
    $key3 = str_replace(':', '', $key2);
    return strtolower($key3);
}

function cleanmon($string) {
    if (strpos(substr($string, 1, 20), ',') !== false) {
        $num = (strpos($string, ', ') - 3);
        return substr($string, 3, $num);
    } else {
        $string2 = substr($string, 3, -1);
        $num = (strpos($string2, '|'));
        return substr($string2, 0, $num);
    }
}

function typedump($typeused, $tstring) {
    global $allpokemon;
    echo "\n";
    $typecounting = array_count_values($typeused);
    arsort($typecounting);
    $typesum = array_sum($typecounting);
    $percent = substr(strval(($typesum / count($allpokemon) * 100)), 0, 5);
    $output = [];
    foreach ($typecounting as $mon => $count) {
        $output[] = "$mon ($count)";
    }
    echo "$tstring Types Used: $typesum/" . count($allpokemon) . " ($percent%) - " . implode(", ", $output) . "\n";
}

// Read and sanitize submitted replays
if (!empty($_POST['statsbox']) && strlen($_POST['statsbox']) > 2) {
    $submitreplays = explode('https://', $_POST['statsbox']);
    foreach ($submitreplays as $value) {
        if (strlen($value) > 10) {
            $url = "https://" . rtrim($value, ". ") . ".log";
            if (!in_array($url, $verifiedreplays)) {
                $verifiedreplays[] = $url;
            }
        }
    }
} else {
    echo "You didn\'t submit any replays.\n";
    $verifiedreplays = $samplereplays;
}

// Player name parsing
if (!empty($_POST['fname'])) {
    $names = explode(',', $_POST['fname']);
    foreach ($names as $name) {
        $clean = fixstr($name);
        $scoutedalts[] = $clean;
        $scoutedaltsExact[] = trim($name);
    }
} else {
    $scoutedalts[] = fixstr('Synonimous');
    $scoutedaltsExact[] = 'Synonimous';
}

// Globals for usage tracking
$allpokemon = [];
$allteams = [];
$nodupespokemon = [];

// Type-specific arrays
$typeArrays = [
    'Fire' => [], 'Water' => [], 'Grass' => [], 'Bug' => [], 'Dark' => [],
    'Dragon' => [], 'Electric' => [], 'Fairy' => [], 'Fighting' => [], 'Flying' => [],
    'Ghost' => [], 'Ground' => [], 'Ice' => [], 'Normal' => [], 'Poison' => [],
    'Psychic' => [], 'Rock' => [], 'Steel' => []
];

// Load each replay
foreach ($verifiedreplays as $replay) {
    $html = file_get_contents($replay);
    if ($html === false) {
        echo "Failed to load replay: $replay\n";
        continue;
    }
    $html = htmlspecialchars($html);

    $players = explode("|player|", $html);
    if (count($players) < 3) continue;

    $p1 = fixstr(substr($players[1], 3, strpos($players[1], '|') - 3));
    $p2 = fixstr(substr($players[2], 3, strpos($players[2], '|') - 3));

    $winner = '';
    if (strpos($html, '|win|') !== false) {
        $winPart = explode('|win|', $html)[1];
        $winner = fixstr(explode('|', $winPart)[0]);
    }

    $opponent = '';
    if (in_array($p1, $scoutedalts)) {
        $opponent = $p2;
    } elseif (in_array($p2, $scoutedalts)) {
        $opponent = $p1;
    } else {
        echo "\nUnrecognized alt: $p1 or $p2\n";
        continue;
    }

    $team = [];
    $split = explode("|poke|", $html);
    for ($i = 1; $i <= 3; $i++) {
        $mon = cleanmon($split[$i] ?? '');
        if ($mon) {
            $team[] = $mon;
            $allpokemon[] = $mon;
        }
    }
    $allteams[] = $team;

    foreach ($team as $mon) {
        foreach ($typeArrays as $type => &$arr) {
            global ${strtolower($type) . 'types'};
            if (in_array($mon, ${strtolower($type) . 'types'})) {
                $arr[] = $mon;
            }
        }
    }
}

$countingfreq = array_count_values($allpokemon);
arsort($countingfreq);

echo "#TEAMS#\n";
foreach ($allteams as $index => $team) {
    echo '{"' . ucfirst($opponents[$index] ?? 'Unknown') . '",';
    foreach ($team as $i => $mon) {
        $url = urlmon($mon);
        $name = $mon;
        echo 'IMAGE("https://www.smogon.com/forums//media/minisprites/' . $url . '.png"), "' . $name . '"';
        if ($i < count($team) - 1) echo ',';
    }
    echo '}\n';
}

echo "\n#POKEMON USAGE#\n";
foreach ($countingfreq as $mon => $count) {
    $url = urlmon($mon);
    $percent = substr(strval(($count / count($allteams) * 100)), 0, 5);
    echo '=IMAGE("https://www.smogon.com/forums//media/minisprites/' . $url . '.png")' . $tab . $mon . $tab . $percent . '%' . $tab . $count . "\n";
}

echo "\n#TYPESUSED#\n";
foreach ($typeArrays as $type => $arr) {
    typedump($arr, $type);
}

print_r($addedopponents);
