<?php 
// ob_end_flush();
$conn->open ( $connStr );
$mostrar = "Faltantes";
$item = 0;

echo "<div class='well'>";
echo "<h4>Faltas em Pedidos que estão em Separação e Conferência</h4><hr>";

// echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=faltasEmPedidos2' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe a ordem que deseja visualizar: </b></span>";
echo "<br/>";

$ordenacao = $_POST ['ordenacao'];

echo "<select name='ordenacao'>";
if (isset ( $ordenacao )) {
	echo "<option value='$ordenacao'>$ordenacao</option>";
} else {
	echo "<option value='-1'>Selecione a ordem</option>";
}

echo "<option value='Local'>Local</option>";
echo "<option value='Pedido'>Pedido</option>";
echo "<option value='Produto'>Produto</option>";

echo "</select><br />";

echo "<span><b>Informe o evento que deseja visualizar: </b></span><br />";
echo "<select name='estado'>";
if (isset ( $estado )) {
	echo "<option value='$estado'>$estado</option>";
} else {
	echo "<option value='-1'>Selecione o evento</option>";
}

echo "<option value='Separacao'>Separação</option>";
echo "<option value='Conferencia'>Conferência</option>";
echo "<option value='Todos'>Todos</option>";

echo "</select><br />";

echo "<input type='submit' value='Buscar'>";
echo "</div>";
/*xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/

echo "<div class='well'>";
$retornaSQL = " SELECT 
		
				C5.C5_NUM AS Pedido,
				C5.C5_CLIENTE AS NumCliente,
				substring(A1.A1_NOME, 0, 20) AS RazaoSocial,
				convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
				C5.C5_EVENTO,
				SUM(C6.C6_QTDVEN) AS QTD_PEDIDO,
				SUM(C6.C6_QTDRESE) AS SEPARADO,
				(SUM(C6.C6_QTDRESE) / SUM(C6.C6_QTDVEN))*100 AS PORCENTAGEM_COMPLETA

				FROM SC6010 C6 WITH (NOLOCK)

				INNER JOIN SC5010 C5 ON C5.C5_FILIAL = C6.C6_FILIAL AND C5.C5_NUM = C6.C6_NUM
				INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
				
				WHERE         C5.D_E_L_E_T_ <> '*' AND C6.D_E_L_E_T_ <> '*'
				               AND C5.C5_EVENTO IN ('6','5')
				               AND C5.C5_FILIAL = '0101'
				
				GROUP BY C5.C5_NUM, C5.C5_CLIENTE, C5.C5_EVENTO, C5.C5_FECENT, A1.A1_NOME
				
				ORDER BY PORCENTAGEM_COMPLETA DESC";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<table class='table table-hover'>";
echo "<tr>";
echo "<th>Pedido</th>";
echo "<th>Cliente</th>";
echo "<th>Previsão</th>";
echo "<th>Evento</th>";
echo "<th>Vendido</th>";
echo "<th>Reservado</th>";
echo "<th>%</th>";
echo "<th></th>";
echo "<th>Pedido</th>";
echo "<th>Cliente</th>";
echo "<th>Previsão</th>";
echo "<th>Evento</th>";
echo "<th>Vendido</th>";
echo "<th>Reservado</th>";
echo "<th>%</th>";
echo "</tr>";

echo "<tr>";
$contador = 0;

while ( !$rs->EOF ) {

	$pedido = $fld [0]->value;
	$cliente = $fld [1]->value;
	$razaoSocial = $fld [2]->value;
	$dataPrevista = $fld [3]->value;
	$evento = $fld [4]->value;
	$qtdVendida = $fld [5]->value;
	$qtdReservada = $fld [6]->value;
	$porcentagemCompleta = $fld [7]->value;
	
	if ($evento == 6) {
		$evento = "Conf.";
	} else if ($evento == 5) {
		$evento = "Sep.";
	}
	
	
	if ($contador%2 == 0) {
		echo "</tr><tr>";
	} else {
		echo "<td bgcolor = '#000000'></td>";
	}
	$contador++;

	echo "<td>$pedido</td>";
	echo "<td>$razaoSocial</td>";
	echo "<td>$dataPrevista</td>";
	echo "<td>$evento</td>";
	echo "<td>$qtdVendida</td>";
	echo "<td>$qtdReservada</td>";
	echo "<td> 
		<div class='progress'>
		  <div class='progress-bar progress-bar-success progress-bar-striped' role='progressbar' aria-valuenow='500' aria-valuemin='0' aria-valuemax='100' style='width: $porcentagemCompleta%;'>
		    ". number_format($porcentagemCompleta, 1) ." %
		  </div>
		</div>
	</td>";


$rs->MoveNext ();
}
echo "</tr>";
echo "</table>";
$rs->Close ();
$rs = null;

echo "</div>";

/*xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/
if (isset ( $estado ) && isset ( $ordenacao )) {
	echo "<div class='well'>";
	echo "<table class='table table-hover'><tr><th>Item</th><th>Pedido</th><th>Cliente</th><th>Data Prevista</th><th>Evento</th><th>Produto</th>";
	echo "<!--<th>Qtd Vendida</th><th>Qtd Reservada</th>--><th>Falta</th><th>Local Produção</th><th>Status</th><th>Estoque</th><th>Trânsito</th><th>+</th></tr>";
	
	// $ordenacao = "local";
	
	if ($ordenacao == "Local") {
		$orderBy = "ORDER BY B1.B1_LOCPAD, C5.C5_NUM";
	} else if ($ordenacao == "Pedido") {
		$orderBy = "ORDER BY C5.C5_NUM, B1.B1_LOCPAD";
	} else {
		$orderBy = "ORDER BY C6.C6_PRODUTO";
	}
	
	if ($estado == "Separacao") {
		$queryEstado = "(C5.C5_EVENTO = '5')";
	} else if ($estado == "Conferencia") {
		$queryEstado = "(C5.C5_EVENTO = '6')";
	} else {
		$queryEstado = "(C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5')";
	}
	
	$retornaProdutividadeSQL = "DECLARE @consultaSoMeta VARCHAR(1);
							DECLARE @filial VARCHAR(4);
							DECLARE @dataMeta DATE;
							DECLARE @localEstoque VARCHAR(6);
							DECLARE @localAreaTransito VARCHAR(6);
							DECLARE @localAreaTransitoExceto VARCHAR(6);
							
							
							SET @consultaSoMeta = '1';
							--SET @dataMeta = 'DATEPART(yymmdd, CURRENT_TIMESTAMP)';
							SET @dataMeta = GETDATE();
							--SET @dataMeta = '20150304';
							SET @filial = '0101';
							SET @localEstoque = 'AP-A01';
							SET @localAreaTransito = '%TR';
							SET @localAreaTransitoExceto = 'TR-TEM';
							
							SELECT  C5.C5_NUM AS Pedido, 
							        C5.C5_CLIENTE AS Cliente,
							        A1.A1_NOME AS RazaoSocial,
							        convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
							        C5.C5_EVENTO AS Evento,
							        C6.C6_PRODUTO AS Produto,
							        C6.C6_QTDVEN AS QtdVendida,
							        C6.C6_RESERVA AS Reserva,
							        C6.C6_QTDRESE AS QtdReservada,
							        B1.B1_LOCPAD AS LocalProducao,
							        ISNULL((SELECT B2.B2_QATU - B2.B2_RESERVA 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE @localEstoque)
							         				AND B2.D_E_L_E_T_ <> '*' 
							         				AND B1.B1_COD = B2.B2_COD
							        	   )
							        ,0) AS EstoqueDisponivel,
							        ISNULL((SELECT TOP 1 B2.B2_QATU 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE @localAreaTransito AND B2.B2_LOCAL NOT LIKE @localAreaTransitoExceto)
							        				AND B2.D_E_L_E_T_ <> '*' 
							        				AND B1.B1_COD = B2.B2_COD
							        				AND B2.B2_FILIAL = '0101'
							        	   )
							        ,0) AS TransitoDasAreas,
							        ISNULL(CASE(@consultaSoMeta) 
							        			WHEN '1' THEN ( SELECT CASE(ZZB_DTMETA) WHEN @dataMeta THEN 'NAO' ELSE 'SIM' END 
															    FROM ZZB010 ZZB WITH (NOLOCK)
															    WHERE ZZB.ZZB_FILIAL = C5.C5_FILIAL
															 	      AND ZZB.ZZB_NUM = C5.C5_NUM
													       		      AND ZZB.D_E_L_E_T_ <> '*'
															          AND ((ZZB.ZZB_DTMETA < @dataMeta
															                AND (SELECT TOP 1 C5_EVENTO FROM SC5010 WITH (NOLOCK) WHERE C5_FILIAL = ZZB.ZZB_FILIAL AND C5_NUM = ZZB.ZZB_NUM AND D_E_L_E_T_ <> '*') NOT IN ('1','3','4','7','8')
															                AND (SELECT TOP 1 D2_DOC FROM SD2010 WITH (NOLOCK) WHERE D2_FILIAL = ZZB.ZZB_FILIAL  AND D_E_L_E_T_ <> '*' AND D2_PEDIDO = ZZB.ZZB_NUM GROUP BY D2_PEDIDO,D2_DOC) IS NULL
															               )
															               OR
															               (ZZB.ZZB_DTMETA = @dataMeta)
															              )
															  )
												ELSE '' 
										   END
									,'') AS PedidoEstaAtrasadoNaMeta,
									C5.C5_CONDPAG AS CondicaoPagamento
							                                                               
							FROM SC5010 C5
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							
							INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
							                                                               
							WHERE 	A1.D_E_L_E_T_ <> '*' AND C6.D_E_L_E_T_ <> '*'
									AND $queryEstado
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = @filial
									AND (
											(@consultaSoMeta = '1' AND  C5.C5_NUM IN (SELECT ZZB.ZZB_NUM
																					  FROM ZZB010 ZZB WITH (NOLOCK)
																					  WHERE ZZB.ZZB_FILIAL = C5.C5_FILIAL
																							AND ZZB.D_E_L_E_T_ <> '*'
																							AND ((ZZB.ZZB_DTMETA < @dataMeta 
																								  AND (SELECT TOP 1 C5_EVENTO 
																								   	   FROM SC5010 WITH (NOLOCK)
																									   WHERE 	C5_FILIAL = ZZB.ZZB_FILIAL 
																												AND C5_NUM = ZZB.ZZB_NUM 
																												AND D_E_L_E_T_ <> '*'
																									  ) NOT IN ('1','3','4','7','8') 
																								  AND (SELECT TOP 1 D2_DOC  
																									   FROM SD2010 WITH (NOLOCK)
																									   WHERE 	D2_FILIAL = ZZB.ZZB_FILIAL  
																												AND D_E_L_E_T_ <> '*' 
																												AND D2_PEDIDO = ZZB.ZZB_NUM 
																									   GROUP BY D2_PEDIDO,D2_DOC
																									  ) IS NULL
																						         )
																						         OR
																						         (ZZB.ZZB_DTMETA = @dataMeta)
							           															)
							
																					 )
											)
											OR
											(@consultaSoMeta = '2')
										)	
									
							$orderBy";
	
	$rs = $conn->execute ( $retornaProdutividadeSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i2 = 0; $i2 < $num_columns; $i2 ++) {
		$fld [$i2] = $rs->Fields ( $i2 );
	}
	
	while ( ! $rs->EOF ) {
		
		$pedido = $fld [0]->value;
		$cliente = $fld [1]->value;
		$razaoSocial = $fld [2]->value;
		$dataPrevista = $fld [3]->value;
		$evento = $fld [4]->value;
		$produto = $fld [5]->value;
		$qtdVendida = $fld [6]->value;
		$reserva = $fld [7]->value;
		$qtdReservada = $fld [8]->value;
		$localProducao = $fld [9]->value;
		$estoque = $fld [10]->value;
		$transito = $fld [11]->value;
		$condicaoPagamento = $fld [12]->value;
		$falta = $qtdVendida - $qtdReservada;
		$status = NULL;
		
		if ($evento == 6) {
			$evento = "Em Conferência";
		} else if ($evento == 5) {
			$evento = "Em Separação";
		}
		
		if ($qtdVendida == $qtdReservada) {
			$status = "ok_16x16";
		} else {
			$status = "alert_16x16";
		}
		
		$corPagamento = NULL;
		if ($condicaoPagamento == '001' || $condicaoPagamento == '000') {
			$corPagamento = '#FFFFFF';
			echo "A vista";
		}
		
		if ($mostrar == "Todos") {
			$item ++;
			echo "<tr><td>$item</td><td>$pedido</td><td>$cliente - $razaoSocial</td><td>$dataPrevista</td>";
			echo "<td>$evento</td><td>$produto</td><!--<td>$qtdVendida</td><td>$qtdReservada</td>--><td>$falta</td><td>$localProducao</td>";
			echo "<td><img src = 'img/$status.png'></td><td>$estoque</td><td>$transito</td></tr>";
		} else if ($mostrar == "Faltantes") {
			if ($status == "alert_16x16") {
				$item ++;
				echo "<tr><td>$item</td><td bgcolor = '#00FF00'>$pedido</td><td>$cliente - $razaoSocial</td><td>$dataPrevista</td>";
				echo "<td>$evento</td><td bgcolor = '#00FF00'>$produto</td><!--<td>$qtdVendida</td><td>$qtdReservada</td>--><td bgcolor = '#00FF00'>$falta</td><td bgcolor = '#00FF00'>$localProducao</td>";
				echo "<td><img src = 'img/$status.png'></td><td>$estoque</td><td>$transito</td>";
				//echo "<td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='.bs-example-modal-lg'>+</button></td>";
				echo "</tr>";
			}
		}
		$rs->MoveNext ();
	}
	
	$rs->MoveNext ();
	
	echo "</tr></table>";
	echo "</div>";
}

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>