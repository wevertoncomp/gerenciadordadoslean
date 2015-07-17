<?php
$conn->open ( $connStr );
$dataAtual = date ( 'Ymd' );
// $dataAtual = '20150715';
$dataAtualFormatada = date ( 'd/m/Y' );
$localProducao = 'MO2-TR';
$produtividadeMedia = 0.6;
$faltaTotaldePecas = 0;
$acrescimoDias = 0;
$diaSemana = date ( 'D' );

if ($diaSemana == "Thu") {
	$acrescimoDiasDataPosterior = 0;
	$acrescimoDiasData2DiasPosterior = 2;
} else if ($diaSemana == "Fri") {
	$acrescimoDiasDataPosterior = 2;
	$acrescimoDiasData2DiasPosterior = 2;
}

// Funções
function imprimeCabecalho() {
	echo "<th><button type='button' class='btn btn-default btn-xs'>Código</button></th>";
	echo "<th><button type='button' class='btn btn-primary btn-xs'>V</button></th>";
	echo "<th><button type='button' class='btn btn-success btn-xs'>R</button></th>";
	echo "<th><button type='button' class='btn btn-info btn-xs'>E</button></th>";
	echo "<th><button type='button' class='btn btn-danger btn-xs'>F</button></th>";
	echo "<th><button type='button' class='btn btn-warning btn-xs'>T</button></th>";
	echo "<th><button type='button' class='btn btn-danger btn-xs'>PMi</button></th>";
	echo "<th><button type='button' class='btn btn-warning btn-xs'>PI</button></th>";
	echo "<th><button type='button' class='btn btn-success btn-xs'>PMa</button></th>";
	echo "<th><button type='button' class='btn btn-default btn-xs'>OP</button></th>";
	echo "<th><button type='button' class='btn btn-default btn-xs'>QOP</button></th>";
	echo "<th><button type='button' class='btn btn-default btn-xs'>TNF</button></th>";
	echo "<th><button type='button' class='btn btn-default btn-xs'>TNPMi</button></th>";
}

// ob_start();
?>

<style type="text/css">
th,td,tr {
	text-align: center;
}
</style>

<div class='well'>
	<h4>Gerenciamento do Setor - <?php echo $localProducao;?></h4>
	<hr />
	<h5>Legenda</h5>
	<button type='button' class='btn btn-primary btn-xs'>V - Vendido</button>
	<button type='button' class='btn btn-success btn-xs'>R - Reservado</button>
	<button type='button' class='btn btn-info btn-xs'>E - Estoque</button>
	<button type='button' class='btn btn-danger btn-xs'>F - Faltante</button>
	<button type='button' class='btn btn-warning btn-xs'>T - Área de
		trânsito</button>
	<button type='button' class='btn btn-danger btn-xs'>PMi - Produção
		Mínima</button>
	<button type='button' class='btn btn-warning btn-xs'>PI - Produção
		Ideal</button>
	<button type='button' class='btn btn-success btn-xs'>PMa - Produção
		Máxima</button>
	<button type='button' class='btn btn-default btn-xs'>OP - Ordem de
		Produção</button>
	<button type='button' class='btn btn-default btn-xs'>QOP - Quantidade
		da OP</button>
	<button type='button' class='btn btn-default btn-xs'>TNF - Tempo Necessário para produzir faltantes, em minutos, com produtividade de <?php echo $produtividadeMedia*100;?> %</button>
	<button type='button' class='btn btn-default btn-xs'>TNPMi - Tempo Necessário para produção mínima, em minutos, com produtividade de <?php echo $produtividadeMedia*100;?> %</button>
	<button type='button' class='btn btn-primary btn-xs'>Em - Empenhado
		pela Ordem de Produção</button>
	<button type='button' class='btn btn-success btn-xs'>Es - Estoque na
		área de entrada da área</button>
	<button type='button' class='btn btn-info btn-xs'>B - Balanço do
		estoque</button>
	<button type='button' class='btn btn-danger btn-xs'>Forn - Fornecedor
		interno</button>
</div>


<!-- Columns start at 50% wide on mobile and bump up to 33.3% wide on desktop -->
<div class="row">
	<div class="col">
		<div class="col-xs-6 col-md-4">
			<div class='well'>
				<h4>Atrasados</h4>

				<table class='table table-hover'>
					<tr>
						<?php imprimeCabecalho(); ?>
					</tr>
			
