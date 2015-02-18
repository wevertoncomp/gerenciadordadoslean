<?php
// ob_end_flush();
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h3>Desconto, Preço e Prazo Médio</h3>";
echo "</div>";

echo "<div class='well'>";

$retornaSQL = "	SELECT 

					A3.A3_COD, A3.A3_NOME, A3.A3_NREDUZ, RTRIM(A3.A3_REGIAO)
					
					FROM SA3010 A3
					
					WHERE A3.D_E_L_E_T_ <> '*' 
					AND A3.A3_MSBLQL <> '1'
					AND A3.A3_COD <> '999997'
					AND A3.A3_COD <> '999998'
					AND A3.A3_COD <> '999999'
					
					ORDER BY A3.A3_REGIAO, A3.A3_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$regiao2 = null;

while ( ! $rs->EOF ) {
	
	$representante = $fld [0]->value;
	$nome = $fld [1]->value;
	$nomeReduzido = $fld [2]->value;
	$regiao = $fld [3]->value;
	
	if (empty ( $regiao2 ) || ($regiao2 != $regiao)) {
		echo "<h2>Região $regiao</h2>";
	}
	$regiao2 = $regiao;
	
	echo "<h3>$representante - $nome - $nomeReduzido</h3>";
	
	echo "<table class='table table-striped'>";
	
	// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	$retornaSQL2 = "SELECT 	DATEPART(mm, F.F2_EMISSAO) AS MES,
					DATEPART(YY, F.F2_EMISSAO) AS ANO,
					F.F2_FILIAL AS FILIAL,
					F.F2_CLIENTE,
					RTRIM(A.A1_NOME),
					F.F2_DOC,
					(SELECT TOP 1 D2_PEDIDO
					 FROM SD2010 WITH (NOLOCK)
					 WHERE 	D_E_L_E_T_ != '*'
						AND D2_DOC = F.F2_DOC
						AND D2_FILIAL = F.F2_FILIAL
					) AS D2_PEDIDO,
					F.F2_EMISSAO,
					(SELECT TOP 1 D2_COMIS1
					 FROM SD2010 WITH (NOLOCK)
					 WHERE 	D_E_L_E_T_ != '*'
						AND D2_DOC = F.F2_DOC
						AND D2_FILIAL = F.F2_FILIAL
					) AS D2_COMIS1,
					E4.E4_XMEDPRA,
					(SELECT ROUND(AVG(1-((1-(C6.C6_ZDESCON/100))*(1-(C6.C6_ZDESCO2/100)))),4)
				 	 FROM SC6010 C6 WITH (NOLOCK)
				 	 INNER JOIN SC5010 C5 ON C5.C5_NUM = C6.C6_NUM
				 	 WHERE 	C6.C6_FILIAL = F.F2_FILIAL
				 	 		AND C6.D_E_L_E_T_ != '*'
				 	 		AND C5.D_E_L_E_T_ != '*'
				 	 		AND (C5.C5_NOTA = F.F2_DOC OR (C5.C5_NOTA = F.F2_DOC AND C5.C5_NUM = C5.C5_XCOMPPE))
					) AS DESCONTO_MEDIO,
					(SELECT SUM(D2_QUANT)
					 FROM SD2010 WITH (NOLOCK)
					 WHERE 	D_E_L_E_T_ != '*'
						AND D2_DOC = F.F2_DOC
						AND D2_FILIAL = F.F2_FILIAL
					) AS QTD_ITENS,
					F.F2_VALBRUT,
				   	F.F2_VALMERC AS VALOR_LIQUIDO,
				   	F.F2_VEND1
			
			FROM SF2010 F WITH (NOLOCK)
			
			INNER JOIN SA1010 A ON F.F2_CLIENTE = A.A1_COD AND A.D_E_L_E_T_ != '*'
			INNER JOIN SA3010 A3 ON A3.A3_COD = F.F2_VEND1 AND A3.D_E_L_E_T_ != '*'
			LEFT OUTER JOIN SE4010 E4 ON E4.E4_FILIAL = F.F2_FILIAL AND E4.E4_CODIGO = F.F2_COND AND E4.D_E_L_E_T_ <> '*'
			
			WHERE 	(F.F2_FILIAL = '0101' OR F.F2_FILIAL = '0201')
				AND F.F2_SERIE = '1'
				AND (SELECT COUNT(E1_PARCELA) FROM SE1010 WITH (NOLOCK) WHERE E1_NUM = F.F2_DOC) > 0
				AND F.F2_EMISSAO BETWEEN '20150101' AND '20150231'
				AND F.D_E_L_E_T_ != '*'
				AND F.F2_VEND1 = '$representante'
				AND F.F2_CLIENTE <> '003718'
				
				ORDER BY F.F2_FILIAL, DATEPART(YY, F.F2_EMISSAO) DESC,
				DATEPART(mm, F.F2_EMISSAO) ASC";
	
	$rs2 = $conn->execute ( $retornaSQL2 );
	
	$num_columns2 = $rs2->Fields->Count ();
	
	$item = 0;
	$filial2 = null;
	$mes2 = null;
	
	$prazoMedioTotal = 0;
	$descontoMedioTotal = 0;
	$quantidadeItensTotal = 0;
	$valorLiquidoTotal = 0;
	$precoMedioTotal = 0;
	
	$prazoMedioXValorTotal = 0;
	$descontoMedioXValorTotal = 0;
	$precoMedioXValorTotal = 0;
	
	
	for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs2->Fields ( $i2 );
	}
	while ( ! $rs2->EOF ) {
		
		$item ++;
		$mes = $fld2 [0]->value;
		$ano = $fld2 [1]->value;
		$filial = $fld2 [2]->value;
		$cliente = $fld2 [3]->value;
		$razaoSocial = $fld2 [4]->value;
		$nota = $fld2 [5]->value;
		$pedido = $fld2 [6]->value;
		$emissao = $fld2 [7]->value;
		$comissao = $fld2 [8]->value;
		$prazoMedio = $fld2 [9]->value;
		$descontoMedio = $fld2 [10]->value;
		$quantidadeItens = $fld2 [11]->value;
		$valorBruto = $fld2 [12]->value;
		$valorLiquido = $fld2 [13]->value;
		$vendedor = $fld2 [14]->value;
		$precoMedio = $valorLiquido/$quantidadeItens;
		$mesPorExtenso = retornaMesPorExtenso($mes);
		
		$prazoMedioTotal += $prazoMedio;
		$descontoMedioTotal += $descontoMedio;
		$precoMedioTotal += $precoMedio;
		$quantidadeItensTotal += $quantidadeItens;
		$valorLiquidoTotal += $valorLiquido;
		
		$prazoMedioXValorTotal += $prazoMedio * $valorLiquido;
		$descontoMedioXValorTotal += $descontoMedio * $valorLiquido;
		$precoMedioXValorTotal += $precoMedio * $valorLiquido;
		
		if (empty ( $filial2 ) || ($filial2 != $filial)) {
			if ($filial == '0101') {
					echo "<tr><th colspan = '16' bgcolor = '#5bc0de'>Pradolux</th></tr>";
					

			} else {
				
					$item = 1;
					$prazoMedioTotal =0;
					$descontoMedioTotal =0;
					$precoMedioTotal =0;
					$quantidadeItensTotal =0;
					$valorLiquidoTotal =0;
					
					$prazoMedioXValorTotal =0;
					$descontoMedioXValorTotal =0;
					$precoMedioXValorTotal =0;
					
					$prazoMedioTotal += $prazoMedio;
					$descontoMedioTotal += $descontoMedio;
					$precoMedioTotal += $precoMedio;
					$quantidadeItensTotal += $quantidadeItens;
					$valorLiquidoTotal += $valorLiquido;
					
					$prazoMedioXValorTotal += $prazoMedio * $valorLiquido;
					$descontoMedioXValorTotal += $descontoMedio * $valorLiquido;
					$precoMedioXValorTotal += $precoMedio * $valorLiquido;
					
					echo "<tr><th colspan = '10'>Médias Simples</th><th>".number_format(($prazoMedioTotal/$item), 2, ',', '.')."</th>";
					echo "<th>".number_format((($descontoMedioTotal/$item)*100), 2, ',', '.')."</th>";
					echo "<th>".number_format(($precoMedioTotal/$item), 2, ',', '.')."</th>";
					echo "<th>".number_format(($quantidadeItensTotal/$item), 0, ',', '.')."</th>";
					echo "<th>".number_format(($valorLiquidoTotal/$item), 0, ',', '.')."</th><th></th></tr>";
						
					echo "<tr><th colspan = '10'>Médias Ponderadas</th><th>".number_format(($prazoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
					echo "<th>".number_format((($descontoMedioXValorTotal/$valorLiquidoTotal)*100), 2, ',', '.')."</th>";
					echo "<th>".number_format(($precoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
				
					echo "<tr><th colspan = '16' bgcolor = '#d9534f'>Luxparts</th></tr>";
			}
		}
		
		if (empty ( $mes2 ) || ($mes2 != $mes)) {
			echo "<tr><th colspan = '16' bgcolor = '#999'>$mesPorExtenso de $ano</th></tr>";
			echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
			echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
			echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";
		}
		$filial2 = $filial;
		$mes2 = $mes;
		
		echo "<tr><td>$item</td><td>$mes</td><td>$ano</td><td>$filial</td><td>$cliente</td><td>$razaoSocial</td>";
		echo "<td>$nota</td><td>$pedido</td><td>".date('d/m/Y', strtotime($emissao))."</td><td>$comissao %</td><td>$prazoMedio</td><td>" . number_format ( $descontoMedio * 100, 2, ',', '.' ) . "</td>";
		echo "<td>".number_format($precoMedio, 2, ',', '.')."</td><td>$quantidadeItens</td><td>".number_format(($valorLiquido), 2, ',', '.')."</td><td>$vendedor</td></tr>";
		$rs2->MoveNext ();
	}
	
	echo "<tr><th colspan = '10'>Médias Simples</th><th>".number_format(($prazoMedioTotal/$item), 2, ',', '.')."</th>";
	echo "<th>".number_format((($descontoMedioTotal/$item)*100), 2, ',', '.')."</th>";
	echo "<th>".number_format(($precoMedioTotal/$item), 2, ',', '.')."</th>";
	echo "<th>".number_format(($quantidadeItensTotal/$item), 0, ',', '.')."</th>";
	echo "<th>".number_format(($valorLiquidoTotal/$item), 0, ',', '.')."</th><th></th></tr>";
	
	echo "<tr><th colspan = '10'>Médias Ponderadas</th><th>".number_format(($prazoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
	echo "<th>".number_format((($descontoMedioXValorTotal/$valorLiquidoTotal)*100), 2, ',', '.')."</th>";
	echo "<th>".number_format(($precoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
	echo "<th></th>";
	echo "<th></th><th></th></tr>";
	
	echo "</tr></table>";
	
	// $rs2->MoveNext ();
	$rs2->Close ();
	$rs2 = null;
	// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	
	$rs->MoveNext ();
}

// $rs->MoveNext ();

echo "</div>";

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>