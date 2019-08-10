/**
 * diverse globale Variablen, die jeweils gewisse Zustände speichern, die von mehreren Funktionen gebraucht und/oder verändert werden
 */
 
	var zustand;
	var zustand2;
	var zustand3;
	var aktuell= true;
	var ausgew = 0;
	var editor;
	var ordnerpfad;
	var nameIcon;
	
/*
 *Funktion ist zuständig für den Wechsel von Cloud-Bereich in den Social-Bereich und umgekehrt
 */
 
	function changeEbene(x){
		if(x){ //wenn auf den Social-Button geklickt wird... (x=true)
		
	/*Wenn aktuell der Cloud-Bereich angewählt ist (aktuell=true), dann wird in den Social-Bereich gewechselt.*/
	
			if(aktuell){
				
	/*Zunächst wird der Zustand der Ordnerstruktur in der Seitenleiste und der des Hauptbereiches in Variabeln gespeichert.*/
	
				zustand2 = $('#aside nav').children();
				zustand = $('#inhalt').children();
				
	/*Formatierung der Buttons Cloud und Social anpassen*/
	
				$('header nav a').not('.selected').attr('class', 'selected2');
				$('header nav .selected').removeAttr('class');
				
	/*Ordnerstruktur wird ausgeblendet und durch neuen Inhalt (->Kontakte) ersetzt*/
	
				$('#aside nav').slideUp(500, function(){
					zustand2.detach(); //Ordnerstruktur aus DOM entfernen
					
	/*Ajax-Anfrage an Server für neuen Inhalt*/
	
					xhrContact = new XMLHttpRequest();
					xhrContact.open("POST", "../php/contact.php", true);
					xhrContact.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
					xhrContact.send();
					xhrContact.addEventListener('readystatechange', function(event){
						if(this.readyState == 4 && this.status == 200){
							$('#aside nav').append(this.responseText); //Einfügen des neuen Inhaltes
						}
					}, false);
	
					$('#aside nav').slideDown(500); //neuer Inhalt wird eingeblendet
				});
				
	/*Der Hauptbereich wird ausgeblendet und durch neuen Inhalt ersetzt.*/
	
				$('#main').animate({left: '-100%'}, 500, function(){
					zustand.detach(); //Inhalt aus DOM löschen
					$('#inhalt').attr('id', 'inhalt2'); //umbenennen wegen CSS-Formatierung
					changeMenu(false); //Funktion, die die Menüs für den geänderten Bereich anpassen.
					
	/*Ajax-Anfrage um Inhalt (->Nachrichten) des Social-Bereiches zu laden*/
	
					xhrMail = new XMLHttpRequest();
					xhrMail.open("POST", "../php/mail.php", true);
					xhrMail.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
					xhrMail.send();
					xhrMail.addEventListener('readystatechange', function(event){
						if(this.readyState == 4 && this.status == 200){
							$('#inhalt2').append(this.responseText); //Einfügen des neuen Inhaltes
							$('title').text('Social'); //Umbenennen der Seite
							psdInhHi2(); //Funktion, die Scrollbar für neuen Inhalt anpasst
						}
					}, false);
					
	/*#main mit neuem Inhalt wieder einblenden*/
	
					$('#main').css('left','100%').animate({left: '200px'}, 440).animate({left: '220px'}, 200);
				});
				aktuell = false; //neuer Zustand
				
	/*Oberes Menü ausblenden*/
	
				ausgew = 0;
				$('#mainMT').stop(true, false).animate({top: '-50px'},200);
				$('#pseudoMAIN').stop(true, false).removeClass('downPM').css({top: '44px'}, 200);
				$('#inhalt').stop(true, false).css('padding-top', '54px');
				$('#inhalt > div .auswahl').children().removeClass('ausgew');
				psdInhHi2();//Funktion, die Scrollbar für neuen Inhalt anpasst
			}
			
	/*Wenn der Bereich schon ausgewählt ist, dann wackelt der Hauptbereich nur ein bisschen.*/
	
			else{
				$('#main').animate({left: '120px'}, 250).animate({left: '240px'}, 350).animate({left: '220px'}, 270);
			}
		}
		else{ //wenn auf den Cloud-Button geklickt wird... (x=true)
		
	/*Wenn aktuell der Social-Bereich angewählt ist (aktuell=false), dann wird in den Cloud-Bereich gewechselt.*/
	
			if(!aktuell){
				$('header nav a').not('.selected2').attr('class', 'selected');
				$('header nav .selected2').removeAttr('class');
				
	/*Nach dem Ausblenden des Inhaltes der Seitenleiste, wird der Inahlt durch die zuvor gespeicherte Ordnerstruktur ersetzt.*/
	
				$('#aside nav').slideUp(500, function(){
					$('#aside nav').children().remove();
					zustand2.appendTo('#aside nav');
					$('#aside nav').slideDown(500);
				});
				
	/*Das gleiche im Prinzip auch im Hauptbereich*/
	
				$('#main').animate({left: '100%'}, 440, function(){
					$('#inhalt2').children().remove();
					$('#inhalt2').attr("id", 'inhalt')
					zustand.appendTo('#inhalt');
					changeMenu(false); //die Menüs anpassen an neuen Inhalt
					psdInhHi(); //Scrollbar anpassen
					$('title').text('Cloud');
					$('#main').css('left','-100%').animate({left: '240px'}, 500).animate({left: '220px'}, 200);
				})
				aktuell = true; //neuer Zustand
			}
			
	/*Wenn der Bereich schon ausgewählt ist, dann wackelt der Hauptbereich nur ein bisschen.*/
	
			else{
				$('#main').animate({left: '320px'}, 250).animate({left: '200px'}, 350).animate({left: '220px'}, 270);
			}
		}
	}
	
/*
 *Diese Funktion ist dafür zuständig, die Menüs oberhalb und unterhalb des Hauptbereiches anzupassen, wenn die Ordnerebene gewechselt wird, die Suchfunktion gestartet/beendet wird oder wenn der Social-Bereich angewählt wird (und umgekehrt).
 *linkstring: kann ein Ordnerpfad, false oder 'search' sein. Je nachdem wird das Menü anders geändert.
 */
 
	function changeMenu(linkstring){
		
	/*Wenn linkstring false ist, wird der aktuelle Ordnerpfad der Variable übergeben.*/
	
		if(linkstring==false){
			var upOrdner=document.getElementById('upOrdner');
			linkstring=upOrdner.value;
		}
		
	/*Hier wird bestimmt ob #inhalt (Cloudbereich) oder #inhalt2 (Socialbereich) angezeigt wird. Je nachdem werden Funktionen angezeigt und ausgeblendet*/
	
  		if($('#inhalt').length){
			$('#mainMB .button').css('display', 'inline-block');
			$('#mainMB .update').css('display', 'none').removeClass('load');
			
	/*der Pfad ist '../Dateien', wenn es sich um eine Gruppe handelt. Ist das der Fall, wird das Menü dementsprechend angepasst.*/
	
			if(linkstring=='../Dateien'){
				
	/*Im Ausklappmenü (im unteren Menü) wird die erste Funktion vom Erstellen von Ordnern hin zum Erstellen von Gruppen umgerüstet, dafür wird der Event-Handler ausgetauscht und das Feld umbenannt*/
	
				$('#mainMB').off('click', '#erstFormOrdner').on('click', '#erstFormOrdner', erstellGruppeForm);
				$('#erstFormOrdner').text(String.fromCharCode(160)+String.fromCharCode(160)+"Gruppe");
				
	/*Die weiteren Funktionen des Klappmenüs werden deaktviert.*/
	
				$('.neu div li').not('#erstFormOrdner').css('background-color', 'rgba(50, 50, 50, 0.2)').css('cursor', 'default').each(function(){
					$(this).off('click');
				});
				$('#mainMT li').css('display', 'none');	
				$('#mainMT .gruppe').css('display', 'inline-block');
				$('#mainMB .uploadOp').css('display', 'none');
			}
			
	/*Ist der Pfad keine Gruppe, wird darauf geprüft, ob es sich um einen regulären Ordnerpfad handelt. Um das zu überprüfen, wird auf bestimmte Kriterien geprüft.*/
	
			else if((linkstring.indexOf("/",12)==30||linkstring.length==30)&&linkstring.substring(0,10)=='../Dateien'){
				
	/*Die erste Funktion des Ausklappmenüs wird hier gerade auf umgekehrte Weise geändert: vom Erstellen von Gruppen zum Erstellen von Ordnern.*/
					
				$('#erstFormOrdner').text(String.fromCharCode(160)+String.fromCharCode(160)+"Ordner");
				$('#mainMB').off('click', '#erstFormOrdner').on('click', '#erstFormOrdner', function(){	
					$('#inhalt').append('<div title="neu"><div class="icon"><div class="dircolor"></div></div><form method="post" id="dirNameForm" name="dirNameForm" action=""><input name="neuOrdner" id="neuOrdner" type="text"></form></div>');
					psdInhHi();//Scrollbar anpassen
					document.dirNameForm.neuOrdner.focus(); //Fokus auf Formularfeld
					closeButtonMenu(); //Klappmenü schliessen
				});
		
	/*Den weiteren Menüpunkten des Klappmenüs werden die Funktionen hinzugefügt: mit Ersteren kann man einen einfachen Texteditor einblenden, mit Zweiteren einen Richtext-Editor. Da das Dateiformat auf HTML basiert, habe ich es als P-HTML bzw. Print HTML bezeichnet.*/
	
				$('#erstText').css('background-color', 'rgba(50, 50, 50, 0.8)').css('cursor', 'pointer')
				$('#mainMB').on('click', '#erstText', function(){
					$('.textfenster').css('display', 'block');
					$('.fensterOut').css('display', 'block');
					closeButtonMenu();
				});
				$('#erstRText').css('background-color', 'rgba(50, 50, 50, 0.8)').css('cursor', 'pointer')
				$('#mainMB').on('click', '#erstRText', function(){
					$('.programmfenster').css('display', 'block');
					$('#aside nav').css('display', 'none');			
					$('#asideTools').css('display', 'none');
					closeButtonMenu();
				});
				$('#mainMB .uploadOp').css('display', 'inline-block');
				$('#mainMT li').css('display', 'inline-block');	
				$('#mainMT .gruppe').css('display', 'none');
			}
			
	/*Wenn man in den Suchmodus wechselt:*/	
			
			else if(linkstring=='search'){
				$('#mainMB .uploadOp').css('display', 'none');
				$('#mainMB .neu').css('display', 'none');
				$('#mainMT li').css('display', 'inline-block');	
				$('#mainMT .deleteMT').not('.gruppe').css('display', 'none');	
				$('#mainMT .copyMT').css('display', 'none');	
			}
			
	/*Wenn "linkstring" keine der Kriterien erfüllt, werden alle Funktionen ausgeblendet*/
	
			else{
				$('#mainMB .button').css('display', 'none');
				$('#mainMT .button').css('display', 'none');
			}
		}
		
	/*Wenn der Socialbereich angewählt wird, werden alle normalen Funktionen ausgeblendet. Dafür wird ein Update-Button angezeigt, mit dem man Posteingang aktualisieren kann.*/
	 
  		else if($('#inhalt2').length){
			$('#mainMB .button').css('display', 'none');
			$('#mainMB .update').css('display', 'inline-block');
  		}
	}
	
