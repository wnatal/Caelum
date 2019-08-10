<?php
/**
 *NACHRICHT SENDEN
 *
 *Das Skript, "senden" ist ein irreführender Begriff, speichert die per Ajax übermittelte Nachricht zum einen beim "Ziel-Nutzer" zum andern beim Nutzer, der die Nachricht gesendet hat.
 *
 *Zunächst wird geprüft, ob der Nutzer angemeldet ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"],$_POST['text'],$_POST['adresse'])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Zunächst werden zwei Dateinamen generiert (Einer für die eigene Nachrichtenbox, einer für die Nachrichtenbox des Adressierten)
 *Im Dateiname sind jeweils enthalten: das Sende-/Erstelldatum (mit Zeit), und die ID des Absenders
 *für die eigene Nachrichtenbox wird noch die ID des Adressierten gespeichert
 *Mit diesen Informationen kann man die Nachrichten leicht sortieren (nach Datum) und herausfinden von bzw. für wen die Nachricht war.
 */
 
	$text = $_POST['text']; //Text der Nachricht
	$adresse = $_POST['adresse']; //Zieladresse (eine "idnummer")
	$name = date('d_m_Y__H_i_s_').$_SESSION['idnummer']; //Sendedatum + Absender (eigene idnummmer)
	$postausgang = '../Nachrichten/'.$_SESSION['idnummer'].'/'.$name.'old'.$adresse.'.cmail';
	$posteingang =	'../Nachrichten/'.$adresse.'/'.$name.'new.cmail';

/**
 *Nun wird eine Datei mit dem dafür erstellten Dateinamen im eigenen Nachrichtenordner erzeugt, in die der Inhalt der Nachricht gepeichert wird.
 *Danach wird die Datei in den Nachrichtenordner der Zieladresse gespeichert (inklusive abgeändertem Dateiname).
 */
 
	$datei = fopen($postausgang, "a");
	if($datei){
		fputs($datei, htmlspecialchars($text, ENT_QUOTES));//HTML-Markup ist nicht erlaubt (->Sicherheitsrisiko).
		fclose($datei); 
	}
	if(copy($postausgang, $posteingang)){ //kopieren
		echo 'send';//Meldung
	}
	else{
		echo 'error';
	};
?>