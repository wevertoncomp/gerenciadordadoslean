<?php
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Conferência de Ponto</h4><hr>";


// echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=conferePonto' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o mês que deseja visualizar: </b></span>";
echo "<br/>";

// $data = $_GET ['dia'];
$mesSelecionado = $_POST ['mes'];
$colaborador = $_POST ['colaborador'];

echo "<select name='mes' class = 'form-control' style = 'width : 400px;'>";
if (isset ( $mesSelecionado )) {
	echo "<option value='$mesSelecionado'>$mesSelecionado</option>";
} else {
	echo "<option value='-1'>Selecione a data desejada</option>";
}
echo "<option value='01'>Janeiro</option>";
echo "<option value='02'>Fevereiro</option>";
echo "<option value='03'>Março</option>";
echo "<option value='04'>Abril</option>";
echo "<option value='05'>Maio</option>";
echo "<option value='06'>Junho</option>";
echo "<option value='07'>Julho</option>";
echo "<option value='08'>Agosto</option>";
echo "<option value='09'>Setembro</option>";
echo "<option value='10'>Outubro</option>";
echo "<option value='11'>Novembro</option>";
echo "<option value='12'>Dezembro</option>";
echo "</select><br />";

echo "<select name='colaborador' class = 'form-control' style = 'width : 400px;'>";
if (isset ($colaborador)) {
	echo "<option value='$colaborador'>$colaborador</option>";
} else {
	echo "<option value='-1'>Selecione o colaborador desejado</option>";
}
$retornaSQL = "SELECT RA.RA_NOME, RTRIM(RA.RA_PIS), RA.RA_DEMISSA
				   	FROM SRA010 RA
				   	WHERE RA.RA_DEMISSA = '' AND RA.RA_CATFUNC = 'M'
				   	ORDER BY RA.RA_NOME";

$rs = $conn->execute ( $retornaSQL );
$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	$nome = $fld [0]->value;
	$pisSQL = $fld [1]->value;
	echo "<option value='$nome'>$nome</option>";
	$rs->MoveNext ();
}
$rs->Close ();
$rs = null;
echo "</select><br />";



echo "<input type='submit' value='Buscar' class = 'btn btn-default'>";
echo "</div>";

$caminho = "P://";
echo "<div class='well'>";

/*$ip = gethostbyname("192.168.117.127");
$fp = fsockopen($ip, "8008");
if (!$fp)
{
	echo "Unknown";
} else {
	echo "Encontrou";
}*/

// ///192.168.117.127//Ponto/M1/AFD00009000250000494.txt
$leitor1 = fopen ( "arquivos/ponto/M1/AFD00009000250000494.txt", "r" );
//$leitor1 = fopen ( "file://axor/Ponto/M1/AFD00009000250000494.txt", "r" );
if ($leitor1 == false)
	die ( 'Não foi possível abrir o arquivo 1 para leitura.' );

$leitor2 = fopen ( "arquivos/ponto/M2/AFD00009000250002234.txt", "r" );
if ($leitor2 == false)
	die ( 'Não foi possível abrir o arquivo 2 para leitura.' );

$pontoCompleto = fopen ( "pontoCompleto.txt", "w+" );

$linha = fgets ( $leitor1 );
while ( ! feof ( $leitor1 ) ) {
	$linha = fgets ( $leitor1 );
	if (! feof ( $leitor1 ))
		fwrite ( $pontoCompleto, $linha );
}
fclose ( $leitor1 );
$linha = fgets ( $leitor2 );
while ( ! feof ( $leitor2 ) ) {
	$linha = fgets ( $leitor2 );
	if (! feof ( $leitor2 ))
		fwrite ( $pontoCompleto, $linha );
}
fclose ( $leitor2 );


$retornaSQL = "SELECT RA.RA_NOME, RTRIM(RA.RA_PIS), RA.RA_DEMISSA
				   	FROM SRA010 RA
				   	WHERE RA.RA_DEMISSA = '' AND RA.RA_CATFUNC = 'M'
						AND RA.RA_NOME LIKE '$colaborador'
				   	ORDER BY RA.RA_NOME";