/*
 *Wenn auf ein Ordnersymbol geklickt wird, wird diese Funktion ausgeführt, die die aktuelle Ordnerebene durch die angeforderte Ordnerebene ersetzt.
 */
 
	function changeOrdner(linkstring){
		
	/*Während der Änderung werden die Events der Icons deaktiviert bzw. gelöscht*/
	
		$('#inhalt').find().removeAttr('href').removeAttr('onclick');
		$('#inhalt').animate({opacity: '0.5'}, 400, 'linear'); //Feedback für User
		$('#upOrdner').attr('value', linkstring); //neuer Pfad in verstecktem Formularfeld speichern
		
	/*Ajax: neuen Inhalt von Server anfordern*/
	
		xhrDir = new XMLHttpRequest();
		xhrDir.open("POST", "../php/icon.php", true);
		xhrDir.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrDir.send("&changelink="+linkstring); //Ordnerpfad wird mitgesendet
		xhrDir.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){				
				var response = this.responseText;
				$('#inhalt').stop(true, false).animate({opacity: '0'}, 200, function(){
					
	/*Inhalt wird durch neuen ersetzt*/
	
					$('#inhalt').children().remove();
					$('#inhalt').append(response);
					changeMenu(linkstring); //Menüs an Ordnerebene anpassen
					
	/*oberes Menü ausblenden, falls eingeblendet*/
	
					ausgew = 0;
					$('#mainMT').stop(true, false).animate({top: '-50px'},200);
					$('#pseudoMAIN').stop(true, false).removeClass('downPM').css({top: '44px'}, 200);
					$('#inhalt').stop(true, false).css('padding-top', '54px');
					psdInhHi(); //Scrollbar aktualisieren
					$(this).animate({opacity: '1'}, 400);
				});
			}
		}, false);
	}
	
/*
 *Funktion für den Socialbereich: öffnet das Fenster mit dem Eingabefeld für die neue Nachricht.
 */
 
	function nachricht(idnummer){		
		$('#mailForm').remove();
		$('.fenster.mail').css('display', 'block');
		$('.fensterOut').css('display', 'block');
		
	/*altes Formular wird durch neues ersetzt*/
	
		$('.fenster.mail div div').children().remove();
		$('.fenster.mail div div').append('<form name="mailForm" id="mailForm"><label for="nachricht">Nachricht:</label><textarea id="nachricht"></textarea><input type="hidden" value="'+idnummer+'"><input type="submit" value="Senden"></form>');
	}
	
/*
 *Funktion zum Sender der geschriebenen Nachricht (siehe obere Funktion)
 */
 
	function sendMail(event){
		var text = $('#mailForm textarea').val(); //Nachricht auslesen
		var adresse = $('#mailForm input[type="hidden"]').val(); //Empfänger auslesen
		
 	/*Nachricht wird per Ajax an den Server geschickt*/
	
		xhrMail = new XMLHttpRequest();
		xhrMail.open("POST", "../php/send.php", true);
		xhrMail.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrMail.send("&text="+text+"&adresse="+adresse);
		xhrMail.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				alert(this.responseText); //Sendebestätigung
				$('#mainMB .update').trigger('click');
			}
		}, false);
	}
		
/*
 *Funktion zum Löschen einer Datei oder eines Ordners
 *linkstring: pfad zur Datei / zum Ordner
 *thisObj: jQuery-0bjekt des angeklickten Icons
 */
 
	function delDatei(linkstring, thisObj){
		
	/*Ajax: löschen der Datei veranlassen*/
	
		xhrDel = new XMLHttpRequest();
		xhrDel.open("POST", "../php/delDatei.php", true);
		xhrDel.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrDel.send("&dellink="+linkstring); //Dateipfad der/des zu löschenden Datei/Ordners
		xhrDel.addEventListener('readystatechange', function(event){
			
	/*Wenn das Löschen erfolgreich war, wird das Icon auch gelöscht*/
	
			if(this.readyState == 4 && this.status == 200){
				thisObj.parent().remove();
				psdInhHi(); //Scrollbar anpassen
				
	/*Da es sich um einen Ordner gehandelt haben könnte, wird auch eine aktualisierte Ordnerstruktur für die Seitenleiste angefordert.*/
	
				xhrNeuAsi = new XMLHttpRequest();
				xhrNeuAsi.open("POST", "../php/neuAside.php", true);
				xhrNeuAsi.send(null);
				xhrNeuAsi.addEventListener('readystatechange', function(event){
					if(this.readyState == 4 && this.status == 200){
						$('#aside nav').children().remove();
						$('#aside nav').append(this.responseText);
						$('.navigation ul').each(function(){
							$(this).css("display", "none"); //Menü zuklappen (standard)
						});
					}
				});
			}
		}, false);
	}
	
/*
 *Beim Absenden des Formulares für neue Ordner, wird diese Funktion ausgeführt, die den Input an den Server übermittelt.
 */
 
	function erstellOrdner(){
		upOrdner = $('#upOrdner').val(); //aktueller Ordnerpfad auslesen
		dName = $('#neuOrdner').val(); //eingegebener Ordnername auslesen
		
	/*Ajax: Werte an Skript auf Server übermitteln, um den Ordner erstellen zu lassen.*/
	
		xhrNeu = new XMLHttpRequest();
		xhrNeu.open("POST", "../php/icon.php", true);
		xhrNeu.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrNeu.send("&dName="+dName+"&upOrdner="+true+"&erstellen="+true);
		xhrNeu.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				
	/*Kontrolle, ob während der Anfrage nicht die Ordnerebene gewechselt wurde*/
	
				if((document.getElementById('upOrdner').value)==upOrdner){
					if(this.responseText=='error:Dublikat'){//wenn der Ordner schon existiert...
						$('#dirNameForm').parent().remove();
						alert('Der Ordner existiert bereits! Bitte wählen Sie einen anderen Ordner');
						$('#erstFormOrdner').trigger('click'); //neues Formular wird angefordert
					}
					else{ //wenn alles OK	...		
						$('#dirNameForm').parent().remove();
						$('#inhalt').append(this.responseText); //neues Icon wird hinzugefügt
					}
					
	/*Ajax: neue Ordnerstruktur für Seitenleiste wird angefordert.*/
	
					xhrNeuAs = new XMLHttpRequest();
					xhrNeuAs.open("POST", "../php/neuAside.php", true);
					xhrNeuAs.send(null);
					xhrNeuAs.addEventListener('readystatechange', function(event){
						if(this.readyState == 4 && this.status == 200){
							$('#aside nav').children().remove();
							$('#aside nav').append(this.responseText);
							$('.navigation ul').each(function(){
								$(this).css("display", "none");
							});
						}
					}, false);
				}
				
	/*ein Blur-Event wird erneut gebunden, da dieser je nach Absende-Methode gelöscht worden sein könnte*/
	
				$('#main').on('blur', '#neuOrdner', function(event){
					$('#dirNameForm').submit();
				});
			}
		}, false);
	}
	
/*
 *Wird ein Ordner in der Seitenleiste erstellt, wird diese Funktion aufgerufen.
 *Ähnlich wie die Funktion erstellOrdner() entspricht der Pfad hier nicht mehr unbedingt der aktuellen Ordnerebene, daher muss serverseitig der Pfad auf eine aufwändigere Art validiert werden.
 */
 
	function erstellOrdnerAside(){
		dName = $('#dirNameFormAs input').val(); //neuer Ordner (Name)
		$('#dirNameFormAs').parent().parent().remove(); //Eingabefeld entfernen
	
	/*Ajax: Absender der Anfrage*/
	
		xhrNeu = new XMLHttpRequest();
		xhrNeu.open("POST", "../php/icon.php", true);
		xhrNeu.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrNeu.send("&dName="+dName+"&upOrdner="+ordnerpfad+"&erstellen="+true+"&aside="+true); //ordnerpfad: siehe nächste Funktion
		xhrNeu.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				$('#aside').on('blur', '#dirNameFormAs', function(event){
					$('#dirNameFormAs').submit();
				});
				if(this.responseText=='error:Dublikat'){ //wenn der Ordner bereits existiert
					alert('Der Ordner existiert bereits! Bitte wählen Sie einen anderen Ordner');
				}
				else{
	
	/*Wenn die Ordnerebene der aktuell offenen Ebene entspricht, wird ein Icon eingefügt.*/
	
					if((document.getElementById('upOrdner').value)==ordnerpfad){
						$('#inhalt').append(this.responseText);
					}
					
	/*Ajax: Ordnerstruktur der Seitenleiste aktualisieren*/
	
					xhrNeuAs = new XMLHttpRequest();
					xhrNeuAs.open("POST", "../php/neuAside.php", true);
					xhrNeuAs.send(null);
					xhrNeuAs.addEventListener('readystatechange', function(event){
						if(this.readyState == 4 && this.status == 200){
							$('#aside nav').children().remove();
							$('#aside nav').append(this.responseText);
							$('.navigation ul').each(function() {
								$(this).css("display", "none");
							});
						}
					}, false);
				}
			}
		}, false);
	}
	
/*
 *Funktion öffnet das Eingabefeld zum Erstellen eines neuen Ordners in der Ordnerstruktur der Seitenleiste, da wo der Nutzer diesen erstellen will.
 *pfad: Pfad zur Ordnerebene, in der der neue Ordner erstellt werden soll
 *object: jQuery-Objekt des angeklickten Elementes (Orientierungspunkt)
 */ 
 
	function erstellOrdAsForm(pfad, object){
		ordnerpfad = pfad; //Pfad wird in globale Variable gespeichert, damit sie beim Absenden von erstellOrdnerAside() ausgelesen werden kann.
		object.parent().parent().before('<li><a><form method="post" name="dirNameFormAs" id="dirNameFormAs"><input type="text"></form></a></li>');
	}

/**
 *Diese Funktion öffnet das Fenster, um Gruppen zu erstellen. Die Funktion ist über das Klappmenü im unteren Menü erreichbar.
 */
 
	function erstellGruppeForm(){
		$('.gruppenfenster').css('display', 'block');
		$('.fensterOut').css('display', 'block');
		
	/*Diese Funktion schliesst das offene Klappmenü*/
	
		closeButtonMenu()
	}
	
