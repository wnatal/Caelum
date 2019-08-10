<?php
/**
 *GRUPPEN BEARBEITEN
 *
 *Mit diesem Skript werden die vom Nutzer geforderten Änderungen an der Gruppe durchgeführt.
 *(Änderungen bezüglich der Nutzer die Zugriff auf die Gruppe haben)
 *Zunächst wird geprüft, ob der Nutzer angemeldet ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])||!$_POST["pfad"]){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	$gruppenMitgl = array();
	if(isset($_POST["gruppenMitglieder2"])){
		$gruppenMitgl = $_POST["gruppenMitglieder2"];//neue Mitgliederliste (Array)
	}
	
/**
 *Der Pfad zum Ordner wird zunächst validiert
 */
 
	$pfad = $_POST["pfad"];
	if(is_dir($pfad)){
		if(strlen($pfad)==30){ //karakeristische Länge 
			include('../php/datenbank.php');
			$idnummer =  substr($pfad, 11);
			
/**
 *Es wird die Liste mit den Nutzern für die Gruppe herausgesucht
 *Wird die Gruppe nicht gefunden oder hat der Nutzer nicht die Rechte die Gruppe zu bearbeiten, wird das Skript abgebrochen.
 */
 
			for($i=0; $i<count($aryGruppennamen); $i++){
				if($idnummer == $aryGruppennamen[$i]){ 
					$nutzerIDs = $dsatzGrup[$i]['nutzerIDs'];
					if(!$aryAdministrator[$i]){
						exit();
					}
					break;
				}
			} 
			if(!isset($nutzerIDs)){
				exit();
			}
			$nutzerIDs = simplexml_load_string($nutzerIDs,'SimpleXMLElement');
			$nutzerIDs = $nutzerIDs->id; //IDs der Nutzer auslesen
			$kontrolle=true; 	
			
/**
 *Die Verbindung zur Datenbank wird hergestellt. Das automatische speichern der Datenbankänderungen wird mit autocommit (->false) verhindert.
 */
 	
			$con = mysqli_connect("kssb.ch","natal","natal00120");
			mysqli_select_db($con, "kssb_ch_matura");
			mysqli_autocommit($con, FALSE);	
			
/**
 *Zunächst (über delGruppe.inc.php), wird bei allen bisherigen Nutzern die Gruppe aus der Gruppen-Liste gelöscht.
 *Danach (über gruppeAd.inc.php) wird ein "XML-String" aufgebaut, der alle neuen Nutzer der Gruppe enthält (nutzerIDs) und allen zur bearbeiteten Gruppe zugehörenden Nutzern die Gruppe (wieder) in die jeweilige Gruppenliste eingetragen
 */
 
			include('delGruppe.inc.php');
			include('gruppeAdd.inc.php');

/**
 *Die alte XML-Nutzerliste der bearbeiteten Gruppe wird durch die neu erstellte Liste ersetzt.
 *Sind die Änderungen in der Datenbank erfolgreich gewesen, werden die Änderungen bestätigt und dauerhaft gespeichert, ansonsten werden die Änderungen in der Datenbank rückgängig gemacht.
 */

			$res = mysqli_query($con, "UPDATE gruppendaten SET nutzerIDs = '".$nutzerID."' WHERE idnummer LIKE BINARY '".$idnummer."'");
			if($res&&$kontrolle){
				mysqli_commit($con);
			}
			else{
				mysqli_rollback($con);
			}
			mysqli_close($con);
		}
	}	
	
?>