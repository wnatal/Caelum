<?php
/**
 *ANMELDESEITE
 *
 *Das ist die Seite, auf der sich der Besucher anmelden kann.
 */
 
/**
 *Mit dem untenstehenden Ausdruck kontrolliere ich, ob der Nutzer angemeldet ist: Beim Anmelden wird die "idnummer" in der Sesson gespeichert. Die Session verhält sich ähnlich wie Cookies, jedoch werden die Daten selbst nicht Lokal, sondern auf dem Server gespeichert. Damit der Server verschiedene Nutzer unterscheiden kann wird eine eindeutige sogenannte SESSION-ID angelegt. Durch das Speichern der Werte auf dem Server, sind die Werte nur schwer manipulierbar. Mit so einem Wert, hier die "idnummer", kann ich prüfen ob ein Nutzer angemeldet ist, da sonst die idnummer nicht angelegt wäre. Natürlich wird die ID beim Abmelden wieder gelöscht. Ein potenzielles Sicherheitsproblem, dass sich so nicht lösen läst, ist, dass ich den Server mit anderen Teilen muss und diese theoretisch auf die Session zugreifen können.
 *Ist der Nutzer schon angemeldet, wird automatisch auf die Hauptseite umgeleitet...
 */
 	
	session_start();	
	if (isset($_SESSION["idnummer"])){					
		header("Location: Seiten/index.php");
		exit();
	}
	else{
		
/**
 *Hier wird die die Seite bestimmt, auf die nach dem Anmelden standardmässig umgeleitet wird.	
 */	
 
		$tempVarS = 'index.php';
			
/**
 *Wurde von einer anderen Seite als der Hauptseite auf die Anmeldeseite umgeleitet, wäre das in der Session vermerkt und der Nutzer würde nach dem anmelden auf diese Seite umgeleitet, dazu wird hier der wert zunächst aus der SESSION gelesen. (Ist aber momentan unnötig, da es nur eine Seite gibt, auf die der Nutzer umgeleitet wird)
 */
 
		if(isset($_SESSION["site"])){
			$tempVarS = $_SESSION["site"];
		}
		
/**
 *Beim Anmelden, wird der User in die Session gespeichert, damit dieser bei erfolgloser Anmeldung automatisch erneut in das Formularfeld eingetragen werden kann, um so dem Nutzer Schreibarbeit zu ersparen, auch dieser Wert wird hier zunächst ausgelesen.
 */	
 
		if(isset($_SESSION["user"])){
			$tempVarU = $_SESSION["user"];
		}
		
/**
 *Um die Sicherheit zu erhöhen, wird die Session aufgelöst und wieder aufgenommen, so wird der Session eine neue ID zugeordnet und allenfalls unnötige Einträge gelöscht, auch kann so theoretisch sichergestellt werden, dass die neu gestartete Session immer über eine gesicherte Verbindung läuft.
 */
 
		session_destroy();
		session_start();
		$_SESSION["site"] = $tempVarS;
	}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
		<title>Caelum</title>
		<link rel="apple-touch-icon" href="start-icon.png">
		<link rel="shortcut icon" href="icon.ico" type="image/png" />
		<link rel="icon" href="icon.ico" type="image/png" />
		<link rel="stylesheet" href="CSS/struktur.css">
		<link rel="stylesheet" href="CSS/anmelden.css">
		<script src="JavaScript/jquery-2.0.3.min.js"></script>
		<script>
/**
 *Da es nur wenig Coda ist, habe ich ihn direkt in die Seite geschrieben, so sind auch weniger HTTP-Anfragen nötig.
 */
 
/*
 *Wird das Formular zum Anmelden abgesendet, wird diese Funktion aufgerufen, die überprüft, ob alle Felder ausgefüllt, je nachdem wird eine error-Zahl gebildet.
 */

			var error = 0;
			function fehlerPruef(){
				error = 0;
				if(document.formular.user.value == ""){
					error +=1;
				}
				if(document.formular.password.value == ""){
					error +=2;
				} 
		
/*Ist die Fehlerzahl ungleich Null, wird eine Funktion aufgerufen, die eine Fehlermeldung generiert und das Absenden des Formulars wird unterbrochen.*/

				if(error !=0){
					warnungFP();
					return false;
				}
			}
	
