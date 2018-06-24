<?php
// eh ducon les headers c'est avant le doctype
require('vendor/autoload.php');
use GuzzleHttp\Client;
$instance = $_REQUEST['instance'];
$output = array('config' => null, 'following-table' => null, 'following' => null, 'followed' => null, 'followed-table' => null);
if (!empty($instance)) {

	$webclient = new Client([
		'base_uri' => 'https://'.$instance.'/api/v1/'
	]);
	$cont      = true;
	$start     = 0;
	$v         = array();

	# Start config

	$config = $webclient->get('config');
	$output['config'] = json_decode($config->getBody());

	# End config

	# Start accounts

	$accounts = $webclient->get('accounts');
	$output['accounts'] = json_decode($accounts->getBody())->total;

	# End accounts

	# Start videos

	$videos = $webclient->get('videos');
	$output['videos'] = json_decode($videos->getBody())->total;

	# End videos

	$start=0;
	$f = array();
	$cont=true;

	# Start counting instances following

	while($cont){
		$response = $webclient->get('server/following?start='.$start);
		$followingsstr = $response->getBody();
		$followings = json_decode($followingsstr, true);
		foreach($followings['data'] as $follower){
			array_push($f,$follower['following']['url']);
		}
		$start += 15;
		if(sizeof($followings['data'])<15)
		$cont = false;
	}

	# End counting instances following

	# Start listing instances following
	
	$output['following'] = "<h2>Following ".sizeof($f)." instances.</h2><b>videos from these instances will be available in ".$instance.".</b>";
	foreach($f as $follower){
		$instance_det = parse_url($follower);
		$instance_url = $instance_det['scheme'] . '://' . $instance_det['host'];
		$output['following-table'] .= "<a href=\"".$instance_url."\">".$instance_det['host']."</a><br/>";
	}

	# End listing instances following

	$start=0;
	$f = array();
	$cont=true;

	# Start counting instances followers

	while($cont){
		$response = $webclient->get('server/followers?start='.$start);
		$followersstr = $response->getBody();
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
		$instance_det = parse_url($follower);
		$instance_url = $instance_det['scheme'] . '://' . $instance_det['host'];
		$output['followed-table'] .= "<a href=\"".$instance_url."\">".$instance_det['host']."</a><br/>";
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
	</head>

	<body>
		<a href="https://github.com/nsaovh/Peertube-Ninja"><img style="position: absolute; top: 0; right: 0; border: 0;" src="forkme.png" alt="Fork me on GitHub" data-canonical-src="forkme2.png"></a>
		<div class="ui center aligned container">
			<form>
				<div class="ui labeled fluid input">
					<div class="ui label">
    					https://
  					</div>
					<input type="text" name="instance" placeholder="Instance domain (ex : peertube.nsa.ovh)">
				</div>
				
				<button class="ui primary button" type="submit">Submit</button>
			</form>
			
			<?php if(!empty($instance)) { ?>
			<div class="ui segment">
				<h2 class="ui header">
  					<img class="ui image" src="https://<?php echo $instance; ?>/client/assets/images/favicon.png">
  					<div class="content">
						<?php echo $output['config']->instance->name ?>
						<div class="sub header"><?php echo $output['config']->instance->shortDescription ?></div>
  					</div>
				</h2>
				<h3 class="ui header">
  					<i class="users icon"></i>
  					<div class="content"><?php echo $output['accounts'] ?> accounts are listed on this instance (from this instance and following instances).</div>
				</h3>
				<h3 class="ui header">
  					<i class="video icon"></i>
  					<div class="content"><?php echo $output['videos'] ?> videos are listed on this instance (from this instance and following instances).</div>
				</h3>
			</div>

			<table>
				<tr>
					<th><?php echo $output['following']; ?></th>
					<th><?php echo $output['followed']; ?></th>
				</tr>
				<tr>
					<td><?php echo $output['following-table']; ?></td>
					<td class="disblock"><?php echo $output['followed-table']; ?></td>
				</tr>
			</table>
			<?php } ?>
			<br/>
			<br/>
			<div class="credits">Powered by Tuto-Craft Corporation, nekmi corp software development and NSA.OVH team</div>
		</div>
		<script src="https://nocdn.nsa.ovh/cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="semantic.min.js"></script>
	</body>
</html>
