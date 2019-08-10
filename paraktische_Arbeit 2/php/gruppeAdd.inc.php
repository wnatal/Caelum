<?php
			
/**
 *GRUPPE ZU GRUPPENLISTEN HINZUFÜGEN
 *
 *Mit diesem zu inkludierem Skript-Fragment, werden Gruppen zu den jeweiligen Gruppenlisten der ausgewählten Nutzer hinzugefügt.
 *Zunächst wird aber geprüft, ob das Skript includiert wurde und der Nutzer angemeldet ist.
 */
 
 	if (!isset($_SESSION["idnummer"])){
		echo '<div>Fehler! Kein Zugriff!</div>';
		exit();
	}
	
/**
 *Mit nutzerID wird ein "XML-String" aufgebaut, der alle neuen Nutzer der Gruppe bzw. alle Nutzer der neuen Gruppe enthält .
 */
 
		$nutzerID="<nutzer>";
		$nutzerID.='<id Administrator="true">'.$_SESSION["idnummer"]."</id>";
			
/**
 *Zunächst wird der Gruppen-Liste des aktuellen Nutzers die Gruppe hinzugefügt.
 */

		$res = mysqli_query($con, "SELECT gruppe FROM nutzerdaten WHERE idnummer LIKE BINARY '".$_SESSION["idnummer"]."'");
		$gruppe = mysqli_fetch_assoc($res);
		$element = new SimpleXMLElement($gruppe['gruppe']);//neues XML-Element
		$neu = $element->addChild('id',$idnummer);//Tagname und Inhalt hinzufügen
		$neu->addAttribute('Administrator', 'true');//Attribut Administrator hinzufügen
		$element=$element->asXML();//bearbeitetes XML in Variable speichern
		$res = mysqli_query($con, "UPDATE nutzerdaten SET gruppe = '".$element."' WHERE idnummer LIKE BINARY '".$_SESSION["idnummer"]."'");//XML in Datenbank aktualisieren
			
/**
 *Danach wird die neue Gruppe bwz. die bearbeitete Gruppe den Gruppenlisten der Nutzer, die zur Gruppe gehören, hinzugefügt.
 */
 
		foreach($gruppenMitgl as $i){
			$nutzerID.="<id>".$i."</id>";//an nutzerID-String anhängen
			$res = mysqli_query($con, "SELECT gruppe FROM nutzerdaten WHERE idnummer LIKE BINARY '".$i."'");
			if($res){
				$gruppe = mysqli_fetch_assoc($res);
				$element = new SimpleXMLElement($gruppe['gruppe']);
				$element->addChild('id',$idnummer);
				$element=$element->asXML();
				$res = mysqli_query($con, "UPDATE nutzerdaten SET gruppe = '".$element."' WHERE idnummer LIKE BINARY '".$i."'");
			}
			else{
				$kontrolle=false;
			}
		}
		$nutzerID.="</nutzer>";//nutzerID-String wird abgeschlossen
			

?>