/**
 *Sendet das Formular zum Gruppen-Erstellen an den Server.
 */
 
	function sendGruppeForm(){
		
	/*mit FormData() werden alle Felder des Formulars auf einmal in eine Variable gespeichert (so verpackt, dass sie über Ajax versendet werden können). So kann man auch Arrays per Ajax senden, sonst hätte man das Array manuel in einen String oder etwas ähnliches umwandeln müssen, was aber das serverseitige Auslesen des Arrays komplizierter machen würde.*/
	
		var formData = new FormData(document.formGruppe);
		
	/*Ajax: formData wird an serverseitiges Skript gesendet.*/
			
		xhrGSend = new XMLHttpRequest();
		xhrGSend.open("POST", "../php/gruppeErstellen.php", true);
		xhrGSend.send(formData);
		xhrGSend.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				if((document.getElementById('upOrdner').value).length<29){
					$('#inhalt').append(this.responseText);
				}
	
	/*Bei Erfolg wird noch die Seitenleiste (per Ajax) aktualisiert, um die neue Gruppe auch dort anzuzeigen.*/
	
				xhrNeuAs = new XMLHttpRequest();
				xhrNeuAs.open("POST", "../php/neuAside.php", true);
				xhrNeuAs.send(null);
				xhrNeuAs.addEventListener('readystatechange', function(event){
					if(this.readyState == 4 && this.status == 200){
						$('#aside nav').children().remove();
						$('#aside nav').append(this.responseText);
						$('.navigation ul').each(function() {
							$(this).css("display", "none");
						});
					}
				}, false);
			}
		}, false);
	}
	
/**
 *Kleine Funktion, die das Klappmenü im unteren Menü schliesst (ohne CSS-Animation)
 */
 
	function closeButtonMenu(){
		
	/*Das Menü wird mit sich selbst ersetzt*/
	
		var button = $('.button.neu');
		$('.button.neu').replaceWith(button);
	}
	
/**
 *sucheStart() versendet den eingegebenen Suchstring an den Server und gibt die Sucheregebnisse, die zurückgegeben werden, aus.
 *der Wert "komplex" gibt an, ob auch die Unterordner durchsucht werden sollen.
 */
 
	function sucheStart(komplex){
		
/*Suchstring aus Imput auslesen*/

		var suche = $('#sucheInput').val();

/*Die Ordnerebene, von der aus die Suche gestartet wird, wird in einer Variable bis zum Ende der Suche zwischengespeichert. Die If-Abfrage dient dazu, zu verhindern, dass bei einer weiteren Suchanfrage, die Ordnerebene durch den Inhalt des vorderen Suchergebnisses überschrieben wird.*/

		if(!$('#inhalt > .olink b').length){
			zustand3 = $('#inhalt').children();
		}
		
/*Die Suchanfrage wird per Ajax gesendet*/

		xhrSearch = new XMLHttpRequest();
		xhrSearch.open("POST", "../php/search.php", true);
		xhrSearch.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrSearch.send("&suche="+suche+"&komplex="+komplex);
		xhrSearch.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				var response = this.responseText;
				
 	/*der aktuelle Inhalt wird durch Surchergebnis ersetzt.*/
	
				$('#inhalt').stop(true, false).animate({opacity: '0'}, 200, function(){
					$('#inhalt').children().remove();
					$('#inhalt').append(response);
					
	/*Die Funktion zum Ändern der Menüs wird aufgerufen, danach wird das eventuell ausgeklappte obere Menü  eingefahren*/ 
	
					changeMenu('search');
					ausgew = 0;
					$('#mainMT').stop(true, false).animate({top: '-50px'},200);
					$('#pseudoMAIN').stop(true, false).removeClass('downPM').css({top: '44px'}, 200);
					$('#inhalt').stop(true, false).css('padding-top', '54px');
					$('#inhalt > div .auswahl').children().removeClass('ausgew');
					psdInhHi();//Scrollbar anpassen
					$(this).animate({opacity: '1'}, 400);
				});
			}
		}, false);
	}
	
/**
 *Mit dieser Funktion wird der Suchmodus beendet und der alte Zustand wieder hergestellt.
 */
 
	function delSuche(){
		$('#inhalt').stop(true, false).animate({opacity: '0', top: '80px'}, 200, 'linear', function(){
			$('#inhalt').children().remove();
			
	/*Der alte Zustand wird aus "zustand3" ausgelesen und dem Inhalt-div wieder eingefügt*/			

			zustand3.appendTo('#inhalt');
			
	/*Hier passiert das gleiche wie in der obigen Funktion*/
	
			changeMenu(false);
			ausgew = 0;
			$('#mainMT').stop(true, false).animate({top: '-50px'},200);
			$('#pseudoMAIN').stop(true, false).removeClass('downPM').css({top: '44px'}, 200);
			$('#inhalt').stop(true, false).css('padding-top', '54px');
			$('#inhalt > div .auswahl').children().removeClass('ausgew');
			psdInhHi(); //Scrollbar anpassen
			$(this).css('top','0px').animate({opacity: '1'}, 400);
		});
	}
	
/**
 *Funktion zum Erstellen und Speichern der Textdatei, die im Texteditor erstellt wurde
 */
 
	function textSpeichern(){
		
	/*Der Dateiinhalt, der Titel und der aktuelle Ordnerpfad wird ausgelesen.*/
	
		var dateiinhalt = $('#textdiv').val();
		var dname = $('#textname').val();
		var upOrdner = $('#upOrdner').val();
		
	/*Variablenwerte werden an Server gesendet*/
	
		xhrText = new XMLHttpRequest();
		xhrText.open("POST", "../php/dateierstellen.php", true);
		xhrText.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrText.send("&dname="+dname+"&dateiinhalt="+dateiinhalt+"&kontrolle="+1);
		xhrText.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				if((document.getElementById('upOrdner').value)==upOrdner){
		
	/*Icon für neue Textdatei wird dem Inhalt-Div hinzugefügt.*/
	
					$('#inhalt').append(this.responseText);
					psdInhHi(); //Scrollbar anpassen
				}
			}
		}, false);
	}
	
/**
 *Inputfeld für das Umbenennen einer Datei / eines Ordners wird eingefügt.
 */
 
	function umbenennen(){
		$('.icon~.auswahl .ausgew').parent().prev().first().each(function(i){
			nameIcon = $(this);
			$(this).replaceWith('<form method="post" id="umbenennenForm" name="umbenennenForm" action=""><input name="umbenennen" id="umbenennen" type="text"></form>');
		});
	}
	
/**
 *Funktion zum Umbenennen einer Datei / eines Ordners. 
 *Liest mit umbenennen() eingefügtes Feld aus und übergibt Werte an serverseitiges Skript, um Datei umzubenennen 
 */
 
	function umbenennenSend(){
		var neu = $('#umbenennen').val();
		var linkstring = $('.icon~.auswahl .ausgew').parent().data('link');
		
	/*Werte übermitteln (Ajax)*/
	
		xhrRename = new XMLHttpRequest();
		xhrRename.open("POST", "../php/umbenennen.php", true);
		xhrRename.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrRename.send("&link="+linkstring+"&neu="+neu);
		xhrRename.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
	
	/*Icon der Datei wird aktualisiert*/
				if(this.responseText!=""){
					$('.icon~.auswahl .ausgew').parent().parent().first().replaceWith(this.responseText);
				}
				else{
					$('.icon~.auswahl .ausgew').parent().prev().first().each(function(){
						$(this).replaceWith(nameIcon);
					});
				}
			}
		}, false);
	}
	
/**
 *Funktion, die Titelanzeige des Players animiert.
 */
 
	function bildlauf(){
		var a = $('.player span').width();
		$('.player span').css('left', 220 - a/2);
		$('.player span').animate({left: 220 -a}, 7500, 'linear', bildlauf);
	}
	
/**
 *Diese Funktion passt die Grösse des #pseudoINHALT im Verhältnis zum #pseudoMAIN an den eigentlichen Inhalt an.
 *Durch diese Funktion wird sichergestellt, dass das Verhältnis von #pseudoMAIN zu #pseudoINHALT das gleiche ist wie für #main zu #inhalt, damit die Scrollbar auch dem Inhalt angepasst angezeigt wird.
 *Zusätzlich gibt psdInhHi() in Form von facScroll eine globale Variable mit dem Grössenverhältnis zwischen #main und #pseudoMAIN aus.
 */
 
	function psdInhHi(){
		facScroll= $('#main').outerHeight() / $('#pseudoMAIN').outerHeight();
		var outerHght = $('#inhalt').outerHeight()/facScroll;
		$('#pseudoINHALT').outerHeight(outerHght);
	}
	
/**
 *Kopie der obigen Funktion, hier aber für #inhalt2, also den Socialbereich.
 */
 
	function psdInhHi2(){
		facScroll= $('#main').outerHeight() / $('#pseudoMAIN').outerHeight();
		var outerHght = $('#inhalt2').outerHeight()/facScroll;
		$('#pseudoINHALT').outerHeight(outerHght);
	}
	
/**
 *Funktion für Richtext-Editor: 
 *Holt Dokumenteninhalt aus dem iFrame und speichert dieses in ein Formularfeld (#speicher)
 *Wird auf den Print-Button geklickt, wird ein Formular an eine spezielle Seite geschickt (wird in einem neuen Tap geöffnet), dass das Dokument anzeigt und die Druckfunktion des Browsers automatisch aufruft. Dafür muss der Dokumenteninhalt aber zunächst dem Formular übergeben werden.
 */
 
	function tool_print(){
		$('#blatt').contents().find('.blatt > div').css('border', 'none');
		$('#speicher').val($('#blatt').contents().find('html').html());
	}
	
