<?php
// v. 0
session_start();

if(isset($_GET['iD'])){
	$iD = $_GET['iD'];
}else{
	$iD = '-1';
} 

if(isset($_GET['utente'])){
	if($_GET['utente'] == '1')
		$_SESSION['Id'] = 1;
	else
		$_SESSION['Id'] = 2;
}


if(isset($_SESSION['Id'])){
	$Id = $_SESSION['Id'];
	
	function primoTagLibero($Id){
		$path = "./elenco/" . $Id;
		$file = fopen($path, "r");
		if(!$file){
			return false;
		}
		$x = 1;
		while($linea = fgets($file)){
			$cleanea = explode(";", $linea);
			$campi = explode(",", $cleanea[0]);
			if($x == $campi[0])
				$x = $x + 1;
		}
		fclose($file);
		return $x;
	}

	function stampaTabella($Id){
		$path = "./elenco/" . $Id;
		$file = fopen($path, "r");
		if(!$file){
			return "errore apertura file";
		}
		
		echo "Elenco link di {$Id}:<br><table>";

		while($linea = fgets($file)){
			$cleanea = explode(";", $linea);
			$campi = explode(",", $cleanea[0]);
			echo "<tr>";
			echo "<td>" . $campi[0] . "</td>";
			echo "<td>
				<a href=\"  ./index.php?iD=" . $campi[0] . "\">" . $campi[1] . "</a>
			</td>";
			echo "</tr>";
		}
		echo "</table>";
		fclose($file);
	}
	stampaTabella($Id);	
}else{
	echo "prima scegli un profilo <br>
	<a href=\"./index.php?utente=1\">1</a> 	
	<a href=\"./index.php?utente=2\">2</a> ";
}



// -------------- OTTENIMENTO LINK DA FILE TRAMITE ID --------------
// v TROVO IL FILE DOVE UN UTENTE MEMORIZZA I LINK
$path = "./elenco/" . $Id;
$file = fopen($path, "r");

if(!$file){
	echo "errore apertura file";
	//return false;
}

while($linea = fgets($file)){
	// v scorro linea per linea leggendo i campi del file fino a trovare il link con ID corretto
	$cleanea = explode(";", $linea);
	$campi = explode(",", $cleanea[0]);
	
	if($campi[0] == $iD){
		$link =  $campi[1];
		// salvo il link desiderato
	}
	echo "<br>";
}
fclose($file);
// ------------------------------------------------------------------
?>
<!DOCTYPE html>
<html>
<head>
	<title>Chat</title>
	<script src="./ws/js/jquery.js" type="text/javascript"></script>
</head>
<body>
    
	<div id="wrapper">
	<div id="chat_output"></div>
	<?if($iD == '-1'){
		// LA PAGINA STA CHIEDENDO UNA CONNESSIONE 
	?>
	
	
	<script type="text/javascript">
	// funzioni websocket:
	jQuery(function($){
			
		// apro un socket dalla tupla  "localhost:80"	
		var websocket_server = new WebSocket("ws://localhost:81/");
			
		// vvvvvvvvvvvv ON OPEN vvvvvvvvvvvv
		websocket_server.onopen = function(e) {
			websocket_server.send(
				JSON.stringify({
					'type': 'conn',
					'user_id': <?=$Id?>
				})
			);
		};
	
		// vvvvvvvvvvvv ON ERROR vvvvvvvvvvvv
		websocket_server.onerror = function(e) {
			console.log("errore");
			// Errorhandling
		}

		// vvvvvvvvvvvv QUANDO RICEVE UN MEX vvvvvvvvvvvv
		websocket_server.onmessage = function(e) {
			var json = JSON.parse(e.data);
			// apre una finestra con il campo passato dal json
			window.open("//" + json.msg);
		}
	});
	</script>


<!-- ---------------------------------------------------------------- -->


	<?}else{
		// LA PAGINA STA INVIANDO UN LINK	
	?>
	<script type="text/javascript">
		// funzioni websocket:
	jQuery(function($){
			
		// apro un socket dalla tupla  "localhost:8080"	
		var websocket_server = new WebSocket("ws://localhost:81/");
		// vvvvvvvvvvvv ON OPEN vvvvvvvvvvvv
		websocket_server.onopen = function(e) {
			websocket_server.send(
				JSON.stringify({
					'type': 'link',
					'user_id': <?=$Id?>,
					'msg': '<?=$link?>'
				})
			);
		};

	});
	</script>
	<?}?>
	</div>
</body>
</html>