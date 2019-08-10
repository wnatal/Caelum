<?php
/**
 *SUCHE
 *
 *Mit diesem Skript wird nach dem eingegebenen Suchbegriff bzw. Suchbegriffen in den Dateien und Ordner der momentan ausgewählten Ordnerebene gesucht. Je nach übergegebenen Parametern werden auch die Unterverzeichnisse in die Suche mit einbezogen.
 *Es werden auch Suchgatter ausgewertet. Damit der Nutzer weiss, wie er eine komplzierte Suchanfrage zu formulieren hat, wird ihm bei der Suche auch eine Hilfestellung zur Formulierung bereitgestellt.
 */
 
/**
 *search(): Leitet die eigentliche Suche ein. 
 *Dazu wird zuerst der zu durchsuchende Ordner geöffnet und der Dateien werden der Funktion filter() übergeben, die die Dateinamen mit dem Suchstring vergleichen.
 *Auf oberster Ebene (wo alle Ordner mit IDs bezeichnet sind), muss der Funkion noch zusätzlich übergeben werden, was der (Anzeige-)Name zur ID ist.
 *Will der Nutzer auch die Unterordner in die Suche mit einbeziehen, wird search() für die gefundenen Unterordner aufgerufen (Rekursion).
 *$linkDir: Pfad zur Ordnerebene
 *$dsatzGrup, $aryGruppennamen: für die Übersetzung der IDs
 *$suche: Suchbegriff/-string
 *$komplex: wenn true (oder zu true auswertbar), dann werden auch die Unterordner durchsucht.
 */
  
 	function search($linkDir, $dsatzGrup, $aryGruppennamen, $suche, $komplex){
		$handle = scandir($linkDir);	
		foreach($handle as $dname){
			if($dname !=".." && $dname !="."  && $dname !=".htaccess"){//Filterung
				if($linkDir=='../Dateien' ){//Auf oberster Ordnerebene muss übersetzt werden.
					if($dname == $_SESSION['idnummer']){//privates Verzeichnis
						filter($suche, $linkDir, $dname, 'Privat');
	 				}
					else{
						for($i=0; $i<count($aryGruppennamen); $i++){//Gruppen
							if($dname == $aryGruppennamen[$i]){
								filter($suche, $linkDir, $dname, $dsatzGrup[$i]['gruppe']);
								break;
							}
						}
					}
				}
				else{
					filter($suche, $linkDir, $dname, false);
				}
				if($komplex){//Unterordner mit einbeziehen in Suche
					$link=$linkDir.'/'.$dname;
					if(is_dir($link)){
						search($link, $dsatzGrup, $aryGruppennamen, $suche, 1);
					}
				}
			}
		}
	}
	
/**
 *filter(): Wandelt zunächst die zugelassenen Suchgatter in eine Einheitliche Schreibweise um. Wenn ein NOT-Gatter enthalten ist, wird der Suchstring dort (beim ersten) in zwei Teile geschnitten. Der Suchstring oder die beiden Suchstringstücker werden (einzeln) searchAnalysieren() übergeben, diese analysiert die Funktion auf die restlichen Suchgatter und gibt 1 zurück wenn es eine Übereinstimmung gibt. Wenn der normale Teil 1 zurückgibt und der verneinte Teil nicht 1, heisst das, dass der untersuchte Datei-/Ordnername mit der Suchanfrage übereinstimmt. Ist das der Fall, wird ein Icon der Datei/Ordners ausgegeben.
 *$suche: Suchstring
 *$linkDir: Pfad zur Datei/Ordner
 *$dname: Datei bzw. Ordnername
 *$opition: Falls nötig, der Name der dem Nutzer angezeigt wird (nicht die ID)
 */
 
	function filter($suche, $linkDir, $dname, $option){
		global $zaehler; //bezieht sich auf Variable, die zählt, vieviele Dateien/Ordner gefunden worden sind.
		if($option){
			$name=$option;
		}
		else{
			$name=$dname;
		}
		$suche = ' '.$suche;
		$gatter = array('&&', ' UND ', ' AND ', '||', ' ODER ', '!', ' NICHT ', ' XODER ', "'", '"', '(', ')', '?');
		$replace = array(' ', ' ', ' ', ' OR ', ' OR ', ' NOT ', ' NOT ', ' XOR ', '', '', ' ', ' ', ' ');
		$suche = str_replace($gatter, $replace, $suche);
		$not = explode(" NOT ", $suche, 2);//beim ersten NOT wird der String geteilt.
		$y=searchAnalysieren($not[0], $name);
		$z = 0;
		if(!empty($not[1])){//falls der String geteilt wurde...
			$not = str_replace(" NOT ", " ", $not[1]); //übrige NOT werden gelöscht
			$z=searchAnalysieren($not, $name);
		}
		if($y==1 && $z!=1){//Wenn Datei den Suchkriterien entspricht
			iconerstellen($linkDir, $dname, $name);
			$zaehler++;
		}
		
	}
	