/**
 *Funktion für Richtext-Editor: 
 *Funktion zum Speichern des Richtext-Dokuments.
 */
 
	function tool_save(e){
	
	/*Standardverhalten für das Formular wird deaktiviert*/
	
		e.preventDefault();
		e.stopPropagation();
		$('#blatt').contents().find('.blatt > div').css('border', 'none');
		
	/*Inhalt des Dokumentes wird in Variable gespeichert.*/
	
		var inhalt = $('#blatt').contents().find('html').html();
		
	/*Die Variable "kontrolle" legt fest, ob serverseitig geprüft werden soll, ob die Datei bereits existiert und dementsprechend anpasst: Standardeinstellung ist 1 (=ja).*/
	
		var kontrolle = 1;
		
	/*Wenn die Datei zum ersten Mal gespeichert wird, wird nach dem Dateinamen gefragt*/
	
		if($('#speicherName').val()==""){
			var dateiname=prompt("Geben Sie bitte den Dateinamen ein!", "Dokument.phtml");
		}
		
	/*Wenn ein Dateiname schon hinterlegt wird, wird automatisch dieser verwendet und "kontrolle" wird auf 0 gesetzt, da die alte Version durch die neue überschrieben werden soll.*/
	
		else{
			kontrolle = 0;
			var dateiname=$('#speicherName').val();
		}
		
	/*Der Wert null zeigt an, dass der Nutzer den Vorgang abgebrochen hat.*/
	
		if(dateiname!=null){
			
	/*Es wird hier geprüft, ob der Dateiname die Mindestlänge erfüllt, sonst wird nachgefragt, bis der Dateiname lange genug ist oder der Vorgang vom Nutzer abgebrochen wird.*/
	
			while(dateiname != null && dateiname.slice(0,dateiname.indexOf('.')).length <= 2){
				dateiname=prompt("Der Dateiname ist zu kurz. Bitte geben Sie einen längeren Dateinamen ein!", "Dokument.phtml");
				kontrolle = 1;
			}
			if(dateiname != null){
				
	/*Dem Dateinamen wird die Endung abgeschnitten und die richtige Endung hinzugefügt*/
	
				dateiname = dateiname.slice(0,dateiname.indexOf('.'));
				dateiname = dateiname+'.phtml';						
				var upOrdner = $('#upOrdner').val();
				
	/*Da der String mit dem Dateiinhalt Überlänge hat, muss das ganze in ein FormData-Objekt gepackt werden.*/ 
	
				var formData = new FormData();	
				formData.append('dname', dateiname);	
				formData.append('dateiinhalt', inhalt);					
				formData.append('kontrolle', kontrolle);
				
	/*formData wird an Server geschickt und wenn die Datei zum ersten Mal gespeichert wird, wird ein Icon im Hintergrund zur Ordnerebene hinzugefügt*/
	
				xhrText = new XMLHttpRequest();
				xhrText.open("POST", "../php/dateierstellen.php", true);
				xhrText.send(formData);
				xhrText.addEventListener('readystatechange', function(event){
					if(this.readyState == 4 && this.status == 200){
						if(kontrolle==1){
							if((document.getElementById('upOrdner').value)==upOrdner){
								$('#inhalt').append(this.responseText);
								$('#speicherName').val($('#inhalt').children('div').last().children('span').text());
								psdInhHi();
							}
						}
					}
				}, false);
			}
		}
		return false;
	}
	
/**
 *Ist das DOM vollständig geladen, wird die folgende Funktion aufgerufen, in der die Event-Handler gebudnen werden und alles erledigt wird, was halt beim Seitenaufruf ansteht.
 */
 
$(document).ready(function(){
	
/*Animation der Titelanzeige des Players*/
	
	bildlauf();	
	
/*Patch für Touch-Geräte damit Mouseover beendet werden kann. (Touch-Gerät belässt Zustand, bis ein anderes Event ausgelöst wird)*/

	$('*').hover(function(){}, function(){});

/**
 *Die Box für die Scrollbar wird beim Laden der Seite an den Inhalt des Hauptbereiches angepasst.
 *Wird die Fenstergrösse geändert, muss die Anpassung neu vorgenommen werden.
 */

	psdInhHi();	
	$(window).resize(function(){
		 if($('#inhalt2').length){
			 psdInhHi2();
		 }
		 else{
		 	psdInhHi();
		 }
	});	
	
/**
 *Mit dem folgendem JavaScript wird die Scrollbewegung von pseudoMAIN/pseudoINHALT auf main/inhalt übertragen und umgekehrt.
 */
 	
	var scrollbox = document.getElementById('pseudoMAIN');
	var scrollbo2 = document.getElementById('main');	
	
/*zunächst wird registriert, in welchem der beiden Bereiche sich die Maus befindet. Wenn man dies nicht bestimmt, kommt es zu einer Rückkopplung, da das Anpassen per Script auch ein Scroll-Event auslöst*/

	scrollClick=false;	
	$('#pseudoMAIN').mouseover(function(){
		scrollClick = true;
	});
	$('#main').mouseover(function(){
		scrollClick = false;
	});	
	
/*Wenn die Scrollbar betätigt wird (im #pseudoMAIN) wird die Scrollposition (mit einem Dreisatz umgerechnet) auf #main übertragen*/

	scrollbox.addEventListener('scroll', function() {
		if(scrollClick){
			$('#main').scrollTop($('#pseudoMAIN').scrollTop()*facScroll);
		}
	}, false);
	
/*hier die Entsprechung für Touchgeräte*/ 

	scrollbox.addEventListener('touchmove',function() {
		$('#main').scrollTop(facScroll*$('#pseudoMAIN').scrollTop());
	}, false);
	
/*Wenn mit dem Mausrad (über #main) gescrollt wird, wird mit diesen Funktionen die Scrollposition von #pseudoMAIN angepasst und somit die Scrollbar.*/

	scrollbo2.addEventListener('scroll', function() {
		if(!scrollClick){
			$('#pseudoMAIN').scrollTop($('#main').scrollTop()/facScroll);
		}
	}, false);
	scrollbo2.addEventListener('touchmove',function() {
		$('#pseudeMAIN').scrollTop($('#main').scrollTop()/facScroll);
	}, false);
	
/*Beim iPad wird die Scrollbar ganz ausgeblendet*/

	if(navigator.platform == "iPad"){
		$('#pseudoMAIN').css('display', 'none');
	}
	
/**
 *Footer/Fussleiste ein- und ausblenden.
 *Die erste Funktion gibt an, was bei Mouseenter geschieht, die zweite, was bei Mouseout geschieht.
 */	
 
	$('#footer').hover(function(){
		$(this).stop(true, false).animate({height: '24px', opacity: '1'}, 600);
		$('#mainMB').stop(true, false).animate({bottom: '18px'}, 400);
		$('#footer a').css('display', 'inline');
	
	}, function(){
		$(this).stop(true, false).animate({height: '10px', opacity: '0'}, 900);
		$('#mainMB').stop(true, false).animate({bottom: '0px'}, {duration: 1200, complete: function(){$('#footer a').css('display', 'none');}});	
	});
	
/**
 *Die folgenden drei Funktionen sind für das ein- und ausblenden der Hilfe zur Suchfunktion zuständig.
 *Per Klick wird die Hilfe ausgeklappt. Ist die Maus länger ausserhalb des Textfeldes mit der Hilfe, wird diese wieder ausgeblendet, was mit einem Mouseenter wiederum aufgehalten werden kann.
 */
 
	$('#inhalt').on('click', '.shilfe p', function(){
		$('#inhalt .shilfe > div').fadeIn(1000);
		psdInhHi();
	});
	$('#inhalt').on('mouseleave', '.shilfe', function(){
		$('#inhalt .shilfe > div').stop(true, false).animate({opacity: '1'}, 2500).fadeOut(2000, function(){
			psdInhHi();
		});
	});
	$('#inhalt').on('mouseenter', '.shilfe div', function(){
		$('#inhalt .shilfe > div').stop(true, false).fadeIn(1000);
	});
	
/**
 *Hier wird dem Kippschalter (oben links) die eigentliche Funktionalität hinzugefügt:
 *Das sogenannte Shoji wird über der Seitenleiste ein- und ausgeblendet
 */

	$('.schalterChBox').click(function(){
		if(this.checked){
			$('#shoji').animate({width: '120px', left: '-125px'}, 1000);
			$('#header > div > div').stop(true, false).animate({top: '0px', left: '-160px', opacity: '1'}, {duration: 0, queue: false});
		}
		else{
			$('#shoji').animate({width: '214px', left: '0px'}, 1000);
			$('#header > div > div').animate({opacity: '0'}, {duration: 150, queue: false, complete: function(){
				$('#header > div > div').animate({top: '-200px'}, 0);}
			});
			
	/*Beim Einblenden wird noch das Widget aktualisiert, das den besetzten Speicherplatz anzeigt.*/
	
			xhrWidget = new XMLHttpRequest();
			xhrWidget.open("POST", "../php/widgetMemory.php", true);
			xhrWidget.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
			xhrWidget.send(null);
			xhrWidget.addEventListener('readystatechange', function(event){
				if(this.readyState == 4 && this.status == 200){
					$('#shoji .mWidget').children().remove();
					$('#shoji .mWidget').append(this.responseText);
				}
			}, false);
		}
	});
	
/**
 *Folgender Ausdruck schliesst beim Laden der Seite das Ordnerstrukur-Klappmenü in der Seitenleiste.
 *Der darauffolgende Event-Handler öffnet und schliesst die Unterordner des angeklickten Ordners.
 */
 
	$('.navigation ul').each(function() {
		$(this).css("display", "none");
	});
	$('#aside nav').on('click', '.navigation a', function(){
		x=$(this);
	
	/*Wenn ausgeblendet, wird eingeblendet und umgekehrt*/
	
		if(x.next('ul').css('display') == "none"){
			x.next('ul').stop(true, false).slideDown(300);
		}
		else{
			x.next('ul').find('ul').andSelf().slideUp(300);
		}	
	});
	
/**
 *Auch wenn Darg&Drop noch nicht implementiert ist, bzw. das Ziehen der Icons über etwas keine Aktion auslöst, habe ich vorausschauend schon mal implementiert, dass sich das jeweilige Untermenü des Ordners öffnet, wenn man mit einer "gedragten" Datei über ihn fährt. 
 */
 
	$('#aside nav').on('dragenter', '.navigation a', function(){
		$(this).next('ul').stop(true, false).slideDown(200);
		$(this).parents().siblings().children('ul').add('ul', this).slideUp(200);
	});

/**
 *Hält man die Maus lange genug über den Namen (oben links), wird ein Abmeldebutton mit Hilfe des untenstehenden JavaScript eingeblendet.
 */
 
	var mouseEnter = true;
	$('#header > div > p').mouseenter(function(){
		mouseEnter = true;		
		setTimeout(function(){ //wartet 500ms
		
	/*Mit mouseEnter wird geprüft, ob nach dem setTimeout sich die Maus noch über dem Name befindet.*/
	
			if(mouseEnter == true){
			$('#header > div > div').stop(true, false).animate({left: '16px'}, 1000).animate({left: '16px'}, 3000).animate({left: '-160px'}, 1000);
			$('#header > div > p').stop(true, false).animate({opacity: '0.2'}, 1000).animate({opacity: '0.1'}, 3000).animate({opacity: '1'}, 2000)
			}
		},500);
	});
	$('#header > div > p').mouseleave(function(){
		mouseEnter = false;
	});
	
/**
 *Wird auf einen der Abmeldebuttons (2) geklickt, wird auf das Abmelde-Skript umgeleitet.
 */
 
	$('.abmelden').click(function(){
		window.location.href = "../php/abmelden.php";
	});	
	
	
/**
 *Mit Hilfe dieses Event-Handlers lassen sich Dateien/Ordner aus- und abwählen, wenn man auf die am Icon angebrachte Checkbox (ein div, nicht ein input) klickt.
 *Sind Dateien ausgewählt, wird das obere Menü eingeblendet.
 *Mit der Variable "ausgew" wird gezählt, wieviel Dateien ausgewählt sind.
 */
	
	$('#main').on('click', '#inhalt > div .auswahl', function(){
		
	/*Ist die Datei bereits ausgewählt, wird sie wieder abgewählt*/
 
		if($(this).children().is('.ausgew')){
			ausgew--;
			$(this).children().removeClass('ausgew');//Klasse "ausgew" wird entfenrnt.	
			
	/*War es die letzte markierte Datei, wird das obere Menü ausgefahren.*/
	
			if(ausgew==0){	
				$('#mainMT').stop(true, false).animate({top: '-50px'},1000);
				$('#pseudoMAIN').stop(true, false).removeClass('downPM').css({top: '44px'}, 1000);
				$('#inhalt').stop(true, false).css('padding-top', '54px');				
			}
		}
		
	/*Ist die Datei noch nicht ausgewählt, wird sie markiert*/
 		
		else{
			$(this).children().addClass('ausgew');//Klasse "ausgew" wird hinzugefügt.
			
	/*Handelt es sich um die erste ausgewählte Datei, wird das obere Menü eingefahren.*/
	
			if(ausgew==0){	
				$('#mainMT').stop(true, false).animate({top: '44px'},{duration: 1000, queue: false, complete: function(){
					$('#pseudoMAIN').stop(true, false).addClass('downPM').css({top: '88px'}, 1000);
					$('#inhalt').stop(true, false).css('padding-top', '92px');
					}
				});
			}
			ausgew++;
		}
		psdInhHi();	//Scrollbar anpassen	
	});

/**
 *Ein- und ausblenden der Löschen-Buttons auf Icons
 */
 
	var delClick=false; //speichert, ob Buttons eingeblendet sind
	$('#mainMB .delete').click(function(){	
		if(delClick){
			delClick = false;
			$('#inhalt > div .delDatei').css('display', 'none');
			$('#inhalt > div .auswahl').css('display', 'block');
		}
		else{
			delClick = true;
			$('#inhalt > div .delDatei').css('display', 'block');			
			$('#inhalt > div .auswahl').css('display', 'none');
		}
	});	
	
/**
 *Wird ein andere Funktion in den Menüs gewählt, verschwinden die Löschen-Buttons auf den Icons.
 */
 
	$('#mainMB .button').not('.delete').click(function(){
		delClick = false;
		$('#inhalt > div .delDatei').css('display', 'none');
		$('#inhalt > div .auswahl').css('display', 'block');
	});
	$('#mainMT .button').click(function(){
		delClick = false;
		$('#inhalt > div .delDatei').css('display', 'none');
		$('#inhalt > div .auswahl').css('display', 'block');
	});
	
/**
 *Löschen über oberes Menü:
 *Löscht alle markierten Dateien/Ordner, indem der Löschen-Button der jeweiligen Datei getriggert wird.
 */
 
	$('.deleteMT').click(function(){
		var x = $('.icon~.auswahl .ausgew').parent();
		if(x.length>1){
			
	/*Bei mehreren ausgewählten Dateien wird beim Anwender nachgefragt.*/
	
			if(confirm("Achtung! es werden mehrere Dateien und/oder Ordner gelöscht. \n Sind Sie mit der Aktion einverstanden?")){
				x.next('.delDatei').trigger('click');
			}
		}else{
			x.next('.delDatei').trigger('click');
		}
	});
	
/**
 *Duplizieren über oberes Menü:
 *Dupliziert die ausgewählten Dateien und Ordner.
 */
 
	$('.copyMT').click(function(){
		upOrdner = $('#upOrdner').val();
	
	/*Für jede Datei wird sein Pfad ausgelesen und jeweils an den Server geschickt. Bei erfolgreichem Duplizieren wird das zurückgegebene Icon zur neuen Datei der Seite hinzugefügt. Zur Unterscheidung der abgesendeten Ajax-Anfragen wird ein Index zu jeder Anfrage hinzugefügt*/
	
		$('.icon~.auswahl .ausgew').parent().each(function(i){
			var dlink = $(this).data('link');
			xhrCopy = new Array;
			xhrCopy[i] = new XMLHttpRequest();
			xhrCopy[i].open("POST", "../php/kopieren.php", true);
			xhrCopy[i].setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
	
	/*Beim Duplizeren wird die Datei in den gleichen Ordner kopiert, daher ist newlink der aktuelle Ordnerpfad (upOrdner); copy=1 bedeutet, dass kopiert und nicht verschoben wird.*/
	
			xhrCopy[i].send("&oldlink="+dlink+"&newlink="+upOrdner+"&copy="+1);
			xhrCopy[i].addEventListener('readystatechange', function(event){
				if(this.readyState == 4 && this.status == 200){
					if((document.getElementById('upOrdner').value)==upOrdner){
						$('#inhalt').append(this.responseText);
						psdInhHi();
						
	/*Zum Schluss wird noch die Seitenleiste aktualisiert.*/
	
						xhrNeuAsi = new Array;
						xhrNeuAsi[i] = new XMLHttpRequest();
						xhrNeuAsi[i].open("POST", "../php/neuAside.php", true);
						xhrNeuAsi[i].send(null);
						xhrNeuAsi[i].addEventListener('readystatechange', function(event){
							if(this.readyState == 4 && this.status == 200){
								$('#aside nav').children().remove();
								$('#aside nav').append(this.responseText);
								$('.navigation ul').each(function() {
									$(this).css("display", "none");
								});
							}
						});
					}
				}
			}, false);
		});
	});
	
/**
 *Verschieben und Kopieren über oberes Menü (folgende 3 Event-Handler)
 *Mit ersterem Event-Handler wird das Fenster geöffnet, wo man auswählen kann, in welchen Ordner verschoben werden soll. Man kann im Fenster ausserdem auswählen, ob kopiert oder verschoben werden soll.
 */
 
	$('.verschiebenMT').click(function(){
		$('.verschiebenfenster').css('display', 'block');
		$('.fensterOut').css('display', 'block');
		
	/*Die Liste wird von einem Skript per Ajax angefordert, so ist sichergestellt, dass die Liste aktuell ist.*/
	
		xhrGruppe = new XMLHttpRequest();
		xhrGruppe.open("POST", "../php/ordnerAuswahl.php", true);
		xhrGruppe.send(null);
		xhrGruppe.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				
	/*Die alte Liste wird durch die neue ersetzt*/
	
				$('#bVerschieben').children().remove();
				$('#bVerschieben').append(this.responseText);
			}
		}, false);
	});	
	
