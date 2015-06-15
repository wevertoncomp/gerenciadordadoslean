<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Demanda Mensal de Matéria-prima</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'>";
	
	$retornaSQL = "	SELECT 

					D3.D3_COD AS Produto,
					SUM (D3.D3_QUANT) AS Quantidade,
					D3.D3_UM,
					DATEPART(mm, D3.D3_EMISSAO) AS Mes,
					DATEPART(yy, D3.D3_EMISSAO) AS Ano,
					RTRIM(B1.B1_DESC) AS Descricao,
					RTRIM(BM.BM_DESC) AS Grupo,
					B1.B1_IMPORT AS Importado
					
					FROM SD3010 D3
					
					INNER JOIN SB1010 B1 ON D3.D3_COD = B1.B1_COD
					INNER JOIN SBM010 BM ON B1.B1_GRUPO = BM.BM_GRUPO
					
					WHERE D3.D3_EMISSAO >= '20150101'
					AND D3.D3_CF = 'RE1'
					AND B1.B1_TIPO = 'PI'
					AND D3.D3_FILIAL = '0101'
					
					GROUP BY B1.B1_GRUPO, D3.D3_COD, D3.D3_UM, DATEPART(yy, D3.D3_EMISSAO), DATEPART(mm, D3.D3_EMISSAO), B1.B1_DESC, BM.BM_DESC, B1.B1_IMPORT
					
					ORDER BY B1.B1_GRUPO, D3.D3_COD, DATEPART(yy, D3.D3_EMISSAO), DATEPART(mm, D3.D3_EMISSAO)";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$grupo2 = null;
	$produto2 = null;
	$contador = 0;
	$total = 0;
	
	
	while ( ! $rs->EOF ) {
	
		$produto = $fld [0]->value;
		$quantidade = $fld [1]->value;
		$unidadeMedida = $fld [2]->value;
		$mes = $fld [3]->value;
		$ano = $fld [4]->value;
		$descricao = $fld [5]->value;
		$grupo = $fld [6]->value;
		$importado = $fld [7]->value;
		
		if (empty($grupo2) || ($grupo2 != $grupo)){
			echo "<tr><td colspan = '5'><h2>$grupo</h2></td></tr>";
			echo "<tr><th>Produto</th><th>Descrição</th><th>Quantidade</th><th>Mês</th><th>Ano</th></tr>";
		}
		
		if (empty($produto2) || ($produto2 != $produto)){
			echo "<tr bgcolor = '#FF8C00'><td colspan = '5'><h4>$produto</h4></td></tr>";
			echo "<tr><th>Produto</th><th>Descrição</th><th>Quantidade</th><th>Mês</th><th>Ano</th></tr>";
			$contador = 0;
			$total = 0;
			$produto2 = $produto;
		}
		
		$color = '#FFFFFF';
		$contador++;
		$total += $quantidade;
		
		if ($importado == 'S') {
			$color = '#00FF00';
		}

		$mesPorExtenso = retornaMesPorExtenso($mes);
		echo "<tr bgcolor = '$color'><td>$produto</td><td>$descricao</td><td>". number_format($quantidade, 0, ',', '.') ." $unidadeMedida</td>";
		echo "<td>$mesPorExtenso</td><td>$ano</td></tr>";
		$rs->MoveNext ();
		
		if (empty($produto2) || ($produto2 != $produto)){
			echo "<tr><td colspan = '5'>Média: $total</td></tr>";
		}
		
		/*if (empty($produto2) || ($produto2 != $produto)){
			echo "<tr bgcolor = '#FF8C00'><td colspan = '5'><h4>$produto</h4></td></tr>";
			echo "<tr><th>Produto</th><th>Descrição</th><th>Quantidade</th><th>Mês</th><th>Ano</th></tr>";
		}*/
		
		$produto2 = $produto;
		$grupo2 = $grupo;
		
	}
	
	$rs->MoveNext ();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;


$conn->Close ();
$conn = null;
?>