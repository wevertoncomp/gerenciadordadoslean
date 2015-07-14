<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Indicadores</h4><hr>";

echo "<form action='?pg=indicadores' method = 'post' class='form-inline'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o intervalo de datas que deseja visualizar: </b></span>";
echo "<br/>";

$dataInicial = $_POST ['dataInicial'];
$dataFinal = $_POST ['dataFinal'];

$dataInicial = str_replace('-', '', $dataInicial);
$dataFinal = str_replace('-', '', $dataFinal);

$dataInicialFormatada = substr($dataInicial, 6, 2) ."/". substr($dataInicial, 4, 2) ."/". substr($dataInicial, 0, 4);
$dataFinalFormatada = substr($dataFinal, 6, 2) ."/". substr($dataFinal, 4, 2) ."/". substr($dataFinal, 0, 4);;

// TextField
?>
<div class="form-group">
<label for="dataInicial">Data Inicial</label>
<input type="date" class="form-control" name="dataInicial" placeholder="Data Inicial" min="2015-01-01">
</div>
<div class="form-group">
<label for="dataFinal">Data Final</label>
<input type="date" class="form-control" name="dataFinal" placeholder="Data Final">
</div>
<?php 

echo "<br />";

echo "<input type='submit' value='Buscar'>";
echo "</form>";

echo "<h4>Mostrando dados de $dataInicialFormatada até $dataFinalFormatada</h4>";

echo "</div>";
/* Peso total de injetados nas áreas do porta palete */
$instrucaoSQL = "SELECT
				SUM(B2.B2_QATU) AS Quantidade,
				SUM(B2.B2_QATU*B1.B1_PESO)/1000 AS PesoTotal
				
				FROM SB2010 B2
					INNER JOIN SB1010 B1
						ON B2.B2_COD = B1.B1_COD
				
				WHERE B2.B2_FILIAL = '0101'
					AND B2.D_E_L_E_T_ <> '*'
					AND B1.D_E_L_E_T_ <> '*'
					AND B2.B2_LOCAL <> '01' AND B2.B2_LOCAL <> '95' AND B2.B2_LOCAL <> '98' AND B2.B2_LOCAL <> ''
					AND B2.B2_LOCAL <> '06' AND B2.B2_LOCAL <> '05' AND B2.B2_LOCAL <> '04' AND B2.B2_LOCAL <> '02'
					AND B2.B2_LOCAL <> '000001' AND B2.B2_LOCAL <> '4' AND B2.B2_LOCAL <> '21' AND B2.B2_LOCAL <> 'ADM-01'
					AND B2.B2_LOCAL NOT LIKE 'AL____' AND B2.B2_LOCAL NOT LIKE '____EN' AND B2.B2_LOCAL NOT LIKE '____TR'
					AND B2.B2_LOCAL NOT LIKE 'AP-___' AND B2.B2_LOCAL NOT LIKE 'AS-___'
					AND B2.B2_COD NOT LIKE 'MOD____' AND B2.B2_LOCAL NOT LIKE 'CX-___'
					AND B2.B2_LOCAL NOT LIKE 'EXP-ES' AND B2.B2_LOCAL NOT LIKE 'PLA-__'
					AND B2.B2_LOCAL NOT LIKE 'TR-TEM' AND B2.B2_LOCAL NOT LIKE 'PP-TRA'
					AND B2.B2_LOCAL NOT LIKE 'PVC___' AND B2.B2_LOCAL NOT LIKE 'TEM-TP'
					AND B2.B2_LOCAL NOT LIKE 'TP-TEM' AND B2.B2_LOCAL NOT LIKE 'Z_____'
					AND B2.B2_LOCAL NOT LIKE 'MKT-ES' AND B2.B2_LOCAL NOT LIKE 'ENG-ES'
					AND B2.B2_QATU <> '0'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$qtdProdutosInjetadosPP		= $fld [0]->value;
$pesoProdutosInjetadosPP	= $fld [1]->value;

/* Valor do estoque de produtos acabados */
$instrucaoSQL = "SELECT
				SUM(B2.B2_QATU*DA1.DA1_PRCVEN) AS ValorTotal--,
				
				FROM SB2010 B2 WITH (NOLOCK)
					INNER JOIN SB1010 B1
						ON B2.B2_COD = B1.B1_COD
					INNER JOIN DA1010 DA1
						ON B1.B1_COD = DA1.DA1_CODPRO
				
				WHERE B2.B2_LOCAL LIKE 'AP-%'
					AND B2.B2_COD LIKE 'PL________'
					AND B2.B2_FILIAL = '0101'
					AND B2.B2_LOCAL <> 'AP-TRA'
					AND B1.B1_XTPLSPR <> '6'
					AND DA1.DA1_CODTAB = '219'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$valorProdutosAcabados = $fld [0]->value;

