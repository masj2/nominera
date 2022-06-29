<?php
if($_SERVER['REQUEST_URI']=="/header.php"){
header("location:index.php");
die();
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="style.css">
<?php header("Content-Type: text/html; charset=utf-8"); ?>
<title>Ung Pirat</title>
</head>
<body>
<?php include("menu.php"); ?>
<section class="section">