<?php
//ob_start();
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
							        Z9.ZZ9_TEMPOP,
							        C2.C2_NUM,
							        C2.C2_QUANT
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							LEFT OUTER JOIN SC2010 C2 ON B1.B1_COD = C2.C2_PRODUTO AND C2.C2_ZEVENTO <> '5' AND C2.D_E_L_E_T_ <> '*'
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT < '$dataAtual'
									AND B1.B1_LOCPAD = '$localProducao'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP, C2.C2_NUM, C2.C2_QUANT
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$tempoNecessarioFaltantesTotal = 0;
$tempoNecessarioTotal = 0;

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	$kanbanVD = $fld [6]->value;
	$kanbanAM = $fld [7]->value;
	$kanbanVM = $fld [8]->value;
	$qtdPorKanban = $fld [9]->value;
	$tempoProducaoUnitario = $fld [10]->value;
	$op = $fld [11]->value;
	$quantidadeOP = $fld [12]->value;
	
	$falta = $qtdVendida - $qtdReservada - $estoque;
	if ($falta < 0) {
		$falta = 0;
	}
	$faltaTotaldePecas += $falta;
	
	$producaoMinima = ($kanbanVD * $qtdPorKanban) + $falta;
	$producaoIdeal = (($kanbanVD + $kanbanAM) * $qtdPorKanban) + $falta;
	$producaoMaxima = (($kanbanVD + $kanbanAM + $kanbanVM) * $qtdPorKanban) + $falta;
	$tempoNecessarioFaltantes = ($falta * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	$tempoNecessario = ($producaoMinima * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	
	if ($falta > 0) {
		// Somatórios
		$tempoNecessarioTotal += $tempoNecessario;
		$tempoNecessarioFaltantesTotal += $tempoNecessarioFaltantes;
		
		echo "<tr>";
		// echo "<td>$data</td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$codigo</button></td>";
		echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
		echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$producaoMinima</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$producaoIdeal</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$producaoMaxima</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$op</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$quantidadeOP</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantes, 2, ',', '.' ) . "</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessario, 2, ',', '.' ) . "</button></td>";
		echo "</tr>";
	}
	$rs->MoveNext ();
}

echo "<tr>";
// echo "<td>$data</td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-primary btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-info btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantesTotal, 2, ',', '.' ) . "</button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioTotal, 2, ',', '.' ) . "</button></td>";
echo "</tr>";

//$conteudo = ob_get_contents();
//ob_get_clean();
//echo $conteudo;
?>
</table>
			</div>


			<div class='well'>
				<h4>Hoje - <?php echo formataData($dataAtual);?></h4>

				<table class='table table-hover'>
					<tr>
						<?php imprimeCabecalho(); ?>
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
							        Z9.ZZ9_TEMPOP,
							        C2.C2_NUM,
							        C2.C2_QUANT
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							LEFT OUTER JOIN SC2010 C2 ON B1.B1_COD = C2.C2_PRODUTO AND C2.C2_ZEVENTO <> '5' AND C2.D_E_L_E_T_ <> '*'
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT = '$dataAtual'
									AND B1.B1_LOCPAD = '$localProducao'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP, C2.C2_NUM, C2.C2_QUANT
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$tempoNecessarioFaltantesTotal = 0;
$tempoNecessarioTotal = 0;

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	$kanbanVD = $fld [6]->value;
	$kanbanAM = $fld [7]->value;
	$kanbanVM = $fld [8]->value;
	$qtdPorKanban = $fld [9]->value;
	$tempoProducaoUnitario = $fld [10]->value;
	$op = $fld [11]->value;
	$quantidadeOP = $fld [12]->value;
	
	$falta = $qtdVendida - $qtdReservada - $estoque;
	if ($falta < 0) {
		$falta = 0;
	}
	$faltaTotaldePecas += $falta;
	
	$producaoMinima = ($kanbanVD * $qtdPorKanban) + $falta;
	$producaoIdeal = (($kanbanVD + $kanbanAM) * $qtdPorKanban) + $falta;
	$producaoMaxima = (($kanbanVD + $kanbanAM + $kanbanVM) * $qtdPorKanban) + $falta;
	$tempoNecessarioFaltantes = ($falta * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	$tempoNecessario = ($producaoMinima * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	
	if ($falta > 0) {
		// Somatórios
		$tempoNecessarioTotal += $tempoNecessario;
		$tempoNecessarioFaltantesTotal += $tempoNecessarioFaltantes;
		
		echo "<tr>";
		// echo "<td>$data</td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$codigo</button></td>";
		echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
		echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$producaoMinima</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$producaoIdeal</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$producaoMaxima</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$op</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$quantidadeOP</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantes, 2, ',', '.' ) . "</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessario, 2, ',', '.' ) . "</button></td>";
		echo "</tr>";
	}
	$rs->MoveNext ();
}

