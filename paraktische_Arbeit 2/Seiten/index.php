<?php
/**
 *HAUPTSEITE
 *
 *Es handelt sich bei diesem Dokument um die Hauptseite, auf der nach dem Anmelden grundsätzlich alles abläuft
 */
 	
/**
 *Mit dem untenstehenden Ausdruck kontrolliere ich, ob der Nutzer angemeldet ist, wenn nicht wird auf die Anmeldeseite umgeleitet. 
 *Die kontrolle findet über die "idnummer" statt, die nur vorhanden ist, wenn der Besucher angemeldet ist.
 *Sollte der Besucher nicht angemeldet sein, wird direkt auf die Anmeldeseite umgeleitet.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		$_SESSION["site"] = "index.php";
		header("Location: ../index.php");
		exit();
	}
?>
<?php
	
/** 
 *Hier wird die Datei dantenbank.php aufgerufen, die einige aus der Datenbank stammende Information aufbereitet und dann anderem (wie diesem) Dateien/Skripten bereitstellt.
 */
 
	include('../php/datenbank.php');

/**
 *Ein später benütztes Array wird hier initialisiert. (Protokoliert die besuchten Ordnerebenen, wird von anderen Skripten zur Validierung benutzt.
 */
	$_SESSION["protokoll"]=array();
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<title>Cloud</title>
		<link class="favicon" rel="shortcut icon" href="../icon.ico" type="image/png" />
		<link class="favicon" rel="icon" href="../icon.ico" type="image/png" />
		<link rel="apple-touch-icon" href="../apple-touch-icon.png">
		<link rel="stylesheet" href="../CSS/struktur.css">
		<script src="../JavaScript/jquery-2.0.3.min.js"></script>
		<script src="../JavaScript/JavaScript.js"></script>	
		<script src="../JavaScript/upload.js"></script>
	</head>
	<body>
    
<!--noscript bzw das untergeordnete div-Element überdeckt im Falle, dass JavaScript nicht funktioniert / deaktiviert ist, die ganze Seite, da deren Benutzung ohne JavaScript ohnehin nicht möglich ist.-->

		<noscript>
			<div>	
  				<div>kein JavaScript. Um sich abzumelden, klicken Sie <a href="../php/abmelden.php">hier</a></div> 
			</div>
		</noscript>
        
<!--der Stabilisator spannt sich auf die ganze Seite auf, er bildet das Fundament des Layouts. Es gibt eine Mindestbreite und eine Mindesthöhe, damit das Layout auch bei sehr kleinen Bildschirmen brauchbar bleibt-->

		<div id="stabilisator">
         
<!--Kopfbereich der Website-->

  			<header id="header">
    			<div>
                
<!--Oben links wird der Name des Nutzers angezeigt-->

      				<p>
						<?php 
						echo $dsatz['vorname']." ".$dsatz['nachname']; 
						?>
                    </p>
                    
<!--Wenn man lange genug mit der Maus auf dem Namen bleibt, wird ein Abmeldebuton eingeblendet (Siehe JavaScript.js)-->

      				<div class="abmelden">Abmelden</div>
                    
<!--Durch html und CSS3 habe ich hier einen Schalter realisiert. Die Grundidee stammt aus "http://proto.io/freebies/onoff/": Das Prinzip ist recht einfach: man blendet die eigentliche Checkbox aus und formatiert das Label je nach Zustand der Checkbox (mit Hilfe von :checked), da die Checkbox über das Label weiterhin bedienbar ist. Die Animation des Schalters erhält man dann durch den CSS-Effekt Transition. Mit dem Schalter kann man ein Menu (->Shoji) einblenden, das geschieht aber über JavaScript-->

      				<input type="checkbox" name="schalten" class="schalterChBox" id="schalten" checked>
      				<label class="schalter" for="schalten">
      					<div class="on schaltBesch"></div>
      					<div class="off schaltBesch"></div>
      					<div class="schaltKreis"></div>
      				</label>
    			</div>
                
