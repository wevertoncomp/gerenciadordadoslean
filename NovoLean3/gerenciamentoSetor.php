<?php
$conn->open ( $connStr );
$dataAtual = date ( 'Ymd' );
$dataAtual = '20150716';
$dataAtualFormatada = date ( 'd/m/Y' );
$localProducao = 'MO2-TR';
?>

<style type="text/css">
th,td {
	text-align: center;
}
</style>

<div class='well'>
	<h4>Gerenciamento do Setor</h4>
</div>


<!-- Columns start at 50% wide on mobile and bump up to 33.3% wide on desktop -->
<div class="row">
	<div class="col-xs-6 col-md-3">
		<div class='well'>
			<h3>Atrasados</h3>

			<table class='table table-hover'>
				<tr>
					<!-- <th>Data</th>  -->
					<th><button type='button' class='btn btn-default btn-xs'>Código</button></th>
					<th><button type='button' class='btn btn-primary btn-xs'>V</button></th>
					<th><button type='button' class='btn btn-success btn-xs'>R</button></th>
					<th><button type='button' class='btn btn-info btn-xs'>E</button></th>
					<th><button type='button' class='btn btn-danger btn-xs'>F</button></th>
					<th><button type='button' class='btn btn-warning btn-xs'>T</button></th>
				</tr>
			
<?php
$retornaSQL = "				SELECT  convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
							        RTRIM(C6.C6_PRODUTO) AS Produto,
							        SUM(C6.C6_QTDVEN) AS QtdVendida,
							        SUM(C6.C6_QTDRESE) AS QtdReservada,
							        ISNULL((SELECT B2.B2_QATU - B2.B2_RESERVA 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE 'AP-A01')
							         				AND B2.D_E_L_E_T_ <> '*' 
							         				AND B1.B1_COD = B2.B2_COD
							        	   )
							        ,0) AS EstoqueDisponivel,
							        ISNULL((SELECT TOP 1 B2.B2_QATU 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE '%TR' AND B2.B2_LOCAL NOT LIKE 'TR-TEM')
							        				AND B2.D_E_L_E_T_ <> '*' 
							        				AND B1.B1_COD = B2.B2_COD
							        				AND B2.B2_FILIAL = '0101'
							        	   )
							        ,0) AS TransitoDasAreas,
							       	Z9.ZZ9_KANBVD,
							        Z9.ZZ9_KANBAM,
							        Z9.ZZ9_KANBVM,
							        Z9.ZZ9_QTDKAN,
							        Z9.ZZ9_TEMPOP
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT < '$dataAtual'
									AND B1.B1_LOCPAD = 'MO1-TR'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	
	$falta = $qtdVendida - $qtdEmpenhada - $estoque;
	
	if ($falta < 0) {
		$falta = 0;
	}
	
	echo "<tr>";
	// echo "<td>$data</td>";
	echo "<td><button type='button' class='btn btn-default btn-xs'>$codigo</button></td>";
	echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
	echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
	echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
	echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
	echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
	echo "</tr>";
	$rs->MoveNext ();
}

?>
</table>
		</div>
	</div>

	<div class="col-xs-6 col-md-3">
		<div class='well'>
			<h3>Atual</h3>

			<table class='table table-hover'>
				<tr>
					<!-- <th>Data</th>  -->
					<th><button type='button' class='btn btn-default btn-xs'>Código</button></th>
					<th><button type='button' class='btn btn-primary btn-xs'>V</button></th>
					<th><button type='button' class='btn btn-success btn-xs'>R</button></th>
					<th><button type='button' class='btn btn-info btn-xs'>E</button></th>
					<th><button type='button' class='btn btn-danger btn-xs'>F</button></th>
					<th><button type='button' class='btn btn-warning btn-xs'>T</button></th>
				</tr>
			
<?php
$retornaSQL = "				SELECT  convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
							        RTRIM(C6.C6_PRODUTO) AS Produto,
							        SUM(C6.C6_QTDVEN) AS QtdVendida,
							        SUM(C6.C6_QTDRESE) AS QtdReservada,
							        ISNULL((SELECT B2.B2_QATU - B2.B2_RESERVA 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE 'AP-A01')
							         				AND B2.D_E_L_E_T_ <> '*' 
							         				AND B1.B1_COD = B2.B2_COD
							        	   )
							        ,0) AS EstoqueDisponivel,
							        ISNULL((SELECT TOP 1 B2.B2_QATU 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE '%TR' AND B2.B2_LOCAL NOT LIKE 'TR-TEM')
							        				AND B2.D_E_L_E_T_ <> '*' 
							        				AND B1.B1_COD = B2.B2_COD
							        				AND B2.B2_FILIAL = '0101'
							        	   )
							        ,0) AS TransitoDasAreas,
							       	Z9.ZZ9_KANBVD,
							        Z9.ZZ9_KANBAM,
							        Z9.ZZ9_KANBVM,
							        Z9.ZZ9_QTDKAN,
							        Z9.ZZ9_TEMPOP
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT = '$dataAtual'
									AND B1.B1_LOCPAD = 'MO1-TR'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	
	$falta = $qtdVendida - $qtdEmpenhada - $estoque;
	
	if ($falta < 0) {
		$falta = 0;
	}
	
	echo "<tr>";
	// echo "<td>$data</td>";
	echo "<td><button type='button' class='btn btn-default btn-xs'>$codigo</button></td>";
	echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
	echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
	echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
	echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
	echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
	echo "</tr>";
	$rs->MoveNext ();
}