echo "<tr>";
// echo "<td>$data</td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-primary btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-info btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantesTotal, 2, ',', '.' ) . "</button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioTotal, 2, ',', '.' ) . "</button></td>";
echo "</tr>";

?>
</table>
			</div>
		</div>
	</div>

	<!-- <div class="col-xs-6 col-md-4">
		
	</div> -->



	<div class="col-xs-6 col-md-4">
		<div class="col">
			<div class='well'>
			<?php $dataPosterior = $dataAtual + 1 + $acrescimoDiasDataPosterior; ?>
				<h4>Próximo dia útil - <?php echo formataData($dataPosterior);?></h4>

				<table class='table table-hover'>
					<tr>
						<?php imprimeCabecalho(); ?>
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
							        Z9.ZZ9_TEMPOP,
							        C2.C2_NUM,
							        C2.C2_QUANT
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							LEFT OUTER JOIN SC2010 C2 ON B1.B1_COD = C2.C2_PRODUTO AND C2.C2_ZEVENTO <> '5' AND C2.D_E_L_E_T_ <> '*'
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT = '$dataPosterior'
									AND B1.B1_LOCPAD = '$localProducao'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP, C2.C2_NUM, C2.C2_QUANT
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$tempoNecessarioFaltantesTotal = 0;
$tempoNecessarioTotal = 0;

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	$kanbanVD = $fld [6]->value;
	$kanbanAM = $fld [7]->value;
	$kanbanVM = $fld [8]->value;
	$qtdPorKanban = $fld [9]->value;
	$tempoProducaoUnitario = $fld [10]->value;
	$op = $fld [11]->value;
	$quantidadeOP = $fld [12]->value;
	
	$falta = $qtdVendida - $qtdReservada - $estoque;
	if ($falta < 0) {
		$falta = 0;
	}
	
	$producaoMinima = ($kanbanVD * $qtdPorKanban) + $falta;
	$producaoIdeal = (($kanbanVD + $kanbanAM) * $qtdPorKanban) + $falta;
	$producaoMaxima = (($kanbanVD + $kanbanAM + $kanbanVM) * $qtdPorKanban) + $falta;
	$tempoNecessarioFaltantes = ($falta * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	$tempoNecessario = ($producaoMinima * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	
	if ($falta > 0) {
		// Somatórios
		$tempoNecessarioTotal += $tempoNecessario;
		$tempoNecessarioFaltantesTotal += $tempoNecessarioFaltantes;
		
		echo "<tr>";
		// echo "<td>$data</td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$codigo</button></td>";
		echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
		echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$producaoMinima</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$producaoIdeal</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$producaoMaxima</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$op</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$quantidadeOP</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantes, 2, ',', '.' ) . "</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessario, 2, ',', '.' ) . "</button></td>";
		echo "</tr>";
	}
	$rs->MoveNext ();
}

echo "<tr>";
// echo "<td>$data</td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-primary btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-info btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantesTotal, 2, ',', '.' ) . "</button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioTotal, 2, ',', '.' ) . "</button></td>";
echo "</tr>";

?>
</table>
			</div>




			<div class='well'>
			<?php $dataPosterior2Dias = $dataAtual + 2 + $acrescimoDiasData2DiasPosterior;?>
				<h4>2 dias úteis à frente - <?php echo formataData($dataPosterior2Dias);?></h4>

				<table class='table table-hover'>
					<tr>
						<?php imprimeCabecalho(); ?>
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
							        Z9.ZZ9_TEMPOP,
							        C2.C2_NUM,
							        C2.C2_QUANT,
							        B1.B1_DESC
							                                                               
							FROM SC5010 C5 WITH (NOLOCK)
							
							INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
							INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
							LEFT OUTER JOIN ZZ9010 Z9 ON B1.B1_COD = Z9.ZZ9_PRODUT
							LEFT OUTER JOIN SC2010 C2 ON B1.B1_COD = C2.C2_PRODUTO AND C2.C2_ZEVENTO <> '5' AND C2.D_E_L_E_T_ <> '*'
							                                                               
							WHERE 	C6.D_E_L_E_T_ <> '*'
									AND (C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '9')
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = '0101'
									AND C5.C5_FECENT = '$dataPosterior2Dias'
									AND B1.B1_LOCPAD = '$localProducao'
							
							GROUP BY C5.C5_FECENT, C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD, Z9.ZZ9_KANBVD, Z9.ZZ9_KANBAM, Z9.ZZ9_KANBVM, Z9.ZZ9_QTDKAN, Z9.ZZ9_TEMPOP, C2.C2_NUM, C2.C2_QUANT, B1.B1_DESC
							ORDER BY B1.B1_COD";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$tempoNecessarioFaltantesTotal = 0;
