<?php
/**
 *LÖSCHEN VON GRUPPEN IN NUTZERDATEN (DATENBANK)
 *
 *Löscht alle Einträge zu einer Gruppe in der Nutzertabelle (nutzerdaten).
 *Zunächst wird aber geprüft, ob das Skript includiert wurde und der Nutzer angemeldet ist.
 */
 
 	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Die Liste der Nutzer mit Zugriff auf die Gruppe wird durchlaufen und es wird jeweils die Gruppe aus der Liste entfernt.
 */
 
	for($i=0; $i<count($nutzerIDs); $i++){
		$id = $nutzerIDs[$i];
		$res = mysqli_query($con, "SELECT gruppe FROM nutzerdaten WHERE idnummer LIKE BINARY '".$id."'");//aufrufen
		if($res){
			$gruppe = mysqli_fetch_row($res);//auslesen
			$gruppeneu = str_replace('<id>'.$idnummer.'</id>',"",$gruppe[0]); //ändern
			$gruppeneu = str_replace('<id Administrator="true">'.$idnummer.'</id>',"",$gruppeneu); //ändern
			$res = mysqli_query($con, "UPDATE nutzerdaten SET gruppe = '".$gruppeneu."' WHERE idnummer LIKE BINARY '".$id."'");//speichern
			if(!$res){
				$kontrolle=false;
			}
		}
		else{
			$kontrolle=false;
		}
	}
?>