/**
 *Handler ist zuständig für das Navigieren durch die Ordnerliste
 *Das genaue Verhalten ist aber schwer zu beschreiben: im Prinzip wird immer nur eine Ordnerebene plus das Elternverzeichnis eingeblendet.
 */
 
	$('#bVerschieben').on('click', 'a', function(){
		$('#bVerschieben').find('a').removeClass('selected');
		$(this).next().find('li').css('display', 'none').children('a').css('background-color', 'rgba(230, 230, 230, .6)');
		$(this).next().children('li').css('display','block');
		$(this).next().children('li').children('a').text(function(){
			return $(this).data('name');
		});
		$(this).parent().siblings('li').css('display', 'none');
		$(this).css('background-color', 'rgba(255, 255, 255, .8)').text('Ausgewählt: '+$(this).data('name'));
		$(this).parentsUntil('div').prev('a').css('display', 'none');
		$('#bVerschieben').children().children().children('a').text($('#bVerschieben').children().children().children('a').data('name'));
		$(this).parent().parent().prev('a').css('background-color', 'rgba(150, 150, 150, .6)').css('display', 'block').text('Ebene nach oben');
		$(this).addClass('selected');
	});	
	
/**
 *Event-Handler wird beim Absenden des Formulars, für das Auswählen des Verzeichnisses, ausgelöst.
 */
 
	$('#formVerschieben').submit(function(event){
		event.preventDefault();//Standardverhalten des Formularfeldes wird blockiert
		event.stopPropagation();//Standardverhalten des Formularfeldes wird blockiert
		
	/*Es wird ausgelesen, welcher Ordner ausgewählt wurde. Wenn keiner ausgewählt wurde, wird mit einem Hinweis abgebrochen.*/
	
		var newlink = $('#bVerschieben').find('.selected').data('link');
		if(!newlink){
			 alert('Bitte wählen Sie einen Ordner!');
		}
		
	/*Ansonsten wird das Fenster für die Verzeichniswahl wieder ausgeblendet*/
	
		else{
			$('.verschiebenfenster').css('display', 'none');
			$('.fensterOut').css('display', 'none');
			var kopieren=0;
			
	/*Jetzt wird ausgelesen, ob "kopieren" ausgewählt worden ist*/
	
			if($('#formVerschieben input[type=checkbox]').is(':checked')){ 
				kopieren=1;
			};
			
	/*Für jede ausgewählte Datei / jeder ausgewählte Ordner wird nun das Kopieren/Verschieben eingeleitet.*/
	
			$('.icon~.auswahl .ausgew').parent().each(function(i){
				
	/*Werte werden ausgelesen*/
	
				var oldlink = $(this).data('link');
				var thisObj=$(this);
				
	/*Ermittelte Werte werden an kopieren.php gesendet (Ajax)*/
				
				xhrCopy = new Array;
				xhrCopy[i] = new XMLHttpRequest();
				xhrCopy[i].open("POST", "../php/kopieren.php", true);
				xhrCopy[i].setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
				xhrCopy[i].send("&oldlink="+oldlink+"&newlink="+newlink+"&copy="+kopieren);
				xhrCopy[i].addEventListener('readystatechange', function(event){
					if(this.readyState == 4 && this.status == 200){
	
	/*Die Antwort wird zunächst in das DOM eingefügt.*/
	
						$('#inhalt').append(this.responseText);
						
	/*Ist alles OK und ist kopieren nicht ausgewählt, wird das alte Icon entfernt*/
	
						if($('p.fehler').length==0 && kopieren==0){
							thisObj.parent().remove();
							psdInhHi();					
						}
						else{
	
	/*Es gibt einen Fehler, wenn man ein Verzeichnis in sich selbst verschieben will, dann wird die folgende Meldung ausgegeben. Und die zuvor eingefügte Fehlermeldung wird wieder aus dem DOM entfernt.*/
	
							if($('p.fehler').text()=="Fehler"){
								 alert('Diese Aktion kann nicht durchgefüht werden. \n'+thisObj.prev().text()+' kann nicht in sich selbst verschoben werden.');
							}
							$('#inhalt').find('p.fehler').remove();
						}
						
	/*Zum Schluss wird noch die Seitenleiste aktualisiert*/
	
						xhrNeuAsi = new Array;
						xhrNeuAsi[i] = new XMLHttpRequest();
						xhrNeuAsi[i].open("POST", "../php/neuAside.php", true);
						xhrNeuAsi[i].send(null);
						xhrNeuAsi[i].addEventListener('readystatechange', function(event){
							if(this.readyState == 4 && this.status == 200){
								$('#aside nav').children().remove();
								$('#aside nav').append(this.responseText);
								$('.navigation ul').each(function() {
									$(this).css("display", "none");
								});
							}
						});
						
					}
				}, false);
				
			});
		}
	});
	
