<?php	
/**
 *LISTE DER MITGLIEDER EINER GRUPPE
 *
 *Hier wird dem Formular zum Ändern einer Gruppe eine Liste von allen Nutzern zur Verfügung gestellt. Die Liste wird alphabetisch geordnet von der Datenbank ausgeliefert.
 *In der Liste wird dabei angezeigt, wer momentan zur Gruppe gehört. 
 *Zunächst wird aber geprüft, ob der Nutzer angemeldet ist.
 */
    
	session_start();
	if (!isset($_SESSION["idnummer"], $_POST['pfad'])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}	
	include('../php/datenbank.php');
	
/**
 *Untenstehend wird die ID der Gruppe aus dessen Pfad ausgeschnitten und danach überprüft ob es die ID gibt und ob der Nutzer die Bearbeitungsrechte  für die Gruppe hat.
 *Gleichzeitig wird aus dem Array mit den Nuzterlisten die benötigte Liste in eine Variable gespeichert ($nutzerIDs).
 *Hat der Nutzer keine Bearbeitungsrechte oder die Gruppe existiert nicht, wird das Skript (mit dem Rückgabewert f) beendet.
 */
 
	$idnummer =  substr($_POST['pfad'], 11);
	$test = false;
	for($i=0; $i<count($aryGruppennamen); $i++){
		if($idnummer == $aryGruppennamen[$i]){ 
			$nutzerIDs = $dsatzGrup[$i]['nutzerIDs'];
			if($aryAdministrator[$i]){
				$test=true;
			}
			break;
		}
	}
	if(!$test){
		echo 'f';
		exit();
	}

/**
 *Jetzt wird die zur Gruppe gehörende Nutzerliste in ein SimpleXMLElement umgewandelt ($liste) und zu allen Usern (ausser einem selbst) idnummer und der vollständige Name abgefragt.
 *Danach werden die idnummern mit $liste verglichen, jenachdem wird gekennzeichnet, dass der Nutzer momentan zur Gruppe gehört.
 */
 
	$liste = simplexml_load_string($nutzerIDs,'SimpleXMLElement');
	$con = mysqli_connect("kssb.ch","natal","natal00120");
	mysqli_select_db($con, "kssb_ch_matura");
	$res = mysqli_query($con, "SELECT idnummer, nachname, vorname FROM nutzerdaten WHERE idnummer NOT LIKE BINARY '".$_SESSION['idnummer']."' ORDER BY nachname ASC, vorname ASC, user ASC");
	mysqli_close($con);
	while($dsatz = mysqli_fetch_assoc($res)) {
		for($i=0; $i<count($liste->id); $i++){
			if($dsatz['idnummer'] == $liste->id[$i]){
				$test=true;
				break;
			}
			else{
				$test=false;
			}
		}
		if($test==true){
			echo '<option value="'.$dsatz['idnummer'].'" selected>'.$dsatz['nachname'].' '.$dsatz['vorname'].' (Mitglied)</option>';
		}
		else{
			echo '<option value="'.$dsatz['idnummer'].'">'.$dsatz['nachname'].' '.$dsatz['vorname'].'</option>';
		}
	}
?>