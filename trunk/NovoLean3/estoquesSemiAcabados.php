<?php
$conn->open ( $connStr );

//$setor = 0;
echo "<div class='well'><a name = 'topo'></a>";
echo "<h4>Estoques</h4>";
echo "</div>";

// Combobox da data

$instrucaoSQL = "SELECT 
				 NR.NNR_CODIGO AS CODIGO, 
				 NR.NNR_DESCRI AS DESCRICAO
				 FROM NNR010 NR WITH (NOLOCK)
				 WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%'
				 AND NR.NNR_DESCRI LIKE '%PRODUCAO%'
				 --AND NR.NNR_DESCRI LIKE '%MONTAGEM 2%'
				 AND NR.NNR_CODIGO <> 'SOM-TR'
				 AND NR.NNR_CODIGO <> '0516TR'
				 AND NR.NNR_CODIGO <> '524-TR'
				 AND NR.NNR_CODIGO <> '524LTR'
				 AND NR.NNR_CODIGO <> '0440TR'
				 AND NR.NNR_CODIGO <> '0716TR'
				 --AND NR.NNR_CODIGO <> 'INJ-TR'
				 AND NR.NNR_CODIGO <> 'EMB-TR'
				 AND NR.NNR_CODIGO <> '600-TR'
				 ORDER BY NR.NNR_DESCRI";
$rs = $conn->execute ( $instrucaoSQL );
//$rs3 = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<div class='well'> | ";
while ( ! $rs->EOF ) {
	$link = "".substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, ' -' ) )."";
	echo "<a href = '#$link'>$link</a>    |    ";
	$rs->MoveNext ();
	}
echo "</div>";

	$rs->MoveFirst();
	
while ( ! $rs->EOF ) {
	echo "<div class='well'>";
			$link = "".substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, ' -' ) )."";
			echo "<a name = '$link'></a><h2>$link</h2> <a href = '#topo'>Voltar ao topo</a>";
		$setor = 				$fld [0]->value;
		
		
/*************/
 
$instrucaoSQL2 = "	SELECT 

					B1.B1_COD,
					Z9.ZZ9_KANBVD,
					Z9.ZZ9_KANBAM,
					Z9.ZZ9_KANBVM,
					Z9.ZZ9_QTDKAN,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND (B2.B2_LOCAL LIKE 'PP%' OR B2.B2_LOCAL LIKE 'M_-%') AND B2.B2_LOCAL <> 'PP-TRA') AS EstoquePP,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE 'AL%' AND B2.B2_LOCAL <> 'AL-TRA') AS EstoqueAL,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE 'PVC%') AS EstoquePVC,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE 'Z%' AND B2.B2_LOCAL <> 'ZIN-TR') AS EstoqueZIN,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE '%EN') AS EstoqueEN,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WITH (NOLOCK) WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE '%TR') AS EstoqueTR,
					B1.B1_LOCPAD,
					RTRIM(Z9.ZZ9_FAMILI)
					
					
					FROM SB1010 B1 WITH (NOLOCK)
					
					LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
					
					WHERE B1.B1_COD NOT LIKE 'PL________'
					AND B1.B1_COD NOT LIKE 'PL00______'
					AND B1.B1_XTPLSPR <> '6'
					AND B1.B1_FANTASM <> 'S'
					AND B1.B1_MSBLQL <> '1'
					AND B1.D_E_L_E_T_ <> '*'
					AND B1.B1_LOCPAD = '$setor'
					
					ORDER BY B1.B1_LOCPAD, Z9.ZZ9_FAMILI, B1.B1_COD";

$rs2 = $conn->execute ( $instrucaoSQL2 );

$num_columns2 = $rs2->Fields->Count ();

for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
	$fld2 [$i2] = $rs2->Fields ( $i2 );
}

$somaEstoque = 0;
$somaEstoqueMaximo = 0;
$somaEstoqueUtil = 0;
$observacoes2 = null;

echo "<table class='table table-hover'><tr><th>Produto</th><th>EstoquePP</th>";
echo "<th>EstoqueAL</th><th>EstoquePVC</th><th>EstoqueZIN</th><th>EstoqueEN</th><th>EstoqueTR</th><th>Total</th>";
echo "<th>Máximo</th><th>Porcentagem</th><th>Qualidade</th><th>Observações</th></tr>";

