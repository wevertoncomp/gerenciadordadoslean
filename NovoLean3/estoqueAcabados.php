
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
	aria-labelledby="myLargeModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content"></div>
	</div>
</div>


<?php
// ob_end_flush();
$conn->open ( $connStr );
$mostrar = "Faltantes";
$item = 0;

echo "<div class='well'>";
echo "<h4>Estoque de Produtos Acabados</h4><hr>";
echo "</div>";

echo "<div class='well'>";
echo "<table class='table table-hover'><tr><th>Item</th><th>Produto</th><th>Qtde</th><th>Local Produção</th><th>Local Kanban</th><th>Estoque Máximo</th>";
echo "<th>%</th><th>Bloqueado</th><th>Tipo</th></tr>";

$retornaSQL = "	SELECT

					B2.B2_COD AS Produto,
					B2.B2_QATU,
					B1.B1_LOCPAD,
					Z9.ZZ9_LOCAL,
					((Z9.ZZ9_KANBVD+Z9.ZZ9_KANBAM+Z9.ZZ9_KANBVM)*Z9.ZZ9_QTDKAN) AS EstoqueMaximo,
					B1.B1_MSBLQL,
					B1.B1_XTPLSPR
					
					FROM SB2010 B2 WITH (NOLOCK)
					
					INNER JOIN SB1010 B1 ON B2.B2_COD = B1.B1_COD
					LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
					
					WHERE B2.B2_LOCAL LIKE 'AP-%'
					AND B2.B2_COD LIKE 'PL________'
					AND B2.B2_FILIAL = '0101'
					AND B2.B2_LOCAL <> 'AP-TRA'
					AND B1.B1_XTPLSPR <> '6'
					
					ORDER BY B1.B1_LOCPAD, B2.B2_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	
	$produto = $fld [0]->value;
	$qtde = $fld [1]->value;
	$localProducao = $fld [2]->value;
	$localKanban = $fld [3]->value;
	$estoqueMaximo = $fld [4]->value;
	$bloqueado = $fld [5]->value;
	$tipo = $fld [6]->value;
	$percentualEstoque = ($qtde / $estoqueMaximo) * 100;
	
	if ($bloqueado == '1') {
		$bloqueado = "Sim";
	} else {
		$bloqueado = "Não";
	}
	
	if ($tipo == '3') {
		$tipo = "Normal";
	} else if ($tipo == '6') {
		$tipo = "Obsoleto";
	} else if ($tipo == '1' || $tipo == '2') {
		$tipo = "Lançamento";
	} else if ($tipo == '5') {
		$tipo = "Obsolescência";
	}
	
	$item ++;
	echo "<tr><td>$item</td><td bgcolor = '#00FF00'>$produto</td><td>$qtde</td><td>$localProducao</td>";
	echo "<td>$localKanban</td><td bgcolor = '#00FF00'>$estoqueMaximo</td><td>".number_format($percentualEstoque, 0)." %</td><td>$bloqueado</td><td>$tipo</td>";
	echo "</tr>";
	
	$rs->MoveNext ();
}

$rs->MoveNext ();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>