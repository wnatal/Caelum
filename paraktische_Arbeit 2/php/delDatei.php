<?php
/**
 *LOSCHEN VON DATEIEN UND ORDNERN
 *
 *Mit diesem Skript werden Dateien, Ordner und Gruppen gelöscht. Das Skript kann über Ajax direkt angesprochen werden, aber auch mit include in andere Skripte eingefügt werden.
 */

/**
 *loschenRek(): Löscht alle Dateien und Ordner im angegebenen Verzeichnis.
 */ 
 
	function loschenRek($dellink){
		foreach(glob($dellink.'/*') as $link){
			if(is_file($link)){
				unlink($link);
			}
			else {
				loschenRek($link);
				rmdir($link);
			}
		}
	}

/**
 *Zunächst wird geprüft, ob die Seite includiert worden ist, wenn ja (also $oldlink existiert), dann muss keine neu Session für die Kontrolle gestartet werden.
 *Jenachdem wird $dellink ein anderer Wert zugewiesen. 
 *$dellink repräsentiert den Pfad der Datei oder des Verzeichnises, die/das gelöscht werden soll.
 */
 
	$loschen=false;
 
	if(!isset($oldlink)){
		session_start();
		
		$dellink = $_POST["dellink"];
	}
	else{
		$dellink=$oldlink;
	}
	if (!isset($_SESSION["idnummer"])){
		exit();
	}
	$kontrolle=true; //registriert Fehler im Zusammenhang mit der DB
	
/**
 *Es wird geprüft, ob es sich bei dem zu löschendem Subjekt um ein Ordner handelt.
 *Wenn ja, dann wird geprüft, ob es sich um eine Gruppe handelt (charakterisch ist eine Stringlänge von 30 Zeichen).
 */
 
	if(is_dir($dellink)){
		if(strlen($dellink)==30){
			
/**
 *Handelt es sich um eine Gruppe, muss datenbank.php eingebunden werden, um die benötigten Werte aus der Datenbank zu erhalten.
 *Des weiteren wird die ID aus dem Dateipfad extrahiert.
 *Danach wird im Array mit den Gruppennamen nach dieser ID gesucht und bei Übereinstimmung kann man das Array $dsatzGrup an der richtigen Stelle auslesen. In $dsatzGrup sind die Nutzer gespeichert, die Zugriff auf die Gruppe haben. (als XML)
 */
 
			include('../php/datenbank.php');
			$idnummer = (substr($dellink, 11));
			for($i=0; $i<count($aryGruppennamen); $i++){
				if($idnummer == $aryGruppennamen[$i]){ 
					$nutzerIDs = $dsatzGrup[$i]['nutzerIDs'];
					
/**
 *Es wird noch überprüft, ob der Nutzer Zugriff auf die Gruppe hat.
 */
 
					if($aryAdministrator[$i]){
						$loschen=true;
					}
					break;
				}
			}
			
/**
 *Das XML mit den Nutzern wird jetzt für die spätere Benutzung in ein Array umgewandelt (oder zumindest in etwas ähnliches);
 */
 
			$nutzerIDs = simplexml_load_string($nutzerIDs,'SimpleXMLElement');
			$nutzerIDs = $nutzerIDs->id;
			
/**
 *Die Datenbank wird geöffnet. Mit autocommit wird eingestellt, dass MySQLi-Querys erst auf Anweisung endgültig ausgeführt werden.
 */
 		
			$con = mysqli_connect("kssb.ch","natal","natal00120");
			mysqli_select_db($con, "kssb_ch_matura");
			mysqli_autocommit($con, FALSE);	
			
/**
 *Ist der Nutzer berechtigt die Gruppe zu löschen, wird zunächst ein Skript includiert, das bei allen zur Gruppe gehörenden Nutzer den Verweis zur Gruppe in der Datenbank löscht (aus dem XML)
 *Danach wird der Datensatz zur Gruppe aus der Gruppentabelle ("gruppendaten") gelöscht
 */
 
 			if($loschen==true){
				include('../php/delGruppe.inc.php'); 		
				$res = mysqli_query($con, "DELETE FROM gruppendaten WHERE idnummer LIKE BINARY '".$idnummer."'");
				if(!$res){
					$kontrolle=false;
				}
			}	
				
/**
 *Hat der Nutzer keine Löschrechte, wird nur in seinem Profil die Gruppe aus der Liste entfernt und dementsprechend muss dann aber noch die Liste der Nutzer bei der Gruppe geändert werden
 */
 
			else{ 
				$res = mysqli_query($con, "SELECT gruppe FROM nutzerdaten WHERE idnummer LIKE BINARY '".$_SESSION['idnummer']."'"); //Gruppenliste anfordern
				if($res){
					$gruppe = mysqli_fetch_row($res);
					$gruppeneu = str_replace('<id>'.$idnummer.'</id>',"",$gruppe[0]); //Gruppe wird aus Liste gelöscht
					$res = mysqli_query($con, "UPDATE nutzerdaten SET gruppe = '".$gruppeneu."' WHERE idnummer LIKE BINARY '".$_SESSION['idnummer']."'");//neue Liste speichern
					if(!$res){
						$kontrolle=false;
					}
				}
				else{
					$kontrolle=false;
				}
				$res = mysqli_query($con, "SELECT nutzerIDs FROM gruppendaten WHERE idnummer LIKE BINARY '".$idnummer."'"); //Nutzerliste anfordern
				if($res){
					$nutzerID = mysqli_fetch_row($res);
					$nutzerIDneu = str_replace('<id>'.$_SESSION['idnummer'].'</id>',"",$nutzerID[0]); //Nutzer wird aus Liste gelöscht
					$res = mysqli_query($con, "UPDATE gruppendaten SET nutzerIDs = '".$nutzerIDneu."' WHERE idnummer LIKE BINARY '".$idnummer."'");//neue Liste speichern
				}
				else{
					$kontrolle=false;
				}
			}
			
/**
 *Wenn kein Fehler aufgetreten ist, werden die Datenbankänderungen bestätigt, der Inhalt des Ordners (->loschenRek()) wird gelöscht und schliesslich wird der Ordner selbst entfernt.
 *Bei einem Fehler, werden die Datenbankänderungen rückgängig gemacht.
 */
 
			if($kontrolle==true){
				mysqli_commit($con);
				if($loschen){
					loschenRek($dellink);
					rmdir($dellink);
				}
			}
			else{
				mysqli_rollback($con);
			}
			mysqli_close($con);
		}
		
/*
 *Handelt es sich um einen Ordner, der keine Gruppe ist, wird zunächst sein Inhalt rekursiv gelöscht, um dann den Ordner selbst zu löschen
 */	
 
 		else{
			loschenRek($dellink);
			rmdir($dellink);
		}
	}
	
/*
 *Handelt es sich um eine Datei, kann sie mit dem untenstehenden Befehl einfach gelöscht weden.
 */
 
 	else{
		unlink($dellink);
	}
?>