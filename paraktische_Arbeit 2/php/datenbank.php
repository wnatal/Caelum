<?php
/**
 *DATENBANK-WERTE AUFBEREITEN
 *
 *Dieses Skript stellt anderen Skripten einige häufig benutzte Daten aus der Datenbank zur Verfügung  und bereitet die Daten für diese auch auf.
 *Zunächst wird aber geprüft, ob der Nutzer angemeldet ist.
 */
 
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	$dsatzGrup = array();	//später benutztes Array erzeugt
	
/**
 *Einige Werte aus Datenbank holen.
 */
 
	$con = mysqli_connect("kssb.ch","natal","natal00120");
	mysqli_select_db($con, "kssb_ch_matura");
	$res = mysqli_query($con, "SELECT idnummer, nachname, vorname, gruppe FROM nutzerdaten WHERE idnummer LIKE '".$_SESSION['idnummer']."'");
	$dsatz = mysqli_fetch_assoc($res);
	
/**
 *Das XML mit den Gruppen wird zunächst in ein SimpleXMLElement umgewandelt um dann die Werte aus dem Objekt zu lesen.
 *Die ausgelesenen Gruppen werden in ein Array gespeichert und ausserdem werden Informationen zur Gruppe aus der Datenbank gelesen und wiederum in ein Array gespeichert.
 */
 
	$gruppeXML = simplexml_load_string($dsatz['gruppe'],'SimpleXMLElement'); //Liste der Gruppen
	for($i=0; $i<count($gruppeXML->id); $i++){
		$arrayGruppe[$i] = $gruppeXML->id[$i];
		$resGrup = mysqli_query($con, "SELECT idnummer, gruppe, nutzerIDs FROM gruppendaten WHERE idnummer LIKE '".$arrayGruppe[$i]."'");
		$dsatzGrup[$i] = mysqli_fetch_assoc($resGrup);//Daten aller Gruppen
	}
	$patchVar = count($gruppeXML->id); //verhindert fehlerhaftes Verhalten von manchen Skripten bei null Gruppen.
	mysqli_close($con);
	
/**
 *Hier werden einige spezielle Werte in eigene Arrays übertragen.
 */
 
	$aryGruppennamen[0]="";
	$aryGruppenkennung[0]="";
	$aryAdministrator[0]="";
	for($i=0; $i<count($dsatzGrup); $i++){
		$aryGruppennamen[$i] = $dsatzGrup[$i]['idnummer'];//Gruppen-ID
		$aryGruppenkennung[$i] = $dsatzGrup[$i]['gruppe'];//Gruppenname
		$aryAdministrator[$i] = $arrayGruppe[$i]->attributes();//Nutzerrechte
	}
?>