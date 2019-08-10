<?php
/**
 *ANMELDUNG UND WEITERLEITUNG DES NUTZERS
 *
 *Dieses Skript ist für die Anmeldung und die darauffolgende Weiterleitung verantwortlich.
 *
 *Zunächst wird geprüft ob die benötigten Werte vorhanden sind und allenfalls direkt auf die Anmeldeseite umgeleitet. Der Username wird in die Session gespeichert, damit er beim fehlgeschlagenen Anmelden, direkt in das Formularfeld für den User voreingetragen wird.
 *Ausserdem wird eine neue Session-ID generiert, da dies, wegen der komplexen Abläufen während der Benutzung, die letzte Gelegenheit ist, dies mit geringen Aufwand zu tun.
 */
	session_start();	
	session_regenerate_id();
	if(isset($_POST["user"])){
	 $_SESSION["user"] = htmlentities($_POST["user"], ENT_QUOTES);
	 if(isset($_POST["password"])){
		 
/**
 *Nach der Vorprüfung werden die Variablen mit der Datenbank verglichen, gesucht wird nach der Kombination von Passwort und Username, wobei das Passwort mit einem Salt zusammen per md5 gehasht werden muss. Der Hash findet sich in der Datenbank, muss also über MySQL geholt werden. 
 */	
 
		$con = mysqli_connect("kssb.ch","natal","natal00120");
		mysqli_select_db($con, "kssb_ch_matura");
		$password = mysqli_real_escape_string($con, $_POST["password"]);//Maskieren für MySQL-Query
		$user = mysqli_real_escape_string($con, $_POST["user"]);//Maskieren für MySQL-Query
		$sql = "SELECT salt FROM nutzerdaten WHERE user LIKE BINARY '". $user."'"; //Salt aus Datenbank holen
		$res = mysqli_query($con, $sql);
		$num = mysqli_num_rows($res);
		if ($num!=0){
			$dsatz = mysqli_fetch_assoc($res);
			$salt = $dsatz["salt"]; //Salt
		}
		$sql = "SELECT idnummer FROM nutzerdaten WHERE passwort
 				LIKE '".md5($password.$salt)."' AND user LIKE BINARY '". $user."'"; //nach Passwort-User-Kombination suchen
		$res = mysqli_query($con, $sql);
		$num = mysqli_num_rows($res);
		
/**
 *Ist das Anmelden erfolgreich, wird die Idnummer, die eindeutig einem Nutzer zuordbar ist, in die Session gespeichert. Danach wird auf die Hauptseite oder theoretisch auf eine andere Seite umgeleitet, die dann in der $_SESSION['site']-Variable gespeichert wäre. Warum theoretisch? Es gibt schlichtweg keine andere Seite, die anstatt der Hauptseite angesprochen werden sollte.
 */
 	
		if ($num!=0){
			$dsatz = mysqli_fetch_assoc($res);
			$_SESSION["idnummer"] = $dsatz["idnummer"];
			if(!isset($_SESSION["site"])){
				$_SESSION["site"] = "index.php";
			}
			header("Location: ../Seiten/".$_SESSION["site"]);	
			mysqli_close($con);			
			exit();
		}	
	 	mysqli_close($con);
	 }
	}
	
/**
 *War die Anmeldung nicht erfolgreich wird auf die Anmeldeseite zurückgeleitet.
 */
 
	header("Location: ../index.php");	
	exit();	
	
/**
 *Das Grundgerüst, wie auch das allgemeine Grundverständnis für Sessions stammt aus dem Buch "Einstieg PHP 5.5 und MySQL 5.6" [1].
 */
 
/*[1]*
 *Titel :
 	*Einstieg PHP 5.5 und MySQL 5.6
 *Autor:
	*9783836224895
 *Seiten:
 	*Kapitel 12
 	*vorallem S.420 ff.
 */
?>