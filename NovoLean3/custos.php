<?php
//ob_end_flush();
$conn->open ( $connStr );
$item = 0;
echo "<div class='well'>";
echo "<h4>Custos de Plásticos</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'><tr><th>Item</th><th>Produto</th><th>Descrição</th><th>Custo Autom.</th><th>Custo Manual</th>";
	echo "<th>Estoque</th><th>Tot. Aut.</th><th>Tot. Man.</th></tr>";
	
	$retornaSQL = "	SELECT 

					B1.B1_COD AS Codigo,
					B1.B1_DESC AS Descricao,
					B1.B1_CUSTD AS CustoAutomatico,
					B1.B1_XCUSTMA AS CustoManual,
					(SELECT SUM(B2.B2_QATU) FROM SB2010 B2 WHERE B2.B2_COD = B1.B1_COD AND B2.B2_LOCAL LIKE 'PLA-V_') AS Estoque,
					B1.B1_UM AS UnidadeMedida
					
					FROM SB1010 B1
					
					WHERE B1.B1_COD LIKE 'MP003%'
					AND B1.B1_MSBLQL <> '1'
					AND B1.D_E_L_E_T_ <> '*'";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$valorAutomaticoTotal = 0;
	$valorManualTotal = 0;
	$estoqueTotal = 0;
	
	while ( ! $rs->EOF ) {
	
		$item++;
		$codigo = $fld [0]->value;
		$descricao = $fld [1]->value;
		$custoAutomatico = $fld [2]->value;
		$custoManual = $fld [3]->value;
		$estoque = $fld [4]->value;
		$unidadeMedida = $fld [5]->value;
		$valorAutomatico = $custoAutomatico * $estoque;
		$valorManual = $custoManual * $estoque;
		$valorAutomaticoTotal += $valorAutomatico;
		$valorManualTotal += $valorManual;
		$estoqueTotal += $estoque;

	
		echo "<tr><td>$item</td><td>$codigo</td><td>$descricao</td><td>$custoAutomatico</td><td>$custoManual</td>";
		echo "<td>". number_format($estoque, 0)." $unidadeMedida</td><td>R$ ". number_format($valorAutomatico, 0) ."</td>";
		echo "<td>R$ ". number_format($valorManual, 0) ."</td></tr>";
		$rs->MoveNext ();
	}
	
	echo "<tr><td></td><td></td><td></td><td></td><th>Totais</th>";
	echo "<th>". number_format($estoqueTotal, 0)." $unidadeMedida</th><th>R$ ". number_format($valorAutomaticoTotal, 0) ."</th>";
	echo "<th>R$ ". number_format($valorManualTotal, 0) ."</th></tr>";
	
	$rs->MoveNext ();
	

echo "</table>";
echo "</div>";

$rs->Close ();
$rs = null;


$conn->Close ();
$conn = null;
?>