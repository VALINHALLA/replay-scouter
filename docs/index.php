<!DOCTYPE html>
<html>
<head>
	<title>goliadscouter</title>
</head>
<?php
include 'pokemontypes.php';
//include 'limit.php';

function twoindexes() {
	global $pkmn;
	//echo count($pkmn);

	$rand1 = rand(0, count($pkmn));
	$rand2 = rand(0, count($pkmn));
	return([$pkmn[$rand1], $pkmn[$rand2]]);
}
?>

<center>
<h1 style="text-decoration:none; display: inline;">
		<img src="https://www.smogon.com/forums//media/minisprites/<?php echo twoindexes()[0]; ?>.png" style="text-decoration:none; display: inline; position: relative; top: 6px;">goliadscouter: sheet edition<img src="https://www.smogon.com/forums//media/minisprites/<?php echo twoindexes()[1]; ?>.png" style="text-decoration:none; display: inline; position: relative; top: 6px;">
	</h1>
	<br />
	<u>Replays</u> (put a newline after each one):<br />
	<form action="scouter.php" method="post">
	<textarea rows="20" cols="60" id="statsbox" name="statsbox"wrap="soft" method="post"></textarea><br />
	<input type="checkbox" name="altusagebox" value="altusageboxvalue" />2 tab pokemon usage <a href="2_tab_pkmn_usage.png" target="_blank" style="text-decoration:none; font-size: 10px">(what is this?)</a><br />
  	<u>Usernames</u>:<br /><input type="text" id="fname" name="fname" method="post"><br />
  	<input type="submit" value="Send" id="poop" name="poop">
	</form>
	<br />
	<!--(check out <a href="http://warpzone.rf.gd/scouter200/guide.txt" target="_blank">guide.txt</a> if you need help)<br />
	more features coming soon, see <a href="http://warpzone.rf.gd/scouter200/todolist.txt" target="_blank">todolist.txt</a> if you wanna see what-->
		<h1 style="text-decoration:none; display: inline;"><a href="https://docs.google.com/spreadsheets/d/1Cu6fz5rAankoKFMYSC2b7dQ5oarxKpYFXHHUqOqnYkA/edit#gid=0" target="_blank" style="text-decoration:none; color: mediumspringgreen; display: inline;">[sheet template]</a></h1>
	<h1 style="text-decoration:none; display: inline;"><a  href="https://youtu.be/Qe-2LLyIJWA" target="_blank" style="text-decoration:none; color: red; display: inline;">[tutorial]</a></h1> 
	<h1 style="text-decoration:none; display: inline;"><a href="https://pastebin.com/raw/vv42UtMY" target="_blank" style="text-decoration:none; color: #FDDA0D;">[example input]</a></h1>
	<h1 style="text-decoration:none; display: inline;"><a href="https://media.discordapp.net/attachments/323936068414078976/989324615249707049/unknown.png?width=1432&height=864" target="_blank" style="text-decoration:none; color: lightskyblue;">[example scout]</a></h1>
	<h1 style="text-decoration:none; display: inline;"><a href="https://github.com/partys-over/replay-scouter" target="_blank" style="text-decoration:none; color: mediumpurple;">[source code]</a></h1>
	</center>
	<br />
	<div style="text-decoration:none; text-align: center;">
	 <div style="display: inline-block; text-align: left;">
	<h3 style="text-decoration:none; display: inline">06/25/23</h3>
	<ul style="text-decoration:none; text-align: left; list-style-position: inside; display: inline;">
	<li>added 2 tab pokemon usage</a>, which allows for a simple top to bottom across 2 sheet tabs</li>
	<li>the scouter now tells you which sheet template is best for you to use depending on amount of replays</li>
	<li>fixed bug where last replay sometimes eaten up</li>
	<li>made form names shorter the tab after mon sprites</li>
	<li>more options/better layout for the sheet templates</li>
	<li>lastly added all the above links/messed with this page's UI as you can see</li>
</ul>
</div>
</div>
</html>


<?php

//  	Dark Mode:<input type="checkbox" name="darkcheck" id="darkcheck" /><br />
// OR pastebin raw (better on mobile):<br /><input type="text" id="fname" name="fname" method="post"><br />

?>
