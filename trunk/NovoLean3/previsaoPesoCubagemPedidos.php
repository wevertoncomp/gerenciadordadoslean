<?php
//ob_end_flush();
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Previsão de peso e cubagem de pedidos</h4>";

//echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=previsaoPesoCubagemPedidos' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o pedido que deseja visualizar: </b></span><br />";
echo "<br/>";

//$data = $_GET ['dia'];
$pedido = $_POST ['pedido'];

// Combobox da data

$instrucaoSQL = "	SELECT C5.C5_NUM, A1.A1_NOME FROM 

					SC5010 C5 WITH (NOLOCK)
					
					INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
					
					WHERE (C5.C5_EVENTO = '9' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '7' OR C5.C5_EVENTO = '8')
					AND C5.D_E_L_E_T_ <> '*' AND (C5.C5_VEND1 <> '999999' AND C5.C5_VEND1 <> '999998' AND C5.C5_VEND1 <> '999997')
					AND C5.C5_TIPO = 'N'
					AND C5.C5_FILIAL = '0101'
					
					ORDER BY C5.C5_NUM";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<select name='pedido' class='form-control'>";
if (isset ( $pedido )) {
	echo "<option value='$pedido'>$pedido</option>";
} else {
	echo "<option value='-1'>Selecione o pedido desejado</option>";
}
while ( ! $rs->EOF ) {

	//for($i = 0; $i < $num_columns; $i ++) {
	echo "<option value=" . $fld [0]->value . ">" . $fld [0]->value . " - " . $fld [1]->value . "</option>";
	//}
	$rs->MoveNext ();
}

echo "</select>";

$rs->Close ();
$rs = null;

echo "</select><br />";

echo "<input type='submit' value='Buscar' class='btn btn-default'>";

echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'>";
	echo "<tr><th>Item</th><th>Produto</th><th>Qtde</th><th>Peso Unit.</th><th>Peso Produto</th>";
	echo "<th>Embalagem</th><th>Peso Emb.</th><th>Transporte</th><th>Emb. Transporte</th><th>Peso Total</th><th>Volume</th></tr>";
	
	$retornaSQL = "	SELECT 

					C6.C6_PRODUTO,
					C6.C6_QTDVEN,
					B1.B1_PESO,
					B5.B5_CODEMB1,
					B5.B5_EMB1,
					B1.B1_QE,
					B5_1.B5_CODEMB1,
					B5_1.B5_EMB1,
					B1_1.B1_QE,
					B1_1.B1_PESO,
					B1_2.B1_PESO,
					B5_2.B5_COMPR,
					B5_2.B5_LARG,
					B5_2.B5_ESPESS,
					A1.A1_CGC,
					A1.A1_CEP,
					C6.C6_VALOR
					
					
					FROM SC6010 C6 WITH (NOLOCK)
					INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
					INNER JOIN SB5010 B5 ON B1.B1_COD = B5.B5_COD
					INNER JOIN SB1010 B1_1 ON B5.B5_CODEMB1 = B1_1.B1_COD
					INNER JOIN SB5010 B5_1 ON B1_1.B1_COD = B5_1.B5_COD
					INNER JOIN SB1010 B1_2 ON B5_1.B5_CODEMB1 = B1_2.B1_COD
					INNER JOIN SB5010 B5_2 ON B1_2.B1_COD = B5_2.B5_COD
					INNER JOIN SA1010 A1 ON C6.C6_CLI = A1.A1_COD
					
					WHERE C6.C6_NUM = '$pedido'
					AND C6.D_E_L_E_T_ <> '*'
					
					ORDER BY B5.B5_EMB1, B1.B1_COD";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$item = 0;
	$pesoProdutosLiquidoTotal = 0;
	$quantidadeTotalProdutos = 0;
	$embalagem2 = null;
	$totalCaixas = 0;
	$totalCaixasEspecifica = 0;
	$totalCaixasTransporte = 0;
	$pesoTotalCaixas = 0;
	$pesoTotalCaixasTransporte = 0;
	$pesoTotal = 0;
	$volume = 0;
	$valorTotalPedido = 0;
	$fatorAjustePeso = 1;
	$fatorAjusteVolume = 1;
	
	while ( ! $rs->EOF ) {
	
		$item++;
		$produto = $fld [0]->value;
		$quantidade = $fld [1]->value;
		$pesoUnitario = $fld [2]->value;
		$pesoTotalLiquidoProduto = ($quantidade * $pesoUnitario)/1000;
		$codigoEmbalagem = $fld [3]->value;
		$embalagem = $fld [4]->value;
		$embalagem = substr($embalagem, 0, 3);
		$quantidadeEmbalagem = $fld [5]->value;
		$caixas = ($quantidade / $quantidadeEmbalagem);
		$embalagemTransporte = $fld [7]->value;
		$embalagemTransporte = substr($embalagemTransporte, 0, 3);
		$quantidadeEmbalagemTransporte = $fld [8]->value;
		$caixasTransporte = ($caixas / $quantidadeEmbalagemTransporte);
		$pesoEmbalagem = $fld [9]->value;
		$pesoEmbalagemPorProduto = ($caixas * $pesoEmbalagem)/1000;
		$pesoEmbalagemTransporte = $fld [10]->value;
		$pesoEmbalagemTransportePorProduto = ($caixasTransporte * $pesoEmbalagemTransporte) / 1000;
		$pesoTotalPorProduto = $pesoTotalLiquidoProduto + $pesoEmbalagemPorProduto + $pesoEmbalagemTransportePorProduto;
		
		$comprimento = $fld [11]->value;
		$largura = $fld [12]->value;
		$altura = $fld [13]->value;
		
		$cnpjCliente = $fld [14]->value;
		$cepCliente = $fld [15]->value;
		$valorProduto = $fld [16]->value;
		
		$volumePorProduto = ($caixasTransporte * ($comprimento * $largura * $altura)) / 1000000000;
		$volume += $volumePorProduto;
		
		$pesoProdutosLiquidoTotal += $pesoTotalLiquidoProduto;
		$totalCaixas += $caixas;
		$totalCaixasTransporte += $caixasTransporte;
		$pesoTotalCaixas += $pesoEmbalagemPorProduto;
		$pesoTotalCaixasTransporte += $pesoEmbalagemTransportePorProduto;
		$pesoTotal += $pesoTotalPorProduto;
		$valorTotalPedido += $valorProduto;
		
		if (empty($embalagem2) || $embalagem != $embalagem2) {
			echo "<tr><td colspan = '11' bgcolor = '#5bc0de'><h4>Caixa $embalagem</h4></td></tr>";
		}
		$embalagem2 = $embalagem;

	
		echo "<tr><td>$item</td><td>$produto</td><td>$quantidade</td><td>$pesoUnitario</td><td>$pesoTotalLiquidoProduto Kg</td>";
		echo "<td>$embalagem ". number_format($caixas, 2) ." cx</td><td>". number_format($pesoEmbalagemPorProduto, 2) ." Kg</td>";
		echo "<td>$embalagemTransporte  ". number_format($caixasTransporte, 2) ." cx</td>";
		echo "<td>". number_format($pesoEmbalagemTransportePorProduto, 2) ." Kg</td><td>". number_format($pesoTotalPorProduto, 2) ." Kg</td>";
		echo "<td>". number_format($volumePorProduto, 4) ." m³</td></tr>";
		$rs->MoveNext ();
	}
	
	//$rs->MoveNext ();
	
	echo "<tr><th colspan = '2'>Totais</th><th>Qtde</th><th>Peso Unit.</th><th>$pesoProdutosLiquidoTotal Kg</th>";
	echo "<th>". number_format($totalCaixas, 0) ." caixas</th><th>". number_format($pesoTotalCaixas, 2) ." Kg</th><th bgcolor = '#5bc0de'>". number_format($totalCaixasTransporte, 2) ." caixas</th>";
	echo "<th>". number_format($pesoTotalCaixasTransporte, 2) ." Kg</th><th  bgcolor = '#5bc0de'>". number_format($pesoTotal, 2) ." Kg</th>";
	echo "<th bgcolor = '#5bc0de'>". number_format($volume, 2) ." m³</th></tr>";
	
	$pesoTotalAjustado = $pesoTotal*$fatorAjustePeso;
	
	echo "<tr><th colspan = '11'></th></tr>";
	echo "<tr><th colspan = '6'></th><th colspan = '3' bgcolor = '#5cb85c'>Quantidade total de volumes</th><th colspan = '2' bgcolor = '#5cb85c'>". number_format($totalCaixasTransporte, 2) ." volumes</th></tr>";
	echo "<tr><th colspan = '6'></th><th colspan = '3' bgcolor = '#5cb85c'>Peso total com fator de ajuste -> $fatorAjustePeso</th><th colspan = '2' bgcolor = '#5cb85c'>". number_format($pesoTotalAjustado, 2) ." Kg</th></tr>";
	echo "<tr><th colspan = '6'></th><th colspan = '3' bgcolor = '#5cb85c'>Volume total com fator de ajuste -> $fatorAjusteVolume</th><th colspan = '2' bgcolor = '#5cb85c'>". number_format($volume * $fatorAjusteVolume, 2) ." m³</th></tr>";
	