/**
 *Download über oberes Menü
 *Dieses Event-Handler löst den Download der markieten Dateien und Verzeichnisse, wobei die Verzeichnisse zunächst als Zip verpackt werden sollen.
 */
 
	$('.downloadMT').click(function(){
		
	/*Aus einem zuvor hinterlegtem Data-Attribut wird bestimmt, ob es sich um ein Verzeichnis oder eine Datei handelt*/
	
		x = $('.icon~.auswahl .ausgew').parent();
		
	/*Handelt es sich um eine Datei, wird der Download-Pfad (Link zu Download-Script mit angehängten Parametern) einem neu erstellten iFrame übergeben, dies ist nötig, da die Seite selbst nicht mehrere Links per document.location.href gleichzeitig öffnen kann.*/
		$('body > iframe').remove(); //alte iFrames löschen
		x.prev('span[data-type="d"]').each(function(i){
			var url = $(this).data('download');
			$('body').append('<iframe id="downFrame'+i+'"></iframe>');
			$('#downFrame'+i).css('display', 'none').attr('src', url); 
			frames = i; //Zähler global speichern
		});
		
	/*Sind (auch) Verzeichnisse ausgewählt, müssen die Ordner zunächst gezipt werden.*/
	
		if(x.prev('span[data-type="o"]').length){
			
	/*Es wird aber zunächst beim Nutzer nachgefragt, ob der Vorgang durchgeführt werden soll. Damit die Frage grammatisch richtig formuliert wird, wird zunächst gezählt, ob es sich um einen oder mehrere Verzeichnisse handelt*/
	
			if(x.prev('span[data-type="o"]').length==1){
				var teststring = 'Soll der';
			}
			else{
				var teststring = 'Sollen die';
			}
			if(confirm('Ordner können nicht heruntergeladen werden! '+teststring+' Ordner in ein Zip-Archiv umgewandelt werden?')){

	/*Für jedes Verzeichnis wir ein einzelnes Zip-Archiv erstellt*/
	
				x.prev('span[data-type="o"]').each(function(i){
					
	/*Obwohl Verzeichnis für Verzeichnis (bzw der Pfad dazu) einzeln an den Server gesendet werden, muss der String in ein Array gepackt werden und daher als JSON-Datei verpackt werden, da das serverseitige Skript ein Array fordert*/
	
					var array = new Array;
					array.push($(this).next().data('link'));
					array = JSON.stringify(array);
					
	/*Ajax-Anfrage mit erstelltem Array*/
	
					xhrZIP = new Array;
					xhrZIP[i] = new XMLHttpRequest();
					xhrZIP[i].open("POST", "../php/zip.php", true);
					xhrZIP[i].setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
					xhrZIP[i].send("&array="+array);
					xhrZIP[i].addEventListener('readystatechange', function(event){
						if(this.readyState == 4 && this.status == 200){
							$('#inhalt').append(this.responseText);
							
	/*der Zip-Archiv wird in das DOM hinzugefügt, danach wird über das hinzugefügte Icon der Download des Archives veranlasst. (nach gleichem Prinzip wie oben)*/
	
							if(this.responseText!=""){
								$('#inhalt').find('span[data-type="d"]').last().each(function(){
									var url = $(this).data('download');
									frames++; //vorhin global gespeicherter Zähler
									$('body').append('<iframe id="downFrame'+frames+'"></iframe>');
									$('#downFrame'+frames).css('display', 'none').attr('src', url); 
								});
							}
	
	/*Handelt es sich um einen leeren Ordner, wird die folgende Meldung zurückgegeben*/
	
							else{
								alert('Leere Ordner können nicht als Zip verpackt werden');
							}
						}
					}, false);
				});
			}
		}
	});

/**
 *Zip-Archiv über oberes Menü erstellen
 *Sind mehrere Dateien und Ordner ausgewählt, werden alle zusammen in ein Archiv gespeichert.
 */
 	
	$('.zipMT').click(function(){
		
	/*Dafür werden alle ausgewählten Dateien und Ordner als Pfad in ein Array gespeichert*/
	
		var array = new Array;
		$('.icon~.auswahl .ausgew').parent().each(function(i){
			array.push($(this).data('link'));
		});
		
	/*Array wird in eine JSON-Datei umgewandelt, danach wird es per Ajax an zip.php gesendet.*/
	
		array = JSON.stringify(array);
		xhrZIP = new XMLHttpRequest();
		xhrZIP.open("POST", "../php/zip.php", true);
		xhrZIP.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrZIP.send("&array="+array);
		xhrZIP.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				
	/*Das empfangene Icon wird dem Inhalt hinzugefügt. Sollte die Antwort leer sein, wird untenstehende Fehlermeldung ausgegeben*/
	
				$('#inhalt').append(this.responseText);
				if(this.responseText==""){
					alert('Leere Ordner können nicht als Zip verpackt werden');
				}
			}
		}, false);
	});
	
/**
 *Mit Hilfe dieser Funktion kann man als Eigentümer der Gruppe, die Mitglieder der Gruppe ändern.
 *Der Pfad wird aus der Icon ausgelesen und an das Skript gesendet.
 */
 
	$('.hinzufugenMT').click(function(){
		var pfad;
		$('.icon~.auswahl .ausgew').parent().first().each(function(){
			 pfad = $(this).data('link');
		});
		xhrHinz = new XMLHttpRequest();
		xhrHinz.open("POST", "../php/hinzufugen.php", true);
		xhrHinz.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrHinz.send("&pfad="+pfad);
		xhrHinz.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				if(this.responseText=="f"){
					alert('keine Bearbeitungsrechte!');
				}
 	/*Hat der Nutzer Bearbeitungsrechte, wird ein Fenster zur Bearbeitung der Gruppe eingeblendet. Bei der, von dieser Ajax-Anfrage erhaltenden, Antwort handelt es sich um eine Liste aller Nutzer, wo vermerkt ist, wer schon zur Gruppe gehört*/
	
				else{
					$('.gruppenfenster2').css('display', 'block');
					$('.fensterOut').css('display', 'block');
					$('.gruppenfenster2 select').children().remove();
					$('.gruppenfenster2 select').append(this.responseText);
				}
			}
		}, false);
	});
	
/**
 *Die vorgenommenen Änderungen werden mit dem untenstehenden Event-Handler ausgelesen und per Ajax gesendet. Danach wird das Fenster geschlossen und zurückgesendet.
 */
 
	$('.gruppenfenster2').on('submit', '#formGruppe2', function(event){
		event.preventDefault();
		event.stopPropagation();
		$('.icon~.auswahl .ausgew').parent().first().each(function(){
			var dlink = $(this).data('link');
	
	/*Formular wird in Formdata verpackt, da es ein Array enthält.*/
	
			var formData = new FormData(document.formGruppe2);	
			formData.append('pfad', dlink)	
			xhrGSend = new XMLHttpRequest();
			xhrGSend.open("POST", "../php/gruppeAendern.php", true);
			xhrGSend.send(formData);
			xhrGSend.addEventListener('readystatechange', function(event){
				if(this.readyState == 4 && this.status == 200){
					alert('Änderung erfolgt.');//Bestätigung
				}
			}, false);
		});
		$('.gruppenfenster2').css('display', 'none'); //Fenster ausblenden
		$('.fensterOut').css('display', 'none');
		document.formGruppe2.reset(); //...und zurücksetzen.
	});

/**
 *Folgender Code implementiert die Musikwiedergabe. In der momentanigen Version ist diese aber nur rudimentär implementiert.
 */
 
	var audioElement = document.getElementById("audioElement");
	
/*Link zur Audiodatei*/
	
	audioElement.src = "../php/audio.php";
	
/*Wenn auf Play gedrückt wird, wird der Song abgespielt und der Pauseknopf eingeblendet.*/

	$('.play').click(function(){	
		audioElement.play();
		$(this).css({display: 'none'});
		
		$('.pause').css({display: 'block'});
	});	
	
/*Wenn auf Pause gedrückt wird, wird der Song angehalten und der Playknopf eingeblendet.*/

	$('.pause').click(function(){	
		audioElement.pause();
		$('.pause').css({display: 'none'});
		$('.play').css({display: 'block'});
	});
	
/*Zurück: Momentan wird der Song einfach neu gestartet*/
	
	$('.playLeiste').children().first().click(function(){
		audioElement.src = audioElement.src;
		audioElement.load()
		if($('.play').css('display')=="none"){
			audioElement.play();
		}
	});
	
/*Vorwärts: Momentan wird der Song einfach neu gestartet*/

	$('.playLeiste').children().last().click(function(){
		audioElement.src = audioElement.src;
		audioElement.load()
		if($('.play').css('display')=="none"){
			audioElement.play();
		}
	});
	
/*Wenn der Song zu Ende ist, wird wieder der Playknopf eingeblendet.*/

	audioElement.onended = function(){		
		$('.pause').css({display: 'none'});
		$('.play').css({display: 'block'});
	}
	
/**
 *Folgendes JavaScript ist für den Richtext-Editor:
 */
 
	var x = 595; //Standardbreite
	var y= 842; //Standardhöhe
	var orand, urand, lrand, rrand;
	orand=urand=lrand=rrand=70; //Standardseitenrand
	
/**
 *Zoom-Funktion:
 *Der ausgewählte Wert wird ausgewählt und es wird dementsprechend gezoomt (dafür müssen recht viele CSS-Werte neu berechnet und angepasst werden).
 */
 
	var zoomalt=1; //alter Zoomwert
	$('#tool_zoom').change(function(){
		var zoom = $('#tool_zoom').val();
		if(parseFloat(zoom)>0){
			var bereichx= parseInt(x*zoom)+30;
			var bereichy= parseInt(y*zoom)+30;
			$('.blattbereich > div').css('height', bereichy+'pt');
			var einzug = parseInt(bereichy/2);
			$('#blatt').css('top', einzug+'pt');
			$('#blatt').css('-webkit-transform', 'scale('+zoom+')');
			$('#blatt').css('transform', 'scale('+zoom+')');
			$('.blattbereich > div').css('min-width', bereichx+'pt');
			var zoomalt=zoom; 
		}
		
	/*Wenn "benutzerdefiniert" ausgewählt wird, wird ein Fenster zur Werteingabe eingeblendet.*/
	
		else{ 
			$('#tool_zoom').children('.benutzerdefiniert').filter(':selected').remove();
			$('#tool_zoom option[value="'+zoomalt+'"]').prop('selected', true); //alter Zoomwert wird zur Sicherheit zwischenzeitlich ausgewählt
			$('.klein .fensterIn > div').children().remove();
			$('.fenster.klein').css('display', 'block');
			$('.fensterOut').css('display', 'block');
			$('.klein .fensterIn > div').append('<form><label for="zoominput">Zoom-Wert hier eingeben: <br></label> <input type="text" name="zoominput" maxlength="4"> % <input type="submit" value="Ok"></form>');
		}
	});
	
