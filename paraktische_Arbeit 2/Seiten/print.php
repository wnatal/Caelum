</html>
<?php
/**
 *In diese Seite wird der Inhalt des mit dem Richtext-Editor erstellten Dokuments kopiert.
 *Danach wird die Anweisung zum Drucken gegeben.
 */
 
	echo $_POST['speicher'];
?>
<script>
	alert("Am besten drucken sie das Dokument mit einer Grösse von 100% und ohne Seitenränder");
	window.print();
</script>
</html>