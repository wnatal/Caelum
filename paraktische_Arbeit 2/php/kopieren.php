<?php
/**
 *KOPIEREN UND VERSCHIEBEN
 *
 *Verschiebt und kopiert Dateien und Verzeichnisse
 */
 
/**
 *kopieren(): Kopiert Datei/Ordner ($oldlink) in das angegebene Verzeichnis ($newlink).
 *$iconset: gibt an, ob ein Icon erstellt werden soll.
 *$kopieren: gibt an, ob kopiert oder verschoben wird.
 *$kontrollieren: ob auf Duplikate geprüft werden soll
 */
 
 	function kopieren($oldlink, $newlink, $iconset, $kopieren, $kontrollieren){
		$dname = substr(strrchr($oldlink, "/"),1);
		
/**
 *Zunächst wird überprüft, ob es sich um einen Ordner oder um eine Datei handelt.
 *Handelt es sich um einen Ordner, wird, falls gewünscht, dieser beim Kopier- oder Veschiebevorgang umbenannt, sollte dieser bereits existieren. Mit einer While-Schleife wird der Vorgang wiederholt, solange bis nichts mehr überschrieben wird. Je nachdem ob es sich insgesammt um einen Verschiebevorgang oder ein Kopiervorgang handelt, wird der Ordner auf eine andere Weise umbenannt.
 */
 
		if(is_dir($oldlink)){
			if($kontrollieren){
				if($kopieren==1){//Kopieren
					$i=1;
					$dname = $dname.'_Kopie'.$i;
					while(file_exists($newlink.'/'.$dname)){
						$i++;
						$dname = substr($dname, 0, -1).$i; 
					}				
				}
				else{//Verschieben
					$i=0;
					$nameTemp=$dname;
					while(file_exists($newlink.'/'.$nameTemp)){
						$i++;
						$nameTemp = $dname.'_'.$i; 
					}
					$dname=$nameTemp;
				}
			}
			$link = $newlink.'/'.$dname;
			
/**
 *Zunächst wird am Zielort ein Ordner mit dem eventuell geänderten Ordnernamen erstellt, eventuell ein Icon erstellt und danach wird der Inhalt des Ordners geöffnet, um diesen mit der Funktion Datei für Datei, Ordner für Ordner in den neu erstellten Ordner zu kopieren (->Rekursion).
 */
 
			mkdir($link); //Ordner erstellen
			if($iconset){//Falls der Ordner in der aktuell angewählten Ordnerebene gespeichert wird
				iconerstellen($newlink, $dname); //Icon erstellen
			}						
			$handle = opendir($oldlink);
			while($name = readdir($handle)){
				if($name !=".." && $name !="."  && $name !=".htaccess"){
					$newoldlink=$oldlink.'/'.$name;
					kopieren($newoldlink, $link, false, $kopieren, false);//Nach dem ersten Aufruf muss nicht mehr auf Duplikate geprüft werden
				}
			}
			closedir($handle);
		}
/**
 *Handelt es sich um eine Datei, wird zunächst, falls gefordert, wie bei den Ordnern auf Duplikate geprüft.
 */ 
		else{
			if($kontrollieren){
				if($kopieren==1){
					$i=1;
					$dname = str_replace('.', '_Kopie'.$i.'.', $dname);
					while(file_exists($newlink.'/'.$dname)){
						$dname = str_replace($i.'.', ($i+1).'.', $dname);
						$i++; 
					}
				}
				else{
					$i=0;
					$nameTemp=$dname;
					while(file_exists($newlink.'/'.$nameTemp)){
						$i++;
						$nameTemp = str_replace('.', '_'.$i.'.', $dname); 
					}
					$dname=$nameTemp;
				}
			}
			$new = $newlink.'/'.$dname;
			
/**
 *Danach wird die Datei an den neuen Speicherort kopiert und wenn nötig, wird ein Icon erstellt.
 */
 
			copy($oldlink, $new); //kopieren
			if($iconset){
				iconerstellen($newlink, $dname); //Icon erstellen
			}
		}
	}
	
