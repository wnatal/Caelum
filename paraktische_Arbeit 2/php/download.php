<?php
/**
 *DATEI DOWNLOADEN
 *
 *Aus Sicherheitsgründen ist der direkte Zugriff auf die Dateien nicht erlaubt. Dadurch kann man die Dateien vor fremden Zugriff schützen und auch verhindern, dass ein gefährliches Skript durch ein Aufruf ausgeführt wird.
 *Durch den Content-Type "application/octet-stream" wird ein Download inizialisiert. Readfile öffnet die eigentliche Datei.
 *Zunächst wird aber geprüft, ob der Speicherort plausibel ist, indem der Link mit einem Array verglichen wird, wo alle bis jetzt aufgerufenen Ordnerebenen gespeichert sind.
 *Auch wenn hinter diesem Script nicht viel geistiges Eigentum steckt, will ich trotztdem "http://www.selfphp.de/kochbuch/kochbuch.php?code=37" (letzter Aufruf: 26.1.2014) als Quelle angeben, jedoch habe ich mich auch auf vielen anderen Seiten informiert und habe das Script vereinfacht, indem ich es dem Browser überlasse, den MIME-Type zuzuordnen.
 */
 
	session_start();
	if (!isset($_SESSION["idnummer"])){
		$_SESSION["site"] = "index.php";
		header("Location: ../index.php");
		exit();
	}
	if(in_array($filename = substr($_GET['ordner'], 0, strrpos($_GET['ordner'], '/')), $_SESSION['protokoll'])){
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$_GET['name'].'"');
		@readfile($_GET['ordner']);
	}
?>