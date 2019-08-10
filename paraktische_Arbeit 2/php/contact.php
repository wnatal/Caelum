<?php
/**
 *LISTE ALLER KONTAKTE
 *
 *Hier wird eine Liste aller Nutzer für den Social-Bereich generiert. Es erfüllt dabei zwei Funktionen, zum einen generiert es eine Liste für die Seitenleiste, zum anderen für den Hauptbereich. Zunächst wird aber (mit Hilfe der idnummer) geprüft, ob der Nutzer angemeldet ist.
 */
 
 	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Nachname, Vorname, User und idnummer werden für alle Nutzer von der Datenbank angefordert.
 */
 
	$con = mysqli_connect("kssb.ch","natal","natal00120");
	mysqli_select_db($con, "kssb_ch_matura");
	$resCont = mysqli_query($con, "SELECT nachname, vorname, user, idnummer FROM nutzerdaten");
	mysqli_close($con);
	
/**
 *Daten werden Nutzer für Nutzer mit HTML strukturiert, damit der Browser die Daten darstellen und per CSS formatieren kann.
 *Hierbei wird auch zu jedem Nutzer ein Event-Handler hinzugefügt, mit dem später das Fenster zum Schreiben einer Nachricht geöffnet wird.
 */	
			
	echo '<span>Kontakte <br>(nachricht an:)</span>';
	echo '<ul class="navigation last-child">';
	while($dsatzCont= mysqli_fetch_assoc($resCont)){		
		if($dsatzCont['idnummer']!=$_SESSION['idnummer']){
			echo '<li><a onclick="nachricht('."'".$dsatzCont['idnummer']."'".')">'.$dsatzCont['nachname'].' '.$dsatzCont['vorname'].'</a></li>';
		}
	}
	echo '</ul>';
	
?>