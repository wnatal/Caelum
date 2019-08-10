<?php	
/**
 *MULTIFUNKTIONAL
 *DIRIGIERT DAS ERSTELLEN DER ICONS FÜR DIE ORDNEREBENEN UND WIRD BEIM ERSTELLEN VON ORDNERN GEBRAUCHT
 *
 *Dieses Skript validiert in erster Linie Wert für das Erstellen von Ordnern und dirigiert das Erstellen von Icons, wenn eine Ordnerebene aufgerufen wird (Werte für das Icon-Erstellen zusammensuchen/-basteln und die Icons für die Dateien und Ordner (getrennt von einander) alphabetisch ordnen. Nebenbei wird das Skript auch dazu gebraucht, um ein Datei-Icon (ohne andere Anweisung) anzufordern, da sonst für diesen Zweck eine eigene Datei nötig gewesen wäre.
 *Zuerst werden die Funktionen für das Icon-Erstellen eingebunden danach wird auf bestimmte Werte überprüft, um festzustellen, ob das Srkipt includiert worden ist oder nicht (jenachdem wird eine Session gestartet.
 */
 
	include("iconFunction.php");//Funktion einfügen
	if(!empty($_POST["upOrdner"])||!empty($_POST["changelink"])){
		session_start();		
	}
	if (!isset($_SESSION["idnummer"])){//prüfen ob Nutzer angemeldet
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *upOrdner zeigt an, dass ein Ordner erstellt werden soll oder ein Icon für eine Datei erstellt werden soll.
 */
 
	if(!empty($_POST["upOrdner"])){
		$dname = $_POST["dName"];

/**
 *Wurde der Ordner über die Seitenleiste erstellt, enthält upOrdner den Pfad zur Ordnerebene.
 *Dieser muss zunächst verifiziert werden. Dafür werden verschiedene Eigenschaften überprüft.
 */
 
		if(!empty($_POST["aside"])){
			include('datenbank.php');
			$ordner = substr(strstr(substr(strstr($_POST["upOrdner"], '/'), 1), '/'), 1).'/';
			$root = substr($ordner, 0, strpos($ordner, '/'));
			for($i=0; $i<count($aryGruppennamen); $i++){//root muss mit einer der Gruppen (oder der ID) des Nutzers übereinstimmen
				if($root == $aryGruppennamen[$i] && $root != ""){
					$testRoot = true;
					break;
				}
				else $testRoot = false;
			}
			$testLink = true;
			if(strpos($ordner.'/', '..')!==false){//kein '..' im Pfad
				$testLink = false;
			};
			if(!(($root == $_SESSION['idnummer'] || $testRoot==true)&&$testLink==true)){ //bei Fehler exit()
				exit();
			}
			$linkDir="../Dateien/".$ordner;
		}
		
/**
 *Wenn der Ordner normal in der aktuellen Ordnerebene erstellt oder nur ein Icon für eine Datei gefordert wurde, kann als Pfad der aktuelle, in der Session gespeicherte(, verifizierte) Pfad genommen werden.
 */
 
		else{
			$linkDir = $_SESSION["upOrdner"];
		}
		
/**
 *Nach dem Pfad wird der Ordner/Dateiname untersucht.
 */
 
		if($dname==""){
			exit();
		}
		if($dname !=".." && $dname !="." && $dname !=".htaccess"){
			if(!strstr($dname,'../')){

/**
 *Häufig verwendete Umlaute werden durch normale Buchstaben ersetzt, danach werden alle "anormalen" Zeichen aus dem Name gelöscht.
 *Bei Ordnern wird noch geprüft, ob ein gleichnamiger Ordner schon existiert.
 */
 
				$search  = array('ä', 'ö', 'ü', 'è', 'é', 'à', 'ê', 'ç');
				$replace = array('ae', 'oe', 'ue', 'e', 'e', 'a', 'e', 'c');	
				$dname = str_replace($search, $replace, $dname);
				$dname = preg_replace("/[^a-zA-Z_0-9\-\.]/" ,'' , $dname);
				if($dname==""||$dname=="."||$dname==".."){
					$dname = "_";
				}
				if(!empty($_POST["erstellen"])){//wenn ein Ordner
					$dname=str_replace('.','_',$dname);			
					$link = $linkDir. '/'. $dname;
					if(is_dir($link)){//Auf Dublikat prüfen
						echo 'error:Dublikat';
						exit();
					}
		
/**
 *Der Ordner wird erstellt und das passende Icon wird erzeugt.
 */
 
					mkdir($link);
				}
				iconerstellen($linkDir, $dname);//Icon erzeugen
			}
		}
					
	}
	
/**
 *Wenn die Hauptseite geladen wird (include) oder die Ordnerebene gewechselt wird, wird das folgende ausgeführt:
 */

	else{
		include('datenbank.php');
		if(!empty($_POST["changelink"])){
			$ordner = substr(strstr(substr(strstr($_POST["changelink"], '/'), 1), '/'), 1).'/'; //Pfad richtig zuschneiden ('../Dateien/' abschneiden)
		}
		else{
			$ordner = 'root/';//Default-Wert für Pfad (
		}
		$_SESSION['ordner']=$ordner; //$ordner für andere Skripte zur Verfügung stellen
		
/**
 *Mit ordnerLink Funktion wird der eigentliche Pfad erzeugt und ausgegeben, dabei findet auch eine validation statt.
 *Die Funktion erzeugt ausserdem eine anklickbare Version des Pfades, die zuoberst im Hauptbereich angezeigt wird.
 *Der neuerzeugte Pfad wird in einem Array gespeichert. Andere Skripte können dieses Array zur einfachen Validation eines Pfades verwenden.
 *Danach wird der Ordner selbst in eine eigene Variable gespeichert. Variable wird von allen Skripten gebraucht, die auf der aktuellen Ordnerebene Änderungen vornehmen wollen.
 */
 
		$linkDir = ordnerLink($ordner,$aryGruppennamen, $aryGruppenkennung, false);
		array_push($_SESSION["protokoll"], $linkDir);
		$_SESSION['upOrdner']=$linkDir;
		
/**
 *Jetzt wird der Ordner geöffnet und Dateien und Ordner (getrennt von einander) werden alphabetisch geordnet.
 */
 
		$handle = scandir($linkDir);
		$handle1=array();
		$handle2=array();
		for($i=0; $i<count($handle); $i++){ //Datein von Ordner trennen
			if(strpos($handle[$i],'.')){
				array_push($handle2, $handle[$i]);
			}
			else{
				array_push($handle1, $handle[$i]);
			}
		}
		natcasesort($handle1);//Ordner sortieren
		natcasesort($handle2);//Dateien sortieren
		$handle = array_merge($handle1, $handle2); //Listen wieder zusammenfügen
		
/**
 *Nach dem Sortieren, wird Ordner für Ordner und Datei für Datei ein Icon erstellt.
 */
 
		foreach( $handle as $dname){
			if($dname !=".." && $dname !="."  && $dname !=".htaccess"){
		
/**
 *Wenn man sich auf der obersten Ordnerebene befindet, müssen noch einige spezielle Werte der Icon-Funktion übergeben werden.
 */
 
				if($linkDir=='../Dateien' ){
					
/**
 *Der Hauptordner der "privaten" Ordnerstruktur wird Privat genannt.
 */
					if($dname == $_SESSION['idnummer']){
						iconerstellen($linkDir, $dname, 'Privat');
					}
						
/**
 *Bei Gruppen wird der Gruppenname bestimmt und nach dem Administrator der Gruppe gesucht, um diesen in das jeweilige Icon namentlich einzufügen.
 */
					else{
 
						for($i=0; $i<count($aryGruppennamen); $i++){
							if($dname == $aryGruppennamen[$i] && $dname != ''){//wenn Übereinstimmung...
								$mitglieder = simplexml_load_string($dsatzGrup[$i]['nutzerIDs'],'SimpleXMLElement');//Nutzer-Liste der Gruppe
								$mitglied=$mitglieder->xpath('id[@Administrator]');//ID des Administrators						
								$con = mysqli_connect("kssb.ch","natal","natal00120");
								mysqli_select_db($con, "kssb_ch_matura");	
								$res = mysqli_query($con, "SELECT nachname, vorname, user FROM nutzerdaten WHERE idnummer  LIKE BINARY '".$mitglied[0]."'");
								$Administrator = mysqli_fetch_assoc($res); //Datenbank-Ergebnis in Variable speichern.
								mysqli_close($con);								
								iconerstellen($linkDir, $dname, $dsatzGrup[$i]['gruppe'], $aryAdministrator[$i], $Administrator['user']);//Icon erzeugen
								break;
							}
						}
					}
				}
	//Bei
				else{
					iconerstellen($linkDir, $dname);//Icon erzeugen
				}
			}
		}		
	}


	?>