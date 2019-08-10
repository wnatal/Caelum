<?php	
/**
 *LISTE ALLER MITGLIEDER
 *
 *Hier wird dem Formular zum Erstellen der Gruppen eine Liste von allen Nutzern zur Verfügung gestellt. Die Liste wird alphabetisch geordnet von der Datenbank ausgeliefert.
 *Zuvor wird aber geprüft, ob der Nutzer angemeldet ist.
 */
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	$con = mysqli_connect("kssb.ch","natal","natal00120");
	mysqli_select_db($con, "kssb_ch_matura");
	$res = mysqli_query($con, "SELECT idnummer, nachname, vorname, user FROM nutzerdaten WHERE idnummer NOT LIKE BINARY '".$_SESSION['idnummer']."' ORDER BY nachname ASC, vorname ASC, user ASC");
	while($dsatz = mysqli_fetch_assoc($res)) { //Nutzer für Nutzer ausgeben:
		echo '<option value="'.$dsatz['idnummer'].'">'.$dsatz['nachname'].' '.$dsatz['vorname'].' ('.$dsatz['user'].')</option>';
	}
	mysqli_close($con);
?>