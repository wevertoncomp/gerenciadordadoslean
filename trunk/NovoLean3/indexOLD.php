<!DOCTYPE html>
<!--[if IE 8]> 				 <html class="no-js lt-ie9" lang="en" > <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title>Gerenciador de Dados Lean</title>

<!-- JQUERY -->
<!-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script> -->
<script src="js/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/jquery-1.7.1.min.js"><\/script>')</script>

<!-- TWITTER BOOTSTRAP CSS -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-button.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-icon.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-progress.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-table.css" rel="stylesheet" type="text/css" />

<!-- TWITTER BOOTSTRAP JS -->
<script src="js/bootstrap.min.js"></script>

<link href="css/estilos.css" rel="stylesheet" type="text/css" />

</head>
<body>

<?php $_Session['dispositivoMovel'] = FALSE;?>

	<script type="text/javascript">
function isMobile(){
	var a = navigator.userAgent||navigator.vendor||window.opera;
	if(/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile|o2|opera m(ob|in)i|palm( os)?|p(ixi|re)\/|plucker|pocket|psp|smartphone|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce; (iemobile|ppc)|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a))
		return true;
	else
		return false;
}

	if (isMobile()) {
		<?php $_Session['dispositivoMovel'] = TRUE;?>
	}
/* Exemplo de Chamada da Fun��o */
/*alert("� um dispositivo m�vel: " + isMobile());*/
</script>

<?php

// INCLUDES
include 'conexao.php';
include 'funcoes.php';
//include 'querys.php';
// PAR�METROS

?>

	<header class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div class="navbar">
					<div class="navbar-inner">
						<div class="span3">
							<div class="container">
								<h5>Gerenciador de Dados Lean</h5>
								<p></p>
							</div>
						</div>
						<div class="span6">
							<div class="container">
							<?php // include 'menu_modelos.php';?>
							<?php //include 'menu.php';?>
							</div>
						</div>
						<div class="span3">
							<div class="container">
							<?php // include 'menu_modelos.php';?>
								<!-- <form class="navbar-search pull-left" method="post"
									action="?pg=pesquisa">
									<input type="text" class="search-query" placeholder="Buscar..."
										id="busca" name="busca">
								</form> -->
							</div>
						</div>
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
</body>
</html>
