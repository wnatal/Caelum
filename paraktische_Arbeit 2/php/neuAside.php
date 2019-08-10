<?php
/**
 *"RAHMEN" FÜR ASIDE.PHP
 *
 *Dieses Skript stellt aside.inc.php auf eigene Beine, bzw. es wird vervollständigt, so, dass man es für Ajax verwenden kann.
 *Bemerkung: Bei anderen Skripten habe ich die Hyprid-Funktion direkt implementiert, was aber tendenziell schwieriger umzusetzen ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	include('../php/datenbank.php');
	include('../php/aside.inc.php');
?>