/* Saida total de peças do porta palete para giro de estoque */
$instrucaoSQL = "SELECT 
				SUM(D3.D3_QUANT) AS Quantidade,
				SUM (D3.D3_QUANT*B1.B1_PESO) AS PesoEmGramas
				
				FROM SD3010 D3 WITH (NOLOCK)
					INNER JOIN SB1010 B1
						ON D3.D3_COD = B1.B1_COD
				
				WHERE D3.D3_LOCAL LIKE 'PP%'
					AND D3.D3_TM > '500'
					AND D3.D3_FILIAL = '0101'
					AND D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$qtdSaidaPPGiroDeEstoque		= $fld [0]->value;
$pesoPecasSaidaPPGiroDeEstoque	= $fld [1]->value;

/* Valor requisitado de MP entre datas a partir do almoxarifado para areas */
$instrucaoSQL = "SELECT
				SUM (D3.D3_QUANT*B1.B1_XCUSTMA) AS ValorMovimentado
				
				FROM SD3010 D3
					INNER JOIN SB1010 B1
						ON D3.D3_COD = B1.B1_COD
					INNER JOIN SBM010 BM
						ON B1.B1_GRUPO = BM.BM_GRUPO
				
				WHERE D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
					AND D3.D3_CF = 'RE1'
					AND D3.D3_COD LIKE 'MP00______'
					AND D3.D3_FILIAL = '0101'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$valorRequisitadoMP = $fld [0]->value;

/* Movimento em valor de produtos do estoque de Acabados */
$instrucaoSQL = "SELECT
				SUM(D3.D3_QUANT*DA1.DA1_PRCVEN) AS ValorTotal
				
				FROM SD3010 D3
					INNER JOIN DA1010 DA1
						ON D3.D3_COD = DA1.DA1_CODPRO
				
				WHERE D3.D3_COD LIKE 'PL________'
					AND D3.D3_TM = '999'
					AND D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
					AND D3.D3_LOCAL = 'AP-A01'
					AND DA1.DA1_CODTAB = '228'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
$fld [$i] = $rs->Fields ( $i );
}

$valorMovimentoProdutosAcabados = $fld [0]->value;

/* Não conformes injetoras(excluindo produtos pequenos) */
$instrucaoSQL = "SELECT
				SUM(BC.BC_QUANT) AS Quantidade, 
				SUM(BC.BC_QUANT*B1.B1_PESO) AS PesoEmGramas 
				
				FROM SBC010 BC WITH (NOLOCK)
					INNER JOIN SC2010 C2
						ON BC.BC_OP = (C2.C2_NUM+C2.C2_ITEM+C2.C2_SEQUEN)
					INNER JOIN SB1010 B1
						ON BC.BC_PRODUTO = B1.B1_COD
				
				WHERE BC.BC_DATA BETWEEN '$dataInicial' AND '$dataFinal'
					AND C2.C2_LOCAL = 'INJ-TR'
					AND B1.B1_COD NOT LIKE '%BOR%'
					AND B1.B1_COD NOT LIKE '%TRM%'
					AND B1.B1_COD NOT LIKE '%LUX%'
					AND B1.B1_COD NOT LIKE '%BMI%'
					AND B1.B1_COD NOT LIKE '%BCH%'
					AND B1.B1_COD NOT LIKE '%BMA%'
					AND B1.B1_COD NOT LIKE '%BME%'
					AND B1.B1_COD NOT LIKE '%BCA%'
					AND B1.B1_COD NOT LIKE '%BRO%'
					AND B1.B1_COD NOT LIKE '%BCH%'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
$fld [$i] = $rs->Fields ( $i );
}

$qtdNaoConformesGrandesInj	= $fld [0]->value;
$pesoNaoConformesGrandesInj	= $fld [1]->value;

/* Não conformes injetoras */
$instrucaoSQL = "SELECT 
				SUM(BC.BC_QUANT) AS Quantidade, 
				SUM(BC.BC_QUANT*B1.B1_PESO) AS PesoEmGramas
				
				FROM SBC010 BC WITH (NOLOCK)
					INNER JOIN SC2010 C2
						ON BC.BC_OP = (C2.C2_NUM+C2.C2_ITEM+C2.C2_SEQUEN)
					INNER JOIN SB1010 B1
						ON BC.BC_PRODUTO = B1.B1_COD
				
				WHERE BC.BC_DATA BETWEEN '$dataInicial' AND '$dataFinal'
					AND C2.C2_LOCAL = 'INJ-TR'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$qtdNaoConformesInj		= $fld [0]->value;
