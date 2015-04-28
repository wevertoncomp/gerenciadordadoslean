<!DOCTYPE html>
<html lang="pt-br">
  <head>
  
    <!--code fo meta tags-->  
    <meta charset="iso-8859-1">
	
	<!--code fo page title-->
    <title>Pradolux - Área Restrita</title>
	
	<!--code for favicon-->
	<!--<link rel="Shortcut Icon" type="image/x-icon" href="http://www.mubbashir10.com/wp-content/uploads/2014/01/cube_blue_rss_black_add_draw-512.png" />-->
	
	<!--code for stylesheets-->
	<link rel="stylesheet" type="text/css" href="stylesheets/reset.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/style.css">
	
	<!--code for fonts-->
    <link href="http://fonts.googleapis.com/css?family=Questrial" rel="stylesheet" type="text/css"/>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet" type="text/css" />
	
	<!--code for scripts-->
	<script src="scripts/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">var switchTo5x=true;</script>
    <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
    <script type="text/javascript">stLight.options({publisher: "52e8f8ab-1343-44f3-b9ec-a46de4aaf274", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>  
	
	<!--code for special treatment of internet explorer, reasons to hate IE-->  	
	<!--[if IE]>
	<script src="scripts/html5-for-iexplorer.js"></script>  
    <script src="scripts/modernizr-1.6.min_.js"></script>
    <script>    
        $(document).ready(function() {           
			if (!Modernizr.input.placeholder)
			{
				
				var placeholderText = $('#username').attr('placeholder');
				
				$('#username').attr('value',placeholderText);
				$('#username').addClass('placeholder');
				
				$('#username').focus(function() {				
					if( ($('#username').val() == placeholderText) )
					{
						$('#username').attr('value','');
						$('#username').removeClass('placeholder');
					}
				});
				
				$('#username').blur(function() {				
					if ( ($('#username').val() == placeholderText) || (($('#username').val() == '')) )                      
					{	
						$('#username').addClass('placeholder');					  
						$('#username').attr('value',placeholderText);
					}
				});
			}                
        });         
    </script>
	<script>    
        $(document).ready(function() {           
			if (!Modernizr.input.placeholder)
			{
				
				var placeholderText = $('#password').attr('placeholder');
				
				$('#password').attr('value',placeholderText);
				$('#password').addClass('placeholder');
				
				$('#password').focus(function() {				
					if( ($('#password').val() == placeholderText) )
					{
						$('#password').attr('value','');
						$('#password').removeClass('placeholder');
					}
				});
				
				$('#password').blur(function() {				
					if ( ($('#password').val() == placeholderText) || (($('#password').val() == '')) )                      
					{	
						$('#password').addClass('placeholder');					  
						$('#password').attr('value',placeholderText);
					}
				});
			}                
        });         
    </script>
	<link rel="stylesheet" type="text/css" href="stylesheets/iexplorer.css">
	<![endif]-->
	
  </head>
  
  <body>
  
  <?php include "../conexao.php";?>
    <!--code for main wrapper-->
	<section id="wrapper">
	
        <!--code for form-->
		<section id="form">
				<br/>				
		        <h1>Login - Área Restrita</h1>
				<form name="login-form" id="smart-login" method="post" action="logar.php">
					<fieldset id="smart-login-fields">
						<input id="login" name="login" type="text" placeholder="Usuário ou e-mail" required>
						<br/>
						<input id="senha" name="senha" type="password" placeholder="Senha" required>
					</fieldset>
					<!-- <span class="password-reset"><a href="#">Esqueceu sua senha?</a></span> --><br/><br/><br/>
					<!-- <span class="cookie"><input type="checkbox" value="true">Manter conectado</span> -->
					<fieldset id="smart-login-actions">
						<input type="reset" id="reset" value="Limpar">
						<input type="submit" id="logar" name="logar" value="Entrar">
					</fieldset>
					<br/>
	  			 </form>
    	</section>
	 
	
	</section>
	<div class="footer"><span style="margin-left:20px;">Copyright &copy; 2014 · Todos os direitos reservados <a href="http://www.pradolux.com.br">Pradolux</a></span></div>
	
  </body>
</html>