$rs2 = $conn->execute ( $retornaSQL );
$num_columns = $rs2->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs2->Fields ( $i );
}

while ( ! $rs2->EOF ) {
	$nome = $fld [0]->value;
	$pisSQL = $fld [1]->value;
	echo "<h3>Nome: $nome <br/> PIS: $pisSQL </h3><br />";
	
	/*
	 * $contadorDia = 1; do { fseek($pontoCompleto, 0); while(!feof($pontoCompleto)) { $linha = fgets($pontoCompleto); $pisTXT = substr($linha, 23, 11); if (strcmp($pisTXT, $pisSQL) == 0) { $contadorTXT = substr($linha, 0, 10); $diaTXT = substr($linha, 10, 2); $mesTXT = substr($linha, 12, 2); $anoTXT = substr($linha, 14, 4); $hora = substr($linha, 18, 2); $minuto = substr($linha, 20, 2); if (($contadorDia == (int)$diaTXT) && ($mesTXT == $mesSelecionado)) echo "Contador: $contadorTXT Data: $diaTXT/$mesTXT/$anoTXT Horario: $hora:$minuto PIS: $pisTXT <br />"; } } $contadorDia++; } while ($contadorDia <= 31);
	 */
	$quebraLinha = 0;
	$color = NULL;
	echo "<table class = 'table table-bordered'><tr>";
	for($contadorDia = 1; $contadorDia <= 31; $contadorDia++, $quebraLinha++) {
		$diaDaSemana = jddayofweek ( cal_to_jd(CAL_GREGORIAN, date($mesSelecionado),date($contadorDia), date("2015")) , 0);
		$confereDias = 0;
		fseek ( $pontoCompleto, 0 );
		if ($diaDaSemana == 0) {
			echo "</tr><tr>";
		}
		if ($diaDaSemana == 0) {
			$diaSemanaPorExtenso = "Domingo";
			$confereDias = 4;
			$color = '#CCC';
		} else if ($diaDaSemana == 6) {
			$color = '#CCC';
			$diaSemanaPorExtenso = "Sábado";
			$confereDias = 4;
		} else {
			$diaSemanaPorExtenso = NULL;
		}
		if ($contadorDia == 1) {
			$contador = 0;
			while (($diaDaSemana > $contador)) {
				echo "<td></td>";
				$contador++;
			}	
		}
		echo "<td bgcolor = '$color' width = '14.28%'>";
		$color = NULL;
		echo "<h4>$contadorDia</h4>";
		echo $diaSemanaPorExtenso;			
		while ( !feof( $pontoCompleto ) ) {  // Percorre o arquivo atrás do usuário e da data especificada
			$linha = fgets ( $pontoCompleto );
			$pisTXT = substr ( $linha, 23, 11 );
			if (strcmp ( $pisTXT, $pisSQL ) == 0) {  // Se o PIS procurado for encontrado
				//$contadorTXT = substr ( $linha, 0, 10 );
				$diaTXT = substr ( $linha, 10, 2 );
				$mesTXT = substr ( $linha, 12, 2 );
				$anoTXT = substr ( $linha, 14, 4 );
				$hora = substr ( $linha, 18, 2 );
				$minuto = substr ( $linha, 20, 2 );
				if (($contadorDia == ( int )$diaTXT) && ($mesTXT == $mesSelecionado)) {  // Se for o dia e o mes procurados
					//echo "Data: $diaTXT/$mesTXT/$anoTXT Horario: $hora:$minuto PIS: $pisTXT <br />";
					echo "$hora:$minuto<br />";
					$confereDias++;
				}
			}
		}
		while (($confereDias < 4) ) {
			echo "Não Registrado <br/>";
			$confereDias++;
		}
		echo "</td>";
	}
	$rs2->MoveNext ();
	echo "</tr></table>";
}

$rs2->MoveNext ();

$rs2->Close ();
$rs2 = null;

$conn->Close ();
$conn = null;

fclose ( $pontoCompleto );
unlink ( $pontoCompleto );
echo "</div>";
?>