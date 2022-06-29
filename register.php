<?php
include('init.php');
$message="";

if(isset($_POST['register'])){
	if(isset($_POST['name'])&&$_POST['name']!=""&&isset($_POST['email'])&&$_POST['email']!=""){
		$passraw = rand(10000000, 99999999);
		$pass = md5($passraw);
		$email = $_POST['email'];
		$email = str_replace("<","",$email);
		$name = $_POST['name'];
		$name = str_replace("<","",$name);
		$q = $sql->query("SELECT count(id) as c FROM voters WHERE email = '$email'");
		$res = $q->fetch_assoc();
		$c = intval($res['c']);
		if($c>0){
			$message .= "<p>Du är redan registrerad sen tidigare. Du kommer få ett mejl med en länk där du kan rösta så snart röstningen öppnar.</p>";
		}else{
			$sql->begin_transaction();
			$sql->query("INSERT INTO voters (name, email, password, verify) VALUES ('$name', '$email', '$pass', 0)");
			$sql->commit();
			header("Location: index.php");
		}
	}else{
		$message .= "<p>Fyll i alla fält för att gå vidare.</p>";
	}
}
include('header.php');
echo '<div class="tile is-ancestor">
  <div class="tile is-6 is-vertical">
  <h2 class="is-size-3 has-text-primary">Registrera dig som röstare</h2>';
if($steg==3){
	echo '<h2 class="is-size-4">Röstningen är öppen!</h2><p>För att kunna rösta i ombudsvalet behöver du först registrera dig som röstare. Verifiering mot pirateweb kommer ske för att säkerställa att alla som röstar är medlemar.</p><p>En länk dit du kan rösta kommer mejlas ut så snart verifieringen är klar.</p>';

}else if($steg==0){
	echo '<h2 class="is-size-4">Valet är klart.</h2>';

}else{
	echo '<h2 class="is-size-4">Röstningen öppnar '.$val_om_text.'</h2><p>För att kunna rösta i ombudsvalet behöver du först registrera dig som röstare. Verifiering mot pirateweb kommer ske för att säkerställa att alla som röstar är medlemar.</p><p>En länk dit du kan rösta kommer mejlas ut så snart verifieringen är klar och när röstningen har öppnat.</p>';
}
echo '</div>
  <div class="tile is-6 is-vertical">';
if($steg!=0){
echo'
<form method="POST" accept-charset="UTF-8" class="form-inline control">
<div class="field"><label for="name">Fullständigt namn<div class="control"><input class="input" type="text" name="name" required></div></label></div>
<div class="field"><label for="email">E-postadress <div class="control"><input class="input" type="email" name="email" required></div></label></div>
<div class="field"><div class="control"><input type="submit" class="button is-primary" name="register" value="Registrera"></div></div>
</form>';
}
echo $message;
echo '</div>
</div>';
include('footer.php');
?>