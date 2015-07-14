<?php
//ob_end_flush();
$conn->open ( $connStr );
$item = 0;
echo "<div class='well'>";
echo "<h4>Estoque e custos de Matéria Prima</h4>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-striped'>";
	
	$retornaSQL = "	SELECT 

					B1.B1_COD AS Codigo,
					B1.B1_DESC AS Descricao,
					B1.B1_CUSTD AS CustoAutomatico,
					B1.B1_XCUSTMA AS CustoManual,
						(SELECT SUM(B2.B2_QATU) 
						FROM SB2010 B2 
						WHERE B2.B2_COD = B1.B1_COD 
						AND (B2.B2_LOCAL LIKE 'PLA-V_'
						OR B2.B2_LOCAL LIKE 'PRE-EN'
						OR B2.B2_LOCAL LIKE 'AL%')) AS Estoque,
					B1.B1_UM AS UnidadeMedida,
					BM.BM_DESC,
					B1.B1_UPRC AS UltimoPreco,
					(SELECT AVG(D3.D3_CUSTO1/D3.D3_QUANT)
					
					FROM SD3010 D3 WITH (NOLOCK)
					WHERE D3.D3_COD = B1.B1_COD
					AND D3.D3_EMISSAO>='20150101'
					AND D3.D_E_L_E_T_ <> '*'
					AND D3.D3_QUANT <> '0') AS CustoMedio
					
					FROM SB1010 B1 WITH (NOLOCK)
					INNER JOIN SBM010 BM ON B1.B1_GRUPO = BM.BM_GRUPO
					
					WHERE (B1.B1_COD LIKE 'MP00%')
					--AND B1.B1_XCUSTMA = '0'
					AND B1.B1_MSBLQL <> '1'
					AND B1.D_E_L_E_T_ <> '*'
					ORDER BY B1.B1_COD";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$valorAutomaticoTotal = 0;
	$valorManualTotal = 0;
	$valorAutomaticoPorGrupo = 0;
	$valorManualPorGrupo = 0;
	$estoqueTotal = 0;
	$demandaTotal = 0;
	$grupo2 = NULL;
	
	while ( ! $rs->EOF ) {
	
		$item++;
		$codigo = $fld [0]->value;
		$descricao = $fld [1]->value;
		$custoAutomatico = $fld [2]->value;
		$custoManual = $fld [3]->value;
		$estoque = $fld [4]->value;
		$unidadeMedida = $fld [5]->value;
		$grupo = $fld [6]->value;
		$ultimoPreco = $fld [7]->value;
		$custoMedio = $fld [8]->value;
		$valorAutomatico = $custoAutomatico * $estoque;
		$valorManual = $custoManual * $estoque;
		$valorMedio = $custoMedio * $estoque;
		$valorAutomaticoTotal += $valorAutomatico;
		$valorManualTotal += $valorManual;
		$valorMedioTotal += $valorMedio;
		$valorAutomaticoPorGrupo += $valorAutomatico;
		$valorManualPorGrupo += $valorManual;
		$valorMedioPorGrupo += $valorMedio;
		$estoqueTotal += $estoque;
		$demandaTotal += $demanda;
		
		if (($custoAutomatico/$custoManual) > 1.2 || ($custoAutomatico/$custoManual) < 0.8) {
			$corValidacao = '#FF0000';
		} else if (($custoAutomatico/$custoManual) > 1.1 || ($custoAutomatico/$custoManual) < 0.9) {
			$corValidacao = '#FF8C00';
		} else {
			$corValidacao = NULL;
		}
		
		if (($valorMedio/$valorManual) > 1.5 || ($valorMedio/$valorManual) < 0.5) {
			$corValidacaoTotal = '#FF0000';
		} else if (($valorMedio/$valorManual) > 1.1 || ($valorMedio/$valorManual) < 0.9) {
			$corValidacaoTotal = '#FF8C00';
		} else {
			$corValidacaoTotal = NULL;
		}
		
		if (empty($grupo2) || $grupo2 != $grupo) {
			echo "<tr><th colspan = '8'>Totais</th><th>R$ ". number_format($valorAutomaticoPorGrupo, 0, ',', '.') ."</th>";
			echo "<th>R$ ". number_format($valorManualPorGrupo, 0, ',', '.') ."</th>";
			echo "<th>R$ ". number_format($valorMedioPorGrupo, 0, ',', '.') ."</th>";
			echo "<th></th>";
			echo "</tr>";
			
			$valorManualPorGrupo = 0;
			
			echo "<tr bgcolor = '#FF8C00'><th colspan = '12'><h3>$grupo</h3></th></tr>";
			echo "<tr><th>Item</th><th>Produto</th><th>Descrição</th><th>Custo STD</th><th>Custo Manual</th><th>Custo Médio</th><th>Ult. Prec</th>";
			echo "<th>Estoque</th><th>Tot. STD</th><th>Tot. Man.</th><th>Tot. Médio</th><th>+</th></tr>";
		}
		$grupo2 = $grupo;
	
		echo "<tr><td>$item</td><td>$codigo</td><td>$descricao</td>";
		echo "<td bgcolor = '$corValidacao'>".number_format($custoAutomatico, 4, ',', '.')."</td>";
		echo "<td>".number_format($custoManual, 4, ',', '.')."</td><td>".number_format($custoMedio, 6, ',', '.')."</td><td>".number_format($ultimoPreco, 4, ',', '.')."</td>";
		echo "<td>". number_format($estoque, 0, ',', '.')." $unidadeMedida</td><td>R$ ".number_format($valorAutomatico, 2, ',', '.')."</td>";
		echo "<td>R$ ".number_format($valorManual, 2, ',', '.')."</td>";
		echo "<td bgcolor = '$corValidacaoTotal'>R$ ".number_format($valorMedio, 2, ',', '.')."</td>";
		echo "<td><a href = 'index.php?pg=entradaNotasPorMateriaPrima&produto=$codigo'>+</a></td></tr>";
		$rs->MoveNext ();
	}

	echo "<tr><th colspan = '8'>Totais</th><th>R$ ". number_format($valorAutomaticoPorGrupo, 0, ',', '.') ."</th>";
	echo "<th>R$ ". number_format($valorManualPorGrupo, 0, ',', '.') ."</th>";
	echo "<th>R$ ". number_format($valorMedioPorGrupo, 0, ',', '.') ."</th>";
	echo "<th></th>";
	echo "</tr>";
	echo "<tr><td colspan = '11'></td></tr>";
	echo "<tr><td colspan = '11'></td></tr>";
	
	echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><th>Total geral</th>";
	echo "<th>". number_format($estoqueTotal, 0, ',', '.')." $unidadeMedida</th><th>R$ ". number_format($valorAutomaticoTotal, 0, ',', '.') ."</th>";
	echo "<th>R$ ". number_format($valorManualTotal, 0, ',', '.') ."</th><th>R$ ". number_format($valorMedioTotal, 0, ',', '.') ."</th>";
	echo "</tr>";
	
	echo "<tr><td></td><td></td><td></td><td></td><td></td><th colspan = '2'>% Erro em relação ao manual</th>";
	echo "<th></th>";
	echo "<th>". number_format(($valorAutomaticoTotal/$valorManualTotal)*100, 2, ',', '.') ." %</th>";
	echo "<th></th>";
	echo "<th>". number_format(($valorMedioTotal/$valorManualTotal)*100, 2, ',', '.') ." %</th>";
	echo "</tr>";
	
	$rs->MoveNext ();
	$rs->Close ();
	$rs = null;
echo "</table>";
			
echo "</div>";

$conn->Close ();
$conn = null;
?>