while ( ! $rs2->EOF ) {

	$produto = 				$fld2 [0]->value;
	$kanbanVD =				$fld2 [1]->value;
	$kanbanAM =				$fld2 [2]->value;
	$kanbanVM =				$fld2 [3]->value;
	$qtdPorKanban =			$fld2 [4]->value;
	$estoquePP =			$fld2 [5]->value;
	$estoqueAL =			$fld2 [6]->value;
	$estoquePVC =			$fld2 [7]->value;
	$estoqueZIN =			$fld2 [8]->value;
	$estoqueEN =			$fld2 [9]->value;
	$estoqueTR =			$fld2 [10]->value;
	$local =				$fld2 [11]->value;
	$observacoes =			$fld2 [12]->value;
	$estoqueTotal = $estoquePP + $estoqueAL + $estoquePVC + $estoqueZIN + $estoqueEN + $estoqueTR;
	$estoqueMaximo = ($kanbanVD+$kanbanAM+$kanbanVM) * $qtdPorKanban;
	$porcentagem = $estoqueTotal / $estoqueMaximo;
	$qualidade = $estoqueTotal / $estoqueMaximo;
	
	if (empty($observacoes2)) {
		if (isset($observacoes)) {
			echo "<tr><th colspan = '12' bgcolor = '#5cb85c'>$observacoes</th>";
			echo "<tr><th>Produto</th><th>EstoquePP</th>";
			echo "<th>EstoqueAL</th><th>EstoquePVC</th><th>EstoqueZIN</th><th>EstoqueEN</th><th>EstoqueTR</th><th>Total</th>";
			echo "<th>Máximo</th><th>Porcentagem</th><th>Qualidade</th><th>Observações</th></tr>";
		}
		
	} elseif ($observacoes2 != $observacoes) {
		echo "<tr><th colspan = '12' bgcolor = '#5cb85c'>$observacoes</th>";
		echo "<tr><th>Produto</th><th>EstoquePP</th>";
		echo "<th>EstoqueAL</th><th>EstoquePVC</th><th>EstoqueZIN</th><th>EstoqueEN</th><th>EstoqueTR</th><th>Total</th>";
		echo "<th>Máximo</th><th>Porcentagem</th><th>Qualidade</th><th>Observações</th></tr>";
	}
	$observacoes2 = $observacoes;
	
	if ($qualidade > 1) {
		$qualidade = 1;
	}
	
	echo "<tr><td>$produto</td><td>$estoquePP</td>";
	echo "<td>$estoqueAL</td><td>$estoquePVC</td><td>$estoqueZIN</td><td>$estoqueEN</td><td>$estoqueTR</td><td>$estoqueTotal</td>";
	echo "<td>$estoqueMaximo</td><td>".number_format(($porcentagem*100), 0)." %</td>";
	echo "<td>".number_format(($qualidade*100), 0)." %</td>";
	echo "<td>$observacoes</td>";
	echo "</tr>";
	
	$somaEstoque += $estoque;
	$somaEstoqueMaximo += $estoqueMaximo;
	
	if ($estoque <= $estoqueMaximo) {
		$somaEstoqueUtil += $estoque;
	} else {
		$somaEstoqueUtil += $estoqueMaximo;
	}

	$rs2->MoveNext ();
}

$porcentagemTotal = $somaEstoque / $somaEstoqueMaximo;
$qualidadeTotal = $somaEstoqueUtil / $somaEstoqueMaximo;

//echo "<tr><th>Totais</th><th>$somaEstoque</th><th>$somaEstoqueMaximo</th><th>".number_format(($porcentagemTotal*100), 2)." %</th>";
//echo "<th>".number_format(($qualidadeTotal*100), 0)." %</th>";
//echo "</tr>";
echo "</table>";

$rs2->Close ();
$rs2 = null;
 
 /********************/		
		
		
	$rs->MoveNext ();
	echo "</div>";
}


$rs->Close ();
$rs = null;


?>