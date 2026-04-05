<?php
	function getBD(){
		$bdd = new PDO('mysql:host=localhost;dbname=addiction;charset=utf8','root', 'root');
		return $bdd;
	}
?>