<?php
require('vendor/autoload.php');
set_exception_handler(function ($ex) {
    echo json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT);
    exit;
});
use GuzzleHttp\Client;
$instance = $_REQUEST['instance'];
$output = array();
$webclient = new Client([
    'base_uri' => 'https://'.$instance.'/api/v1/'
]);
header('Content-Type: application/json');
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

$output['following'] = sizeof($f);
foreach($f as $follower){
    $instance_det = parse_url($follower);
    $instance_url = $instance_det['scheme'] . '://' . $instance_det['host'];
    $output['followingTable'][] = $instance_url;
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

$output['followed'] = sizeof($f);
foreach($f as $follower){
    $instance_det = parse_url($follower);
    $instance_url = $instance_det['scheme'] . '://' . $instance_det['host'];
    $output['followedTable'][] = $instance_url;
}

# End listing instances followers

$output['host'] = $instance;

echo json_encode($output, JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
?>