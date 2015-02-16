<?php

function mostraProdutividade ($data) {
$data = '20141111';
$horas = 10;
//echo "<div class='jumbotron'>";

echo "<table cellspacing = 3 class='table table-striped'><tr><th>Produto</th><th>Tempo Unitário</th><th>Qtd Produzida</th><th>Tempo Ideal</th><th>Produtividade</th></tr>";

$instrucaoSQL = "SELECT D3.D3_COD, Z9.ZZ9_TEMPOP, D3.D3_QUANT, Z9.ZZ9_QTDOPE FROM SD3010 D3

INNER JOIN ZZ9010 Z9 ON D3.D3_COD = Z9.ZZ9_PRODUT

WHERE D3.D3_LOCAL = 'MO2-TR' AND D3.D3_EMISSAO = '$data'";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$produtividadeTotal = 0.00;
$quantidadeTotal = 0;
$tempoIdealTotal = 0;

while ( ! $rs->EOF ) {

	$produto = $fld [0]->value;
	$tempoUnitario = $fld [1]->value;
	$quantidadeProduzida = $fld [2]->value;
	$tempoTotal = ($tempoUnitario * $quantidadeProduzida);
	$produtividade = $tempoTotal / ($horas * 3600);
	$produtividadeTotal += ( float ) $produtividade;
	$quantidadeTotal += $quantidadeProduzida;
	$tempoIdealTotal += $tempoTotal;

	echo "<tr><td>$produto</td><td>$tempoUnitario seg</td><td>$quantidadeProduzida un</td>";
	echo "<td>" . number_format ($tempoTotal / 60, 0, '.', '' ) . " min.</td><td>" . number_format ( $produtividade * 100, 2, '.', '' ) . " %</td></tr>";
	$rs->MoveNext ();
}

echo "<tfoot><td>Totais</td><td></td><td>$quantidadeTotal</td><td>". number_format ($tempoIdealTotal/60, 0, '.', '' ) ." min.</td>";
echo "<td>". number_format ($produtividadeTotal*100, 2, '.', '' ) ." %</td></tfoot></table>";

//echo "</div>";
$rs->Close ();
$rs = null;
}
?>