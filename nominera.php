<?php
include('init.php');
$message="";
if($steg==1){
	if(isset($_POST['register'])){
		if(isset($_POST['nom_email'])&&$_POST['nom_email']!=""&&isset($_POST['nom_name'])&&$_POST['nom_name']!=""&&isset($_POST['name'])&&$_POST['name']!=""&&isset($_POST['mot'])&&$_POST['mot']!=""){
			$pass = md5(rand(10000000, 99999999));
			$email = $_POST['nom_email'];
			$email = rtrim($email);
			$email = str_replace("<","",$email);
			$name = $_POST['nom_name'];
			$name = rtrim($name);
			$name = str_replace("<","",$name);
			$av = $_POST['name'];
			$av = rtrim($av);
			$av = str_replace("<","",$av);
			$tel = "";
			if(isset($_POST['nom_tel'])){
				$tel = $_POST['nom_tel'];
				$tel = str_replace(' ', '', $tel);
				$tel = str_replace('<', '', $tel);
			}
			$mot = $_POST['mot'];
			$mot = str_replace("<","",$mot);
			
			$beskrivning = "";
			$accept = 0;
			$email_message='Du har blivit nominerad till '.$kongressnamn.' av '.$av.'. Motiveringen lyder: '.$mot.' <a href="'.$sitename.'/login.php?t='.$pass.'">Klicka här för att logga in och acceptera eller tacka nej till nomineringen.</a> Nomineringar måste godkännas via länken senast '.$nom_om_text.'. Det går inte att svara på detta automatgenererade meddelande.';
			if($name==$av){
				$beskrivning = $mot;
				$accept = 0;//sätt till 1 för auto-accept. Nu behöver man klicka på länken.
				$mot = "";
				$email_message='Du har nominerat dig själv till '.$kongressnamn.'. Klicka på länken för att bekräfta nomineringen. <a href="'.$sitename.'/login.php?c='.$pass.'">Bekräfta nomineringen.</a> Du måste bekräfta via länken senast '.$nom_om_text.'. Det går inte att svara på detta automatgenererade meddelande.';
			}
			
			$q = $sql->query("SELECT count(name) as c FROM nominerade WHERE name = '$name'");
			$res = $q->fetch_assoc();
			$c = $res['c'];
			if($c==0){
				$sql->begin_transaction();
				$sql->query("INSERT INTO nominerade (name, av, motivering, password, tel, email, beskrivning, accept, votes, placering, logintime) VALUES ('$name', '$av', '$mot', '$pass', '$tel', '$email', '$beskrivning', $accept, 0, 0, 0)");
				$sql->commit();
				$_SESSION['email'] = $email;
				$_SESSION['message'] = $email_message;
				header("Location: mail.php");
			}else{
				$message = "<p>Personen har redan blivit nominerad.</p><p>Är detta du så kontakta fs@ungpirat.se för att acceptera nomineringen om du inte redan har fått ett mejl angående detta.</p>";
			}
		}else{
			$message .= "<p>Fyll i alla obligatoriska fält för att gå vidare.</p>";
		}
	}

	include('header.php');	
	echo '<div class="tile is-ancestor">
  <div class="tile is-6 is-vertical">';
  
	echo '<h2 class="is-size-3 has-text-primary">Nominera till kongressombud</h2><p>För att nominera någon till kongressombud, fyll i följande uppgifter.</p>
	<p>Den nominerade kommer få e-post där den kan svara på sin nominering så det är viktigt att du tar reda på rätt e-postadress innan du nominerar någon.</p>
	<p>Dettta är endast nomineringen till kongressombud. Vill du nominera någon till någon av våra personval (förbundsstyrelsen, valberedningen eller revisor) så kontakta valberedning@ungpirat.se</p>';
	
	echo '</div>
  <div class="tile is-6 is-vertical">';

echo '
	<form method="POST" accept-charset="UTF-8" class="form-inline control">
	<div class="field"><b>Uppgifter på den du nominerar</b><br>
	<label for="nom_name">Fullständigt namn<div class="control"><input type="text" name="nom_name" class="input" required></div></label></div>
	<div class="field"><label for="nom_email">E-postadress <div class="control"><input type="email" name="nom_email" class="input" required></div></label></div>
	<div class="field"><label for="nom_tel">Telefonnummer (Frivilligt) <div class="control"><input type="tel" name="nom_tel" class="input"></div></label></div>
	<div class="field"><b>Uppgifter på dig själv</b><br>
	<label for="name">Fullständigt namn <div class="control"><input type="text" name="name" class="input" required></div></label></div>
	<div class="field"><label for="mot">Din motivering <div class="control"><input type="text" name="mot" class="input" required></div></label></div>
	<div class="field"><div class="control"><input type="submit" class="button is-primary" name="register" value="Skicka"></div></div>
	</form>';
	echo $message;
echo '</div>
</div>';
}else{
	include('header.php');
	echo '<p>Nomineringen till kongressombud har nu stängt för i år.</p>';
}
include('footer.php');
?>