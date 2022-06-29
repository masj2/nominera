<?php
require('init.php');

if(isset($_POST['vote'])){
	$vote = $_POST['vote'];
	$nr_votes = sizeof($vote);
	if($nr_votes>5) die("Du får inte rösta på fler än 5 personer.");
	$q = $sql->query("SELECT voted FROM voters WHERE id = $vote_id limit 1");
	$res = $q->fetch_assoc();
	$voted = $res['voted'];
	if($voted>0) die('<p>Du har redan röstat.</p>');
	foreach ($vote as $v=>$id) {
		$sql->begin_transaction();
		$sql->query("UPDATE nominerade SET votes = votes + 1 WHERE id = $id");
		$sql->query("UPDATE voters SET voted = $nr_votes WHERE id = $vote_id");
		$sql->commit();
	}
}

require('header.php');
//huvudpanel
echo '<div class="tile is-ancestor">
  <div class="tile is-5 is-vertical">';
if($steg==0){
	echo '<h2 class="is-size-3 has-text-primary">Kongressombud till'.$kongressnamn.'</h2><p>Resultatet från ombudsvalet är nu fastställt. Vid lika antal röster har vi slumpat ordningen.</p>';
}else if($steg==1) {
	echo '<h2 class="is-size-3 has-text-primary">Nomineringar för kongressombud för '.$kongressnamn.'.</h2>
	<p>Det är möjligt att nominera någon samt acceptera sin nominering fram till 8 veckor innan kongressen. Nomineringen och möjlighet att acceptera stänger <strong>'.$nom_om_text.'</strong>
	<br>En vecka senare kommer röstningen att öppna för de som har anmält sitt röstdeltagande och man har då 6 dagar på sig att rösta innan röstningen stänger.</p>';
}else if($steg==2) {
	echo '<h2 class="is-size-3 has-text-primary">Kandidater för kongressombudsval för '.$kongressnamn.'.</h2>
	<p>Nomineringsperioden är nu över och de nominerade som har tackat ja är nu fastställt i listan på denna sida.
	<br>Röstningen kommer öppna <strong>'.$val_om_text.'</strong> och stänger 6 veckor innan kongressen (6 dagar efter den öppnades).</p>
	<p>För att kunna rösta behöver du registrera ditt röstdeltagande uppe i menyn. Du kommer få en länk på mejlen när röstningen har öppnat där du kan rösta från.</p>';
}else if($steg==3) {
	echo '<h2 class="is-size-3 has-text-primary">Kandidater för kongressombudsval för '.$kongressnamn.'.</h2>
	<p>Röstningsperioden är igång och de nominerade som går att rösta på står i listan på denna sida.
	<br>Röstningen stänger <strong>'.$close_om_text.'</strong> (6 veckor innan kongressen)</p>
	<p>Varje medlem får rösta på max 5 ombud, <strong>men det går bara att rösta en gång.</strong></p>
	<p>För att rösta, klicka i de du vill rösta på (max 5) och klicka sedan på Rösta-knappen. Det går tyvärr inte att ändra sin röst eller rösta igen om du inte valde 5 stycken när du röstade.</p>
	<p>Ordningen listas enligt nomineringsordningen och antalet totala röster visas inte förrän röstningen är avslutad.</p>
	<p>För att kunna rösta behöver du registrera ditt röstdeltagande uppe i menyn. Du kommer få en länk på mejlen där du kan rösta från så snart vi har verifierat att du är medlem.</p>';
}
//sidopanel
echo '</div>
  <div class="tile is-7 is-vertical">';

