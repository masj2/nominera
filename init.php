<?php
ini_set("session.cookie_lifetime",0);
ini_set('session.gc_maxlifetime', 0);
session_start();


if($_SERVER['REQUEST_URI']=="/init.php"){
header("Location: ".$sitename);
die();
}
 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$time=time();
require 'config.php';
require 'databaseinfo.php';
$sql = new mysqli($db_host, $db_username, $db_password, $db_name, 3306);

$nom_id = 0;
$vote_id = 0;
if(isset($_SESSION['nom_id'])) $nom_id = $_SESSION['nom_id'];
if(isset($_SESSION['vote_id'])) $vote_id = $_SESSION['vote_id'];
$days_left=ceil(($time_congress-$time)/86400);
$days_left_nom=$days_left-(8*7);
$days_val_open=$days_left_nom+7;
$days_left_val=$days_val_open+6;
$steg=1; //nominering

$nom_om_text = 'om '.$days_left_nom.' dagar';
if($days_left_nom==1) $nom_om_text = 'ikväll';
else if($days_left_nom==2) $nom_om_text = 'imorgon';
if($days_left_nom<1) {
	$steg=2; //listning
	$val_om_text = 'om '.$days_val_open.' dagar';
	if($days_val_open==1) $val_om_text = 'imorgon';//sista dagen den är stängd = öppna imorgon
	if($days_val_open<1) {
		$steg=3; //val
		$close_om_text = 'om '.$days_left_val.' dagar';
		if($days_left_val==1) $close_om_text = 'ikväll';
		else if($days_left_val==2) $close_om_text = 'imorgon';
		if($days_left_val<1) $steg=0; //stängt
	}
}
?>