<?php
$conn->open ( $connStr );
session_start();

ob_start();

include "../conexao.php";

$login = $_POST['login'];
//$senha = $_POST['senha'];

/*$sql = mysql_query("SELECT * FROM st_usuarios WHERE login = '$login'
		--AND senha = '".md5($senha)."' AND (nivel = 3 OR nivel = 1) AND liberado = 1
		");*/

$retornaSQL = "SELECT RA.RA_MAT
				FROM SRA010 RA
				WHERE RA.RA_DEMISSA = '' AND RA.RA_CATFUNC = 'M' AND RA.RA_MAT = RA.RA_XENCAR
				AND RA.RA_MAT = '$login'";

$rs = $conn->execute ( $retornaSQL );
$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld1 [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
 $login = $fld1 [0]->value;
$rs->MoveNext ();
}

if(isset ($login))
{
	$_SESSION['login_session'] = $login;
	//$_SESSION['senha_session'] = $senha;
	header("location: pagina2.php");
	include "pagina2.php";
}else{
	unset($_SESSION['login_session']);
	//unset($_SESSION['senha_session']);
	include "index.php";
}
/*while ( ! $rs->EOF ) {
	$usuario = $fld1 [0]->value;
	$rs->MoveNext ();
}*/
$rs->Close ();
$rs = null;


	
	ob_end_flush();
?>