/**
 *Hilfslinien ein- und ausblenden
 */
 
	$('#tool_hlinie').change(function(){
		if($(this).is(':checked')){
			$('#blatt').contents().find('.blatt>div').css('border-width', '1pt');
		}
		else{
			$('#blatt').contents().find('.blatt>div').css('border-width', '0pt');
		}
	});
	
/**
 *Dokumentenformat einstellen:
 *Momentan steht nur A4 und A4 im Hoch und Querformat zur Verfügung.
 *tool_zoom wird verwendet/missbraucht, um Blatt richtig zu positionieren.
 */
 
	$('#tool_format').change(function(){
		var format = $('#tool_format').val();
		if(format=='A4h'){
			x = 595;
			y= 842;
			$('#tool_zoom').trigger('change');
		}else if(format=='A4b'){
			y = 595;
			x= 842;
			$('#tool_zoom').trigger('change');
		}else if(format=='A5h'){
			y = 595;
			x= 421;
			$('#tool_zoom').trigger('change');
		}else if(format=='A5b'){
			x = 595;
			y= 421;
			$('#tool_zoom').trigger('change');
		}
		var xhalf=-x/2;
		var yhalf=-y/2;
	
	/*Grössen werden angepasst*/
	
		$('#blatt').css({width: x+'pt', height: y+'pt', 'margin-left': xhalf+'pt', 'margin-top': yhalf+'pt'});
		$('#blatt').contents().find('.blatt').css({width: x+'pt', height: y+'pt'});
		$('#blatt').contents().find('.blatt .hauptbereich').css({width: (x-lrand-rrand)+'pt', height: (y-urand-orand)+'pt'});
	});
	
/**
 *tool_rand (Masseinheit) und tool_rand_mass (Werte) wird der Seitenrand angepaast.
 */
 
	$('.tool_rand').change(function(){
		$('#tool_rand_mass').trigger('change');
	});
	$('#tool_rand_mass').change(function(){
		orand=$('#tool_rand1').val();
		urand=$('#tool_rand2').val();
		lrand=$('#tool_rand3').val();
		rrand=$('#tool_rand4').val();
		
	/*Umrechnung von mm in pt.*/
	
		if($('#tool_rand_mass').val()=='mm'){
			orand=parseInt(orand/0.35277777777)
			urand=parseInt(urand/0.35277777777)
			lrand=parseInt(lrand/0.35277777777)
			rrand=parseInt(rrand/0.35277777777)
		}
		
	/*Seitenrand anpassen*/
	
		$('#blatt').contents().find('.hauptbereich').css({'margin-top': (orand-1)+'pt', 'margin-bottom': (urand-1)+'pt', 'margin-left': (lrand-1)+'pt', 'margin-right': (rrand-1)+'pt'});
		$('#tool_format').trigger('change');
	});
	
/**
 *Diese Event-Handler ändert die Hintergrundfarbe
 */
 
	$('#tool_background div').click(function(){
		var color = $('#tool_background .selected').attr('title');
		$('#tool_background div').removeClass('selected');
		$(this).addClass('selected');
		if($(this).hasClass('w')){
			$('#blatt').contents().find('.blatt').css('background-color', 'white');
		}
		else if($(this).hasClass('p')){
			$('#blatt').contents().find('.blatt').css('background-color', '#E8E2CE');
		}
		else{ //wenn benutzerdefiniert angewählt worden ist
			$('.fenster.klein').css('display', 'block'); //Fenster öffnen
			$('.fensterOut').css('display', 'block');
			$('.klein .fensterIn > div').append('<form><label for="bginput">Farbe (hexadezimal): <br></label> #<input type="text" name="bginput" maxlength="6" value="'+color+'"> <input type="submit" value="Ok"></form>');//Formular für Farbwahl im geöffneten Fenster anzeigen.
		}
	});	
	
/**
 *Einige Funktionen dürfen erst gebunden werden, wenn auch das iFrame geladen ist.
 */
 
	$('#blatt').load(function(){
		
	/*execCommand-Befehle werden normalerweise an das "document" gebunden, da es sich hier um ein iFrame handelt, muss das "document" des iFrame aufgerufen werden. Je nach Browser gibt es zwei Methoden, dies zu tun. [1]*/
	
		var editorf = document.getElementById ("blatt");
        if (editorf.contentDocument){
            editor = editorf.contentDocument;
		}
        else{
            editor = editorf.contentWindow.document;
		}
		
	/*Voreinstellung für execCoomand: Formatierung über CSS realisieren und nicht über HTML-Tags [2]*/
	
	 	try{
			editor.execCommand("styleWithCSS", false, true);
		}
		catch (e){	
			try{
				editor.execCommand("useCSS", false, true);
			}
			catch(e){}
		}
	
	/*Aktualisiert das Imputfeld für die Schriftgrösse mit dem Wert des Textes der aktuellen Curserposition im Dokument. Da diese in Pixel gemessen wird, rechne ich diese in Points um und setze Sie gerundet in das Imputfeld ein.*/
	
		$('#blatt').contents().find('.blatt').on('mousedown', '*', function(event){
			event.stopPropagation('*'); //Weitergabe des Ereignisses/Events verhindern
			$('#tool_fontsize').val(Math.round(parseInt($(this).css('font-size'))*72 / 96));
		});
		var fontsizeVal = 16; //Standardschriftgrösse
		
	/*Wird die Maus über das Eingabefeld gehalten, wird der aktuelle Wert temporär entfernt, damit direkt ein neuer Wert eingegeben werden kann. Wenn das Feld leer ist, lässt sich durch ein Klick ausserdem eine Liste vordefinierter Werte einblenden (sonst nicht).*/
	
		$('#tool_fontsize').mouseover(function(){
			fontsizeVal = $(this).val();
			$(this).val("")
			$(this).trigger('focus');
		});
		$('#tool_fontsize').mouseout(function(){
			$(this).val(fontsizeVal); //alter oder eingegebener Wert wird eingesetzt
		});
		
	/*Tätigt der Nutzer eine Eingabe, wird der alte Wert mit dem neuem überschrieben*/
		$('#tool_fontsize').on('change keyup input', function(){
			fontsizeVal = $(this).val();
			if(fontsizeVal==""){//bei leerer Eingabe...
				fontsizeVal=16;//...Standardwert
			}
			
	/*Da die Schriftgrösse sich über execCommand nur sehr ungenau bzw. nur relativ einstellen lässt, wird dies nur als Anker benutzt. Im zweiten Schritt wird der Anker durch die richtige Schriftgrösse (per jQuery) ersetzt.*/
	
			if(editor.execCommand("fontSize", false, "7")){
				$('#blatt').contents().find('.hauptbereich').find("[size]").removeAttr('size').css('font-size', fontsizeVal+'pt');
				$('#blatt').contents().find('.hauptbereich').find("[style*='xxx-large']").css('font-size', fontsizeVal+'pt');
			}	
		});
		
	/*Schriftfarbe einstellen. Ähnlich wie das Ändern der Hintergrundfarbe. 
	 *Zunächst wird das angeklickte Farbfeld als ausgewählt gekennzeichnet, bei den Standardfarben wird per execCommand die Farbe auf den markierten Text angewendet.*/
	
		$('#tool_color div').click(function(){
			var color = $('#tool_color .selected').attr('title');	
			$('#tool_color div').removeClass('selected');
			$(this).addClass('selected');
			if($(this).hasClass('b')){
				editor.execCommand("foreColor",false,"#000000");
			}
			else if($(this).hasClass('g')){
				editor.execCommand("foreColor",false,"#606060");
			}
			else if($(this).hasClass('db')){
				editor.execCommand("foreColor",false,"#063099");
			}
			else if($(this).hasClass('r')){
				editor.execCommand("foreColor",false,"#EE0000");
			}
			else{
				
	/*Wird das Benutzerdefinierte ausgewählt, wird ein Eingabefenster geöffnet.*/
	
				$('.fenster.klein').css('display', 'block');
				$('.fensterOut').css('display', 'block');
				$('.klein .fensterIn > div').append('<form><label for="colorinput">Farbe (hexadezimal): <br></label> #<input type="text" name="colorinput" maxlength="6" value="'+color+'"> <input type="submit" value="Ok"></form>');
			}
		});
		
	/*Standard execCommand befehle: fett, kursiv, unterstrichen und durchgestrichen*/
	
		$('.tool_font_b').click(function(){
			editor.execCommand ('bold', false, null);
		});
		$('.tool_font_i').click(function(){
			editor.execCommand ('italic', false, null);
		});
		$('.tool_font_u').click(function(){
			editor.execCommand ('underline', false, null);
		});
		$('.tool_font_s').click(function(){
			editor.execCommand ('strikethrough', false, null);
		});	
	});
	
/**
 *Versteckte Funktion: Hält man die Maus über den Social-Button, wird geprüft, ob eine neue Nachricht gekommen ist, wenn ja wird rechts am Button angezeigt, um vieviele es sich handelt.
 */
 
	$('#header > nav > a').mouseover(function(){
		xhrUpdate = new XMLHttpRequest();
		xhrUpdate.open("POST", "../php/emailUpdate.php", true);
		xhrUpdate.send(null);
		xhrUpdate.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				$("#header > nav > a > div").remove();
				$("#header > nav > a:contains('Social')").append(this.responseText);
			}
		});		
	});
	
/**
 *Button im unteren Menü für den Social-Bereich: mit ihm kann man den Posteingang aktualisieren.
 *Drehen per CSS realisiert: Um Animation zu starten, wird der Button erneut eingefügt,
 */
 	
	$('#mainMB').on('click', '.update', function(){
		var x = $(this).parent();
		$(this).remove();
		x.append('<li class="button update load">&#xe647;</li>');
		x.find('.update').css('display', 'inline-block');
		xhrMail = new XMLHttpRequest();
		xhrMail.open("POST", "../php/mail.php", true);
		xhrMail.setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
		xhrMail.send();
		xhrMail.addEventListener('readystatechange', function(event){
			if(this.readyState == 4 && this.status == 200){
				$('#inhalt2').children().remove();
				$('#inhalt2').append(this.responseText);
				psdInhHi2();
			}
		}, false);
	});
	