<!--Buttons um zwischen Socialbereich und Cloudbereich zu wechseln. (per Javascript)
	Der Button für den Socialbereich weisst eine versteckte Funktion auf: Bei Mouseover wird überprüft, ob neue Nachrichten angekommen sind, wenn ja wird ein kleiner Kreis mit der Anzahl neuer Nachrichten angezeigt (-> newmail.php)-->
    
    			<nav> 
    				<a class="selected" onclick="changeEbene(false)">Cloud</a> 
    				<a onclick="changeEbene(true)">Social
         				<?php
						include('../php/newmail.php');		
						?> 
        			</a>
    			</nav>
  			</header>
            
<!--Oberes Menü: Es wird nur eingeblendet, wenn Dateien markiert sind. Je nachdem, was momentan Angezeigt wird, werden manche Funktionen ausgeblendet. (Funktionen sind in JavaScript.js beschrieben)--> 
 
			<nav id="mainMT">
  				<ul>
  					<li class="button deleteMT gruppe">&#xe000;/Verlassen</li>        
  					<li class="button deleteMT">&#xe000;</li>
  				    <li class="button copyMT">&#xe003;</li>
                    <li class="button verschiebenMT">&#xe00c;</li>
                    <li class="button umbenennenMT">Abc</li>
                    <li class="button downloadMT">&#xe015;</li>
                    <li class="button zipMT">.zip</li>
                    <li class="button hinzufugenMT gruppe">&#xe643</li>
				</ul>
  			</nav>
            
<!--Seitenleiste-->
  
  			<aside id="aside">
            
<!--Die Ordnerstruktur wird als Klappmenü angezeigt. Per Einfachklick klappt man die Unterordner ein oder aus, per Doppelklick wird der Ordner im Hauptbereich geöffnet-->

    			<nav>
    				<?php
					include('../php/aside.inc.php');		
					?>      
    			</nav>
    			<div id="asideFuss">
                
<!--Menü für die Ordnerdarstellung, momentan wird nur ein Löschen-Button angezeigt, der aber noch nicht implementiert ist.-->

      				<ul id="asideTools">
        				<li class="button">&#xe000;</li>
      				</ul>
                   
<!--Musikplayer: Der Player ist nur rudimentär implementiert.-->

      				<div class="player">
        				<div class="playLeiste">
        					<div>&#xe00e;</div>
            				<div class="play">&#xe00f;</div>
            				<div class="pause">&#xe010;</div>
            				<div>&#xe00d;</div>
            			</div>
        				<audio id="audioElement">
                    		sorry
        				</audio>
                        
<!--Bildlaufleiste, die Informationen zum Song anzeigt. (momentan nur eine Demo)-->

        				<div class="playDisplay">
        					<span>Live in Vanilla &nbsp;-&nbsp; von Unknow &nbsp;-&nbsp; 2013 &nbsp;---&nbsp; Live in Vanilla &nbsp;-&nbsp; von Unknow &nbsp;-&nbsp; 2013 &nbsp;---&nbsp; </span>
          					<div onclick="alert('Momentan können Sie nur die Demo abspielen. Die Auswahl von eigener Musik wird bald implementiert');">+</div>
        				</div>
      				</div>
    			</div>
    			<div id="verschwinden"></div>
  			</aside>
            
<!--Menü, dass mit dem Schiebeschalter eingeblendet werden kann. Bietet momentan nur einen Abmeldebutton und zeigt den Speicherplatz/-verbrauch an (mWidget).-->
    
			<aside id="shoji">
  				<ul>
  					<li class="abmelden knopf">Abmelden</li>
    				<li class="mWidget"></li>
   				</ul>
  			</aside>
            
<!--Hauptbereich: Beim Aufruf der Seite wird zunächst die oberste Ordnerebene, also die Ebene, wo alle Gruppen angezeigt werden, geladen.-->

  			<div id="main">
    			<div id="inhalt">     	
    				<?php
					include('../php/icon.php');		
					?>
 	    		</div>
  			</div>
            
<!--durchsichtige Box für Scrollbar des Hauptbereichs. Per Script werden die Scrollbewegungen zwischen main/inhalt und pseudoMAIN/pseudoINHALT übertragen. (Die richtige Scrollbar befindet sich ausserhalb des Stabilisators, also ausserhalb des Bildes)-->

  			<div id="pseudoMAIN">
    			<div id="pseudoINHALT"></div>
  			</div> 
            
