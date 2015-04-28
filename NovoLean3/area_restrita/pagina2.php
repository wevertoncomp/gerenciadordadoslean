<?
session_start();
if(!isset($_SESSION['login_session']) AND !isset($_SESSION['senha_session'])){
	header("location: index.php");
	exit;
}

$pagina = $_GET['pg'];
$login = $_SESSION['login_session'];
$senha = $_SESSION['senha_session'];

if (!isset($pagina)){
	$pagina = "principal";
}

include '../conexao.php';

/* Pegando dados para gravar o LOG */
$sql_usuário = mysql_query("SELECT U.idUsuario AS idUsuario, U.nivel AS nivelAcesso
		FROM st_usuarios U WHERE U.login = '$login' AND U.senha = '".md5($senha)."' AND (U.nivel = 3 OR U.nivel = 1) AND U.liberado = 1");

while ($array = mysql_fetch_array($sql_usuário)){
	$idUsuario = $array['idUsuario'];
	$nivelAcesso = $array['nivelAcesso'];
}
$ipUsuario = $_SERVER['REMOTE_ADDR'];
$server = $_SERVER['SERVER_NAME'];
$endereco = $_SERVER ['REQUEST_URI'];
$enderecoCompleto = "http://" . $server . $endereco;
/* Fim dos dados para o LOG */
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="shortcut icon" href="favicon.ico"> -->

    <title>Pradolux - Área restrita para representantes</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- Gráfico -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    
	<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>  -->
	<script type="text/javascript">
	$(document).ready(function(){
		var content = $('#content');
 
		//pre carregando o gif
		loading = new Image(); loading.src = 'images/loading.gif';
		$('#menu a').live('click', function( e ){
			e.preventDefault();
			content.html( '<img src="images/loading.gif" />' );
 
			var href = $( this ).attr('href');
			$.ajax({
				url: href,
				success: function( response ){
					//forçando o parser
					var data = $( '<div>'+response+'</div>' ).find('#content').html();
 
					//apenas atrasando a troca, para mostrarmos o loading
					window.setTimeout( function(){
						content.fadeOut('slow', function(){
							content.html( data ).fadeIn();
						});
					}, 500 );
				}
			});
 
		});
	});
	</script>

	<script type="text/javascript">
	$(function(){
	    $('#container').highcharts({
	        chart: {
	            plotBackgroundColor: null,
	            plotBorderWidth: null,
	            plotShadow: false
	        },
	        title: {
	            text: 'Meta de Vendas'
	        },
	        tooltip: {
	    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	        },
	        plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
	                    enabled: true,
	                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
	                    style: {
	                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
	                    }
	                }
	            }
	        },
	        series: [{
	            type: 'pie',
	            name: 'Browser share',
	            data: [
	                ['Vendido',   45.0],
	                ['Falta',       26.8]
	            ]
	        }]
	    });
	});
	
	</script>
  </head>

  <body>
  
  	<script src="js/highcharts.js"></script>
	<script src="js/modules/exporting.js"></script>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Pradolux Indústria e Comércio LTDA</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="?pg=principal">Página Inicial</a></li>
            <!-- <li><a href="?pg=perfil">Perfil</a></li>
            <li><a href="?pg=ajuda">Ajuda</a></li> -->
            <li><a href="sair.php">Sair</a></li>
          </ul>
          <!-- <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Busca...">
          </form> -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar" id="menu">
          <ul class="nav nav-pills nav-stacked">
          	<li <?php if ($pagina == "principal") echo "class='active'";?>><a href="?pg=principal">Página Inicial</a></li>
          	<li <?php if ($pagina == "pedidos_aberto") echo "class='active'";?>><a href="?pg=pedidos_aberto">Pedidos em aberto</a></li>
          	<li <?php if ($pagina == "historico_pedidos") echo "class='active'";?>><a href="?pg=historico_pedidos">Histórico Pedidos</a></li>
            <li <?php if ($pagina == "fichas_tecnicas") echo "class='active'";?>><a href="?pg=fichas_tecnicas">Fichas Técnicas</a></li>
            <li <?php if ($pagina == "videos"||$pagina == "video") echo "class='active'";?>><a href="?pg=videos">Vídeos</a></li>
            <?php if ($nivelAcesso == '1') { ?>
            	<li <?php if ($pagina == "log_acesso") echo "class='active'";?>><a href='?pg=log_acesso'>Log de uso</a></li>
            <?php }?>
            <!-- <li><a href="?pg=listas_precos">Listas de Preços</a></li>
            <li><a href="?pg=material_publicitario">Material Publicitário</a></li> -->
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main" id="content">
			<?php include "paginas.php";?>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/docs.min.js"></script>
    <?php $insertLog = mysql_query("INSERT st_usuarioslog(idUsuario, ip, endereco) VALUES ($idUsuario, '$ipUsuario', '$enderecoCompleto')");?>
  </body>
</html>
