<?php
/**
 *ABMELDEN
 *
 *Mit diesem einfachen Skript melde ich mich von der Website ab:
 *Es wird die Session mit all ihren Variablen gelöscht und dann auf die Anmeldeseite umgeleitet
 */
 
	session_start();
	$_SESSION = array();
	if (ini_get("session.use_cookies")) {
    	$params = session_get_cookie_params();
    	setcookie(session_name(), '', time() - 42000, $params["path"],
        	$params["domain"], $params["secure"], $params["httponly"]
    	);
	}
	session_destroy();
	header("Location: ../index.php");//Weiterleitung
	exit();
	
/**
 *Sollte irgendetwas nicht funktionieren, kann man dieses Skript direkt ansteuern, um sich abzumelden.
 *Das Gerüst und die Funktionsweise stammt aus  php.net [1].
 */

/*[1]*
 *Link :
 	*http://ch1.php.net/session_destroy
 *letzer Zugriff:
	*22.1.14
 */ 
?>