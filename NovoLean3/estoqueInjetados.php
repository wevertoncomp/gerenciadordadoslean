<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Estoque de Injetados</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'><tr><th>Quantidade</th><th>Peso Total</th></tr>";
	
	$retornaSQL = "	SELECT 
					--*
					SUM(B2.B2_QATU) AS Quantidade,
					SUM(B2.B2_QATU*B1.B1_PESO)/1000 AS PesoTotal
					
					FROM SB2010 B2
					
					INNER JOIN SB1010 B1 ON B2.B2_COD = B1.B1_COD
					
					WHERE B2.B2_FILIAL = '0101'
					AND B2.D_E_L_E_T_ <> '*'
					AND B1.D_E_L_E_T_ <> '*'
					AND	B1.B1_LOCPAD = 'INJ-TR'
					AND B1.B1_COD NOT LIKE '%-VI'
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
					AND B2.B2_QATU <> '0'
					
					--ORDER BY B2.B2_LOCAL";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	
	while ( ! $rs->EOF ) {
	
		$quantidade = $fld [0]->value;
		$pesoTotal = $fld [1]->value;

	
		echo "<tr><td>". number_format($quantidade, 0) ." unidades</td><td>". number_format($pesoTotal, 0) ." Kg</td></tr>";
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