?>
</table>
		</div>
	</div>



	<div class="col-xs-6 col-md-3">
		<div class='well'>
			<h3>Amanhã</h3>

			<table class='table table-hover'>
				<tr>
					<th>Data</th>
					<th>Código</th>
					<th>V</th>
					<th>R</th>
					<th>E</th>
					<th>F</th>
					<th>T</th>
				</tr>
			
<?php
$dataAmanha = $dataAtual + 1;

$retornaSQL = "				SELECT  convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
							        RTRIM(C6.C6_PRODUTO) AS Produto,
							        SUM(C6.C6_QTDVEN) AS QtdVendida,
							        SUM(C6.C6_QTDRESE) AS QtdReservada,
							        ISNULL((SELECT B2.B2_QATU - B2.B2_RESERVA 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE 'AP-A01')
							         				AND B2.D_E_L_E_T_ <> '*' 
							         				AND B1.B1_COD = B2.B2_COD
							        	   )
							        ,0) AS EstoqueDisponivel,
							        ISNULL((SELECT TOP 1 B2.B2_QATU 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE '%TR' AND B2.B2_LOCAL NOT LIKE 'TR-TEM')
							        				AND B2.D_E_L_E_T_ <> '*' 
							        				AND B1.B1_COD = B2.B2_COD
							        				AND B2.B2_FILIAL = '0101'
							        	   )
							        ,0) AS TransitoDasAreas
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							
							INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
							                                                               
							WHERE 	A1.D_E_L_E_T_ <> '*' AND C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT = '$dataAmanha'
									AND B1.B1_LOCPAD = 'MO1-TR'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	
	$falta = $qtdVendida - $qtdEmpenhada - $estoque;
	
	if ($falta < 0) {
		$falta = 0;
	}
	
	echo "<tr>";
	echo "<td>$data</td>";
	echo "<td>$codigo</td>";
	echo "<td>$qtdVendida</td>";
	echo "<td>$qtdReservada</td>";
	echo "<td>$estoque</td>";
	echo "<td>$falta</td>";
	echo "<td>$transito</td>";
	echo "</tr>";
	$rs->MoveNext ();
}

?>
</table>
		</div>
	</div>



	<div class="col-xs-6 col-md-3">
		<div class='well'>

			<table class='table table-hover'>
				<tr>
					<th>Código</th>
					<th>Empenho</th>
					<th>Estoque</th>
					<th>Balanço</th>
					<th>Fornecedor</th>
				</tr>
			
<?php
$retornaSQL = "	DECLARE @local VARCHAR(6);
							SET @local = 'MO2-EN'
							
							SELECT 
							
							RTRIM(D4.D4_COD),
							SUM(D4.D4_QUANT) AS QtdEmpenhada, 
							--D4.D4_DATA, 
							--D4.D4_OP, 
							--D4.D4_LOCAL, 
							--D4.D4_OPORIG,
							SUM(B2.B2_QATU) AS EstoqueArea,
							(SUM(B2.B2_QATU)-SUM(D4.D4_QUANT)) AS Balanco,
							B1.B1_LOCPAD
							
							FROM SD4010 D4 WITH (NOLOCK)
							LEFT OUTER JOIN SB2010 B2 ON D4.D4_COD = B2.B2_COD AND B2.B2_LOCAL = @local
							LEFT OUTER JOIN SB1010 B1 ON D4.D4_COD = B1.B1_COD
							
							WHERE D4.D_E_L_E_T_ <> '*'
							AND D4.D4_LOCAL = @local
							AND D4.D4_COD NOT LIKE 'MOD%'
							AND D4.D4_QUANT > '0'
							
							GROUP BY D4.D4_COD, D4.D4_LOCAL, B1.B1_LOCPAD--, D4.D4_QUANT
							ORDER BY B1.B1_LOCPAD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

while ( ! $rs->EOF ) {
	
	$codigo = $fld [0]->value;
	$qtdEmpenhada = $fld [1]->value;
	$estoqueArea = $fld [2]->value;
	$balanco = $fld [3]->value;
	$fornecedorInterno = $fld [4]->value;
	
	echo "<tr>";
	echo "<td>$codigo</td>";
	echo "<td>$qtdEmpenhada</td>";
	echo "<td>$estoqueArea</td>";
	echo "<td>$balanco</td>";
	echo "<td>$fornecedorInterno</td>";
	echo "</tr>";
	$rs->MoveNext ();
}

?>
</table>
		</div>
	</div>



<?php
$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>