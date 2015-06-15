<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciador de Dados Lean</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-datepicker.css" rel="stylesheet">
    <script src="js/jquery-1.11.2.min.js"></script>

	<script src="Chart.min.js"></script>
	
	    <style type="text/css">

    .box {
        margin: 0px auto;
        width: 70%;
    }

    .box-chart {
        width: 100%;
        margin: 0 auto;
        padding: 10px;
    }

    </style> 
    
        <script type="text/javascript">
        var randomnb = function(){ return Math.round(Math.random()*300)};
    </script>  
    
  </head>
  <body>
 
 
 
 <?php

// INCLUDES
include 'conexao.php';
include 'funcoes.php';
// PARï¿½METROS

?>

<a name= 'topo'></a>
	<header class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
								<div class='well'>
									<div class="span7">
										<h2>Gerenciador de Dados Lean</h2>
									</div>
									<div class="span3">
										<a href = 'index.php'>Voltar a página inicial</a>
									</div>
								</div>
			</div>
		</div>
	</header>

	<!-- CLASSE QUE DEFINE O CONTAINER COMO FLUIDO (100%) -->
	<div class="container-fluid">
		<!-- CLASSE PARA DEFINIR UMA LINHA -->
		<div class="row-fluid">
			<div class="span12">
				
			<?php include 'paginas.php';?>

			</div>
		</div>
	</div>

	    	<script>
			document.write('<script src=' + ('__proto__' in {} ? 'js/vendor/zepto' : 'js/vendor/jquery') + '.js></script>

	<script src="js/foundation.min.js"></script>

	<script>
			$(document).foundation();
		</script>
 
 

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.2.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
	

  </body>
</html>