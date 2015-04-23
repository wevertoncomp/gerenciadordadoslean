<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Consulta STL em Pedido</h4><hr>";

//echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";

echo "</div>";


	echo "<table class='table table-hover'><tr><th>Item</th><th>Pedido</th><th>Produto</th><th>Cliente</th>";
	echo "<th>Razao Social</th><th>Data Prevista</th><th>Evento</th><th>Qtd. Vendida</th>";
	echo "<th>Reserva</th><th>Qtd. Reservada</th><th>Local</th><th>Estoque</th><th>Transito</th><th>Vendedor</th><th>Nome Vendedor</th></tr>";
	
$instrucaoSQL = "	SELECT
					C5.C5_NUM AS Pedido,
					B1.B1_COD AS Codigo,
					C5.C5_CLIENTE AS Cliente,
					A1.A1_NOME AS RazaoSocial,
					convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
					(CASE C5.C5_EVENTO 
						WHEN '5' THEN 'Sep.'
						WHEN '6' THEN 'Conf.'
						WHEN '9' THEN 'Ag. Sep.'
					 END) AS Evento,
					C6.C6_QTDVEN AS QtdVendida,
					C6.C6_RESERVA AS Reserva,
					C6.C6_QTDRESE AS QtdReservada,
					B1.B1_LOCPAD AS LocalProducao,
					(SELECT B2.B2_QATU FROM SB2010 B2 WITH (NOLOCK)
					WHERE (B2.B2_LOCAL LIKE 'AP-A01')
					AND B2.D_E_L_E_T_ <> '*' AND B1.B1_COD = B2.B2_COD) AS Estoque,
					(SELECT TOP 1 B2.B2_QATU FROM SB2010 B2 WITH (NOLOCK)
					WHERE (B2.B2_LOCAL LIKE '%TR')
					AND B2.D_E_L_E_T_ <> '*' AND B1.B1_COD = B2.B2_COD) AS Transito,
					C5.C5_VEND1 AS Vendedor,
					A3.A3_NOME AS NomeVendedor
					
					
					FROM SC5010 C5 WITH (NOLOCK)
					INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
					INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
					INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
					INNER JOIN SA3010 A3 ON C5.C5_VEND1 = A3.A3_COD
					
					WHERE (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
					AND B1.B1_FABRIC = 'STL'
					AND A1.D_E_L_E_T_ <> '*' AND C6.D_E_L_E_T_ <> '*'
					
					ORDER BY C5.C5_VEND1, B1.B1_COD, B1.B1_LOCPAD, C5.C5_NUM";

	$rs = $conn->execute ( $instrucaoSQL);
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$item = 0;
	$qtdTotal = 0;
	$qtdReservadaTotal = 0;
	
	while ( ! $rs->EOF ) {
		$item++;
		$pedido = $fld [0]->value;
		$codigo = $fld [1]->value;
		$cliente = $fld [2]->value;
		$razaoSocial = $fld [3]->value;
		$dataPrevista = $fld [4]->value;
		$evento = $fld [5]->value;
		$qtdVendida = $fld [6]->value;
		$reserva = $fld [7]->value;
		$qtdReservada = $fld [8]->value;
		$local = $fld [9]->value;
		$estoque = $fld [10]->value;
		$transito = $fld [11]->value;
		$vendedor = $fld [12]->value;
		$codVendedor = $fld [13]->value;
		$qtdTotal += $qtdVendida;
		$qtdReservadaTotal += $qtdReservada;
	
		echo "<tr><td>$item</td><td>$pedido</td><td>$codigo</td><td>$cliente</td><td>$razaoSocial</td>";
		echo "<td>$dataPrevista</td><td>$evento</td><td>$qtdVendida</td><td>$reserva</td><td>$qtdReservada</td>";
		echo "<td>$local</td><td>$estoque</td><td>$transito</td><td>$vendedor</td><td>$codVendedor</td></tr>";
		$rs->MoveNext ();
	}
	
	echo "<tfoot><td colspan = '7'>Totais</td><td>$qtdTotal</td><td></td>";
	echo "<td>$qtdReservadaTotal</td></tfoot></table>";
	
	$rs->Close ();
	$rs = null;



echo "</tr></table>";


$conn->Close ();
$conn = null;
?>