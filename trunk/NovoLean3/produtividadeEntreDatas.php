<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Produtividade Diária</h4><hr>";

echo "<form action='?pg=produtividadeEntreDatas' method = 'post' class='form-inline'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o intervalo de datas que deseja visualizar: </b></span>";
echo "<br/>";

//$dataInicial = $_POST ['dataInicial'];
//$dataFinal = $_POST ['dataFinal'];

$dataInicial = '20150101';
$dataFinal = '20150131';

// TextField
?>
<!-- <div class="form-group">
<label for="dataInicial">Data Inicial</label>
<input type="text" class="form-control" id="dataInicial" placeholder="Data Inicial">
</div>
<div class="form-group">
<label for="dataFinal">Data Final</label>
<input type="text" class="form-control" id="dataFinal" placeholder="Data Final">
</div> -->
<?php 

echo "Mostrando valores entre ". $dataInicial." e ".$dataFinal;

echo "<br />";

echo "<input type='submit' value='Buscar'>";
echo "</form>";
echo "</div>";



//echo "<table cellspacing = 3 ><tr>";
$instrucaoSQL = "SELECT 
				 NR.NNR_CODIGO AS CODIGO, 
				 NR.NNR_DESCRI AS DESCRICAO,
				 SUM(Z8.ZZ8_TOTAL) AS HORAS
				 FROM NNR010 NR
				 INNER JOIN ZZ8010 Z8 ON NR.NNR_CODIGO = Z8.ZZ8_LOCAL
				 WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%'
				 AND NR.NNR_DESCRI LIKE '%PRODUCAO%'
				 AND NR.NNR_CODIGO <> 'RXR-TR'
				 AND NR.NNR_CODIGO <> 'SOM-TR'
				 AND Z8.ZZ8_DATA BETWEEN '$dataInicial' AND '$dataFinal'
				 GROUP BY NR.NNR_CODIGO, NR.NNR_DESCRI
				 ORDER BY NR.NNR_DESCRI";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$contador = 1;
$aprovado = true;
while ( ! $rs->EOF ) {
	$setor = null;
	$setor = $fld [0]->value;
	$horas = $fld [2]->value;
	// for($i = 0; $i < $num_columns; $i ++) {
	// if ($aprovado == true) {
	// echo "<td>" . $fld [0]->value . "</td><td>" . $fld [2]->value . " h</td><td><img src='images/ok_16x16.png'></td><td bgcolor='#999'></td>";
	//echo "<td>" . substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, " -" ) ) . "</td><td>" . $fld [2]->value . " h</td><td bgcolor='#999'></td>";
	echo "<div class='well'>";
	if (isset($horas)) {
		echo "<button type='button' class='btn btn-primary btn-lg' disabled='disabled'>" . substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, " -" ) ) . " - " . $fld [2]->value . " h </button>";
	} else {
		echo "<button type='button' class='btn btn-warning btn-lg' disabled='disabled'>" . substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, " -" ) ) . " - Horas não cadastradas</button>";
	}
	/*
	 * } else { echo "<td>" . $fld [0]->value . "</td><td>" . $fld [2]->value . " h</td><td><img src='images/alert_16x16.png'></td><td bgcolor='#999'></td>"; }
	 */
	/*if ($contador % 4 == 0)
		echo "</tr><tr>";
	$contador ++;*/
	// }
	

	if (isset($horas) || isset($data)){
	
	echo "<table class='table table-hover'><tr><th>Item</th><th>Produto</th><th>Tempo Unitário</th><th>Qtd Produzida</th><th>Tempo Ideal</th><th>Produtividade</th></tr>";
	
	$retornaProdutividadeSQL = "SELECT D3.D3_COD, Z9.ZZ9_TEMPOP, D3.D3_QUANT, Z9.ZZ9_QTDOPE FROM SD3010 D3
	
	INNER JOIN ZZ9010 Z9 ON D3.D3_COD = Z9.ZZ9_PRODUT
	
	WHERE D3.D3_LOCAL = '$setor' 
	AND D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
	AND D3.D3_TM = '010'
	
	ORDER BY D3.D3_COD";
	
	$rs2 = $conn->execute ( $retornaProdutividadeSQL );
	
	$num_columns2 = $rs2->Fields->Count ();
	
	for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs2->Fields ( $i2 );
	}
	
	$produtividadeTotal = 0.00;
	$quantidadeTotal = 0;
	$tempoIdealTotal = 0;
	$item = 0;
	
	while ( ! $rs2->EOF ) {
	
		$produto = $fld2 [0]->value;
		$tempoUnitario = $fld2 [1]->value;
		$quantidadeProduzida = $fld2 [2]->value;
		$tempoTotal = ($tempoUnitario * $quantidadeProduzida);
		$produtividade = $tempoTotal / ($horas * 3600);
		$produtividadeTotal += ( float ) $produtividade;
		$quantidadeTotal += $quantidadeProduzida;
		$tempoIdealTotal += $tempoTotal;
		$item++;
	
		echo "<tr><td>$item</td><td>$produto</td><td>$tempoUnitario seg</td><td>$quantidadeProduzida un</td>";
		echo "<td>" . number_format ($tempoTotal / 60, 0, '.', '' ) . " min.</td><td>" . number_format ( $produtividade * 100, 2, '.', '' ) . " %</td></tr>";
		$rs2->MoveNext ();
	}
	
	echo "<tfoot><td>Totais</td><td></td><td></td><td>$quantidadeTotal</td><td>". number_format ($tempoIdealTotal/60, 0, '.', '' ) ." min.</td>";
	echo "<td><button type='button' class='btn btn-lg btn-primary' disabled='disabled'>". number_format ($produtividadeTotal*100, 2, '.', '' ) ." %</button></td></tfoot></table>";
	
	$rs2->Close ();
	$rs2 = null;
	}
	echo "</div>";
	echo "</div>";
	
	$rs->MoveNext ();
}

echo "</tr></table>";
//echo "</div>";

$rs->Close ();
$rs = null;

/*$horaInicialDiurno = '04:00:00';
$horaFinalDiurno = '03:59:59';
$horaInicialNoturno = '17:00:00';
$horaFinalNoturno = '23:59:59';*/
// $horasTrabalhadas = '109';

//mostraProdutividade($data);



$conn->Close ();
$conn = null;
?>