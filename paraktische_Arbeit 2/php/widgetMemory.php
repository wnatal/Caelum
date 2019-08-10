<?php
/**
 *WIDGET
 *
 *Erstellt ein Widget, mit dem der verbrauchte Speicherplatz angezeigt wird.
 */
 
/**
 *Diese Funktion rechnet die Anzahl Bytes in eine höhere Masseinheit um, so dass die Zahl möglichst leserlich ist.
 */
 
	function umformen($wert, $i){
		$wert = $wert/1024;
		$i++;
		if($wert>1024&&$i!=3){//solange der Wert grösser als 1024 ist und GB noch nicht erreicht
			return umformen($wert, $i);
		}
		else{
			if($i==1){
				return round($wert, 2).'KB';
			}
			else if($i==2){
				return round($wert, 2).'MB';
			}
			else if($i==3){
				return round($wert, 2).'GB';
			}
			else{
				return '0';
			}
		}
	}
	
/**
 *Zuerst wird geprüft, ob der Nutzer angemeldet ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		$_SESSION["site"] = "index.php";
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *size.inc.php liefert die Werte zum Speicherverbrauch und zum Speicherplatz.
 *Jezt wird ausgerechnet, wieviel Prozent des Speichers verbraucht sind. Danach wird ausgerechnet, wieviel Prozent davon vom privaten Bereich verbraucht werden.
 */
 
	include('../php/size.inc.php');
	$alles = round(($speicher/$maxsize)*100);
	if($alles==0){
		$alles=0.1;
	}
	$privat = round(($privatSpeicher/$maxsize)*10000/$alles);
	
/**
 *Die ausgerechneten Prozent-Werte geben an, wie lange die Div-Balken für den Speicherverbrauch werden sollen.
 */
 
	echo '<article id="memory"><div>Speicherverbrauch</div><div><div>Kapazität: '.umformen($maxsize, 0).'</div><div>Verbrauch: '.umformen($speicher, 0).'</div></div>';
	echo '<div class="verbrauch"><div style="width:'.$alles.'%"><div style="width:'.$privat.'%"></div></div></div>';
	echo '<ul><li><div></div> Privat</li><li><div></div> Gruppen</li></ul></article>';
	
?>