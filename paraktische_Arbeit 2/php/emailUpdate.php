<?php
/**
 *NEUE-NACHRICHTEN-MELDUNG (Ajax)
 *
 *Includiert newmail.php, das ermittelt, ob eine neue Nachricht angekommen ist.
 *Dieses Gerüst hier ist nur nötig, da das eigentliche Skript inkludiert werden muss (eine Session muss gestartet werden; newmail.php hat keine Hybrid-Funktionalität zwischen eigenständig und includieren)
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	include('newmail.php');
?>