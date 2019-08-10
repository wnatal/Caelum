<?php
/**
 *UMBENENNEN
 *
 *Dateien und Ordner umbenennen.
 *
 *Zunächst wird geprüft, ob der Nutzer angemeldet ist. 
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	include('../php/iconFunction.php');//um Icons zu erstellen
	$linkold = $_POST['link']; //Pfad mit altem Namen
	$neu = $_POST['neu'];//neuer Name
	$name = substr(strrchr($linkold, "/"), 1);//alter Name aus Pfad
	
/**
 *Zunächst wird der neue Name überprüft und angepasst: Häufig gebrauchte Umlaute werden durch normale Buchstaben ersetzt. Danach werden alle noch übrigen, nicht Datei- und Ordnername konformen, Zeichen gelöscht. Bleibt ein nicht gültiger Name übrig, wird das Skript abgebrochen
 */
 
	$search  = array('ä', 'ö', 'ü', 'è', 'é', 'à', 'ê', 'ç');
	$replace = array('ae', 'oe', 'ue', 'e', 'e', 'a', 'e', 'c');	
	$neu = str_replace($search, $replace, $neu);
	$neu = preg_replace("/[^a-zA-Z_0-9\-\.]/" ,'' , $neu);
	if($neu==""||$neu=="."||$neu==".."||$neu==".htaccess"){
		exit();
	}
	if(substr($neu, 0, 1)=='.'){//nur aus Dateiendung bestehend
		exit();
	}
	
/**
 *Im nächsten Schritt wird der Pfad validiert, indem er mit einem Array von Pfaden verglichen wird, die der Besucher während der Session aufgerufen hat.
 */
 
	if(!in_array($verzeichnis = substr($linkold, 0, strrpos($linkold, '/')), $_SESSION['protokoll'])){
		exit();
	}

/**
 *Handelt es sich um eine Datei, wird die eingegebene Dateiendung durch die alte ersetzt, da der Dateitype vom Nutzer nicht veränderbar sein soll. Danach wird geprüft, ob der neue Dateiname bereits besteht, wenn ja, wird solange eine Zahl angehängt, bis keine gleichnamige Datei mehr gefunden wird.
 *Danach kann die Datei umbenannt werden.
 */
 
	if(!is_dir($linkold)){
		$neu=substr($neu, 0, strpos($neu.'.', '.')).strrchr('.'.$name, ".");
		if($neu!=$name){
			$i=0;
			$nameTemp=$neu;
			while(file_exists($verzeichnis.'/'.$nameTemp)){
				$i++;
				$nameTemp = str_replace('.', '_'.$i.'.', $neu); 
			}
			$neu=$nameTemp;
		}
		rename($verzeichnis.'/'.$name, $verzeichnis.'/'.$neu);
	}
	
/**
 *Ist es ein Ordner, wird zunächst geprüft, ob es sich um eine Gruppe handelt (30 Zeichen im Pfad), wenn ja, wird das Skript beendet (Gruppen lassen sich momentan noch nicht umbenennen). Ansonsten wird eine eventuell vom Nutzer eingegebene Dateiendung entfernt. Danach wird wie bei den Dateien geprüft, ob der Ordner bereits existiert und gegebenenfalls umbenannt. Zum Schluss wird dann das Verzeichnis umbenennt.
 */
 
	else{
		if(strlen($linkold)>30){
			$neu=substr($neu, strrpos('.'.$neu, '.'));
			if($neu==""){
				$neu=$name;
			}
			if($name!=$neu){
				$i=0;
				$nameTemp=$neu;
				while(file_exists($verzeichnis.'/'.$nameTemp)){
					$i++;
					$nameTemp = $neu.$i; 
				}
				$neu=$nameTemp;
			}
			rename($verzeichnis.'/'.$name, $verzeichnis.'/'.$neu);
		}
		else{
			exit();
		}
	}
	
/**
 *Hat der Nutzer während dem Umbenennen nicht den Ordner gewechselt, wird noch das neue Icon ausgegeben.
 */
 
	if($verzeichnis==$_SESSION['upOrdner']){
		iconerstellen($verzeichnis, $neu);
	}
?>