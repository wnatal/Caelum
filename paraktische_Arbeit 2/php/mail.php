<?php 
/**
 *NACHRICHTEN ANZEIGEN
 *
 *Dieses Skript ist für das Bereitstellen der Nachrichten im Social-Bereich verantwortlich. Es wird nicht zwischen Posteingang und Poastausgang unterschieden.
 *Zunäst wird geprüft ob der Nutzer überhaupt anemeldet ist.
 */
    
    session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Hier wird der Ordner mit den Nachrichten geöffnet, die Dateinamen der Nachrichten werden in einem Array gespeichert.
 *Das Array wird (absteigend) sortiert. (Der Dateiname beginnt mit dem Datum und der Zeit, so werden die Dateien nach Datum (absteigend) sortiert.)
 */
 
	$files=array();
	$handle = opendir('../Nachrichten/'.$_SESSION['idnummer']);
   	while ($datei = readdir($handle)) {
	  		$files[] = $datei;
	}
	closedir($handle);
	rsort($files);
	
/**
 *Nachname, Vorname und idnummer aller Nutzer aus Datenbank abrufen.
 *Das Ergebnis wird in ein Array gespeichert, wobei die idnummer als Key dient und Vor- und -Nachname als ein Wert gespeichert werden.
 */
 
	$con = mysqli_connect("kssb.ch","natal","natal00120");
	mysqli_select_db($con, "kssb_ch_matura");
	$res = mysqli_query($con, "SELECT nachname, vorname, idnummer FROM nutzerdaten");
	mysqli_close($con);
	$daten=array();
	while($dsatzCont= mysqli_fetch_assoc($res)){
		if($dsatzCont['idnummer']!=$_SESSION['idnummer']){
			$daten[$dsatzCont['idnummer']]=$dsatzCont['nachname'].' '.$dsatzCont['vorname'];
		}
	}
	
/**
 *Markup mit Nachrichtten aus Daten erstellen:
 */
 
	echo '<span>Nachrichten:<span>';
	$i=0;
	foreach($files as $mail){
		if($mail!="."&&$mail!=".."){
			$filename = '../Nachrichten/'.$_SESSION['idnummer'].'/'.$mail;
			$i++;
			
/**
 *Es werden maximal 80 Nachrichten angezeigt, ältere Nachrichten werden dann gelöscht.
 *Zunächst wird die im Dateiname enthaltene idnummer mit den Schlüsseln des vorhin erstellten Arrays verglichen, um herauszufinden, wer hinter der Nachricht steckt.
 */
 
			if($i<80||strrpos($mail, 'new')){
				foreach($daten as $key => $name){
					if(strrpos($mail, $key)){
						$person = $name;
						break;
					}
				}
				if(!isset($person)){ //Fallback
					$person = 'unbekannt';
				}
				
/**
 *neuen Nachrichten wird eine spezielle CSS-Klasse zugeordnet, gleichzeitig werden sie in alte Nachrichten umbenannt, so dass sie beim nächsten Aufruf normal dargestellt werden.
 */
 
				if(strrpos($mail, 'new')){
					echo '<div class="nachricht neu ';
					rename($filename, $filename = str_replace('new', 'old', $filename));
				}
				else{
					echo '<div class="nachricht ';
				}
				
/**
 *Ist im Dateiname die eigene ID enthalten, heisst das, dass es sich um eine gesendete Nachricht handelt, sonst um eine empfangene Nachricht.
 */
 
				if(strrpos($mail, $_SESSION['idnummer'])){
					echo 'vonmir"><div><div class="vonmir">für: '.$person.'</div>';
				}
				else{
					echo 'furmich"><div><div class="furmich">von: '.$person.'</div>';
				}
	
/**
 *Das Empfangs- bzw. Sendedatum:
 *Obwohl im Dateiname gespeichert, wird hier, der Einfachheit wegen, das Datum aus den Metadaten gelesen.
 *Danach wird der eigentliche Dateiinhalt eingelsen. 
 */				
 
				echo '<div>'.date('d.m.Y - H:i', filemtime($filename)).'</div><div>';
				readfile($filename);
				echo '</div></div></div>';
			}
			else{
				unlink($filename); //löscht die alten Nachrichten.
			}
		}
	}
?>