<!--Unteres Menü für Hauptbereich: Immer eingeblendet, wird aber auch, je nach Inhalt des Hauptbereiches, angepasst. -->
  
			<nav id="mainMB">
    			<ul>
      				<li class="button delete">&#xe000;</li>
                    
<!--Klappmenü: Der Inhalt / Die Funktionen werden in einer aufwendigen CSS-Animation ein und ausgeblendet.-->

      				<li class="button neu"> 
                    	<span>&#xe013;</span> 
      					<div>
        					<ul>
            					<li id="erstFormOrdner">&nbsp;&nbsp;Gruppe</li>
                				<li id="erstText">Text</li>
                				<li id="erstRText" title="Print-HTML">P-HTML</li>
            				</ul>
        				</div>
      				</li>
                    
<!--Upload-Button: Man klickt eigentlich nicht auf den angezeigten Button, sondern auf ein im Button enthaltenes transparentes File-Input-Feld. Im versteckten Imput-Feld ist der aktuelle Ordnerpfad gespeichert und wird von diversen JavaScript-Funktionen ausgelesen.-->

      				<li class="button uploadOp"> 
      					<span>&#xe012;</span>
        				<form enctype="multipart/form-data" action="" method="post" id="upload">
        					<input name="upfile[]" id="upfile" type="file" size="0" multiple>
            				<?php 
							echo '<input type="hidden" id="upOrdner" name="upOrdner" value="'.$linkDir.'">'; 
							?>
         				</form>
      				</li>
                    
<!--Bei Mouseover wandelt sich der Button in ein Eingabefeld für die Suchanfrage.-->                    
                    
      				<li class="button suche"> <span>&#xe016;</span>
        				<form id="sucheForm">
          					<input type="search" id="sucheInput">
          					<input type="submit" value="&#xe016;">
        				</form>
      				</li>      
      				<li class="button update">&#xe647;</li>
     			</ul>
			</nav>
              
<!--Fussleiste: Sie wird erst angezeigt, wenn der Nutzer die Maus an den unteren Browserrand bewegt.-->
  
  			<footer id="footer"> 
  				<span> Caelum&copy;</span>
    			<span> 
                	weitere Informationen: 
    				<a href="http://www.matura-na94.jimdo.com"> zum Blog </a>
    			</span> 
  			</footer>
            
<!--Diverse Fenster, die bei Bedarf eingeblendet werden. 
	Beim fensterOut handelt es sich um ein transparentes Element, das sich über die Seite legt, wenn ein Fenster geöffnet ist und so die Bedienung der Seite, während ein Fenster geöffnet ist, verhindert. Per Doppelklick kann man das offene Fenster schliessen.-->
  
  			<div class="fensterOut"></div>
            
<!--Fenster zum Erstellen von Gruppen.-->
  
  			<div class="fenster gruppenfenster">
  	  			<div class="fensterIn">
      				<div>
      	  				<form name="formGruppe" action="../php/gruppeErstellen.php" method="post" id="formGruppe">
        					<label for="gruppenName">Name:</label><input type="text" id="gruppenName" name="gruppenName">
       		     			<label for="gruppenMitglieder[]">Mitglieder:</label>
            	            <select multiple id="gruppenMitglieder" name="gruppenMitglieder[]">
                            <?php 
								include('../php/liste.inc.php');
							?>
                            </select>
            				<p>[ctrl]-Taste gedrückt halten um mehrere Personen zur Gruppe hinzuzufügen.</p>
        	    			<input type="submit" value="Ok">
        	  			</form>
      				</div>
      			</div>
      			<div class="close">&#xe013;</div>
  			</div>
            
<!--Fenster zum Bearbeiten von Gruppen.-->
  
            <div class="fenster gruppenfenster2">
  	  			<div class="fensterIn">
      				<div>
      	  				<form name="formGruppe2" action="../php/gruppeErstellen.php" method="post" id="formGruppe2">
       		     			<label for="gruppenMitglieder2[]">Mitglieder:</label>
            	            <select multiple id="gruppenMitglieder2" name="gruppenMitglieder2[]"></select>
            				<p>[ctrl]-Taste gedrückt halten um mehrere Personen zur Gruppe hinzuzufügen.</p>
        	    			<input type="submit" value="Ok">
        	  			</form>
      				</div>
      			</div>
      			<div class="close">&#xe013;</div>
  			</div>            
            