$pesoNaoConformesInj	= $fld [1]->value;

/* Produção entre datas do setor INJ-TR sem BOR - TRM - LUX e outros */
$instrucaoSQL = "SELECT 
				SUM(D3.D3_QUANT) AS Quantidade,
				SUM(D3.D3_QUANT*B1.B1_PESO) AS PesoEmGramas
				
				FROM SD3010 D3 WITH (NOLOCK)
					INNER JOIN SB1010 B1
						ON D3.D3_COD = B1.B1_COD
					
				WHERE D3.D3_LOCAL = 'INJ-TR' 
					AND D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
					AND D3.D3_TM = '010'
					AND B1.B1_COD NOT LIKE '%BOR%'
					AND B1.B1_COD NOT LIKE '%TRM%'
					AND B1.B1_COD NOT LIKE '%LUX%'
					AND B1.B1_COD NOT LIKE '%BMI%'
					AND B1.B1_COD NOT LIKE '%BCH%'
					AND B1.B1_COD NOT LIKE '%BMA%'
					AND B1.B1_COD NOT LIKE '%BME%'
					AND B1.B1_COD NOT LIKE '%BCA%'
					AND B1.B1_COD NOT LIKE '%BRO%'
					AND B1.B1_COD NOT LIKE '%BCH%'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
$fld [$i] = $rs->Fields ( $i );
}

$qtdProducaoINJTRComFiltros		= $fld [0]->value;
$pesoProducaoINJTRComFiltros	= $fld [1]->value;

/* Quantidade e peso total da produção da INJ-TR */
$instrucaoSQL = "SELECT 
				SUM(D3.D3_QUANT) AS Quantidade,
				SUM(D3.D3_QUANT*B1.B1_PESO) AS PesoEmGramas
				
				FROM SD3010 D3
					INNER JOIN SB1010 B1
						ON D3.D3_COD = B1.B1_COD
					
				WHERE D3.D3_LOCAL = 'INJ-TR' 
					AND D3.D3_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
					AND D3.D3_TM = '010'";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
$fld [$i] = $rs->Fields ( $i );
}

$qtdProducaoINJTR	= $fld [0]->value;
$pesoProducaoINJTR	= $fld [1]->value;

echo "</tr></table>";

echo "<div class='well'>";
echo "<h3>Indicadores Gerais da Pradolux</h3>";
echo "<div class='panel panel-default'>";
echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>Produtos Injetados no Porta Palete</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Quantidade: " .number_format($qtdProdutosInjetadosPP, 2, ',', '.');
echo "		<br/>Peso Total: " .number_format($pesoProdutosInjetadosPP, 2, ',', '.');
echo "	</div>";

echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>Produtos no Acabados</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Movimento: R$ " .number_format($valorMovimentoProdutosAcabados, 2, ',', '.');
echo "		<br/>Estoque: R$ " .number_format($valorProdutosAcabados, 2, ',', '.');
echo "	</div>";

echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>Saída de Produtos do Porta Palete para Giro de Estoque</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Quantidade: " .number_format($qtdSaidaPPGiroDeEstoque, 2, ',', '.');
echo "		<br/>Peso Total: " .number_format($pesoPecasSaidaPPGiroDeEstoque, 2, ',', '.');
echo "	</div>";

echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>MPs Requisitadas do Almoxarifado</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Quantidade: " .number_format($valorRequisitadoMP, 2, ',', '.');
echo "	</div>";

echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>Não Conformes Injetora</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Quantidade(grandes): " .number_format($qtdNaoConformesGrandesInj, 2, ',', '.');
echo "		<br/>Peso(grandes): " .number_format($pesoNaoConformesGrandesInj, 2, ',', '.');
echo "		<br/>";
echo "		<br/>Quantidade Total: " .number_format($qtdNaoConformesInj, 2, ',', '.');
echo "		<br/>Peso Total:" .number_format($pesoNaoConformesInj, 2, ',', '.');
echo "	</div>";

echo "	<div class='panel-heading'>";
echo "		<h3 class='panel-title'>Produção INJ-TR</h3>";
echo "	</div>";
echo "	<div class='panel-body'>";
echo "		Quantidade(com filtros): " .number_format($qtdProducaoINJTRComFiltros, 2, ',', '.');
echo "		<br/>Peso(com filtros): " .number_format($pesoProducaoINJTRComFiltros, 2, ',', '.');
echo "		<br/>";
echo "		<br/>Quantidade Total: " .number_format($qtdProducaoINJTR, 2, ',', '.');
echo "		<br/>Peso Total: " .number_format($pesoProducaoINJTR, 2, ',', '.');
echo "	</div>";
echo "</div>";
echo "</div>";

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>