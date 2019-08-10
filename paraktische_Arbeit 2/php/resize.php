<?php
/**
 *SUCHE
 *
 *Dieses Skript generiert die Vorschaubilder für die Bild-Icons. Es generiert die Bilder momentan in doppelt so grosser Auflösung als das Image-Element im Icon. Das kommt daher, das mobile Geräte heute so eine hohe Auflösung haben, dass sie die Websiten auf das doppelte zoomen, das führt häufig dazu, das Websites mit Bildern unscharf sind, dass wird hier vermieden. Man könnte natürlich eine Browserweiche einsetzen, um die Auflösung für die Vorschaubilder zu bestimmen, jedoch ist eine zuverlässige Browserweiche schwierig umzusetzen und nicht mobile Geräte haben meist eine schnelle Flatrate-Internetverbindung. Mobile Geräte mit wenig Auflösung sind sowiso nicht geeignet, um die Seite anzuzeigen. Die Funktion wir per GET aufgerufen (über das scr-Attribut im img-Element).
 *Zunächst wird geprüft, ob der Nutzer angemeldet ist.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}

/**
 *Zunächst wird ein Header erzeugt, der angibt, dass es sich beim erzeugten Inhalt um ein JPEG handelt. Dann wird bestimmt, um was für ein Dateityp es sich beim Ausgangsbild handelt: in Frage kommen  JPEG, PNG und GIF. 
 */

	function resizePicture($file, $width, $height){
    	header('Content-type: image/jpeg');
		$info = getimagesize($file);
		if($info[2] == 1){
    		$image = imagecreatefromgif($file);
    	}
    	elseif($info[2] == 2){
        	$image = imagecreatefromjpeg($file);
    	}
    	elseif($info[2] == 3){
        	$image = imagecreatefrompng($file);
    	}
    	else{
        	return false;
    	}
		
/**
 *Mit einer If-Anweisung wird nun bestimmt, ob das Bild verkleinert oder nur richtig zugeschnitten werden muss. Diese Unterscheidung mache ich, da sont unnötig Datenvolumen für aufgeblähte Vorschaubilder verbraucht würde. Danach wird bestimmt ob rechts oder unten abgeschnitten werden muss. 
 */
 
		if($height < $info[1]&&$width < $info[0]){
			$imagetc = imagecreatetruecolor($width, $height);
			if(($info[0]/$width*$height)<$info[1]){
        		$height = ($width / $info[0]) * $info[1];
			}
			else{
				$width = ($height / $info[1]) * $info[0];
			}
			imagecopyresampled($imagetc, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
		}
		else{
			if(($info[0]/$width*$height)<$info[1]){
				$height = $info[0]/$width*$height;
        		$width = $info[0];
			}
			else{
				$width = $info[1]/$height*$width;
				$height = $info[1];
			}
			$imagetc = imagecreatetruecolor($width, $height);
			imagecopy($imagetc, $image, 0, 0, 0, 0, $info[0], $info[1]);
		}
		
/**
 *Das Bild ausgegeben und der Speicher freigegeben.
 */
					
    	imagejpeg($imagetc, null, 88);    
		imagedestroy($imagetc);
    } 
	
/**
 *Funktion aufrufen.
 */
 
	resizePicture($_GET['file'], $_GET['x'], $_GET['x']/10*7);
	
/**
 *Das Gerüst und die Funktionsweise stammt aus www.selfphp.de [1], wurde aber noch ergänzt, ich habe mich dafür in "Einstieg PHP 5.5 und MySQL 5.6"[2] informiert.
 */

/*[1]*
 *Link :
 	*http://www.selfphp.de/kochbuch/kochbuch.php?code=62
 *letzer Zugriff:
	*1.1.14
 */ 
/*[2]*
 *Titel :
 	*Einstieg PHP 5.5 und MySQL 5.6
 *Autor:
	*9783836224895
 *Seiten:
 	*Kapitel 12
 	*vorallem 420 ff.
 */
				
?>