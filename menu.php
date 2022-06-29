<?php
if($_SERVER['REQUEST_URI']=="/menu.php"||(!isset($sitename))){
header("location:index.php");
die();
}
$lista="Lista";
if($vote_id!=0)$lista="Rösta";
$nom='<a class="navbar-item" href="nominera.php">
	Nominera
</a>';
if($steg!=1)$nom="";
$min_nom='<a class="navbar-item" href="presentation.php">
	Min nominering
</a>';
if($nom_id==0)$min_nom="";
else if ($nom_id==1)$min_nom='<a class="navbar-item" href="presentation.php">
	Admin
</a>';
if($steg==0)$regist="";
else $regist='<a class="navbar-item" href="register.php">
	Registera röstdeltagande
</a>';
echo'
<nav class="navbar is-primary centered" role="navigation" aria-label="main navigation">
  <div id="nav-links" class="navbar-brand">
    <div class="navbar-start">
	  <a class="navbar-item" href="'.$sitename.'">
	     <img class="is-3by1" src="https://ungpirat.se/wp-content/uploads/2019/06/up-white.png" width="100">
	  </a>
	  <a class="navbar-item" href="'.$sitename.'">
        '.$lista.'
      </a>
      '.$nom.'
	  '.$regist.'
	  '.$min_nom.'
	  <a class="navbar-item" href="'.$sitename.'/Stadgar-for-Ung-Pirat-revision-2020.pdf" target="_blank" rel="noopener noreferrer">
        Stadgar
      </a>
	</div>';
if($nom_id!=0||$vote_id!=0){
	echo'
    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button is-link" href="login.php">
            <strong>Logga ut</strong>
          </a>
        </div>
      </div>
    </div>
	';
}
?>
  </div>
</nav>