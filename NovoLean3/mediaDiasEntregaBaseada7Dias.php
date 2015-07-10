<?php
//ob_end_flush();
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Média de dias de entrega baseada em 7 dias</h4><hr>";

//echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=mediaDiasEntregaBaseada7Dias' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o dia que deseja visualizar: </b></span>";
echo "<br/>";

//$data = $_GET ['dia'];
$data = $_POST ['data'];
$dataFormatada = substr($data, 0, 4)."".substr($data, 5, 2)."".substr($data, 8, 2);

echo "<div class='form-group'>";
echo "<label for='data'>Data Inicial</label>";
echo "<input type='date' class='form-control' name='data' placeholder='Data Inicial' min='2015-01-01'>";
echo "</div>";

echo "</select>";


// Combobox do setor
/*
 * $instrucaoSQL = "SELECT NR.NNR_CODIGO, NR.NNR_DESCRI FROM NNR010 NR WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%' AND NR.NNR_DESCRI LIKE '%PRODUCAO%' ORDER BY NR.NNR_DESCRI"; $rs = $conn->execute ( $instrucaoSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); } echo "<select name='setor'>"; echo "<option value='-1'>Selecione o local</option>"; while ( ! $rs->EOF ) { echo "<option value=" . $fld [0]->value . ">" . $fld [1]->value . "</option>"; $rs->MoveNext (); } $rs->Close (); $rs = null; echo "</select>";
 */
echo  $data;
echo $dataFormatada;

echo "<input type='submit' value='Buscar'>";
echo "</div>";



	

	echo "<table class='table table-hover'><tr><th>Item</th><th>Pedido</th><th>Entrada</th><th>Nota</th><th>Previsão</th><th>Dias Previstos</th><th>Saída</th><th>Dias Entrega</th><th>Valor</th><th>Valor X Dias</th><th>Razão Social</th><th>Cliente</th></tr>";
	
	$retornaSQL = "SELECT 

      C5.C5_NUM AS PEDIDO,
      C5.C5_EMISSAO AS ENTRADA, 
      C5.C5_NOTA AS NOTA,
      C5.C5_FECENT AS PREVISAO,
      DATEDIFF(DAY, C5.C5_EMISSAO, C5.C5_FECENT) AS DiasPrevistos,
      F2.F2_EMISSAO AS SAIDA,
      DATEDIFF(DAY, C5.C5_EMISSAO, F2.F2_EMISSAO) AS DiasEntrega,
      F2.F2_VALMERC AS ValorSemImpostos,
      (DATEDIFF(DAY, C5.C5_EMISSAO, F2.F2_EMISSAO)*F2.F2_VALMERC) AS DiasXValor,
      A1.A1_NOME,
      A1.A1_COD,
      C5.C5_XPEDAGE
      
FROM SC5010 C5

INNER JOIN SF2010 F2 ON C5.C5_NOTA = F2.F2_DOC
INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD

WHERE         F2.F2_EMISSAO BETWEEN DATEADD ( DAY , -7, '$dataFormatada') AND '$dataFormatada'
			   --AND DATEPART(yy,F2.F2_EMISSAO) = DATEPART(yy, CURRENT_TIMESTAMP)
			AND DATEPART(yy,F2.F2_EMISSAO) = DATEPART(yy, '$dataFormatada')
               AND C5.C5_EVENTO != '4' AND C5.C5_EVENTO != '3'
               AND C5.C5_FILIAL = '0101'
               --AND DATEDIFF(DAY, C5.C5_EMISSAO, F2.F2_EMISSAO) < 20
               AND DATEDIFF(DAY, C5.C5_EMISSAO, F2.F2_EMISSAO) > 0
               AND F2.F2_VEND1 <> '999999' AND F2.F2_VEND1 <> '999998' AND F2.F2_VEND1 <> '999997'
               AND F2.F2_FILIAL = '0101'
               AND C5.C5_XPEDAGE <> 'S'
               AND F2.F2_VALMERC > '0' --Não pegar pedidos com valor mercadoria = 0 que seriam pedidos de RC
               AND A1.A1_COD <> '000673' --Não pegar cliente Pradolux
               AND C5.C5_FECENT <> '' --Não pegar pedidos sem data prevista
               AND DATEDIFF(DAY, C5.C5_EMISSAO, C5.C5_FECENT) >= 0 --Corrigir erro de pedidos que foi cadastrada dataPrevista antes da dataEntrada
               AND DATEDIFF(DAY, C5.C5_EMISSAO, C5.C5_FECENT) <= 20 --Não pegar pedidos com dataPrevista maior que x dias
               AND A1.A1_EST <> 'EX' -- não pegar pedidos de exportação";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns2 = $rs->Fields->Count ();
	
	for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs->Fields ( $i2 );
	}
	
	$valorSemImpostosTotal = 0.00;
	$diasXValorTotal = 0;
	$item = 0;
	
	while ( ! $rs->EOF ) {
	
		$pedido = $fld2 [0]->value;
		$entrada = $fld2 [1]->value;
		$nota = $fld2 [2]->value;
		$previsao = $fld2 [3]->value;
		$diasPrevistos = $fld2 [4]->value;
		$saida = $fld2 [5]->value;
		$diasEntrega = $fld2 [6]->value;
		$valorSemImpostos = $fld2 [7]->value;
		$diasXValor = $fld2 [8]->value;
		$nome = $fld2 [9]->value;
		$codigo = $fld2 [10]->value;
		$item++;
		$valorSemImpostosTotal += $valorSemImpostos;
		$diasXValorTotal += $diasXValor;
	
		echo "<tr><td>$item</td><td>$pedido</td><td>$entrada</td><td>$nota</td>";
		echo "<td>$previsao</td><td>$diasPrevistos</td><td>$saida</td><td>$diasEntrega</td><td>$valorSemImpostos</td>";
		echo "<td>$diasXValor</td><td>$nome</td><td>$codigo</td></tr>";
		$rs->MoveNext ();
	}
	
	$diasParaEntrega = $diasXValorTotal / $valorSemImpostosTotal;
	echo "<tfoot><td colspan = '8'>Totais</td>";
	echo "<td>".number_format($valorSemImpostosTotal, 2, ',', '.')."</td>";
	echo "<td>".number_format($diasXValorTotal, 2, ',', '.')."</td>";
	echo "<td><button type='button' class='btn btn-lg btn-primary' disabled='disabled'>".number_format($diasParaEntrega, 2, ',', '.')."</button></td>";
	echo "</tfoot></table>";
	
	$rs->Close ();
	$rs = null;
	

;



$conn->Close ();
$conn = null;
?>