if($steg==0) {
	$q = $sql->query("SELECT count(id) as c FROM nominerade WHERE placering = 1");
	$res = $q->fetch_assoc();
	$c = $res['c'];
	if($c==0){
		$res = $sql->query("SELECT id FROM nominerade WHERE votes > 0 ORDER BY votes DESC, RAND()");
		$count=0;
		if($res) {
			while($row = mysqli_fetch_row($res)){
				$count+=1;
				$id = $row[0];
				$sql->begin_transaction();
				$sql->query("UPDATE nominerade SET placering = $count WHERE id = $id");
				$sql->commit();
			}
		}
	}
	$output='<table class="table is-striped is-hoverable"><tr class="is-selected"><th>Ombudsnummer *</th><th>Röster</th><th>Vald person</th></tr>';
	$res = $sql->query("SELECT name, votes, placering FROM nominerade WHERE placering > 0 ORDER BY placering");
	if($res) {
		while($row = mysqli_fetch_row($res)){
			$name = $row[0];
			$votes = $row[1];
			$placering = $row[2];
			$output .= '<tr><td>'.$placering.'</td><td>'.$votes.'</td><td>'.$name.'</td></tr>';
		}
	}
	echo $output."</table><p>* Ombudsnumren är preliminära och kan komma att ändras om någon inte kan delta.</p>";
}else{
	if($vote_id!=0){
		if($steg==3){
			//Vote
			echo '<h2 class="is-size-3 has-text-primary">Rösta</h2>';
			$q = $sql->query("SELECT voted FROM voters WHERE id = $vote_id limit 1");
			$res = $q->fetch_assoc();
			$voted = $res['voted'];
			if($voted>0){
				echo '<p>Du har redan röstat.</p>';
			}else{
				$output='<table class="table is-striped is-hoverable"><form method="POST"><tr class="is-selected"><th>Rösta</th><th>Nominerad</th><th>Nominerad av</th><th>Motivering</th><th>Egen beskrivning</th></tr>';
				$res = $sql->query('SELECT id, name, av, motivering, beskrivning FROM nominerade WHERE accept > 0 AND id > 1 ORDER BY id');
				if($res) {
					while($row = mysqli_fetch_row($res)){
						$id = $row[0];
						$name = $row[1];
						$avp = $row[2];
						$mot = $row[3];
						$beskrivning = $row[4];
						$output .= '<tr><td><input class="checkbox" type="checkbox" name="vote[]" onchange="changeCheck(this)" value="'.$id.'"></td><td>'.$name.'</td><td>'.$avp.'</td><td>'.$mot.'</td><td>'.$beskrivning.'</td></tr>';
					}
				}
				echo $output.'<tr><th><input type="submit" class="button is-primary" value="Rösta"></form></th></tr></table>';
			}
		}else{
			echo "<p>Röstningen är inte öppen än.</p>";
		}
	}else{
		$output='<table class="table is-striped is-hoverable"><tr class="is-selected"><th>Nominerad</th><th>Nominerad av</th><th>Motivering</th><th>Egen beskrivning</th></tr>';
		$eq="";
		if($steg == 1) $eq="=";
		$res = $sql->query('SELECT name, av, motivering, beskrivning, accept FROM nominerade WHERE accept >'.$eq.' 0 AND id > 1 ORDER BY id');
		if($res) {
			while($row = mysqli_fetch_row($res)){
				$name = $row[0];
				$avp = $row[1];
				$mot = $row[2];
				$beskrivning = $row[3];
				$accept = $row[4];
				$accept_class = "";
				if($accept == 1 && $steg==1) $accept_class = $green;
				$output .= '<tr'.$accept_class.'><td>'.$name.'</td><td>'.$avp.'</td><td>'.$mot.'</td><td>'.$beskrivning.'</td></tr>';
			}
		}
		echo $output."</table>";
		if($steg==1) echo '<p>Personer som accepterat sina nomineringar blir grön i listan och har möjlihet att lägga till en egen beskrivning.</p>';
		else if($steg == 2 || $steg==3) echo '<p>Grönmarkeringen är borttagen, precis som alla personer som inte accepterade sina nomineringar. Alla i listan har accepterat sin nominering och går at rösta på.</p>';
	}
}
echo '</div>
</div>';
if($steg==3){
?>
<script>
var limit = 5;
var checkedCount = 0;
function changeCheck(box) {

    if (box.checked)
        checkedCount++;
    else
        checkedCount--;
    
    var boxes = document.getElementsByName("vote[]");
    if (checkedCount==limit){
        // Disable unchecked checkboxes
        for (var i = 0; i < boxes.length; i++) { 
            if (boxes[i].checked == false)
                boxes[i].disabled = true;
        }
    } else {
        // Enable all checkboxes
        for (var i = 0; i < boxes.length; i++) { 
            boxes[i].disabled = false;
        }
    }
    
}
</script>
<?php
}
include('footer.php');
?>