$tempoNecessarioTotal = 0;

while ( ! $rs->EOF ) {
	
	$data = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdVendida = $fld [2]->value;
	$qtdReservada = $fld [3]->value;
	$estoque = $fld [4]->value;
	$transito = $fld [5]->value;
	$kanbanVD = $fld [6]->value;
	$kanbanAM = $fld [7]->value;
	$kanbanVM = $fld [8]->value;
	$qtdPorKanban = $fld [9]->value;
	$tempoProducaoUnitario = $fld [10]->value;
	$op = $fld [11]->value;
	$quantidadeOP = $fld [12]->value;
	$descricao = $fld [13]->value;
	
	$falta = $qtdVendida - $qtdReservada - $estoque;
	if ($falta < 0) {
		$falta = 0;
	}
	
	$producaoMinima = ($kanbanVD * $qtdPorKanban) + $falta;
	$producaoIdeal = (($kanbanVD + $kanbanAM) * $qtdPorKanban) + $falta;
	$producaoMaxima = (($kanbanVD + $kanbanAM + $kanbanVM) * $qtdPorKanban) + $falta;
	$tempoNecessarioFaltantes = ($falta * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	$tempoNecessario = ($producaoMinima * $tempoProducaoUnitario) / 60 / $produtividadeMedia;
	
	if ($falta > 0) {
		// Somatórios
		$tempoNecessarioTotal += $tempoNecessario;
		$tempoNecessarioFaltantesTotal += $tempoNecessarioFaltantes;
		
		echo "<tr>";
		// echo "<td>$data</td>";
		echo "<td><button type='button' class='btn btn-default btn-xs' title='$descricao'>$codigo</button></td>";
		echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdVendida</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$qtdReservada</button></td>";
		echo "<td><button type='button' class='btn btn-info btn-xs'>$estoque</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$falta</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$transito</button></td>";
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$producaoMinima</button></td>";
		echo "<td><button type='button' class='btn btn-warning btn-xs'>$producaoIdeal</button></td>";
		echo "<td><button type='button' class='btn btn-success btn-xs'>$producaoMaxima</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$op</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>$quantidadeOP</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantes, 2, ',', '.' ) . "</button></td>";
		echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessario, 2, ',', '.' ) . "</button></td>";
		echo "</tr>";
	}
	$rs->MoveNext ();
}

echo "<tr>";
// echo "<td>$data</td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-primary btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-info btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-danger btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-warning btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-success btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'></button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioFaltantesTotal, 2, ',', '.' ) . "</button></td>";
echo "<td><button type='button' class='btn btn-default btn-xs'>" . number_format ( $tempoNecessarioTotal, 2, ',', '.' ) . "</button></td>";
echo "</tr>";

// $conteudo = ob_get_contents();

// ob_get_clean();

