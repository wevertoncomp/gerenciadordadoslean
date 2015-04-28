<?php
session_start();

ob_start();

include "../conexao.php";

$login = $_POST['login'];
$senha = $_POST['senha'];

$sql = mysql_query("SELECT * FROM st_usuarios WHERE login = '$login' AND senha = '".md5($senha)."' AND (nivel = 3 OR nivel = 1) AND liberado = 1");

	if(mysql_num_rows($sql) == 1)
	{
		$_SESSION['login_session'] = $login;
		$_SESSION['senha_session'] = $senha;
		header("location: pagina2.php");
		include "pagina2.php";
	}else{
		unset($_SESSION['login_session']);
		unset($_SESSION['senha_session']);
		include "index.php";
	}
	
	ob_end_flush();
?>