/**
 *searchAnalysieren(): vergleicht den Datei-/Ordnernamen mit dem Suchstring: bei Übereinstimmung wird 1 zurückgegeben.
 */	
	
	function searchAnalysieren($suche, $name){
		
/**
 *Zunächst wird der Suchstring an den XORs aufgespalten (falls vorhanden)
 *Die einzelnen Suchstrings werden mit dem Dateinamen verglichen: $y (der Rückgabewert) ist 1, wenn genau mit einem Teilstring ein Treffer erziehlt wird, bei 0 gäbe es überhaupt keine Übereinstimmung mit dem Dateiname, bei mehr als einem Suchtreffer ist das exklusive ODER nicht mehr erfüllt.
 */
 
		$y=0;
		$xor = explode(" XOR ", $suche);
		foreach($xor as $key => $heightSuchString){	
		
/**
 *Innerhalb einer einzelnen "XOR-Suchanfrage" wird das OR ausgewertet, auch hier wird der (schon zerteilte) Suchstring wiederum beim Gatter getrennt und die einzelnen neuen Suchstrings werden mit dem Dateinamen verglichen: Das Prinzip ist das gleiche wie bei XOR, nur darf es hier auch bei mehreren Teil-Suchstring Übereinstimmungen mit dem Dateinamen geben, da das ODER nicht mehr exklusiv ist.
 */
 		
			$x=0;	
			$or = explode(" OR ", $heightSuchString);	
			foreach($or as $lowSuchString){	
				$i=0;
				
/**
 *Auf der untersten Suchebene werden die einzelnen Begriffe im (Teil-)Suchstring mit dem Dateiname verglichen: auf dieser Ebene müssen alle Begriff gefunden werden (Leerzeichen werden wie UND interpretiert)
 */
 
				$suchbegriffe = explode(" ", $lowSuchString);
				foreach($suchbegriffe as $begriff){
					if(stripos($name, $begriff)!==false) {
						$i++;
					}
					if($begriff=="") $i++;
				}
				if($i==count($suchbegriffe)){//Auswertung der Suchergebnisse bzgl. OR.
					if($x==0){
						$x=1;
					}
					else if($x==1){
						$x=2;
					}
				}
			}
			if($x>0){//Auswertung der Suchergebnisse bzgl. XOR.
				if($y==0){
					$y=1;
				}
				else if($y==1){
					$y=2;
				}
			}
		}
		return $y;
	}
	
/**
 *Zunächst wird überprüft, ob der Nutzer angemeldet ist.
 */
 	
	session_start();
	if (!isset($_SESSION["idnummer"])){
			echo '<div>Fehler! Kein Zugriff!</div>';
			exit();
	}
	include("../php/datenbank.php");//Werte aus Datenbank
	include("../php/iconFunction.php");//um Icons zu erstellen
	$suche = $_POST["suche"];//Suchstring
	$komplex = $_POST["komplex"];//Suchoption
	$zaehler=0;//Zähl die Anzahl der Ergebnisse
	
/**
 *mit ordnerLink() (aus iconFunction.php) wird zum einen ein validierter Pfad erstellt, zum anderen wird (->letzer Parameter: true) ein, für die Suche angepasster, Kopfbereich erstellt, wo der Pfad zum aktuellen Pfad angezeigt wird und ausserdem ein Kreuzlein (x), um die Suche zu beenden.
 */
 
	$linkDir = ordnerLink($_SESSION['ordner'], $aryGruppennamen, $aryGruppenkennung, true);
	
/**
 *Die eigentliche Suche und die Ausgabe des Ergebnis wird über search() durchgeführt.
 */
	
	search($linkDir, $dsatzGrup, $aryGruppennamen, $suche, $komplex);
	
	if($zaehler==0){//Wenn kein Suchergebnis
		echo '<p>Leider keine Übereinstimmung.</p>';
	}
	if($komplex!=true){//zum Starten einer komplexen Suchanfrage: Unterordner werden dann auch in die Suche einbezogen.
		echo '<div class="ulink"><a onclick="sucheStart(1)">Unterordner in Suche miteinbeziehen</a></div>';
	}
	echo 	'<div class="shilfe"><p>Hilfe zur Formulierung von Suchanfragen</p><div>
				<p>Standartmässig wird nach allen Suchbegriffen im Datei- oder Ordnername gesucht. Unabhängig von der Grosskleinschreibung müssen alle Begriffe im Wort (Reihenfolge egal) vorkommen, damit die Datei oder der Ordner angezeigt wird. Unter Suchbegriffe wird hierbei durch Leerzeichen getrennte Buchstabengruppen verstanden. Die Suche lässt sich jedoch mit Logikgattern verfeinern, unterstützt werden: <b>UND</b>, <b>ODER</b>, <b>exklusives ODER</b> und <b>NICHT</b>. Andere Gatter, Klammern und Anführungszeichen werden nicht unterstützt und werden als Suchbegriffe ausgewertet oder ignoriert.</p>
				<p>Folgende Schreibweisen werden ausgewertet:</p>
				<ul>
					<li>für UND: " UND ", " AND ", "&&", " " (Leerzeichen)</li>
					<li>für ODER: " ODER ", " OR ", "||"</li>
					<li>für exklusives ODER: " XODER ", " XOR "</li>
					<li>für NICHT: " NICHT ", " NOT "</li>
				</ul>
				<p>Bitte achten Sie auf die Leerzeichen vor und hinter den Wörtern und auf die Grossschreibung!</p>
				<p>Die Gatter werden in der folgenden Reihenfolge ausgewertet: NICHT, XODER, ODER, UND. Wie schon erwähnt kann diese Reihenfolge nicht durch Klammer beeinflusst werden. Zu beachten ist auch, dass nur ein NICHT ausgewertet wird, d.h. alle Begriffe hinter dem ersten NICHT werden diesem zugeordnet und weitere NICHTs werden ignoriert.</p>
				<p>Hier noch eine Beispielsuchanfrage inklusive Auswertung: "ab OR da NOT q XOR t XOR x" wird zu "alle Ordner- und Dateinamen, die <i>ab</i> oder <i>da</i> enthalten, nicht aber <i>t</i> oder <i>q</i> oder <i>x</i> alleine enthalten" ausgewertet.</p>
			</div></div>';	 //Hilfe zur Formulierung von Suchanfragen
	
?>