<?
session_start();
if(!isset($_SESSION['login_session']) AND !isset($_SESSION['senha_session'])){
	header("location: index.php");
	exit;
}

$mesNome[1] = "janeiro";
$mesNome[2] = "fevereiro";
$mesNome[3] = "mar�o";
$mesNome[4] = "abril";
$mesNome[5] = "maio";
$mesNome[6] = "junho";
$mesNome[7] = "julho";
$mesNome[8] = "agosto";
$mesNome[9] = "setembro";
$mesNome[10] = "outubro";
$mesNome[11] = "novembro";
$mesNome[12] = "dezembro";

$diaSemana[1] = "Segunda-feira";
$diaSemana[2] = "Ter�a-feira";
$diaSemana[3] = "Quarta-feira";
$diaSemana[4] = "Quinta-feira";
$diaSemana[5] = "Sexta-feira";
$diaSemana[6] = "S�bado";
$diaSemana[7] = "Domingo";

$ano = date('Y');
$mes = date('n');
$dia = date('d');
$semana = date('w');

?>

<?php 

$login = $_SESSION['login_session'];
$senha = $_SESSION['senha_session'];

//echo $login, $senha;

$sql = mysql_query("SELECT U.nome AS nome, U.idUsuario AS idUsuario, U.codigoSistema AS codigoSistema, U.acessos AS acessos, U.tipoUsuario AS tipoUsuario 
					FROM st_usuarios U WHERE U.login = '$login' AND U.senha = '".md5($senha)."' AND (U.nivel = 3 OR U.nivel = 1) AND U.liberado = 1");

/*while($array = mysql_fetch_array($sql))
{
	$nome =  $array['nome'];
}*/

while ($array = mysql_fetch_array($sql)){
$nome = $array['nome'];
$idUsuario = $array['idUsuario'];
$codigoSistema = $array['codigoSistema'];
$tipoUsuario = $array['tipoUsuario'];
$acessos = $array['acessos'];
}
$acessos++;
$insert = mysql_query("UPDATE st_usuarios SET acessos = '$acessos' WHERE idUsuario = $idUsuario");

if ($tipoUsuario == "R") {
	$tipo = "Representante";
} else if ($tipoUsuario == "I"){
	$tipo = "Usu�rio Interno";
} else if ($tipoUsuario == "S"){
	$tipo = "Supervisor";
}

//$nome = $sql["nome"];

//$nome = $sql["nome"];

?>
        
        <h1 class="sub-header">Bem vindo(a) <small><?php echo "$nome - $tipo $codigoSistema";?></small></h1>
<div class="table-responsive">
<h3 class="sub-header"><?php echo "$diaSemana[$semana], $dia de $mesNome[$mes] de $ano ";?><small><?php echo "Acesso de n� $acessos";?></small></h3>

<br />
<p>Caro(a) <?php echo "$nome";?>,</p>

<p>Esta �rea restrita est� em desenvolvimento, � uma vers�o inicial e tem como finalidade ser uma ferramenta que possibilite a consulta de maneira mais r�pida de informa��es comerciais, principalmente
para representantes e supervisores, dos status de seus pedidos, se estes est�o em or�amento e processo de libera��o, em f�brica ou j� foram faturados,
as datas previstas de faturamento e a data de sa�da.</p>

<p>Como forma de apoio a seu trabalho, est�o dispon�veis tamb�m fichas t�cnicas e v�deos com informa��es detalhadas de linhas e produtos espec�ficos.</p>

<p>Est�o previstas as seguintes melhorias:</p>
<ul>
<li>P�gina para trocar nome de usu�rio e senha;</li>
<li>Lista e classifica��o dos clientes de acordo com suas compras;</li>
<li>Status das quotas de faturamento: Geral e de STL;</li>
<!-- <li>Status de pagamento: clientes com faturas em aberto;</li> -->
<li>Download de materiais publicit�rios em PDF: cat�logos, folders;</li>
<li>Download de listas de pre�os;</li>
<li>Download de planilhas de or�amento;</li>
</ul>

<p>Navegue pelo menu lateral, conhe�a e utilize sua nova ferramenta de apoio �s vendas.</p>
<p>Qualquer d�vida, sugest�o, cr�tica ou reclama��o estamos a inteira disposi��o.</p>

<p><strong>TI Pradolux</strong></p>

        <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

</div>