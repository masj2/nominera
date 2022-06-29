<?php
include('init.php');

if($nom_id==1){//admin
	if(isset($_POST['remove'])){//kan bli nominerad igen
		$id = $_POST['id'];
		$sql->begin_transaction();
		$sql->query("DELETE FROM nominerade WHERE id = $id");
		$sql->commit();
	}else if(isset($_POST['block'])){//kan inte bli nominerad igen
		$id = $_POST['id'];
		$sql->begin_transaction();
		$sql->query("UPDATE nominerade SET accept = -1 WHERE id = $id");
		$sql->commit();
	}else if(isset($_POST['accept'])){
		$id = $_POST['id'];
		$sql->begin_transaction();
		$sql->query("UPDATE nominerade SET accept = 1, logintime = $time WHERE id = $id");
		$sql->commit();
	}else if(isset($_POST['send'])){
		$name = $_POST['by'];
		$email = $_POST['email'];
		$mot = $_POST['mot'];
		$pass = $_POST['pass'];
		$_SESSION['email'] = $email;
		$_SESSION['message'] = 'Detta är en påminnelse att du har blivit nominerad som kongressombud för Ung Pirat av '.$name.'. Motiveringen lyder: '.$mot.' <a href="'.$sitename.'/login.php?t='.$pass.'">Klicka här för att logga in och acceptera eller tacka nej till nomineringen.</a> Nomineringar måste godkännas via länken senast '.$nom_om_text.'. Det går inte att svara på detta automatgenererade meddelande.';
		header("Location: mail.php");
	}else if(isset($_POST['verify'])){
		$id = $_POST['id'];
		$sql->begin_transaction();
		$sql->query("UPDATE voters SET verify = 1 WHERE id = '$id'");
		$sql->commit();
	}else if(isset($_POST['reject'])){
		$id = $_POST['id'];
		$sql->begin_transaction();
		$sql->query("UPDATE voters SET verify = -1 WHERE id = '$id'");
		$sql->commit();
	}else if(isset($_POST['sendvote'])){
		$email = $_POST['email'];
		$q = $sql->query("SELECT password FROM voters WHERE email = '$email' limit 1");
		$res = $q->fetch_assoc();
		$pass = $res['password'];
		$_SESSION['email'] = $email;
		$_SESSION['message'] = 'Du kan nu rösta i ombudsvalet. Du får rösta på max 5 kandidater. <a href="'.$sitename.'/login.php?v='.$pass.'">Klicka här för att rösta.</a> Röstningen stänger '.$close_om_text.'. Det går inte att svara på detta automatgenererade meddelande.';
		header("Location: mail.php");
	}else if(isset($_POST['gallra'])){
		$two_years=$time-63072000;
		$sql->begin_transaction();
		$sql->query("DELETE FROM voters WHERE logintime = 0");
		$sql->commit();
		$sql->begin_transaction();
		$sql->query("DELETE FROM voters WHERE verify = -1");
		$sql->commit();
		$sql->begin_transaction();
		$sql->query("DELETE FROM voters WHERE logintime < $two_years");
		$sql->commit();
	}else if(isset($_POST['reset'])){//tar bort alla nominerade
		$sql->begin_transaction();
		$sql->query("DELETE FROM nominerade WHERE id != 1");
		$sql->commit();
	}
	include('header.php');
	echo '<p>Du är inloggad som admin.</p><p>Behöver beskrivning ändras går det att göra via login-länken.</p>';
	//röstare (inför val)
	if($steg==2||$steg==3){
		$output='<p>Lista över röstare.</p><p>Det går att skicka ut mejl från när röstningen har öppnat.</p><table class="table is-striped is-hoverable is-narrow"><tr class="is-selected"><th>Namn</th><th>Röstat</th><th>E-post</th><th>Verifiera</th><th>Ta bort</th><th>Skicka mejl</th></tr>';
		$res = $sql->query("SELECT id, name, email, password, verify, voted FROM voters");
		if($res) {
			while($row = mysqli_fetch_row($res)){
				$id = $row[0];
				$name = $row[1];
				$email = $row[2];
				$password = $row[3];
				$verify = $row[4];
				$voted = $row[5];
				$line='<td>'.$name.'</td><td>'.$voted.'</td><td>'.$email.'</td>';
				if($verify==0) $line.='<td><form method="POST"><input type="hidden" name="id" value="'.$id.'"><input type="submit" class="button is-success" name="verify" value="Verfiera"></form></td>'; else $line.='<td>Verifierad</td>';
				if($verify==0) $line.='<td><form method="POST"><input type="hidden" name="id" value="'.$id.'"><input type="submit" name="reject" class="button is-danger" value="Ta bort"></form></td>'; else $line.='<td>Ej möjligt</td>';
				if($verify==1 && $voted==0 && $steg==3) $line.='<td><form method="POST"><input type="hidden" name="email" value="'.$email.'"><input type="submit" class="button is-primary" name="sendvote" value="Mejla länk"></form></td>'; else $line .= '<td>Ej möjligt</td>';//endast under val (steg 3)
				if($verify==0) $output .= '<tr>'.$line.'</tr>'; else if($verify==1)$output .= '<tr class="accept">'.$line.'</tr>';
			}
		}
		echo $output.="</table>";
	}
	
	//nomineringar
	$output='<p>Hantera nominerade.</p><table class="table is-striped is-hoverable is-narrow"><tr class="is-selected"><th>Nominerad</th><th>Telefon</th><th>E-post</th><th>Login-länk</th><th>Svarat ja</th><th>Skicka mejl</th><th>Svarat nej</th></tr>';
	$accept_count =0;
	$where="";
	if($steg!=1)$where=" AND accept=1";//visa endast accepterade efter nomineringsperioden
	$res = $sql->query("SELECT id, name, av, motivering, password, tel, email, accept FROM nominerade WHERE id > 1".$where);
	if($res) {
		while($row = mysqli_fetch_row($res)){
			$id = $row[0];
			$name = $row[1];
			$av = $row[2];
			$mot = $row[3];
			$password = $row[4];
			$tel =  $row[5];
			$email = $row[6];
			$accept = $row[7];
			$line='<td>'.$name.'</td><td>'.$tel.'</td><td>'.$email.'</td><td>'.$sitename.'/login.php?t='.$password.'</td>';
			if($accept==0) $line.='<td><form method="POST"><input type="hidden" name="id" value="'.$id.'"><input type="submit" class="button is-success" name="accept" value="Svarat ja"></form></td>'; else if($accept==1)$line .= '<td>Redan svarat</td>';
			if($accept==0) $line.='<td><form method="POST"><input type="hidden" name="mot" value="'.$mot.'"><input type="hidden" name="email" value="'.$email.'"><input type="hidden" name="by" value="'.$av.'"><input type="hidden" name="pass" value="'.$password.'"><input type="submit" class="button is-primary" name="send" value="Skicka mejl"></form></td>'; else $line .= '<td>Ej möjligt</td>';
			if($accept==0) $line.='<td><form method="POST"><input type="hidden" name="id" value="'.$id.'"><input type="submit" class="button is-danger" name="block" value="Svarat nej"></form></td>'; else if($accept==1)$line .= '<td>Ej möjligt</td>';
			if($accept==0) $output .= '<tr>'.$line.'</tr>'; else if($accept==1)$output .= '<tr'.$green.'>'.$line.'</tr>';
			if($accept==1) $accept_count += 1;
		}
	}
	echo $output."</table><p>Accepterat: $accept_count </p>";

}else if($nom_id>1){//användare
	if($steg==1){//nomineringsperiod
		if(isset($_POST['accept'])){
			$beskrivning = "";
			if(isset($_POST['beskrivning'])) $beskrivning = $_POST['beskrivning'];
			$sql->begin_transaction();
			$beskrivning = str_replace("<","",$beskrivning);
			$sql->query("UPDATE nominerade SET beskrivning = '$beskrivning', accept = 1 WHERE id = $nom_id");
			$sql->commit();
		}else if(isset($_POST['reject'])){
			$sql->begin_transaction();
			$sql->query("UPDATE nominerade SET accept = -1 WHERE id = $nom_id");
			$sql->commit();
		}
		
		include('header.php');
		echo '<div class="tile is-ancestor">
		<div class="tile is-5 is-vertical">
		<h2 class="is-size-3 has-text-primary">Min nominering</h2>';
		$q = $sql->query("SELECT accept FROM nominerade WHERE id = '$nom_id' limit 1");
		$res = $q->fetch_assoc();
		$accept = intval($res['accept']);
		if($accept==-1){
			echo '<h2 class="is-size-3 has-text-danger">Du har tackat nej.</h2><p>Det går bra att stänga denna sida nu. Ångrar du dig kan du använda formuläret nedan.</p><br>';
		}
		echo '<p>Du har blivit nominerad som kongressombud för Ung Pirat. Accepterar du din nominering så skriv en kort presentation om dig själv.</p>
		<p>Accepterar du <b>inte</b> din nominering så klicka här.</p><form method="POST"><input type="submit" class="button is-danger" name="reject" value="Tacka nej"></form>
		<p>Det är inte bindande att tacka ja i detta skede. Det går att ångra sig fram till resebokningarna är klara.</p>';
		
		$q = $sql->query("SELECT beskrivning FROM nominerade WHERE id='$nom_id' limit 1");
		$res = $q->fetch_assoc();
		$beskrivning = $res['beskrivning'];
		echo '<form method="POST" accept-charset="UTF-8" class="form-inline control">
		<div class="field"><label for="beskrivning">Kort beskrivning av dig <div class="control"><input type="text" name="beskrivning" class="input" value="'.$beskrivning.'"></div></label></div>
		<div class="field"><div class="control"><input type="submit" class="button is-success" name="accept" value="Tacka ja (Spara)"></div></div>
		</form>';
		echo '</div>
  <div class="tile is-7 is-vertical">';
  echo '</div>
</div>';
	}else{
		include('header.php');
		echo '<p>Det är nu för sent att tacka ja eller göra några ändringar.</p>';
	}

}else{//inte inloggad
	header("location:index.php");
	die();
}
include('footer.php');
?>