<?php
// eh ducon les headers c'est avant le doctype
header("X-Author: skid9000 & leonekmi");
$instance = $_REQUEST['instance'];
$output = array();
if (!empty($instance)) {

	$url      = "https://" . $instance;
	$cont     = true;
	$start    = 0;
	$f        = array();

	# Start counting instances following

	while($cont){
		$followingsstr= file_get_contents($url."/api/v1/server/following?start=".$start); // TODO : omg
		$followings = json_decode($followingsstr, true);
		foreach($followings['data'] as $follower){
			array_push($f,$follower['following']['url']);
		}
		$start+=15;
		if(sizeof($followings['data'])<15)
		$cont=false;
	}

	# End counting instances following

	# Start listing instances following
	
	$output['following'] = "<h2>Following ".sizeof($f)." instances.</h2><b>videos from these instances will be available in ".$instance.".</b>";
	foreach($f as $follower){
		$instance_url = str_replace('/accounts/peertube', '', $follower);
		$instance_short_1 = str_replace('https://', '', $instance_url);
		$instance_short = str_replace('http://', '', $instance_short_1);
		$output['following-table'] .= "<a href=\"".$instance_url."\">".$instance_short."</a><br />";
	}

	# End listing instances following

	$start=0;
	$f = array();
	$cont=true;

	# Start counting instances followers

	while($cont){
		$followersstr= file_get_contents($url."/api/v1/server/followers?start=".$start);
		$followers = json_decode($followersstr, true);
		foreach($followers['data'] as $follower){
			if (strpos($follower['follower']['url'], 'accounts/peertube') !== false)
			array_push($f,$follower['follower']['url']);
		}
		$start+=15;
		if(sizeof($followers['data'])<15)
		$cont=false;
	}

	# End counting instances followers

	# Start listing instances followers

	$output['followed'] = "<h2>Followed by ".sizeof($f)." instances"."</h2><b>videos of ".$instance." will be available in these instances</b>";
	foreach($f as $follower){
		$instance_url = str_replace('/accounts/peertube', '', $follower);
		$instance_short_1 = str_replace('https://', '', $instance_url);
		$instance_short = str_replace('http://', '', $instance_short_1);
		$output['followed-table'] .= "<a href=\"".$instance_url."\">".$instance_short."</a><br/>";
	}

	# End listing instances followers
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Peertube Ninja</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="semantic.min.css">
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="css/icons.css">
	</head>

	<body>
		<a href="https://github.com/skid9000/Peertube-Ninja"><img style="position: absolute; top: 0; right: 0; border: 0;" src="forkme.png" alt="Fork me on GitHub" data-canonical-src="forkme2.png"></a>
		<div class="ui center aligned container">
			<form>
				<div class="ui labeled fluid input">
					<div class="ui label">
    					http(s)://
  					</div>
					<input type="text" name="instance" placeholder="Instance domain (ex : peertube.nsa.ovh)">
				</div>
				
				<button class="ui primary button" type="submit">Submit</button>
			</form>

			<table>
				<tr>
					<th><?php echo $output['following']; ?></th>
					<th><?php echo $output['followed']; ?></th>
				</tr>
				<tr>
					<td class="disblock"><?php echo $output['following-table']; ?></td>
					<td class="disblock"><?php echo $output['followed-table']; ?></td>
				</tr>
			</table>
			<br/>
			<br/>
			<div class="credits">Powered by Tuto-Craft Corporation, nekmi corp software development and NSA.OVH team</div>
		</div>
		<script src="https://nocdn.nsa.ovh/cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="semantic.min.js"></script>
	</body>
</html>