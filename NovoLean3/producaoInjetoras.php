<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Produção das Injetoras</h4><hr>";

//echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=producaoInjetoras' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o dia que deseja visualizar: </b></span>";
echo "<br/>";

//$data = $_GET ['dia'];
$data = $_POST ['dia'];

// Combobox da data

$instrucaoSQL = "	SELECT TOP 10 D3.D3_EMISSAO FROM SD3010 D3
		
					WHERE D3.D3_LOCAL = 'INJ-TR'
					AND D3.D3_TM = '010'
					
					GROUP BY D3.D3_EMISSAO
					ORDER BY D3.D3_EMISSAO DESC";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<select name='dia'>";
if (isset ( $data )) {
	echo "<option value='$data'>$data</option>";
} else {
	echo "<option value='-1'>Selecione a data desejada</option>";
}
while ( ! $rs->EOF ) {
	
	//for($i = 0; $i < $num_columns; $i ++) {
		echo "<option value=" . $fld [0]->value . ">" . $fld [0]->value . "</option>";
	//}
	$rs->MoveNext ();
}

echo "</select>";

$rs->Close ();
$rs = null;

echo "</select><br />";

// Combobox do setor
/*
 * $instrucaoSQL = "SELECT NR.NNR_CODIGO, NR.NNR_DESCRI FROM NNR010 NR WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%' AND NR.NNR_DESCRI LIKE '%PRODUCAO%' ORDER BY NR.NNR_DESCRI"; $rs = $conn->execute ( $instrucaoSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); } echo "<select name='setor'>"; echo "<option value='-1'>Selecione o local</option>"; while ( ! $rs->EOF ) { echo "<option value=" . $fld [0]->value . ">" . $fld [1]->value . "</option>"; $rs->MoveNext (); } $rs->Close (); $rs = null; echo "</select>";
 */

echo "<input type='submit' value='Buscar'>";
echo "</div>";

echo "<div class='well'>";

	if (isset($data)){
	
	echo "<table class='table table-hover'><tr><th>Item</th><th>Produto</th><th>Peso Unitário</th><th>Qtd Produzida</th><th>Peso Total</th></tr>";
	
	$retornaProdutividadeSQL = "SELECT D3.D3_COD, D3.D3_QUANT, B1.B1_PESO FROM SD3010 D3 WITH (NOLOCK)
	
								INNER JOIN SB1010 B1 ON D3.D3_COD = B1.B1_COD
	
								WHERE D3.D3_LOCAL = 'INJ-TR' 
								AND D3.D3_EMISSAO = '$data'
								AND D3.D3_TM = '010'
								AND D3.D3_FILIAL = '0101'
								
								ORDER BY D3.D3_COD";
								
	$rs2 = $conn->execute ( $retornaProdutividadeSQL );
	
	$num_columns2 = $rs2->Fields->Count ();
	
	for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs2->Fields ( $i2 );
	}
	
	$quantidadeTotal = 0;
	$pesoProduzidoTotal = 0;
	$item = 0;
	
	while ( ! $rs2->EOF ) {
	
		$produto = $fld2 [0]->value;
		$quantidadeProduzida = $fld2 [1]->value;
		$peso = $fld2 [2]->value;
		$pesoProduzido = $quantidadeProduzida * $peso;
		$quantidadeTotal += $quantidadeProduzida;
		$pesoProduzidoTotal += $pesoProduzido;
		$item++;
	
		echo "<tr><td>$item</td><td>$produto</td><td>$peso gr</td><td>$quantidadeProduzida un</td>";
		echo "<td>" . number_format ($pesoProduzido/1000, 2, ',', '.' ) . " Kg</td></tr>";
		$rs2->MoveNext ();
	}
	
	echo "<tfoot><td>Totais</td><td></td><td></td><td><button type='button' class='btn btn-lg btn-primary'>". number_format($quantidadeTotal, 0, ',', '.') ." un</button></td>";
	echo "<td><button type='button' class='btn btn-lg btn-primary'>". number_format ($pesoProduzidoTotal/1000, 2, '.', '' ) ." Kg</button></td></tfoot></table>";
	
	$rs2->Close ();
	$rs2 = null;
	}

	echo "</div>";
	


$conn->Close ();
$conn = null;
?>