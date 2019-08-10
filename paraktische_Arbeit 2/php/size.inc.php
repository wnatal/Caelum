<?php
/**
 *SPEICHERPLATZ
 *
 *Stellt Informationen zum Speicherverbrauch bereit.
 */
 
/**
 *dirSize(): Ermittelt die Grösse eines Verzeichnisses, indem die Grösse der einzelnen Dateien (rekursiv) ermittelt und addiert werden.
 */
 
	function dirSize($pfad){
		$handle = scandir($pfad);
		$size = 0;				
		foreach($handle as $dname){
			if(is_file($pfad.'/'.$dname)){//Wenn Datei...
				$size += filesize($pfad.'/'.$dname);//Dateigrösse zu $size addieren
			}
			else if(is_dir($pfad.'/'.$dname)&&($dname !=".." && $dname !=".")){
				$size += dirSize($pfad.'/'.$dname);
			}
		}
		return $size;//Gibt ermittelte Grösse zurück
	}
	if (!isset($_SESSION["idnummer"])){
		exit();
	}
	
/**
 *$maxsize: momentan definiere ich den maximalen Speicherplatz direkt im Skript (100MB)
 *Zunächs wird der Speicherverbrauch des privaten Bereiches errechnet.
 *Danach wird der Speicherverbrauch aller Gruppen, in denen der Nutzer der Administrator ist, errechnet.
 */
	
	$maxsize=104857600;
	include('../php/datenbank.php'); 
	$privatSpeicher = dirSize("../Dateien/".$_SESSION['idnummer']); //verbrauchter Speicherplatz für "Privat"
	$gruppenSpeicher = 0;
	for($i=0; $i<count($aryGruppennamen); $i++){
		if($aryAdministrator[$i]==true){ 
			$pfad="../Dateien/".$aryGruppennamen[$i];
			if(is_dir($pfad)){
				$gruppenSpeicher += dirSize($pfad); //verbrauchter Speicherplatz für Gruppen
			}
		}
	}
	$speicher=$gruppenSpeicher+$privatSpeicher;  //Gesamter Speicherverbrauch
	
?>