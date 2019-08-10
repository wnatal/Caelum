<?php
/**
 *UPLOAD
 *
 *Mit diesem Skript werden die hochgeladenen Dateien überprüft und abgespeichert.
 *Zunächst die Kontrolle, ob noch angemeldet
 */
 	
 	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 * wichtige Daten aus Datenbank abrufen
 */
 
	include('../php/datenbank.php');
	include('../php/size.inc.php');
	
/**
 *Upload konrollieren und Speichern:
 *Das untenstehende Skript prüft ob die Daten korrekt hochgeladen sind und speichert bei erfolgreichem Upload die Datei an ihrem Bestimmungsort oder reagiert bei einem Fehler mit der passenden Meldung, die dann vom JavaScript ausgewertet kann.
 *In der ersten Zeile wird geprüft, ob alle benötigten Variablen verfügbar sind und prüft, ob die Variable 'upOrdner', die den Ordner, der zum Zeitpunk des Uploadbeginns offen war, repräsentiert, korrekt ist, indem er mit einem Array verglichen wird, wo alle bis jetzt erfolgreich (verifiziert) geöffneten Ordner als Pfad gespeichert sind.
 *Nach der Prüfung wird das Array mit den hochgeladenen Datein (bzw. die Informationen zu den Dateien) durchlaufen.
 */ 
	
	if(!empty($_FILES['upfile'])&&!empty($_POST['upOrdner'])&&in_array($_POST['upOrdner'], $_SESSION['protokoll'])){ 
		foreach($_FILES['upfile']['name'] as $key => $name){

/**
 *Zuerst wird geprüft, ob noch genug Speicherplatz für die Datei vorhanden ist. (Gegebenenfalls wird eine Warnmeldung ausgegeben)
 *Als nächstes wird geprüft, ob die Datei leer ist, was auch der Fall ist, wenn die Datei zu gross ist.
 *Danach wird der Dateiname validiert und wenn nötig abgeändert: Es werden nur normale Buchstaben, Zahlen, Bodenstrich, Bindestrich und Punkt angenommen. Einige häufige Umlaute werden in normale Buchstaben umgewandelt, andere Sonderzeichen werden gelöscht. Hat der Dateiname vor oder hinter dem Punkt keinen Inhalt mehr, wird für den fehlenden Teil der standard Name 'unbekannt' bzw. die Standarddateiendung .txt angefügt
 */
 
			$speicherTemp = $speicher + $_FILES['upfile']['size'][$key];
		 	if($speicherTemp <= $maxsize){
			  	if($_FILES['upfile']['size'][$key]!=0){
					$search  = array('.htaccess', '.htpasswd', 'ä', 'ö', 'ü', 'è', 'é', 'à', 'ê', 'ç');
					$replace = array('.txt', '.txt', 'ae', 'oe', 'ue', 'e', 'e', 'a', 'e', 'c');	
					$name = str_replace($search, $replace, $name);
					$name = preg_replace("/[^a-zA-Z_0-9\-\.]/" ,'' , $name);
					$name = preg_replace('/\.\.+/', '.', $name); //Mehrfachpunkte durch einen Punk ersetzen
					if($name==""){
						$name==".";
					}
					if(substr($name, 0, 1)=='.'){
						$name = 'unbekannt'.$name;
					}
					if(substr($name, -1)=='.'){
						$name = $name.'txt';
					}
					
/** 
 *Jetzt wird überprüft, ob die Datei bereits existiert und wird gegebenenfalls umbenannt.
 */
 
					$i=0;
					$nameTemp=$name;
					while(file_exists($_POST['upOrdner'].'/'.$nameTemp)){
						$i++;
						$nameTemp = str_replace('.', '_'.$i.'.', $name); 
					}
					$name=$nameTemp;

/**
 *Hier wird geprüft, ob irgend ein Fehler beim Upload aufgetreten ist, danach wird die temporäre Datei auf dem Server gespeichert. 
 *Zum Schluss wird eine Rückmeldung generiert, mit deren Hilfe JavaScript ein Icon anfordert.
 */						
 
 					if($_FILES['upfile']['error'][$key] == 0 && move_uploaded_file($_FILES['upfile']['tmp_name'][$key], $_POST['upOrdner'].'/'.$name)){
						$uploaded[] = $_POST['upOrdner'].'/'.$name;
						$speicher = $speicher + $_FILES['upfile']['size'][$key]; //$speicher aktualisieren
					}
					
/**
 *Die Kennzeichnung (*#fehler-+0 ... qrs.dd*db) der Fehler ist etwas umfangreich ausgefallen... naja.
 */
 
					else{
						$uploaded[] = '*#fehler-+0'.$name.'qrs.dd*db';//Fehler beim Speichern
					}
				}
				else{
					$uploaded[] = '*#fehler-+1'.$name.'qrs.dd*db';//Grösse Null
				}
			}
			else{
				$uploaded[] = '*#fehler-+2'.$name.'qrs.dd*db';//kein Speicherplatz
			}
		}
	}
	
/**
 *Das Array mit den Antworten für jede Datei, wird verpackt und so ausgegeben, dass es vom JavaScript ausgewertet kann
 */ 
	if (!empty($_POST['ajax'])){
		if($uploaded){
			die(json_encode($uploaded));
		}
	}
	
/**
 *der Upload ist einer der ersten Skripte, die ich realisiert habe, er basiert, wie auch das dazugehörige JavaScript auf einem Video-Tutorial [1], daher ist sein Aufbau eher untypisch, vorallem arbeite ich kaum mit JSON und brauche auch nicht den Befehl die(), dafür sind es dann aber meistens zum Grossteil meine eigenen Umsetzungen. Das komische Zeugs, dass bei einem Fehlschlag ausgegeben wird, hat nicht viel zu bedeuten, sondern dient nur dazu, dass ich die Fehlermeldungen eindeutig vom String unterscheiden kann, der bei Erfolg ausgegeben wird, wahrscheinlich habe ich es ein bisschen übertrieben
 */
 
/*[1]*
 *Titel des Tutorials:
 	*JavaScript Tutorials: AJAX File Upload with Progress Indicator
 *Source:
 	*http://www.youtube.com/watch?v=pTfVK73CUk8
	*http://www.youtube.com/watch?v=T-SawkxuYnI
	*http://www.youtube.com/watch?v=GMUOAN60vHY
	*http://www.youtube.com/watch?v=ImIIHXRlL4k
	*http://www.youtube.com/watch?v=7U-UbMEpjUY
 *bereitgestellt von:
 	*http://phpacademy.org
 */



?>
