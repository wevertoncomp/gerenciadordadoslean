<?php

function preencheHeaderTabela() {
	
}

// ob_end_flush();
$conn->open ( $connStr );

echo "<div class='well'>";
echo "<h3>Desconto, Preço e Prazo Médio</h3>";

echo "<form action='?pg=descontoPrecoePrazoMedio' method = 'post' class='form-inline'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o intervalo de datas que deseja visualizar: </b></span>";
echo "<br/>";

$dataInicial = $_POST ['dataInicial'];
$dataFinal = $_POST ['dataFinal'];

//$dataInicial = '20150222';
//$dataFinal = '20150228';

// TextField
?>
<div class="form-group">
<label for="dataInicial">Data Inicial</label>
<input type="date" class="form-control" name="dataInicial" placeholder="Data Inicial" min="2014-01-01">
</div>
<div class="form-group">
<label for="dataFinal">Data Final</label>
<input type="date" class="form-control" name="dataFinal" placeholder="Data Final">
</div>
<?php 

echo "<br />";

echo "<input type='submit' value='Buscar'>";
echo "</form>";
echo "</div>";

if (isset($dataInicial) && isset($dataFinal)) {
	
	$dataInicial = str_replace('-', '', $dataInicial);
	$dataFinal = str_replace('-', '', $dataFinal);
	
	$dataInicialFormatada = substr($dataInicial, 6, 2) ."/". substr($dataInicial, 4, 2) ."/". substr($dataInicial, 0, 4);
	$dataFinalFormatada = substr($dataFinal, 6, 2) ."/". substr($dataFinal, 4, 2) ."/". substr($dataFinal, 0, 4);;

echo "<div class='well'>";
echo "<h4>Mostrando dados de $dataInicialFormatada até $dataFinalFormatada</h4>";


echo "<a class='page-scroll' href = '#A'>Região A</a> | <a class='page-scroll' href = '#B'>Região B</a>";
echo "<p>* Pedidos que passaram por aprovação da direção . Válido somente a partir de 10/10/2014 quando foi feita a personalização e estas aprovações começaram a ser gravadas no banco de dados. Pedidos anteriores a esta data deve ser analisado o valor, desconto e prazo de pagamento.</p>";
echo "</div>";

echo "<div class='well'>";

$retornaSQL = "	SELECT 

					A3.A3_COD, A3.A3_NOME, A3.A3_NREDUZ, RTRIM(A3.A3_REGIAO)
					
					FROM SA3010 A3 WITH (NOLOCK)
					
					WHERE A3.D_E_L_E_T_ <> '*' 
					AND A3.A3_MSBLQL <> '1'
					AND A3.A3_COD <> '999997'
					AND A3.A3_COD <> '999998'
					AND A3.A3_COD <> '999999'
					--AND A3.A3_REGIAO LIKE '%B%'
					
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
	
	$prazoMedioRepresentante = 0;
	$descontoMedioRepresentante = 0;
	$precoMedioRepresentante = 0;
	
	$prazoMedioXValorTotalRepresentante = 0;
	$descontoMedioXValorTotalRepresentante = 0;
	$precoMedioXValorTotalRepresentante = 0;
	$valorLiquidoTotalRepresentante = 0;
	
	if (empty ( $regiao2 ) || ($regiao2 != $regiao)) {
		echo "<h2><a name = '$regiao'></a>Região $regiao</h2> <a class='page-scroll' href = '#topo'>Voltar ao topo</a>";
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
				   	F.F2_VEND1,
				   	(SELECT TOP 1 Z5.ZZ5_NOMUSU FROM ZZ5010 Z5 WITH (NOLOCK)
					INNER JOIN SD2010 D2 ON Z5.ZZ5_PEDIDO = D2.D2_PEDIDO
					WHERE (Z5.ZZ5_NOMUSU = '9001' OR Z5.ZZ5_NOMUSU = '9002' OR Z5.ZZ5_NOMUSU = '9004')
					AND D2_DOC = F.F2_DOC AND D2_FILIAL = F.F2_FILIAL) AS Aprovador
			
			FROM SF2010 F WITH (NOLOCK)
			
			INNER JOIN SA1010 A ON F.F2_CLIENTE = A.A1_COD AND A.D_E_L_E_T_ != '*'
			INNER JOIN SA3010 A3 ON A3.A3_COD = F.F2_VEND1 AND A3.D_E_L_E_T_ != '*'
			LEFT OUTER JOIN SE4010 E4 ON E4.E4_FILIAL = F.F2_FILIAL AND E4.E4_CODIGO = F.F2_COND AND E4.D_E_L_E_T_ <> '*'
			
			WHERE 	(F.F2_FILIAL = '0101' OR F.F2_FILIAL = '0201')
				AND F.F2_SERIE = '1'
				AND (SELECT COUNT(E1_PARCELA) FROM SE1010 WITH (NOLOCK) WHERE E1_NUM = F.F2_DOC) > 0
				AND F.F2_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
				AND F.D_E_L_E_T_ != '*'
				AND F.F2_VEND1 = '$representante'
				AND F.F2_CLIENTE <> '003718'
				
				ORDER BY DATEPART(YY, F.F2_EMISSAO) DESC, DATEPART(mm, F.F2_EMISSAO) DESC, F.F2_FILIAL 
				";
	
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
	
	$prazoMedioRepresentante = 0;
	$descontoMedioRepresentante = 0;
	$precoMedioRepresentante = 0;
	
	$prazoMedioXValorTotal = 0;
	$descontoMedioXValorTotal = 0;
	$precoMedioXValorTotal = 0;
	
	
	for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs2->Fields ( $i2 );
	}
	while ( ! $rs2->EOF ) {
		
		$mes = $fld2 [0]->value;
		$ano = $fld2 [1]->value;
		$filial = $fld2 [2]->value;
		$mesPorExtenso = retornaMesPorExtenso($mes);
		
		if (empty ( $filial2 ) || ($filial2 != $filial)) {
			if ($filial == '0101') {
				echo "<tr><th colspan = '16' bgcolor = '#5bc0de'>Pradolux</th></tr>";
				/*echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
				echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
				echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";*/
					
		
			} else {
					
				/*$prazoMedioTotal += $prazoMedio;
				$descontoMedioTotal += $descontoMedio;
				$precoMedioTotal += $precoMedio;
				$quantidadeItensTotal += $quantidadeItens;
				$valorLiquidoTotal += $valorLiquido;
					
				$prazoMedioXValorTotal += $prazoMedio * $valorLiquido;
				$descontoMedioXValorTotal += $descontoMedio * $valorLiquido;
				$precoMedioXValorTotal += $precoMedio * $valorLiquido;*/
					
				echo "<tr><th colspan = '10'>Médias Simples</th><th>".number_format(($prazoMedioTotal/$item), 2, ',', '.')."</th>";
				echo "<th>".number_format((($descontoMedioTotal/$item)*100), 2, ',', '.')."</th>";
				echo "<th>".number_format(($precoMedioTotal/$item), 2, ',', '.')."</th>";
				echo "<th>".number_format(($quantidadeItensTotal/$item), 0, ',', '.')."</th>";
				echo "<th>".number_format(($valorLiquidoTotal/$item), 0, ',', '.')."</th><th></th></tr>";
		
				echo "<tr><th colspan = '10'>Médias Ponderadas</th><th>".number_format(($prazoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
				echo "<th>".number_format((($descontoMedioXValorTotal/$valorLiquidoTotal)*100), 2, ',', '.')."</th>";
				echo "<th>".number_format(($precoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
		
				echo "<tr><th colspan = '16' bgcolor = '#d9534f'>Luxparts</th></tr>";
				echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
				echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
				echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";
				$item = 0;
				$prazoMedioTotal =0;
				$descontoMedioTotal =0;
				$precoMedioTotal =0;
				$quantidadeItensTotal =0;
				$valorLiquidoTotal =0;
					
				$prazoMedioXValorTotal =0;
				$descontoMedioXValorTotal =0;
				$precoMedioXValorTotal =0;
			}
		}
		$filial2 = $filial;
		
		if (empty ( $mes ) || ($mes2 != $mes)) {
			if (empty($mes2)) {
				echo "<tr><th colspan = '16' bgcolor = '#999'>$mesPorExtenso de $ano</th></tr>";
				
				echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
				 echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
				echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";
					
		
			} else {
				/*$prazoMedioTotal += $prazoMedio;
				 $descontoMedioTotal += $descontoMedio;
				$precoMedioTotal += $precoMedio;
				$quantidadeItensTotal += $quantidadeItens;
				$valorLiquidoTotal += $valorLiquido;
					
				$prazoMedioXValorTotal += $prazoMedio * $valorLiquido;
				$descontoMedioXValorTotal += $descontoMedio * $valorLiquido;
				$precoMedioXValorTotal += $precoMedio * $valorLiquido;*/
					
				echo "<tr><th colspan = '10'>Médias Simples</th><th>".number_format(($prazoMedioTotal/$item), 2, ',', '.')."</th>";
				echo "<th>".number_format((($descontoMedioTotal/$item)*100), 2, ',', '.')."</th>";
				echo "<th>".number_format(($precoMedioTotal/$item), 2, ',', '.')."</th>";
				echo "<th>".number_format(($quantidadeItensTotal/$item), 0, ',', '.')."</th>";
				echo "<th>".number_format(($valorLiquidoTotal/$item), 0, ',', '.')."</th><th></th></tr>";
		
				echo "<tr><th colspan = '10'>Médias Ponderadas</th><th>".number_format(($prazoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
				echo "<th>".number_format((($descontoMedioXValorTotal/$valorLiquidoTotal)*100), 2, ',', '.')."</th>";
				echo "<th>".number_format(($precoMedioXValorTotal/$valorLiquidoTotal), 2, ',', '.')."</th>";
		
				echo "<tr><th colspan = '16' bgcolor = '#999'>$mesPorExtenso de $ano</th></tr>";
				echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
				echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
				echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";
				$item = 0;
				$prazoMedioTotal =0;
				$descontoMedioTotal =0;
				$precoMedioTotal =0;
				$quantidadeItensTotal =0;
				$valorLiquidoTotal =0;
					
				$prazoMedioXValorTotal =0;
				$descontoMedioXValorTotal =0;
				$precoMedioXValorTotal =0;
			}
		}
		$mes2 = $mes;
		
		
		$item ++;
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
		$aprovador = NULL;
		$aprovador = $fld2 [15]->value;
		
		if (isset($aprovador)) {
			$aprovador = "*";
		}
		$precoMedio = $valorLiquido/$quantidadeItens;
		
		$prazoMedioTotal += $prazoMedio;
		$descontoMedioTotal += $descontoMedio;
		$precoMedioTotal += $precoMedio;
		$quantidadeItensTotal += $quantidadeItens;
		$valorLiquidoTotal += $valorLiquido;
		
		$prazoMedioXValorTotal += $prazoMedio * $valorLiquido;
		$descontoMedioXValorTotal += $descontoMedio * $valorLiquido;
		$precoMedioXValorTotal += $precoMedio * $valorLiquido;
		
		// Variáveis para calcular valor total por representante
		$prazoMedioRepresentante += $prazoMedio;
		$descontoMedioRepresentante += $descontoMedio;
		$precoMedioRepresentante += $precoMedio;
		$valorLiquidoTotalRepresentante += $valorLiquido;
		
		$prazoMedioXValorTotalRepresentante += $prazoMedio * $valorLiquido;
		$descontoMedioXValorTotalRepresentante += $descontoMedio * $valorLiquido;
		$precoMedioXValorTotalRepresentante += $precoMedio * $valorLiquido;
		
		/*if (empty ( $mes2 ) || ($mes2 != $mes)) {
			echo "<tr><th colspan = '16' bgcolor = '#999'>$mesPorExtenso de $ano</th></tr>";
			echo "<tr><th>Item</th><th>Mês</th><th>Ano</th><th>Filial</th>";
			echo "<th>Cliente</th><th>Razão Social</th><th>Nota</th><th>Pedido</th><th>Emissão</th>";
			echo "<th>Comissão</th><th>Prazo M</th><th>Desconto M</th><th>Preço M</th><th>Itens</th><th>Val. Liq.</th><th>Vendedor</th></tr>";
		}
		$mes2 = $mes;*/
		
		echo "<tr><td>$item</td><td>$mes</td><td>$ano</td><td>$filial</td><td>$cliente</td><td>$razaoSocial</td>";
		echo "<td>$nota</td><td>$pedido</td><td>".date('d/m/Y', strtotime($emissao))."</td><td>$comissao %</td><td>$prazoMedio</td><td>" . number_format ( $descontoMedio * 100, 2, ',', '.' ) . "</td>";
		echo "<td>".number_format($precoMedio, 2, ',', '.')."</td><td>$quantidadeItens</td><td>".number_format(($valorLiquido), 2, ',', '.')."</td><td>$vendedor $aprovador</td></tr>";
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
	
	echo "<tr><th colspan = '10'  bgcolor = '#00FF00'>Médias Ponderadas do Representante</th>";
	echo "<th bgcolor = '#00FF00'>".number_format(($prazoMedioXValorTotalRepresentante/$valorLiquidoTotalRepresentante), 2, ',', '.')."</th>";
	echo "<th bgcolor = '#00FF00'>".number_format((($descontoMedioXValorTotalRepresentante/$valorLiquidoTotalRepresentante)*100), 2, ',', '.')."</th>";
	echo "<th bgcolor = '#00FF00'>".number_format(($precoMedioXValorTotalRepresentante/$valorLiquidoTotalRepresentante), 2, ',', '.')."</th>";
	echo "<th bgcolor = '#00FF00'></th>";
	echo "<th bgcolor = '#00FF00'></th><th bgcolor = '#00FF00'></th></tr>";
	
	echo "</tr></table>";
	
	// $rs2->MoveNext ();
	$rs2->Close ();
	$rs2 = null;
	// xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
	
	$rs->MoveNext ();
}

// $rs->MoveNext ();

echo "</div>";
} else {
	echo "<div class='well'>";
	echo "<h4>Preencha as datas para visualizar</h4>";
	echo "</div>";
}

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>