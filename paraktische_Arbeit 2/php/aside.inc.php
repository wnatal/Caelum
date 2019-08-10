
<?php
/**
 *ORDNERSTRUKTUR ERSTELLEN
 *
 *Mit diesem Skript wird die Ordnerstruktur für die Seitenleiste erstellt.
 */

/*
 *ordnerMake(): Diese Funktion bildet die Ordnerstruktur des gewählten Hauptordners ab. Hierfür wird die Ordnerstruktur rekursiv durchlaufen und mit den gewonnenen Informationen Schritt für Schritt den angeforderten HTML-Aufbau für das Klappmenü erstellt. 
 *Auf Gruppenebene muss zunächst für die Beschriftung der jeweilige "idnummer" die richtige Kennung zugeordnet werden: Hierfür wird $aryGruppennamen und $aryGruppenkennung verwendet. Während zunächst der Pfad (idnummer) mit $aryGruppennamen verglichen wird, um herauszufinden in welcher Position die idnummer liegt, wird danach dieser Positionsindex auf $aryGruppenkennung angewendet um den dazugehörigen Namen herauszufinden.
 */
  
	function ordnerMake($OrdnerName, $sprung, $aryGruppennamen, $aryGruppenkennung, $link){
		$newlink = $link.$OrdnerName;
		if($aryGruppennamen){
			for($j=0; $j<count($aryGruppennamen); $j++){
				if($OrdnerName == $aryGruppennamen[$j]){
					$OrdnerName = $aryGruppenkennung[$j];
					break;
				}
			}
		}
		echo '<li><a href="#" ondblclick="changeOrdner('."'".$newlink."'".')">'.$OrdnerName.'</a>';
	
/*
 *Der Ordner wird geöffnet, danach werden die Unterordner ermittelt und mit den neuen Angaben die Funktion erneut aufgerufen. (Rekursion)
 *die Verzeichnisse '.' und '..' werden ausgeschlossen, da zum einen der Punkt bewirken würde, dass die Verzeichnisebene erneut geöffnet würde und '..' sogar auf den Elternordner verweist.
 */
 
		$handle = opendir($newlink);
		echo '<ul>';
		while($name = readdir($handle)){
			if(is_dir($newlink.'/'.$name)&&$name!="."&&$name!=".."){
				ordnerMake($name, false, false, false, $newlink.'/');			
			}
		}
	  	echo'<li><a href="#"><span class="neu">neuer Ordner</span><span class="plus" onclick="erstellOrdAsForm('."'".$newlink."'".', $(this))">+</span></a></li>';
		echo '</ul>';	
		closedir($handle);
		echo '</li>';
	}	
	
/**
 *Zunächst wird geprüft ob der Nutzer angemeldet ist und die Seite "includiert" worden ist (um auf eine $_SESSION-Variable zuzugreifen, muss eine Session gestartet werden, was dieses Dokument alleine nicht macht.) 
 */ 
 
	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Das Aside-Menü ist in zwei Teile geteilt, der obere für die eigenen Ordner, der untere für die Gruppen. Die beiden Bereiche verlangen verschiedene Voraussetzungen, bevor die eigentliche Funktion rekursiv aufgerufen werden kann. Beim ersten wird nur der Ordnerpfad zum privaten Bereich und die Namen der Unterordner benötigt. Für den zweiten Bereich muss eine XML-Strukturen mit allen Gruppen ausgelsen werden. Damit man der jeweiligen idnummer den dazugehörigen Gruppenname zuordnen kann, müssen noch zwei Arrays übergeben werden. Der Funktion werden folgende  Werte übergeben: 
 *erster Wert: Name des Ordners
 *zweiter Wert: Bestimmt ob jeweils an den Schluss ein "neuer Ordner"-Feld hinzugefügt werden soll (dies ist nötig, da der zweite Bereich mit mehreren Ordner-Bäumen startet)
 *dritter und vierter Wert: nötig um der ID der Gruppe den richtigen Namen zuzuordnen 
 *fünfter Wert: Pfad zur Ordnerebene
 */
 

	echo '<ul class="navigation">';
	$handle = opendir("../Dateien/".$_SESSION['idnummer']);
	while($name = readdir($handle)){
		if(is_dir("../Dateien/".$_SESSION['idnummer']."/".$name)&&$name!="."&&$name!=".."){
    		ordnerMake($name, false, false, false, ("../Dateien/".$_SESSION['idnummer']."/"));				
		}
	}
	closedir($handle);
	echo '<li><a href="#"><span class="neu">neue Gruppe</span><span class="plus" onclick="erstellOrdAsForm('."'../Dateien/".$_SESSION['idnummer']."'".', $(this))">+</span></a></li>';
    echo '</ul>';
    echo '<span>Gruppen</span><ul class="navigation last-child">';  
	if($patchVar!=0){
		for($i=0; $i<count($gruppeXML->id); $i++){
        	$OrdnerName = $gruppeXML->id[$i];				
			ordnerMake($OrdnerName, true, $aryGruppennamen, $aryGruppenkennung, "../Dateien/");
		}
	}
	echo '<li><a href="#"><span class="neu">neue Gruppe</span><span class="plus" onclick="erstellGruppeForm()">+</span></a></li>';
    echo '</ul>';
?>