echo "</table>";
echo "</div>";

$rs->Close ();
$rs = null;

echo "<div class = 'well'>";

$totalCaixasTransporte = round($totalCaixasTransporte, 0);
$pesoTotalAjustado = round ($pesoTotalAjustado, 0);

$cnpj = '18925438000146'; // cnpj da empresa de origem
$empOrigem = '2'; // valor fixo solicitado pela BrasPress
$cepOrigem = '37701386';
$cepDestino = $cepCliente;
$cnpjRemetente = '18925438000146';
$cnpjDestinatario = $cnpjCliente;
$tipoFrete = '1'; // 1 para CIF ou 2 para FOB
$peso = $pesoTotalAjustado; // Peso em Kg
$valorNF = $valorTotalPedido; // Valor da nota fiscal da carga
$volumes = $totalCaixasTransporte; // Quantidade de volumes da carga
$modal = 'R'; // R para Rodoviário e A para Aéreo

echo "<h4>Dados adicionais para cotação do frete</h4>";

echo "<h3><a href= 'http://www.braspress.com.br/cotacaoXml?param=$cnpj,$empOrigem,$cepOrigem,$cepDestino,$cnpjRemetente,$cnpjDestinatario,$tipoFrete,$peso,$valorNF,$volumes,$modal'>Clique para estimar frete via BrasPress</a></h3>";

/*function calcula_frete ($P_CIC_NEGC, $P_CEP, $P_VLR_CARG,$P_PESO_KG, $P_CUBG, $P_COG_REGN, $P_UF){

	// URL do WebService
	$jamef = "http://www.jamef.com.br/internet/e-comerce/calculafrete.asp".$P_CIC_NEGC."&sP_CIC_NEGC=".$P_CEP."&nP_CEP=".$P_VLR_CARG."&nP_VLR_CARG".$P_PESO_KG."&nP_PESO_KG=".$P_CUBG."&nP_CUBG=".$P_COG_REGN."&sP_COG_REGN=".$P_UF."&sP_UF=";
	// Carrega o XML de Retorno

	$xml = simplexml_load_file($jamef);

	echo $jamef;
}

calcula_frete(12417449000, 89202400, 150,00, 10, 0.9333, 111, SC);*/

echo "</div>";


$conn->Close ();
$conn = null;
?>