// echo $conteudo;
?>
</table>
			</div>
		</div>
	</div>
		
		<?php
		/**
		 * Segunda parte de informações
		 */
		
		// ob_start();
		?>

	<div class="col">
		<div class="col-xs-6 col-md-3">
			<div class='well'>
				<h4>Componentes OP's abertas</h4>

				<table class='table table-hover'>
					<tr>
						<th><button type='button' class='btn btn-default btn-xs'>Código</button></th>
						<th><button type='button' class='btn btn-primary btn-xs'>Em</button></th>
						<th><button type='button' class='btn btn-success btn-xs'>Es</button></th>
						<th><button type='button' class='btn btn-info btn-xs'>B</button></th>
						<th><button type='button' class='btn btn-danger btn-xs'>Forn</button></th>
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
							B1.B1_LOCPAD,
							B1.B1_DESC
							
							FROM SD4010 D4 WITH (NOLOCK)
							LEFT OUTER JOIN SB2010 B2 ON D4.D4_COD = B2.B2_COD AND B2.B2_LOCAL = @local
							LEFT OUTER JOIN SB1010 B1 ON D4.D4_COD = B1.B1_COD
							
							WHERE D4.D_E_L_E_T_ <> '*'
							AND D4.D4_LOCAL = @local
							AND D4.D4_COD NOT LIKE 'MOD%'
							AND D4.D4_QUANT > '0'
							
							GROUP BY D4.D4_COD, D4.D4_LOCAL, B1.B1_LOCPAD, B1.B1_COD, B1.B1_DESC--, D4.D4_QUANT
							ORDER BY B1.B1_LOCPAD, B1.B1_COD";

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
	$descricao = $fld [5]->value;
	
	echo "<tr>";
	echo "<td><button type='button' class='btn btn-default btn-xs' title='$descricao'>$codigo</button></td>";
	echo "<td><button type='button' class='btn btn-primary btn-xs'>$qtdEmpenhada</button></td>";
	echo "<td><button type='button' class='btn btn-success btn-xs'>$estoqueArea</button></td>";
	if ($balanco < 0) {
		echo "<td><button type='button' class='btn btn-danger btn-xs'>$balanco</button></td>";
	} else {
		echo "<td><button type='button' class='btn btn-info btn-xs'>$balanco</button></td>";
	}
	echo "<td><button type='button' class='btn btn-danger btn-xs'>$fornecedorInterno</button></td>";
	echo "</tr>";
	$rs->MoveNext ();
}

?>
</table>





			</div>
		</div>
	</div>






	<div class="col">
		<div class="col-xs-6 col-md-1">

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Produção do dia</h3>
				</div>
				<div class="panel-body">
							<?php
							$retornaSQL = "	SELECT 
			
							SUM(D3.D3_QUANT) 
			
							FROM SD3010 D3 WITH (NOLOCK)
	
							WHERE D3.D3_LOCAL = '$localProducao' 
							AND D3.D3_EMISSAO = '$dataAtual'
							AND D3.D3_TM = '010'
							AND D3.D3_FILIAL = '0101'";
							
							$rs = $conn->execute ( $retornaSQL );
							
							$num_columns = $rs->Fields->Count ();
							
							for($i = 0; $i < $num_columns; $i ++) {
								$fld [$i] = $rs->Fields ( $i );
							}
							
							// while ( ! $rs->EOF ) {
							
							$quantidade = $fld [0]->value;
							echo "$quantidade peças";
							$rs->MoveNext ();
							// }
							
							?>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Peças faltantes</h3>
				</div>
				<div class="panel-body">
							<?php
							// date_default_timezone_set('America/Sao_Paulo');
							// $horaAtual = date('H:i') - 7;
							// $horaAtual = $horaAtual - 7.00;
							/*
							 * $retornaSQL = ""; $rs = $conn->execute ( $retornaSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); } // while ( ! $rs->EOF ) { $quantidade = $fld [0]->value; echo $horaAtual; $rs->MoveNext (); // }
							 */
							// echo $horaAtual;
							echo "$faltaTotaldePecas peças";
							?>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Média de horas</h3>
				</div>
				<div class="panel-body">
							<?php
							$retornaSQL = "	SELECT AVG(Z8.ZZ8_TOTAL) AS Media 

											FROM ZZ8010 Z8 WITH (NOLOCK) 
											
											WHERE Z8.ZZ8_LOCAL = '$localProducao' 
											AND Z8.ZZ8_DATA BETWEEN DATEADD ( DAY , -7, CURRENT_TIMESTAMP) AND CURRENT_TIMESTAMP 
											AND Z8.D_E_L_E_T_<> '*'";
							
							$rs = $conn->execute ( $retornaSQL );
							
							$num_columns = $rs->Fields->Count ();
							
							for($i = 0; $i < $num_columns; $i ++) {
								$fld [$i] = $rs->Fields ( $i );
							}
							
							// while ( ! $rs->EOF ) {
							
							$mediaHorasSetor = $fld [0]->value;
							echo number_format ( $mediaHorasSetor, 2, ',', '.' ) . " horas<br />";
							echo number_format ( $mediaHorasSetor * 60, 2, ',', '.' ) . " minutos";
							$rs->MoveNext ();
							// }
							
							?>
				</div>
			</div>

		</div>
		<!--  Fecha a coluna secundária -->
	</div>
	<!--  Fecha a coluna principal -->




</div>



<!-- <div class="col-xs-6 col-md-4">
	
	</div>  -->







<?php
// $conteudo = ob_get_contents();

// ob_get_clean();

// echo $conteudo;

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>