<?php
ob_end_flush();


// Variáveis globais
$dataInicial = '20150101';
$dataFinal = '20150131';

echo "<div class='well'>";
echo "<h4>Não conformes</h4><hr>";
echo "<p>Mostrando dados de $dataInicial até $dataFinal";
echo "</div>";






	echo "<div class='well'>";
	
	echo "<table class='table table-hover'><tr><th>Descrição</th><th>Quantidade</th><th>Peso</th></tr>";
	$conn->open ( $connStr );
	$retornaSQL = "SELECT 
			
								SUM(BC.BC_QUANT) AS Quantidade, 
								SUM(BC.BC_QUANT*B1.B1_PESO) AS Peso

								FROM SBC010 BC WITH (NOLOCK)

								INNER JOIN SC2010 C2 ON BC.BC_OP = (C2.C2_NUM+C2.C2_ITEM+C2.C2_SEQUEN)
								INNER JOIN SB1010 B1 ON BC.BC_PRODUTO = B1.B1_COD
								
								WHERE 
								
								BC.BC_DATA BETWEEN '$dataInicial' AND '$dataFinal'
								AND C2.C2_LOCAL = 'INJ-TR'";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	while ( ! $rs->EOF ) {
	
		$quantidade = $fld [0]->value;
		$peso = $fld [1]->value;
	
		echo "<tr><td>Não conformes das Injetoras</td><td>$quantidade</td><td>$peso</td></tr>";
		$rs->MoveNext ();
	}
	
	$rs->MoveNext ();
	$rs->Close ();
	$rs = null;
	$conn->Close ();
	$conn = null;
	
	// Termina o primeiro
	
	$conn->open ( $connStr );
	$retornaSQL = "	SELECT
		
					SUM(BC.BC_QUANT) AS Quantidade,
					SUM(BC.BC_QUANT*B1.B1_PESO) AS Peso
					
					FROM SBC010 BC WITH (NOLOCK)
					
					INNER JOIN SC2010 C2 ON BC.BC_OP = (C2.C2_NUM+C2.C2_ITEM+C2.C2_SEQUEN)
					INNER JOIN SB1010 B1 ON BC.BC_PRODUTO = B1.B1_COD
					
					WHERE
					
					BC.BC_DATA BETWEEN '$dataInicial' AND '$dataFinal'
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
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
	}
	
	while ( ! $rs->EOF ) {
	
	$quantidade = $fld [0]->value;
	$peso = $fld [1]->value;
	
	echo "<tr><td>Não conformes das Injetoras sem</td><td>$quantidade</td><td>$peso</td></tr>";
	$rs->MoveNext ();
	}
	
	$rs->MoveNext ();
	$rs->Close ();
	$rs = null;
	$conn->Close ();
	$conn = null;
	
echo "</table>";
echo "</div>";





?>