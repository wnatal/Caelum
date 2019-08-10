<?php
/**
 *ORDNERSTRUKTUR KOMPAKT
 *
 *Funtkoniert im Prinzip wie aside.inc.php: Stellt die Ordnerstruktur dar. Jedoch gibt es im ausgegebenen Markup grössere Unterschiede, da der Einsatzzweck ein anderer ist.
 */
 
 /**
  *ordnerMake(): Rekursive Funktion, die Ordnerstruktur durchwandert und die gefundenen Ordner mit HTML-Markup "anreichert".
  *$OrdnerName: Name des Ordners
  *link: Pfad zum Ordner
  *aryGruppennamen, $aryGruppenkennung: Array um den richtigen Gruppenname der Gruppen-ID zuzuordnen.
  */
 
 	function ordnerMake($OrdnerName, $aryGruppennamen, $aryGruppenkennung, $link){
		$newlink = $link.$OrdnerName; //Pfad
		if($aryGruppennamen){ //Bei Gruppen muss der ID der passende Namen zugeordnet werden.
			for($j=0; $j<count($aryGruppennamen); $j++){
				if($OrdnerName == $aryGruppennamen[$j]){
					$OrdnerName = $aryGruppenkennung[$j];
					break;
				}
			}
		}
		echo '<li><a data-link="'.$newlink.'" data-name="'.$OrdnerName.'">'.$OrdnerName.'</a>';
		$handle = opendir($newlink);
		echo '<ul>';
		while($name = readdir($handle)){ //Unterordner suchen
			if(is_dir($newlink.'/'.$name)&&$name!="."&&$name!=".."){
				ordnerMake($name, false, false, $newlink.'/');//Funktion mit Unterordner aufrufen			
			}
		}
		echo '</ul>';
		closedir($handle);
		echo '</li>';		  
	}
	
	session_start(); //Session starten
	include('../php/datenbank.php');//datenbank.php einbinden
	
/**
 *Ordnerstruktur erstellen: Im Grundgerüst eingebettet wird ordnerMake() jeweils für den privaten Hauptordner und die Gruppen aufgerufen.
 *Bei den Gruppen müssen noch zwei Array mitgegeben werden, damit der idnummer (der Ordnername), die richtige Kennung, also der richtige Gruppenname zugeordnet werden kann.
 */
 
	echo '<ul class="ordnerAuswahl"><li><a data-name="Bitte Ordner wählen">Bitte Ordner wählen!</a><ul><li><a data-link="../Dateien/'.$_SESSION['idnummer'].'" data-name="Privat">Privat</a><ul>';
	$handle = opendir("../Dateien/".$_SESSION['idnummer']);
	while($name = readdir($handle)){
		if(is_dir("../Dateien/".$_SESSION['idnummer']."/".$name)&&$name!="."&&$name!=".."){
    		ordnerMake($name, false, false, ("../Dateien/".$_SESSION['idnummer']."/"));				
		}
	}
	closedir($handle);
    echo '</ul></li>';  
	if($patchVar!=0){
		for($i=0; $i<count($gruppeXML->id); $i++){
        	$OrdnerName = $gruppeXML->id[$i];				
			ordnerMake($OrdnerName, $aryGruppennamen, $aryGruppenkennung, "../Dateien/");
		}
	}
    echo '</ul></li></ul>';
?>