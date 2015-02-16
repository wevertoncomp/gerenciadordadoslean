<?php
$conn->open ( $connStr );

//$setor = 0;
echo "<div class='well'>";
echo "<h4>Estoques</h4><hr>";
echo "</div>";

// Combobox da data

$instrucaoSQL = "SELECT 
				 NR.NNR_CODIGO AS CODIGO, 
				 NR.NNR_DESCRI AS DESCRICAO
				 FROM NNR010 NR
				 WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%'
				 AND NR.NNR_DESCRI LIKE '%PRODUCAO%'
				 --AND NR.NNR_DESCRI LIKE '%MONTAGEM 2%'
				 AND NR.NNR_CODIGO <> 'SOM-TR'
				 AND NR.NNR_CODIGO <> 'INJ-TR'
				 AND NR.NNR_CODIGO <> 'MET-TR'
				 AND NR.NNR_CODIGO <> 'RXR-TR'
				 AND NR.NNR_CODIGO <> 'PRE-TR'
				 AND NR.NNR_CODIGO <> 'PIN-TR'
				 ORDER BY NR.NNR_DESCRI";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	echo "<div class='well'>";
		echo "<h4>" . substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, " -" ) ) . "</h4><br />";
		$setor = 				$fld [0]->value;
		
		
/*************/
 
$instrucaoSQL2 = "	SELECT 

					B1.B1_COD,
					Z9.ZZ9_KANBVD,
					Z9.ZZ9_KANBAM,
					Z9.ZZ9_KANBVM,
					Z9.ZZ9_QTDKAN,
					B2.B2_QATU
					
					FROM SB1010 B1 WITH (NOLOCK)
					
					LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
					LEFT OUTER JOIN SB2010 B2 ON B1.B1_COD = B2.B2_COD AND B2.B2_LOCAL = 'AP-A01' AND B2.B2_FILIAL = '0101'
					
					WHERE B1.B1_COD LIKE 'PL________'
					AND B1.B1_COD NOT LIKE 'PL00______'
					AND B1.B1_XTPLSPR <> '6'
					AND B1.B1_LOCPAD = '$setor'

					ORDER BY B1.B1_COD";

$rs2 = $conn->execute ( $instrucaoSQL2 );

$num_columns2 = $rs2->Fields->Count ();

for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
	$fld2 [$i2] = $rs2->Fields ( $i2 );
}

$somaEstoque = 0;
$somaEstoqueMaximo = 0;
$somaEstoqueUtil = 0;

echo "<table class='table table-hover'><tr><td>Produto</th><th>Estoque</th><th>Máximo</th><th>Porcentagem</th><th>Qualidade</th></tr>";

while ( ! $rs2->EOF ) {

	$produto = 				$fld2 [0]->value;
	$kanbanVD =				$fld2 [1]->value;
	$kanbanAM =				$fld2 [2]->value;
	$kanbanVM =				$fld2 [3]->value;
	$qtdPorKanban =			$fld2 [4]->value;
	$estoque =				$fld2 [5]->value;
	$estoqueMaximo = ($kanbanVD+$kanbanAM+$kanbanVM) * $qtdPorKanban;
	$porcentagem = $estoque / $estoqueMaximo;
	$qualidade = $estoque / $estoqueMaximo;
	
	if ($qualidade > 1) {
		$qualidade = 1;
	}
	
	echo "<tr><td>$produto</td><td>$estoque</td><td>$estoqueMaximo</td><td>".number_format(($porcentagem*100), 0)." %</td>";
	echo "<td>".number_format(($qualidade*100), 0)." %</td>";
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

echo "<tr><th>Totais</th><th>$somaEstoque</th><th>$somaEstoqueMaximo</th><th>".number_format(($porcentagemTotal*100), 2)." %</th>";
echo "<th>".number_format(($qualidadeTotal*100), 0)." %</th>";
echo "</tr>";
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