/**
 *linkverifizieren(): Mit dieser Funktion wird $ordner validiert, der wesentliche Teil der Kontrolle ist aber auf verifizieren.inc.php ausgelagert.
 */
 
	function linkverifizieren($ordner, $aryGruppennamen){
		include('verifizieren.inc.php');
		if(($root == $_SESSION['idnummer'] || $testRoot==true)&&$testLink){//bei erfolgreicher Prüfung wird der fertige Pfad zurückgegeben...
			return $linkDir = '../Dateien/'.$ordner;
		}
		else{
			exit(); //sonst wird das Skript beendet.
		}
	}
	
/**
 *Zunächst werden benötigte Skripte inkludiert und die per POST übermittelte Werte werden in normale Variablen gespeichert.
 *Im zweiten Schritt werden die Pfade kontrolliert, dazu müssen sie zuerst gekürzt werden (wird beim verifizieren wieder rückgängig gemacht).
 */
 
	session_start();
	include("iconFunction.php");
	include("datenbank.php");
	$oldlink = $_POST["oldlink"];
	$newlink = $_POST["newlink"];
	$kopieren = $_POST["copy"];
	$ordner = substr(strstr(substr(strstr($oldlink, '/'), 1), '/'), 1).'/';
	$oldlink=linkverifizieren($ordner, $aryGruppennamen);
	$ordner = substr(strstr(substr(strstr($newlink, '/'), 1), '/'), 1).'/';
	$newlink=linkverifizieren($ordner, $aryGruppennamen);
	$iconset=false;
	
/**
 *Als nächstes werden zwei Sonderfälle behandelt:
 *Erster Fall: Wenn ein Ordner in sich selbst kopiert werden soll. Das In-sich-Verschieben ist aus logischen Gründen nicht möglich, beim Kopieren würde es normalerweise zu einer Art Rückkopplung kommen, da die gerade kopierten Dateien erneut kopiert würden (wegen dem rekursiven Vorgehen). In diesem Fall wird der Ordner in ein temporäres Verzeichnis kopiert (und von dort dann in den Zielort verschoben).
 */
 
	if(strlen($oldlink)>29&&strlen($newlink)>29){//Zusätzliches Kriterium für Pfade	
		if(strpos($newlink.'/', $oldlink.'/')!==false){
			if($kopieren==0){
				 echo '<p class="fehler" style="display:none">Fehler</p>';
				 exit();
			}
			$temporar = '../Dateien/'.$_SESSION['idnummer'].'/Temporar'.substr($oldlink, 10);
			mkdir($temporar, 0, true);//temporären Ordner erstellen
			kopieren($oldlink, $temporar, false, $kopieren, true);//in temporären Ordner kopieren
			$oldlink=$temporar.'/'.substr(strrchr($oldlink, "/"),1).'_Kopie1'; //alter Pfad: Pfad zu Temporärem Ordner
			$kopieren=0; //von temporärem Ordner in neues Verzeichnis verschieben, nicht kopieren
		}
	
/**
 *Zweiter Fall: Wird in die gleiche Ordnerebene verschoben, wird das Skript abgebrochen, da dies nur unnötig Rechenleistung benötigte. Beim Kopieren muss in diesem Fall ein Icon erstellt werden (->iconset = true);
 */
 
		else if($newlink == substr($oldlink,0,(strrpos($oldlink, '/')))){
			if($kopieren==0){
				echo '<p class="fehler" style="display:none">unnötig</p>';
				exit();
			}
			$iconset=true;	
		}
		
/**
 *Die Dateien und Ordner werden zunächst kopiert. Sollen die Dateien und Ordner "nur" verschoben werden, werden die alten Dateien und Ordner nach dem Kopieren gelöscht (per delDatei.php).
 */
		
		kopieren($oldlink, $newlink, $iconset, $kopieren, true);
		if($kopieren==0){
			if(isset($temporar)){
				$oldlink='../Dateien/'.$_SESSION['idnummer'].'/Temporar';
			}
			include('../php/delDatei.php');
		}
	}
	else{
		echo '<p>nicht erlaubt!</p>';//Fehlermeldung
	}
	

?>