<!--Fenster zum Verschieben von Daten und Ordnern.-->
  
  			<div class="fenster verschiebenfenster">
  	  			<div class="fensterIn">
      				<div>
        				<form id="formVerschieben">
        					<label>Dateien Verschieben nach:</label>
            				<div id="bVerschieben"></div>
            				<input type="checkbox"> kopieren
            				<input type="submit" value="Ok">
        				</form>          
      				</div>
      			</div>
      			<div class="close">&#xe013;</div>
  			</div>      
            
<!--ein einfacher Texteditor-->
   
  			<div class="fenster textfenster">
  	  			<div class="fensterIn">
      				<div>
        				<form name="formText">
            				<label for="textname">Dateiname:</label>
                            <input type="text" name="textname" id="textname" value="text.txt">
      						<textarea id="textdiv"></textarea>
            				<input type="submit" value="Speichern">
            			</form>
      				</div>
  				</div>
    			<div class="close">&#xe013;</div> 
  			</div>      
            
<!--Editor zum Schreiben von Nachrichten-->
  
  			<div class="fenster mail">
  				<div class="fensterIn">
    				<div></div>
    			</div>
    			<div class="close">&#xe013;</div>
  			</div>      
            
<!--kleines Fenster; für mehrere Zwecke verwendet-->
  
  			<div class="fenster klein">
  	 			<div class="fensterIn">
      				<div></div>
  				</div>
    			<div class="close">&#xe013;</div> 
  			</div> 
             
<!--sogenannter Print-HTML-Editor: Angelehnt an simple Richtext-Editoren.-->
  
  			<div class="programmfenster">
            
<!--Menü zum Bearbeiten des Dokumentes: Momentan sind noch recht wenig Formatierungsmöglichkeiten verfügbar.-->

    			<nav class="tools">
    				<nav class="Ansicht">
        				<label for="tool_zoom">&#xe609;</label>
            			<select name="tool_zoom" id="tool_zoom">
            				<option value="0.3">30%</option>
      						<option value="0.5">50%</option>
      						<option value="0.75">75%</option>
      						<option value="1" selected="selected">100%</option>
      				 		<option value="1.25">125%</option>
   							<option value="1.5">150%</option>
   							<option value="2">200%</option>
               				<option value="5">400%</option>
               				<option value="0">benutzerd.</option>
   		 				</select><br>
           				<input type="checkbox" name="tool_hlinie" id="tool_hlinie" value="true"><label for="tool_hlinie">Hilfslinien </label>
             			<p>Ansicht</p>
        			</nav>
        			<nav class="Layout">
        				<label for="tool_format">Form.: </label>
                        <select name="tool_format" id="tool_format">
            				<option value="A4h">A4 hoch</option>
      						<option value="A4b">A4 breit</option>
      						<option value="A5h">A5 hoch</option>
      						<option value="A5b">A5 breit</option>
   			 			</select> 
						| <div id="tool_background">
							<div class="w selected" title="FFFFFF"></div> 
                            <div class="p" title="E8E2CE"></div> 
                            <div class="d"></div>
             			</div><br>
             			<label for="tool_format">&#xe00c;</label>
             			o:<input type="text" class="tool_rand" name="tool_rand1" id="tool_rand1" maxlength="3" value="70">
             			u:<input type="text" class="tool_rand" id="tool_rand2" maxlength="3" value="70">
             			l:<input type="text" class="tool_rand" id="tool_rand3" maxlength="3" value="70">
             			r:<input type="text" class="tool_rand" id="tool_rand4" maxlength="3" value="70">
             			<select name="tool_format" id="tool_rand_mass">
             				<option value="pt">pt</option>
                			<option value="mm">mm</option>
             			</select>
             			<p>Layout</p>
        			</nav>
        			<nav class="Schriftart">
                    
