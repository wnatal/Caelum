<?php
/**
 *AUDIODATEI ÜBER SKRIPT ÖFFNEN
 *
 *Generiert die Audiodatei, die von der Hauptseite wiedergegeben werden kann, da ein direkter Zugriff auf die Audiodatei nicht möglich sein soll.
 *Zunächst wird aber geprüft, ob der Nutzer angemeldet ist.
 */
 	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/*
 *Legt fest, als was das Skript interpretiert werden soll: hier als Audiodatei.
 */
 
	header('Content-Type: audio/mpeg');
	
/**
 *Die eigentliche Datei wird mit readfile augegeben.
 *Es ist kein "echo" nötig, es wäre sogar falsch, da dann auch die Anzahl Zeichen augegeben werden würde.
 */
 
	readfile('../Audio/Vanilla.mp3');
?>