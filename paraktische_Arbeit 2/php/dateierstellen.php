<?php
/**
 *DATEI SPEICHERN
 *
 *Das Skript speichert die mit einem der beiden Editoren erstellten Dateien auf dem Server ab und erstellt mit iconerstellen() ein passendes Icon.
 */
 
	include("iconFunction.php");	//Funktion-Sammlung einfügen
 
/**
 *dateiendung(): Überprüft die Dateiendung. 
 *Der Richtext wird momentan nur als .phtml gespeichert, später soll der Nutzer die Wahl zwischen .pthml und .html haben.
 *(Momentan entspricht .phtml .html und wie die Text-Dateien können diese noch nicht wieder mit den Editoren geöffnet werden)
 */ 
	
	function dateiendung($testO){
		$dateiTyp = array('.txt', '.phtml', '.html');
		foreach($dateiTyp as $i) {
			if($testO==$i){
				return true;
			}
		}
		return false;
	}
	
/**
 *Zunächst wird wie immer überprüft, ob der Nutzer angemeldet ist.
 *Danach wird geprüft, ob alle benötigten Werte übergeben worden sind
 */
 
	session_start();	
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}	
	if(!empty($_SESSION["upOrdner"])&&!empty($_POST["dname"])&&!empty($_POST["dateiinhalt"])&&isset($_POST["kontrolle"])){
		$dname = $_POST["dname"];
		if($dname=="")exit(); //Bei leerem Dateiname wird Skript beendet.
		$linkDir = $_SESSION["upOrdner"];
		$dateiinhalt = $_POST["dateiinhalt"];
		
/**
 *Die unteren zwei Zeilen stellen sicher, dass die erzeugten Textdatein (Bei den anderen Dateitypen im Prinzip egal) mit allen Texteditoren richtig dargestellt werden.
 *Viele Programme stellen einen Zeilenumbruch mit einem einfachen \n nicht dar, sie interpretieren nur \r\n.
 *Im ersten Schritt müssen alle \r\n durch \n ersetzt werden, da sonst aus \r\n ein \r\r\n würde.
 */
 
		$dateiinhalt = str_replace("\r\n", "\n", $dateiinhalt);
		$dateiinhalt = str_replace("\n", "\r\n", $dateiinhalt);
		
/**
 *Dateiname wird überprüft und angepasst.
 */
 
 		$search  = array('ä', 'ö', 'ü', 'è', 'é', 'à', 'ê', 'ç');
		$replace = array('ae', 'oe', 'ue', 'e', 'e', 'a', 'e', 'c');	
		$dname = str_replace($search, $replace, $dname);
		$dname = preg_replace('/\.\.+/', '.', $dname);
		$dname = preg_replace("/[^a-zA-Z_0-9\-\.]/" ,'' , $dname);
		if($dname==""||$dname=="."||$dname==".."){
			$dname==".";
		}
		if(substr($dname, 0, 1)=='.'){
			$dname = 'unbekannt'.$dname;
		}
					
/**
 *Wird die Datei zum ersten Mal gespeichert, wird geprüft, ob bereits eine gleichnamige Datei existiert.
 *Wenn ja, wird eine Zahl zum Namen hinzugefügt, dieser Vorgang wird wiederholt, bis keine gleichnamige Datei mehr gefunden wird.
 */
 
		if($_POST["kontrolle"]!='0'||!strstr($dname, '.phtml')){
			$i=0;
			$nameTemp=$dname;
			while(file_exists($linkDir.'/'.$nameTemp)){
				$i++;
				$nameTemp = str_replace('.', '_'.$i.'.', $dname); 
			}
			$dname=$nameTemp;
		}
		$link = $linkDir. '/'. $dname;//Dateipfad
		
/**
 *Die Dateiendung wird noch geprüft, danach wird die Datei gespeichert und ein Icon erstellt.
 */
 
		if(dateiendung(strstr($dname,"."))){
			$datei = fopen($link, "a");
			if($datei){
				fputs($datei, $dateiinhalt);
				fclose($datei);
				if($_POST["kontrolle"]!='0'){//beim ersten Speichern...
					iconerstellen($linkDir, $dname);//...wird ein Icon erstellt.
				}
			}
		}				
	}
?>