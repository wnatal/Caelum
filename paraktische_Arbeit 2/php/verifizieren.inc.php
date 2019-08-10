<?php
/**
 *KONTROLLE
 *
 *Führt für andere Skripte einige Tests bzgl. Ordnerpfade durch, die bei der Validierung dieser helfen.
 *Zunächst wird aber geprüft, ob das Skript includiert worden und der Nutzer angemeldet ist.
 */
		 
 	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Hier wird überprüft, ob die Datei / der Ordner in einer Gruppe liegt, zu der der Nutzer Zugang hat.
 */
 
	$root = substr($ordner, 0, strpos($ordner, '/'));
	for($i=0; $i<count($aryGruppennamen); $i++){
		if($root == $aryGruppennamen[$i] && $root != ""){
			$testRoot = true;
			break;
		}
		else $testRoot = false;
	}
	if(substr($ordner,-1)=='/'){ //Pfadabschluss vereinheitlichen
		$ordner = substr($ordner,0, -1);
	}

/**
 *Hier wird Pfad auf '../' untersucht.
 */
 
	$testLink = true;
	if(strpos($ordner.'/', '../')!==false){
		$testLink = false;
	}
?>