/* 
 *Diese Funktion gibt die zur Fehlerzahl passende Meldung aus.
 */
 
			function warnungFP(){
	  			if($('#fehlerInfo')){//alte Fehlermeldungen werden hier gelöscht.
					$('#fehlerInfo').remove();	
	  			}
		
/*aus Konfortgründen wird direkt das Userfeld ausgewählt, damit der Nutzer direkt die Eingabe tätigen kann.*/

	  			document.formular.user.focus();
	  			if(error!=0){
					$('#stabilisator').prepend('<div id="fehlerInfo"></div>');
	  			}
	  			if(error ==1){
					$('#fehlerInfo').text('Bitte geben Sie ihren Nutzernamen ein!');
	  			}
	  			if(error ==2){	
					$('#fehlerInfo').text('Bitte geben Sie das Kennwort ein!');
			
/*fehlt nur noch das Passwort, wird natürlich das Passwortfeld fokusiert.*/

					document.formular.password.focus();
	  			}
	  			if(error ==3){		
					$('#fehlerInfo').text('Bitte geben Sie sowohl ihren Nutzernamen als auch das Kennwort ein!');
	  			}
	  			if(error ==4){		
					$('#fehlerInfo').text('das Login ist falsch! Bitte überprüfen Sie den Nutzernamen und das Kennwort!');
	  			}
			}
	
/*Ist das Login falsch, wird nach dem neuladen der Seite hier der Fehler vermerkt, damit dieser dann von der Funktion warnungFP() ausgewertet werden kann.*/
 
<?php
  			if(isset($tempVarU)){
				echo 'error = 4;';
			}
 ?>
	
/*Beim Laden der Seite wird die Funktion zur Fehleranzeige auch aufgerufen, um festzustellen, ob der Seitenaufruf auf ein fehlgeschlagenen Anmeldeversuch folgt.*/

			$(document).ready(function(){
				warnungFP();
			});
		</script>
	</head>
	<body>
    
<!-- Der Stabilisator sorgt dafür, dass die Seite den ganzen Browser ausfüllt und nur in ausnahmefällen ein Scrollbalken angezeigt wird. Theoretisch ist der Stabilisator wahrscheinlich nicht nötig, aber es macht die Arbeit deutlich leichter. -->

		<div id="stabilisator">
  			<header id="header"><span>Caelum &copy;</span></header>
  
  			<div class="anmeld 
<?php			
/**
 *Diese Klasse wird nur bei Erstaufruf der Seite eingetragen, so wird die Animation des Fensters nur einmal ausgeführt.
 */

		  		if(!isset($tempVarU)){
					echo " anmeldfirst";
		  		}
?>
            ">
    			<form action="php/weiterleiten.php" method="post" onSubmit="return fehlerPruef()" name="formular">
      				<label for="userAB">User</label>
      				<input type="text" id="userAB" name="user" 
<?php					
/**
 *Hier wird der zuletzt eingegebene Username bei fehlgeschlagener Anmeldung automatisch eins Formulafeld eingetragen
 */
 
		  				if(isset($tempVarU)){
							echo 'value="';
							echo $tempVarU;
							echo '"';
		  				}
?>
      				>
      				<label for="passowrtAB">Passwort</label>
      				<input type="password" id="passwortAB" name="password">
      				<input type="submit" value="Anmelden" id="anmeldAB">
    			</form>
  			</div>
  			<noscript>
  				<article id="anmeld">
    				<h1>kein JavaScript gefunden!</h1>
    				<p>Bitte aktivieren Sie in Ihrem Browser JavaScript oder verwenden Sie einen Browser der JavaScript unterstützt.</p>
  				</article>
  			</noscript> 
<?php
/**
 *Hier wird eine SVG-Grafik direkt in die Seite geschrieben, d.h. das Bild wird nicht über eine HTTP-Anfrage, also über den Browser, aufgerufen, sondern es wird direkt mit der Seite ausgeliefert, So kann ich den direkten Zugriff auf das Verzeichnis per CHMOD oder .htaccess (verwende ich) verbieten. Das Skript öffnet zunächst die Datei, danach wird Zeile für Zeile in das Dokument geschrieben. 
 *Das Skript habe ich mehr oder weniger aus dem Buch "Einstieg in PHP 5.5 und MYSQL 5.6" (Thomas Theis, 2013, S.330-331)
 */
  
				$i = false;
				$svg = fopen("Bilder/Logo.svg", "r");	
   				while (!feof($svg)){
					$x = fgets($svg);
					$begriff = "<svg";
					if (strstr($x, $begriff)!=false){
						$i = true;
					}
					if ($i){
						echo $x;
					}		
				}
?>  
		</div>
	</body>
</html>

