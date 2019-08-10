<?php
/**
 *NEUE-NACHRICHTEN-MELDUNG
 *
 *Sucht nach neuen Nachrichten und gibt die Anzahl der gefundenen Nachrichten in einem Div aus.
 *Zunächst wird aber geprüft, ob der Nutzer angemeldet ist.
 */
 
	if (!isset($_SESSION["idnummer"])){
		echo 'E';
		exit();
	}
	$files=array();
	$i=0;
	
/**
 *Verzeichnis mit Nachrichten des Nutzers öffnen und Dateinamen nach 'new' durchsuchen.
 */
 
	$handle = opendir('../Nachrichten/'.$_SESSION['idnummer']);
   	while ($datei = readdir($handle)) {
		if(strrpos($datei, 'new')){
			$i++;
		}
	}
	closedir($handle);
	if($i!=0){
		echo '<div>'.$i.'</div>';//Ausgabe
	}
?>