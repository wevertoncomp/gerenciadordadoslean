<?php
// ob_end_flush();
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Venda mensal do produto</h4><hr>";

// echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=vendaItemMensal' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o produto: </b></span>";
echo "<br/>";

// $data = $_GET ['dia'];
$produto = $_POST ['produto'];

// Combobox da data

$instrucaoSQL = "	SELECT RTRIM(B1.B1_COD) FROM SB1010 B1 WITH (NOLOCK)
					WHERE B1.B1_COD LIKE 'PL________'
					AND B1.D_E_L_E_T_ != '*'
					AND B1.B1_COD NOT LIKE 'PL00______'
					AND B1.B1_XTPLSPR != '6'
					ORDER BY B1.B1_COD";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<select name='produto'>";
if (isset ( $produto )) {
	echo "<option value='$produto'>$produto</option>";
} else {
	echo "<option value='-1'>Selecione o produto</option>";
}
while ( ! $rs->EOF ) {
	
	// for($i = 0; $i < $num_columns; $i ++) {
	echo "<option value=" . $fld [0]->value . ">" . $fld [0]->value . "</option>";
	// }
	$rs->MoveNext ();
}

echo "</select>";

$rs->Close ();
$rs = null;

echo "<input type='submit' value='Buscar'>";
echo "</div>";

echo "<div class='well'>";
echo "<table class='table table-hover'><tr><th>Ano</th><th>Mês</th><th>Código</th><th>Quantidade</th></tr>";

$instrucaoSQL = "	SELECT

					DATEPART(mm,F2.F2_EMISSAO),
					D2.D2_COD,
					SUM(D2.D2_QUANT) AS QuantidadeTotal,
					DATEPART(yy,F2.F2_EMISSAO)
					
					FROM SF2010 AS F2 WITH (NOLOCK)
					INNER JOIN SD2010 D2 ON F2.F2_DOC = D2.D2_DOC
					
					WHERE F2.D_E_L_E_T_ <> '*' 
					AND (F2_FILIAL = '0101' OR F2_FILIAL = '0201')
					AND F2_CLIENTE != '003718'
					AND F2_EMISSAO BETWEEN '20140601' AND '20151231'
					AND (SELECT COUNT(E1_PARCELA) FROM SE1010 WHERE E1_NUM = F2.F2_DOC) > 0 AND F2_SERIE = '1' 
					AND D2.D2_COD = '$produto'
					
					GROUP BY D2.D2_COD, DATEPART(yy,F2.F2_EMISSAO), DATEPART(mm,F2.F2_EMISSAO)
					ORDER BY DATEPART(yy,F2.F2_EMISSAO), DATEPART(mm,F2.F2_EMISSAO) ASC";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$qtdTotal = 0;
$qtdReservadaTotal = 0;

while ( ! $rs->EOF ) {
	$mes = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtd = $fld [2]->value;
	$ano = $fld [3]->value;
	
	echo "<tr><td>$ano</td><td>$mes</td><td>$codigo</td><td>$qtd</td></tr>";
	$rs->MoveNext ();
}

echo "</table>";

$rs->Close ();
$rs = null;

echo "</tr></table>";
echo "</div>";


$conn->Close ();
$conn = null;
?>



