<?php
/**
 *DIVERSE FUNKTIONEN (bzgl. ICON-ERSTELLUNG)
 */
 
/**
 *iconerstellen(): Erstellt aus den übergebenen Werten ein Icon.
 *Die ersten beiden Werte, der Pfad zum Verzeichnis und der Datei-/Ordnername, müssen immer übergeben werden.
 *Der ditte Wert wird übergeben, wenn der angezeigte Name nicht mit dem Namen in der Dateistrukur übereinstimmen soll, sondern das Icon wie der übergebene Name heissen soll
 *Der vierte Wert gibt an, ob (betrifft nur Gruppen) der Nutzer der Administrator ist oder nicht.
 *Der fünfte Wert gibt den Name des Administrators bei einer Gruppe an.
 */
 
	function iconerstellen($linkDir, $dname, $name="", $admin=true, $administrator=false){
		$bild = ""; 
		$link = $linkDir. '/'. $dname;//Ordnerpfad + Dateiname (bzw.Ordnername) = Dateipfad
		
/**
 *Der Verweis auf das Download-Skript wird erstellt.
 *Wenn es sich bei der Datei um ein Bild handelt (siehe endungTest()), wird der Image-Tag mit Verweis zum Skript für die Vorschaubilder erstellt.
 *Bei einem Ordner wird, wenn verfügbar, ein Vorschaubild eines im Ordner enthaltenden Bildes angezeigt, dazu wird zunächst der Image-Tag mit allem drum und dran erzeugt..
 */
 
		$downloaddata = "../php/download.php?name=".$dname.'&ordner='.$link;
		$download = "window.location.href = '".$downloaddata."'";
		if(endungTest($dname)){
			$info = getimagesize($link);
			$bild = '<div><img src="../php/resize.php?file='.$link.'&x=200" /><span>'.$info[0].'x'.$info[1].'</span></div>';
		}				
		if(is_dir($link)){
			$handle2 = opendir($link);
			while($dname2 = readdir($handle2)){
				if(endungTest($dname2)){
					$bild = '<div><img src="../php/resize.php?file='.$link.'/'.$dname2.'&x=116" /></div>';
					break;	
				}
			}
			closedir($handle2);
		}
		$iconText = strtoupper(substr(strrchr($dname, "."),1));//Dateiendung
		$iconColor = iconTyp($iconText);//passende Formatierung/Farbe für Dateityp7-endung bestimmen.
		if($iconText == ""){
			$iconColor= 'dircolor';//bei Ordner
		}
		
/**
 *Jetzt wird das Icon ausgeben:
 */
 						
		echo '<div draggable="true" ';//Icon ziehbar
		if($name=='Privat'){
			echo 'class="iconMain" ';//Klassse für Hauptordner des privaten Bereiches
		}
		echo'title="'. $dname.'"><div class="icon" onclick="';
		if(is_dir($link)){
			echo "changeOrdner('".$link."')"; //Bei Klick Ordner wechseln
		}
		else{
			echo $download;//Bei Klick Datei downloaden
		}
		echo '"><div class="'.$iconColor.'">'.$bild;
		if($administrator){//nur bei Gruppen
			echo '<span>'.$administrator.'</span></div></div><span ';//Administrator anzeigen
		}
		else{
			echo '<span>'.$iconText.'</span></div></div><span ';//bei Dateien Dateiendung anzeigen
		}
		if($name=='Privat'){
			echo 'class="icoSpMain" ';//Klassse für Hauptordner des privaten Bereiches
		}
		echo 'onclick="';
		if(is_dir($link)){
			echo "changeOrdner('".$link."')".'" data-type="o';//data-type: o wie Ordner
		}
		else{
			echo $download.'" data-download="'.$downloaddata.'" data-type="d';//data-type: d wie Datei
		}
		echo '">';
		if($name){//Benutzerdefinierter Name
			echo $name;
		}else{
			echo $dname;
		}
		echo '</span>';
		if($link!='../Dateien/'.$_SESSION['idnummer']){
			echo '<div class="auswahl" data-link="'.$link.'"><div></div></div>';//Attribut mit Pfad
			if($admin==true){
				echo '<div onclick="'."delDatei('".$link."', $(this))".'" class="delDatei">löschen</div>';//Löschenbutton
			}
			else{//Bei Gruppen ohne Administratorenrechte:				
				echo '<div onclick="'."delDatei('".$link."', $(this))".'" class="delDatei">verlassen</div>';//Verlassenbutton
			}
		}
		echo '</div>';
	}
	
