// JavaScript Document
/**
 * diverse globale Variablen und Arrays, die jeweils gewisse Zustände speichern, die von mehreren Funktionen gebraucht und/oder verändert werden
 */
 
	var zahl=0;
	UPgeladen = new Array; //geladenes Dateivolumen
	UPtotal= new Array;//ganzes Dateivolumen
	UPgeladen[0] = 0.001;
	UPtotal[0] = 0.001;	
	xhr = new Array;
	var icon = 0;
	var confirmCloseWin = false;
	
/**
 *Diese Funktion addiert alle Werte innerhalb eines Arrays und gibt die Summe zurück.
 */
 
	function arrayAdd(y){
		var x = 0;
		for (var i = 0; i < y.length; i++){
			x = x + y[i];
		}
		return x;
	}
	
/**
 *Diese Funktion managt den File-Upload
 */
 
	function handleUpload(){
		
	/*Solange confirmCloseWin true ist, bedeutet das, dass ein Upload stattfindet und daher die Seite nicht verlassen werden sollte.*/
 
		confirmCloseWin = true;
		
	/*Läuft momentan kein Upload (zu ladene Bytes und geladene Bytes sind gleich gross), dann muss zunächst die Statusanzeige für den Upload eingeblendet werden.*/
 
		if(arrayAdd(UPtotal) == arrayAdd(UPgeladen)){
			$('.status').css('right', '-164px');
		}
		
	/*Für jeden Upload wird eine Zahl vergeben, damit die verschiedenen Uploads unterschieden werden können*/
 
		var zahlfix=zahl;
		zahl++;	
		
	/*Files aus Upload-Formular auslesen und aktuellen Ordnerpfad einlesen und FormDate (Container/Objekt zum übertragen diverser Datentypen per Ajax) hinzufügen*/
	
		var fileInput = document.getElementById('upfile');
		var upOrdner = $('#upOrdner').val();
		var data = new FormData();		
		data.append('ajax', true);
		data.append('upOrdner', upOrdner);		
		for (var i = 0; i < fileInput.files.length; ++i){
			data.append('upfile[]', fileInput.files[i]);//Files Array in FormData hinzufügen
			
	/*Hier werden die einzelnen Datein zur Liste in der Statusbar hinzugefügt. Zunächst wird die Dateigrösse in ein leserlichen Einheit umgeformt. Danach werden die Informationen in HTML-Markup gepackt*/
	 
			var sizeFile;
			if (fileInput.files[i].size > 1000000){
				sizeFile = (Math.round(fileInput.files[i].size/100000)/10) + "MB";
			}
			else{
				sizeFile = (Math.round(fileInput.files[i].size/100)/10) + "KB";
			}
			$('.status > div:last-child').prepend(
			'<div class="d'+i+'"><div>am Laden:</div> <div>'+fileInput.files[i].name+ //'d+1: zum Unterscheiden der Files innerhalb eines Uploads
			'</div><div>Size: '+sizeFile+'; Type: '+
			fileInput.files[i].type+'</div><div class="UpNum'+zahlfix+'"></div></div>');//'UpNum'+Zahlfix: zum Unterscheiden der Uploads nötig
		}
	
	/*Ajax: neuer Request (für Upload)*/
	
		var request = new XMLHttpRequest();
		
	/*Event-Handler für Uploadprogress*/
	
		request.upload.addEventListener('progress', function(event){
			if(event.lengthComputable){//falls verfügbar...
			
	/*Die Gesammtgrösse und die zum Zeitpunkt des Uploads geladenen Bytes werden in die jeweiligen Arrays gespeichert. Danach wird ausgerechnet, wieviel Prozent bereits hochgeladen wurden. Mit diesen Werten wird der Eintrag in der Statusbar aktualisiert*/
	
				UPgeladen[zahlfix]=event.loaded;
				UPtotal[zahlfix]=event.total;							
				var percent = arrayAdd(UPgeladen) / arrayAdd(UPtotal); 
				var percentP = (100 * event.loaded / event.total );
				var percentPcent = percentP + "%";				
				$('.status > div:first-child > div:first-child').empty();
				$('.status > div:first-child > div:first-child').append('<div>&#xe012;</div> '+(Math.round(percent * 1000)/10) + '%');
				var hohe = (percent*120);
				$('.status > div:first-child > div:last-child').css('height', hohe);
				$('.status > div:last-child > div > .UpNum'+zahlfix).css('width', percentPcent);
				$('.status > div:last-child > div > .UpNum'+zahlfix).empty();
				$('.status > div:last-child > div > .UpNum'+zahlfix).append(Math.round(percentP * 1000)/1000 + '%');
			}
			
	/*Während eines Uploads blinkt das Favicon im Tab orange-blau. So bekommt der Nutzer auch ein Feedback, wenn er in anderen Tabs arbeitet.
	 *Die Farbe ändert zweimal pro drei Progress-Events.*/
	
			if(icon<1){
				$('.favicon').replaceWith('<link class="favicon" rel="icon" href="../icon1.ico" type="image/png" />');
			}
			else{
				$('.favicon').replaceWith('<link class="favicon" rel="icon" href="../icon.ico" type="image/png" />');
			}
			icon++;
			if(icon == 2){
				icon=0;
			}
		}, false);	
		
	/*Event-Handler, wenn die Dateien hochgeladen sind.*/
	
		request.upload.addEventListener('load', function(event){
				UPgeladen[zahlfix] = UPtotal[zahlfix];
				
	/*Wenn es der letzte Upload gewesen ist, also die Anzahl geladene Bytes mit der Gesammtzahl Bytes übereinstimmt, verschwindert die Statusbar wieder und die beiden Arrays werden zurückgesetz (nicht auf 0, da es sonst zu einer Division durch 0 kommen kann).*/
		
				if(arrayAdd(UPtotal) == arrayAdd(UPgeladen)){
					$('.status').css('right', '-200px');
					for (var i = 0; i < UPgeladen.length; ++i){
						UPgeladen[i] = 0.001;
						UPtotal[i] = 0.001;	
					}
					confirmCloseWin = false;//Upload beendet
				}
				$('.favicon').replaceWith('<link class="favicon" rel="icon" href="../icon.ico" type="image/png" />');//Standard-Favicon
		}, false);
		
	/*Event-Handler für ein Fehler-Event*/
	
		request.upload.addEventListener('error', function(event){
			alert('Upload fehlgeschlagen! Versuchen Sie es noch einmal.'); //Warnmeldung
			UPgeladen[zahlfix] = 0.01; //Werte in Array für diesen Upload zurücksetzen
			UPtotal[zahlfix] = 0.01;	
				
	/*Wenn es der letzte Upload gewesen ist, also die Anzahl geladene Bytes mit der Gesammtzahl Bytes übereinstimmt, verschwindert die Statusbar wieder.*/
		
			if(arrayAdd(UPtotal) == arrayAdd(UPgeladen)){
				$('.status').css('right', '-200px');
			}
			confirmCloseWin = false;//Upload beendet
			$('.favicon').replaceWith('<link class="favicon" rel="icon" href="../icon.ico" type="image/png" />');//Standard-Favicon
		}, false);
		
	/*Event-Handler für Statusänderung*/
	
		request.addEventListener('readystatechange', function(event){
			if(this.readyState == 4){//Status: Antwort gesendet (=4)
			
	/*Ist alles OK, wird die Antwort ausgelesen und ausgewertet.*/
	
				if(this.status == 200){//Wenn alles OK...
					var links = $('.status > div:last-child');
					if(this.response != 0){
						var uploaded = eval(this.response);//Antwort auslesen
					}
					if(arrayAdd(UPtotal) == arrayAdd(UPgeladen)){//wenn kein anderer Upload mehr
					   $('.status').css('right', '-200px');//Statusbar ausblenden
						confirmCloseWin = false;
					}
					var div, a;
					
	/*Das Array mit den Antworten wird abgearbeitet: Die Antworten werden jeweils auf Fehlermeldungen überprüft.
	 *Gibt es keinen Fehler, wird mit dem übergebenen Pfad ein Icon angefordert.*/
	
					for(var i = 0; i < uploaded.length; i++){
						if(uploaded[i].slice(0,10)!='*#fehler-+' && uploaded[i].slice(-9)!='qrs.dd*db'){//Auf Fehler überprüfen
							var dName = uploaded[i].slice(uploaded[i].lastIndexOf('/')+1); //Dateiname aus Antwort extrahieren
							$('.status > div:last-child > div.d'+i+' > .UpNum'+zahlfix).parent().replaceWith('<div class="UpFertig">Fertig: '+ dName +'</div>'); //Status anpassen
							if(document.getElementById('upOrdner').value==upOrdner){//Falls im richtigen Ordner
								xhr[i] = new XMLHttpRequest();//Icon anfordern
								xhr[i].open("POST", "../php/icon.php", true);
								xhr[i].setRequestHeader("Content-Type", "application/x-WWW-form-urlencoded");
								xhr[i].send("&dName="+dName+"&upOrdner="+upOrdner);
								xhr[i].addEventListener('readystatechange', function(event){
									if(this.readyState == 4 && this.status == 200){
										if((document.getElementById('upOrdner').value)==upOrdner){
											$('#inhalt').append(this.responseText); //Antwort (Icon) einfügen
											psdInhHi();
										}
									}
								});
							}
							
						}
						else{//bei Fehler
							var ende = uploaded[i].slice(11)
							ende = ende.slice(0,-9);
							if(uploaded[i].slice(0,11)=='*#fehler-+0'){
								alert('Die Datei "'+ende+'" konnte nicht gespeichert werden. Möglicherweise ist die Datei zu gross oder hat einen ungültigen Dateinamen oder Dateityp!');
							}
							if(uploaded[i].slice(0,11)=='*#fehler-+1'){
								alert('"'+ende+'" ist eine leere Datei! Leere Dateien werden nicht hochgeladen. (Eventuell ist die Datei auch zu gross.)');
							}
							if(uploaded[i].slice(0,11)=='*#fehler-+2'){
								alert('"'+ende+'" konnte nicht gespeichert werden, da nicht genügend Speicherplatz zur Verfügung steht.');
							}
							$('.status > div:last-child > div.d'+i+' > .UpNum'+zahlfix).parent().replaceWith('<div class="UpFehler">Fehler: '+ende +'</div>');//Status anpassen
						}
					}
				}
				else{//Irgend ein Fehler (nicht im Skript)
					alert('Upload fehlgeschlagen! Versuchen Sie es noch einmal');
					for (var i = 0; i < fileInput.files.length; ++i){
						$('.status > div:last-child > div > .UpNum'+zahlfix).parent().replaceWith('<div class="UpFehler">Fehler!</div>'); //Status anpassen
						confirmCloseWin = false;
					}
				}
			}
		}, false);	
		
	/**
	 *Hier weden die Parameter für den Request eingestellt und dann der Request abgesendet.
	 */
	 	
		request.open('POST', '../php/upload.php', true);
		request.setRequestHeader('Cache-Control', 'no-cache')
		request.send(data);
	}
		
	$(document).ready(function(){//Wenn das Dokument geladen ist
	
/**
 *Ist Formdate verfügbar, wird der Upload über Ajax durchgeführt, ansonsten wird das Formular normal abgesendet.
 */
	
		$('#upfile').change(function(){
			if(typeof FormData != 'undefinded'){
				handleUpload();
			}else{
				$('#upload').submit();
			}			
		});
		
/**
 *Statusbar aus- und einklappen
 */
	 
		$('.status').hover(function(){//einklappen
			$(this).css('right', '-4px');
		}, function(){//zuklappen
			if(arrayAdd(UPtotal) == arrayAdd(UPgeladen)){//Wenn der Upload fertig ist,...
					$(this).css('right', '-200px');//...Statusbar ausblenden.
			}
			else{
				$(this).css('right', '-164px');
			}
		});	
			
	/*beim Fenster schliessen während Upload*/

		window.onbeforeunload = function(e) {
			if(confirmCloseWin){
				return 'Wenn Sie die Seite verlassen, wird der Upload frühzeitig beendet';
			}
		};	
	});
	
	//
		