/**
 *Beim Absenden des Formulares für das Erstellen von Ordnern, wird die Funktion erstellOrdner() aufgerufen.
 */
 
	$('#main').on('submit', '#dirNameForm', function(event){
		$('#main').off('blur', '#neuOrdner');//mehrfaches Auslösen des Events verhindern
		event.preventDefault();//Standardverhalten von Submit unterbinden
		event.stopPropagation();// ""
		erstellOrdner();
	});
	
/**
 *Wird das Imputfeld für den Ordnernamen verlassen (blur), wird das Formular auch abgeschickt.
 */
 
	$('#main').on('blur', '#neuOrdner', function(){
		$('#dirNameForm').submit();
	});
	
/**
 *Wird der Ordner über die Seitenleiste erstellt, können die folgenden zwei Events ausgelöst werden (äquivalent zu den obigen Event-Handlern)
 */
 
	$('#aside').on('submit', '#dirNameFormAs', function(event){
		$('#aside').off('blur', '#dirNameFormAs input'); //mehrfaches Auslösen des Events verhindern
		event.preventDefault();
		event.stopPropagation();
		erstellOrdnerAside();
	});
	$('#aside').on('blur', '#dirNameFormAs input', function(){	
		$('#dirNameFormAs').submit();
	});
	
/**
 *Submit-Event für Eingabefeld zum Umbenennen eines Ordners
 */
 
	$('#main').on('submit', '#umbenennenForm', function(event){
		$('#main').off('blur', '#umbenennen');//mehrfaches Auslösen des Events verhindern
		event.preventDefault();
		event.stopPropagation();
		umbenennenSend();
	});
	
/**
 *Auch hier wird das Absenden bei einem Blur-Event ausgelöst.
 */
 
	$('#main').on('blur', '#umbenennen', function(){
		$('#umbenennenForm').submit();
	});
	
/**
 *Event-Handler für den Button zum Erstellen von Gruppen
 */
 
	$('#mainMB').on('click', '#erstFormOrdner', erstellGruppeForm);
	
/**
 *Evnet-Handler für den Umbenennen-Button
 */
 
	$('.umbenennenMT').click(umbenennen);
	
/**
 *Suchstring abschicken -> sucheStart() aufrufen.
 */
	
	$('.button.suche').on('submit', '#sucheForm', function(event){
		event.preventDefault();
		event.stopPropagation();
		sucheStart(0);
	});
	
/**
 *Im Texteditor wird hinter den Dateiname automatisch die richtige Endung gesetzt.
 */
 
	$('#textname').blur(function(){
		var textname=$('#textname').val()+' ';
		textname = textname.slice(0,textname.indexOf('.'))
		if(textname.length==0){
			textname='text';
		}
		$('#textname').val(textname+'.txt');
	});
	
/**
 *Beim Speichern des Textes wird die Speicherfunktion (textSpeichern()) aufgerufen und das Fenster geschlossen.
 */
 
	$('.textfenster form').submit(function(event){
		event.preventDefault();
		event.stopPropagation();
		textSpeichern();
		$('.textfenster').css('display', 'none');
		$('.fensterOut').css('display', 'none');
		document.formText.reset();
	});
	
/**
 *Event zum Schliessenbutton eines Fenster. Es werden dabei standardmässig einige Formulare zurückgesetzt.
 */
 
	$('.fenster .close').click(function(){
		$('.fenster').css('display', 'none');
		$('.fensterOut').css('display', 'none');
		$('.klein .fensterIn > div').children().remove();
		document.formGruppe.reset();
		document.formGruppe2.reset();
	});
	
/**
 *transparente Box, die den Inhalt der Seite, bei einem geöffnetem Fenster, überdeckt:
 *Per Doppelklick neben das Fenster, also auf fensterOut, kann man das Fenster schliessen
 */
 
	$('div.fensterOut').dblclick(function(){
		$('.fenster').css('display', 'none');
		$('.fensterOut').css('display', 'none');
		$('.klein .fensterIn > div').children().remove();
		document.formGruppe.reset();
		document.formGruppe2.reset();
	});
	
/**
 *Fenster zum Gruppen-Erstellen abschicken, bzw. Funktion dazu aufrufen.
 */
 
	$('.gruppenfenster').on('submit', '#formGruppe', function(event){
		event.preventDefault();
		event.stopPropagation();
		sendGruppeForm();
		$('.gruppenfenster').css('display', 'none');
		$('.fensterOut').css('display', 'none');
		document.formGruppe.reset();
	});
	
/**
 *Nachicht abschicken, bzw. Funktion dazu aufrufen.
 */
 
	$('.fenster.mail').on('submit', '#mailForm', function(event){
		event.preventDefault();
		event.stopPropagation();
		sendMail();
		$('.fenster.mail').css('display', 'none');
		$('.fensterOut').css('display', 'none');
	});
	
/**
 *Das kleine Fenster kann mehrere Funktionalitäten erfüllen, daher muss zunächst überprüft werden, welches Formular sich im Fenster momentan befindet.
 */
 
	$('.fenster.klein').on('submit', 'form', function(event){
		event.preventDefault();
		event.stopPropagation();
	
	/*->Benutzerdefinierter Zoom*/
	
		if($('.fenster.klein input[name="zoominput"]').length){
			var value = $('.fenster.klein input[name="zoominput"]').val();
			value = Math.abs(value / 100);
			$('#tool_zoom option').removeClass("selected");
			$('#tool_zoom').append('<option value="'+value+'" selected="selected" class="benutzerdefiniert"># '+value*100+'%</option>');
			$('.fenster.klein').css('display', 'none');
			$('.fensterOut').css('display', 'none');
			
	/*Nach dem Eintragen des neuen Wertes in die Select-Liste, wird die dazugehörige Funktion noch einmal getriggert.*/
	
			$('#tool_zoom').trigger('change');
		}
		
	/*->Benutzerdefinierte Hintergrundfarbe*/
	
		else if($('.fenster.klein input[name="bginput"]').length){
			var value = $('.fenster.klein input[name="bginput"]').val();
			
	/*Aus der Eingabe werden alle nicht hexadeximalen Werte gelöscht*/
	
			var re = /[0-9a-fA-F]*/g;
			valuem = value.match(re);
			
	/*Eingabe wird mit Nullen aufgefüllt, bis man eine Hexadezimaler RGB-Wert erhält. (also 3 oder 6 Zeichen)*/
	 
			var add="";
			if(valuem.join("").length<4){
				for(var i=0; i<(3-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			else{
				for(var i=0; i<(6-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			if(valuem.join("").length!=value.length){
				
	/*Dem Nutzer wird bei Korrektur kurz mitgeteilt, dass eine Korrektur stattfand.*/
	
				alert('Die Eingabe wurde korrigiert: '+'#'+valuem.join("")+add);
			}
			
	/*Farbe wird angewendet und das Fenster geschlossen. Das Formular wird entfernt.*/
	
			$('#blatt').contents().find('.blatt').css('background-color', '#'+valuem.join("")+add);			
			$('.fenster').css('display', 'none');
			$('.fensterOut').css('display', 'none');
			$('.klein .fensterIn > div').children().remove();
			$('#tool_background .selected').attr('title', valuem.join("")+add)
		}
		
	/*->Benutzerdefinierte Schriftfarbe*/
	
		else if($('.fenster.klein input[name="colorinput"]').length){
			var value = $('.fenster.klein input[name="colorinput"]').val();
			
	/*Wie bei benutzerdefinierter Hintergrundfarbe*/
	
			var re = /[0-9a-fA-F]*/g;
			valuem = value.match(re); 
			var add="";
			if(valuem.join("").length<4){
				for(var i=0; i<(3-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			else{
				for(var i=0; i<(6-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			if(valuem.join("").length!=value.length){
				alert('Die Eingabe wurde korrigiert: '+'#'+valuem.join("")+add);
			}	
			
	/*per execCommand wird Farbe auf markierten Text angewendet.*/
			
			editor.execCommand("foreColor",false,'#'+valuem.join("")+add);
			$('.fenster').css('display', 'none');
			$('.fensterOut').css('display', 'none');
			$('.klein .fensterIn > div').children().remove();
			$('#tool_color .selected').attr('title', valuem.join("")+add)
		}
	});
	
/**
 *Live-Vorschau für Hintergrundfarbe
 *Ähnlich aufgebaut wie die Endauswertung (->innerhalb des oberen Eventhandlers zu finden)*/
	
	$('.fenster.klein').on('keyup', 'input[name="bginput"]', function(){
		var value = $('.fenster.klein input[name="bginput"]').val();
			var re = /[0-9a-fA-F]*/g;
			valuem = value.match(re);
			var add="";
			if(valuem.join("").length<4){
				for(var i=0; i<(3-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			else{
				for(var i=0; i<(6-valuem.join("").length); i++){
					add= add+'0';
				}
			}
			
	/*Bei falscher Eingabe wird das Inputfeld rot markiert. (Im Hintergrund wird der Wert aber korrigiert und trotzdem eine Live-Vorschau generiert)*/
	
			if(valuem.join("").length!=value.length){
				$('.fenster.klein input[name="bginput"]').css('color', 'red');
			}
			else
			{
				$('.fenster.klein input[name="bginput"]').css('color', 'black');
			}
		
	/*generierte Farbe anwenden -> Der Anwender sieht die Vorschau.*/
	
			$('#blatt').contents().find('.blatt').css('background-color', '#'+valuem.join("")+add);
	});	
	
/**
 *Beim Klick auf das Schliessen-X (Kreuzlein) wird zunächst nachgefragt, ob das Fenster wirklich geschlossen werden soll.
 *Jedoch ist momentan das versehentliche Schliessen nicht tragisch, da im Hintergrund das Dokument geöffnet bleibt.
 */
 
	$('.programmfenster .tools > div').click(function(){
		if(confirm('Wollen Sie den Editor wirklich schliessen? Nicht gespeicherte Änderungen gehen verloren!')){
			$('.programmfenster').css('display', 'none');
			$('#speicherName').val("");
			$('#aside nav').css('display', 'block');			
			$('#asideTools').css('display', 'block');
		}
	});
	
/**
 *Bei Klick auf Löschen-Button in der Seitenleiste:
 */
	$('#asideTools .button').click(function(){
		alert('Funktion noch nicht verfügbar');
	});
	
	alert('Es handelt sich um eine Beta-Version! Die Eigentümer und Entwickler dieser Webanwendung haften nicht für Datenverlust und andere Fehlfunktionen. \r\nVergessen Sie nicht, sich am Ende abzumelden!\r\nViel Spass!'); //Soll den Nutzer vor dieser Seite warnen. ;-)
});
	
//[1] http://help.dottoro.com/ljcvtcaw.php	übernommen (letzer Aufruf 21.1.2014)		
	
//[2] http://stackoverflow.com/questions/536132/stylewithcss-for-ie (letzer Aufruf 21.1.2014)
		