/**
 *ordnerLink: gibt einen validierten Dateipfad zurück und erstellt eine anklickbare Version des Pfades, die zuoberst im Hauptbereich angezeigt wird.
 *$ordner ist das Ausgangsmaterial für den fertigen Dateipfad
 *$aryGruppennamen und $arryGruppenkennung sind zum Übersetzen von Gruppen-ID in Gruppenname da.
 *$option bestimmt, wie genau der anklickbare Pfad aussehen soll
 */
 	
	function ordnerLink($ordner, $aryGruppennamen, $aryGruppenkennung, $option){
		
/**
 *Includiertes Skript validiert $ordner.
 */

		include('verifizieren.inc.php');
		
/**
 *Verlief die Validation sauber, wird jetzt aus $ordner der geforderte Ordnerpfad und die HTML-Darstellung des Pfades erstellt.
 *Zunächst wird die ID-Nummer im Pfad durch den Gruppenname bzw durch Privat ersetzt
 *Danach werden die '/' durch HTML-Tags ersetzt.
 *Darauf folgend muss jedem Pfadfragment bwz. deren click-Handler der richtige Ordnerpfad zugeordnet werden
 */
 
		if(($root == $_SESSION['idnummer'] || $testRoot==true)&&$testLink==true){
			$x=0;
			$j=0;
			$stringOrdnerHref = str_replace($_SESSION['idnummer'],'Privat', str_replace($aryGruppennamen,$aryGruppenkennung,$ordner));
			$stringOrdnerHref = "'../Dateien/'".')"> '.str_replace('/','</span> / <span onclick="changeOrdner('."'../Dateien/'".')">', $stringOrdnerHref);
			for($i=0; $i<substr_count($stringOrdnerHref, "../Dateien"); $i++){
				$j= strpos($ordner.'/', '/', $j+1);
				$ordnerHrefx = substr($ordner.'/', 0, $j);
				$x = strpos($stringOrdnerHref, '../Dateien/', $x);
				$stringOrdnerHref = substr_replace($stringOrdnerHref,'../Dateien/'.$ordnerHrefx, $x, 11);
				$x = $x + 1;
			}
			
/**
 *Das erzeugte Markup wird mit dem Drumherum ausgegben.
 */
								
			echo '<div class="olink">';
			if($option){
				echo '<b>Suche in: </b> ';
			}
			echo '<span onclick="changeOrdner('."'../Dateien'".')">Main </span>/<span onclick="changeOrdner('.$stringOrdnerHref.'</span> ';	
			if($option){
				echo ' &nbsp;&nbsp;<a onclick="'."delSuche()".'"> x </a>';
			}
			echo '</div>';
				
			return $linkDir = '../Dateien/'.$ordner;//Rückgabewert: fertiger Dateipfad
		}
		
/**
 *Wenn eine Unstimmigkeit beim Validieren aufgetreten ist, wird ein Pfad zur Hauptebene erstellt.
 */
 
		else{
			echo '<div class="olink">';
			if($option){
				echo '<b>Suche in: </b> ';
			}
			echo '<span onclick="changeOrdner('."'../Dateien'".')">Main</span> ';
			if($option){
				echo ' &nbsp;&nbsp;<a onclick="'."delSuche()".'"> x </a>';
			}
			echo '</div>';
			return $linkDir = '../Dateien';
		}					
	}
	
/**
 *endungTest(): Es wird überprüft, ob es sich um eine Bilddatei handelt.
 */
 
	function endungTest($name){
		$endungen = array('.jpeg','.jpg','.png','.gif');
		for($i = 0; $i < count($endungen); $i++){
			if(stristr($name, $endungen[$i])){
				return true;
			}
		}
		return false;
	}
	
/**
 *iconTyp(): Weist den Dateiendungen verschiedene Klassen zu, über die per CSS die Icons eingefärbt werden.
 *Znächst werden Arrays mit Dateitypen definiert, die jeweils gleich dargestellt werden sollen: So haben z.B. alle Bilddateien-Icons die gleiche Farbe.
 */
 
	function iconTyp($iconText){
		$bildTyp = array('jpeg','jpg','png','gif','tif','svg','bmp');
		$wordTyp = array('doc', 'odt', 'pages', 'dot');
		$pptTyp = array('ppt','otp','pages','pps','pot');
		$exelTyp = array('xls','ots','numbers','xlt');
		$publTyp = array('pub');
		$adobTyp = array('pdf','swf');
		$musicTyp = array('mp2','mp3','m4','aac','aif','wav','fla','ogg','wma');
		$videoTyp = array('m4v','mp4','wmv','mpg','avi','flv','mov','swf');
		$archivTyp = array('zip','rar','tar');
		$htmlTyp = array('htm', 'html', 'xhtml', 'dhtml', 'shtml');
		$phtmlTyp = array('phtml');
		$cssTyp = array('css');
		$progrTyp = array('php', 'pl', 'exe', 'bin', 'asp');
		$javaTyp = array('java');
		$psdTyp = array('psd');
		$textTyp = array('text','rtf','ps'); 
		$inDesTyp = array('indd');
		$illuTyp = array('ai');
		
/**
 *Jetzt wird die übergebene Dateiendung mit den Arrays abgeglichen und es wird dementsprechend ein Wert (->CSS-Klasse) zurückgegeben. 
 */
 
		foreach($bildTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "orange";
			}
		}foreach($adobTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "darkred";
			}
		}foreach($wordTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "blue";
			}
		}foreach($pptTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "red";
			}
		}foreach($exelTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "green";
			}
		}foreach($publTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "turky";
			}
		}foreach($musicTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "lightblue";
			}
		}foreach($videoTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "orangered";
			}
		}foreach($archivTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "brown";
			}
		}foreach($htmlTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "skyblue";
			}
		}foreach($phtmlTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "blueturky";
			}
		}foreach($cssTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "white";
			}
		}foreach($progrTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "black";
			}
		}foreach($javaTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "darkorange";
			}
		}foreach($psdTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "darkblue";
			}
		}foreach($textTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "lightgrey";
			}
		}foreach($inDesTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "violet";
			}
		}foreach($illuTyp as $i) {
			if(stripos($iconText, $i)===0){
				return "yelloworange";
			}
		}
		return "grey"; //Standardformatierung
	}
	

			
?>