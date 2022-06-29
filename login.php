<?php
if(($_SERVER['REQUEST_URI']=="/login.php")&&(!isset($_GET['t']))){//logout
session_start();
session_destroy();
header("location:index.php");
die();
} else if(isset($_GET['t'])){//nominerad
	include('init.php');
	$t=$_GET['t'];
	$q = $sql->query("SELECT id, count(id) AS c FROM nominerade WHERE password='$t'");
	$res = $q->fetch_assoc();
	$id = $res['id'];
	$c = $res['c'];
	if($c==1){
		$sql->begin_transaction();
		$sql->query("UPDATE nominerade SET logintime = $time WHERE id = $id");
		$sql->commit();
		$_SESSION['nom_id']=$id;
		header("Location: presentation.php");
	}else if($c>1){
		die("Flera användare med dessa uppgifter hittades. Vänligen kontakta martin.sjoberg@ungpirat.se för hjälp att logga in.");
	}else{
		die("Fel lösenord");
	}
} else if(isset($_GET['c'])){//självnominering
	include('init.php');
	$c=$_GET['c'];
	$q = $sql->query("SELECT id, count(id) AS c FROM nominerade WHERE password='$c'");
	$res = $q->fetch_assoc();
	$id = $res['id'];
	$c = $res['c'];
	if($c==1){
		$sql->begin_transaction();
		$sql->query("UPDATE nominerade SET accept = 1, logintime = $time WHERE id = $id");
		$sql->commit();
		header("Location: index.php");
	}else if($c>1){
		die("Flera användare med dessa uppgifter hittades. Vänligen kontakta martin.sjoberg@ungpirat.se för hjälp att logga in.");
	}else{
		die("Fel lösenord");
	}
} else if(isset($_GET['v'])){//rösta
	include('init.php');
	$v=$_GET['v'];
	$q = $sql->query("SELECT id, count(id) AS c FROM voters WHERE password='$v'");
	$res = $q->fetch_assoc();
	$id = $res['id'];
	$c = $res['c'];
	if($c==1){
		$sql->begin_transaction();
		$sql->query("UPDATE voters SET logintime = $time WHERE id = $id");
		$sql->commit();
		$_SESSION['vote_id']=$id;
		header("Location: index.php");
	}else if($c>1){
		die("Flera användare med dessa uppgifter hittades. Vänligen kontakta martin.sjoberg@ungpirat.se för hjälp att logga in.");
	}else{
		die("Fel lösenord");
	}
}
?>