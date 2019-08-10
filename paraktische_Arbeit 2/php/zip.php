<?php
/**
 *ZIP ERSTELLEN
 *
 *Dieses Skript erstellt Zipdateien
 */
 
/**
 *zipR(): Sucht rekursiv nach allen Dateien, die in das Archiv übertragen werden sollen und speichert den Pfad zur Datei und den Dateinamen jeweils in einem Array. Man muss den ".."-Ordner und den "."-Ordner ausschliessen, sonst würde die Funktion "wuchern" und zu keinem Ende gelangen. Das Funktionsprinzip ist, dass bei einem Ordner, die Dateien ausgelesen werden und der Funktion wiederum übergeben werden (Rekursion).
 *$link: Pfad zum Ordner
 *$ziplink: Zukünftiger Pfad des Ordners innerhalb des Archives
 */
	
	function zipR($link, $ziplink){
		if(is_dir($link)){//Ordner
			$handle = opendir($link);
			while($dname = readdir($handle)){
				if($dname !=".." && $dname !="."  && $dname !=".htaccess"){
					$newlink=$link.'/'.$dname;
					$newzlink=$ziplink.'/'.$dname;
					zipR($newlink, $newzlink);
				}
			}
			closedir($handle);
		}
		else{//Datei
			global $ordnerArray; //global: Referenz auf global angelegte Variable
			global $zipArray;
			array_push($zipArray, $ziplink);
			array_push($ordnerArray, $link);
		}
	}
	
/**
 *Dieses Skript generiert Zip-Archiv, beispielsweise um mehrere Dateien inklusive Ordnerstruktur zu downloaden
 *Zunächst wird aber geprüft, ob der Nutzer angemeldet ist.
 */

	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}

/**
 *Damit das Skript funktioniert, muss die Datenbank.php-Datei eingebunden werden und die iconFunction.php-Datei, es handelt sich dabei um eine Sammlung von Funktionen, um ein Icon zu erstellen.
 *Danach wird das per POST übergebene Array mit den Dateien und Ordner die "gezipt" werden sollen von einem JSON-Objekt in ein Array umgewandelt. Da Arrays nicht direkt per POST übertragen werden können (auch nicht per GET), wurde es in ein JSON-Objekt gepackt.
 */
	
	include('../php/iconFunction.php');
	$link = str_replace("\\", "", $_POST['array']); //entfernt die Backslashes, die sich aus irgendeinem Grund in den Json-String schmuggeln.
	$link=json_decode($link, true);
	$ordnerArray = array(); //Array für Pfade zu den zu zipenden Dateien
	$zipArray = array(); //Array für neuen Pfad innerhalb des Zip-Archives
	
/**
 *Sind mehrere Dateien und Verzeichise ausgewählt, wird zunächst für jede ausgewählte Datei und jeden ausgewählten Ordner die Funktion zipR() aufgerufen, die die Datei bzw. die in den Ordnern enthaltenen Dateien $ordnerArray hinzufügen, oder besser gesagt, der Pfad zu diesen Dateien, und den zukünftigen Speicherort innerhalb desArchives in $zipArray speichert.
 */
 
	if(count($link)>1){
		foreach($link as $key => $oldlink){
			$name = substr(strrchr($oldlink, "/"), 1);
			zipR($oldlink, $name);
			
/**
 *Sind mehrere Dateien und Verzeichnisse ausgewählt, muss man auch einen gemeinsamen Archivnamen bestimmen, dazu wird aus dem Pfad zur ersten Datei oder zum ersten Ordner der Dateiname abgeschnitten, um den Pfad zur Ordnerebene, auf der die ausgewählten Dateien und Ordner liegen, zu erhalten. Danach wird der Name für den Ordner bestimmt (standardmässig ZipOrdner.zip) und geprüft, ob dieser schon existiert und gegebenenfalls angepasst und an den Pfad zum Elternverzeichnis angefügt..
 */
 
			if($key==0){
				$filename = substr($link[0], 0, strrpos($oldlink, '/'));
				$i=0;
				$dname="ZipOrdner.zip";
				$nameTemp=$dname;
				while(file_exists($filename.'/'.$nameTemp)){
					$i++;
					$nameTemp = str_replace('.', '_'.$i.'.', $dname); 
				}
				$filename=$filename.'/'.$nameTemp; //Elternverzeichnis-Pfad + Zip-Archiv-Name
			}
		}
	}
	
/**
 *Bei nur einer Datei oder einem Ordner gestaltet sich die Sache ein bisschen einfacher. Als Name wird der Dateiname bzw. der Ordnername genommen (bei einer Datei wird der Punkt durch einen Unterstrich ersetzt), danach wird auch hier geprüft, ob der Pfad schon existiert und wird gegebenenfalls angepasst.
 */
 
	else{
		$name = substr(strrchr($link[0], "/"), 1);
		zipR($link[0], $name);
		$filename = str_replace('.', '_', substr($link[0], 3)).'.zip';
		$i=0;
		$nameTemp=$filename;
		while(file_exists(substr($link[0], 0, 3).$nameTemp)){
			$i++;
			$nameTemp = str_replace('.', '_'.$i.'.', $filename); 
		}
		$filename= substr($link[0], 0, 3).$nameTemp;
	}
	
/**
 *Im letzten Teil wird das eigentliche Zip-Archiv erstellt, in welches alle ermittelten Dateien gespeichert werden. Zunächst wird aber noch geprüft, ob der Dateipfad zulässig ist, indem er mit einem Array verglichen wird, wo alle bis zu diesem Zeitpunkt aufgerufenen Ordnerebenen gespeichert sind. 
 *Bemerkung: leere Ordner können nicht archiviert werden.
 */
 
	$linkDir = substr($link[0], 0, strrpos($filename, '/'));
	if(!in_array($linkDir, $_SESSION['protokoll'])){
		exit();
	}
	$dname = substr(strrchr($filename, "/"), 1);
	if(count($ordnerArray)>0){
		$zip = new ZipArchive();
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
    		exit();
		}
		
/*
 *die im $ordnerArray gespeicherten Dateien werden nun dem neu ereugten Array hinzugefügt. Danach wird das Ziparchiv geschlossen, d.h. das Ziparchiv wurde erstellt.
 *Hat der Nutzer während dem Erstellen des Archives nicht den Ordner gewechselt, wird noch ein Icon erzeugt.
 */
 
		foreach($ordnerArray as $key => $oldlink){
		   $zip->addFile($ordnerArray[$key], $zipArray[$key]);	
		}
		$zip->close();
		if($_SESSION['upOrdner'] == $linkDir){
			iconerstellen($linkDir, $dname);
		}
	}
	
/**
 *Die Informationen, wie man ZipArchiv in PHP benutzt, habe ich aus php.net, die Programmierlogik ist aber von mir alleine ... wahrscheinlich gäbe es bessere Wege.
 */
?>