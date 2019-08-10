<?php
/**
 *NEUE GRUPPE ERSTELLEN
 *
 *Mit diesem Skript wird eine neue Gruppe erstellt.
 *Zunächst wird geprüft, ob der Nutzer angemeldet ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	include("iconFunction.php");//diverse Funktionen (u.a. Icons erstellen)
	
/**
 *Funktion erstellt eine zufällige "idnummer".
 *Da mt_rand nicht so lange Zufallszahlen generieren kann, werden mehrere aneinandergehängt.
 *Es wird danach geprüft, ob die ID bereits existiert (sehr unwahrscheinlich). Es wird nicht nur bei den Gruppen geprüft, sondern auch bei den Nutzern, daher der etwas kompliziertere Query. Wenn es die ID schon gibt, wird die Funktion nocheinmal aufgerufen.
 */
 	
	function zufallRek(){
		$zufall = mt_rand(1000000,9999999).mt_rand(1000000,9999999).mt_rand(10000,99999);
		$con = mysqli_connect("kssb.ch","natal","natal00120");
		mysqli_select_db($con, "kssb_ch_matura");
		$res = mysqli_query($con, "SELECT nutzerdaten.idnummer, gruppendaten.idnummer FROM nutzerdaten, gruppendaten WHERE (nutzerdaten.idnummer='".$zufall."') OR (gruppendaten.idnummer='".$zufall."')");
		mysqli_close($con);
		if(mysqli_num_rows($res)>0) {
			zufallRek();
		}
		else return $zufall;
	}
	
	$gruppenMitgl = array();
	if(isset($_POST["gruppenMitglieder"])){
		$gruppenMitgl = $_POST["gruppenMitglieder"];//Mitglieder der Gruppe
	}
	
/**
 *Existiert ein Name für die Gruppe, wird das folgende Skript ausgeführt.
 *Beim öffnen der Datenbank wird autocommit auf false gestellt, so werden die Änderungen erst endgültig gespeichert, wenn dies bestätigt wird. Das ist hier von Bedeutung, da es wichtig ist, dass alle Änderungen erfolgreich ausgeführt werden, sonst wird die Datenbank in sich inkonsitent und würde auch nicht mehr vollständig mit der tatsächlichen Ordnerstruktur übereinstimmen.
 */
 
	if(!empty($_POST["gruppenName"])){
		$dname = $_POST["gruppenName"]; //Ordnername		
		$idnummer=zufallRek();
		if($dname==""){
			exit();	
		}
		$con = mysqli_connect("kssb.ch","natal","natal00120");
		mysqli_select_db($con, "kssb_ch_matura");
		mysqli_autocommit($con, FALSE);	
		$kontrolle=true;
			
/**
 *Über gruppeAdd.inc.php wird die neue Gruppe in die Gruppenlisten der augewählten Nutzern geschrieben, dabei wird auch ein XML-String erstellt, der eine Liste dieser Nutzer enthält (wird später in die Datenbank eingetragen ->Variable: nutzerIDs)
 */
	
		include("gruppeAdd.inc.php");
		
/**
 *Die alte XML-Nutzerliste der bearbeiteten Gruppe wird durch die neu erstellte Liste ersetzt.
 *Sind die Änderungen in der Datenbank erfolgreich gewesen, werden die Änderungen bestätigt und dauerhaft gespeichert, ansonsten werden die Änderungen in der Datenbank rückgängig gemacht. Bei Erfolg wird noch ein Icon erstellt, das dann clientseitig in das DOM eingefügt wird.
 */
 
		$res = mysqli_query($con, "INSERT INTO gruppendaten (idnummer, gruppe, erstelldatum, nutzerIDs) VALUES ('".$idnummer."','".$dname."',CURDATE(),'".$nutzerID."')");
		if($res&&$kontrolle){
			mysqli_commit($con);
			mkdir("../Dateien/".$idnummer);
			iconerstellen("../Dateien", $idnummer, $dname);
		}
		else{
			mysqli_rollback($con);
		}
		mysqli_close($con);
	}
?>