<!--Das Eingabefeld für die Schriftgrösse ist eine Kompination aus Eingabefeld und Auswahlmenü.-->

        				<label for="tool_fontsize">&#xe616; </label><input name="tool_fontsize" id="tool_fontsize" list="sizeliste" value="16">
                        <label for="tool_fontsize"> pt</label>
            			<datalist id="sizeliste">
      						<option value="2"></option>
      						<option value="4"></option>                
      		    		  	<option value="6"></option>
      						<option value="8"></option>
      						<option value="10"></option>
                			<option value="12"></option>
      						<option value="14"></option>                
            				<option value="16"></option>
      						<option value="18"></option>
      						<option value="20"></option>
                			<option value="22"></option>
      						<option value="24"></option>                
            				<option value="26"></option>
      						<option value="28"></option>
      						<option value="32"></option>
            			    <option value="36"></option>
      						<option value="40"></option>                
            				<option value="48"></option>
      						<option value="56"></option>
      						<option value="64"></option>
            			    <option value="80"></option>
      						<option value="96"></option>
						</datalist>
   			 			| <div id="tool_color">
             				<div class="b selected" title="000000"></div> 
                            <div class="g" title="606060"></div> 
                            <div class="db" title="063099"></div> 
                            <div class="r" title="EE0000"></div> 
                            <div class="d"></div>
            			</div><br> 
        				<button class="tool_font tool_font_b">&#xe617;</button>
            			<button class="tool_font tool_font_i">&#xe619;</button>
            			<button class="tool_font tool_font_u">&#xe618;</button>
            			<button class="tool_font tool_font_s">&#xe641;</button>
        				<p>Schriftart</p>
					</nav>
        			<nav class="Absatz"><p>Absatz</p></nav>
        			<nav class="Einfügen"><p>Einfügen</p></nav>
        			<nav class="Links">
                        <form method="post" id="tool_save" onSubmit="return tool_save(event)">
                        	<input type="hidden" name="speicher" id="speicherName">
                			<input type="submit" value="&#xe60b;">
             			</form>
             			<form method="post" id="tool_print" action="print.php" onSubmit="return tool_print()" target="Drucken"> 
			 				<input type="hidden" name="speicher" id="speicher">
                			<input type="submit" value="&#xe606;">
             			</form>
        				<p>Save/Print</p>
                   	</nav>
        			<div>x</div>
    			</nav> 
                
<!--Bereich mit dem bearbeitbarem Dokument, das als IFrame eingebunden ist, da sonst der ausgewählt Text beim Hantieren im Menü den Fokus verlieren würde und dadurch die ausgewählte Funktion keinen markierten Text mehr vorfinden würde, auf die sie angewendet werden kann. -->
  
  				<div class="blattbereich">
    				<div>
                    	<iframe id="blatt" src="blatt.html"></iframe>
                	</div>
    			</div>
  			</div> 
             
<!--Zeigt den Status des Uploades an, wird nur bei Upload eingeblendet und zeigt zusätzliche Informationen bei Mouseover an.-->
   
  			<div class="status">
    			<div>
    				<div>
        				<div>&#xe012;</div>
					</div>
        			<div></div>
    			</div>
  				<div></div>
  			</div>
  		</div>
	</body>
</html>
<?php
/**
 *Allgemein als Quellen für diese Arbeit zu nennen, sind insbesondere die unten Aufgeführten:
  *PHP:
	*Einstieg in PHP 5.5 und MySQL 5.6 - Ideal für Programmieranfänger geeignet
 	 *9.Auflage, 2013, von Thomas Theis, Galileo Computing
	*www.php.net
	*www.selfphp.de
 *JavaScript und HTML
 	*JavaScript - Das umfassende Referenzwerk
	 *6.Auflage, 2012, von David Flanagan, übersetzt von Lars Schulten und Thomas Demming, O'Reilly
	*HTML5 Programmierung - von Kopf bis Fuss
	 *2012, von Eric Freeman und Elisabeth Robson, übersetzt von Stefan Fröhlich, O'Reilly
	*jQuery - Das Praxisbuch
	 *2. aktualisierte und erweiterte Auflage, 2011, von Maximilian Vollendorf und Frank Bongers, Galileo Computing
	*api.jquery.com
 *CSS und HTML
 	*Modernes Webdesign mit CSS - Schritt für Schritt zur perfekten Website
	 *2013, von Heiko Stiegert, Galileo Design
	*Apps mit HTML5 unc CSS3
	 *2., aktualisierte und erweiterte Auflage, 2013, Florian Franke und Johannes Ippen
 *allgemein
    *www.w3schools.com
	*www.selfhtml.org
	*www.tutorials.de
	*www.html5rocks.com
	
 */
?>