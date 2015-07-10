<?php
// ob_end_flush();
$conn->open ( $connStr );
$item = 0;
ob_start();

$mostraComponentes = $_POST ['mostraComponentes'];
$pedidosAteODia = 'N';
$pedidosAteODia = $_POST ['checkboxDia'];
$mostraMP = $_POST ['mostraMP'];
$dataAtual = date ( 'Ymd' );
$dataAtualFormatada = date ( 'd/m/Y' );
$diaSemana = date ( 'D' );
$conteudo = NULL;

echo "<div class='well'>";
echo "<h4>Faltas em Pedidos que estão em Separação e Conferência</h4><hr>";

// echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=faltasEmPedidosComComponentes' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe a ordem que deseja visualizar: </b></span>";
echo "<br/>";

echo "<span><b>Informe o evento que deseja visualizar: </b></span><br />";
echo "<select name='estado'>";
if (isset ( $estado )) {
	echo "<option value='$estado'>$estado</option>";
} else {
	echo "<option value='-1'>Selecione o evento</option>";
}

echo "<option value='Separacao'>Separação</option>";
echo "<option value='Conferencia'>Conferência</option>";
echo "<option value='Todos'>Todos</option>";

echo "</select><br />";

// Checkboxes
echo "<div class='checkbox'>";
echo "<label>";
echo "<input type='checkbox' name='mostraComponentes' value = 'S' checked> Mostrar componentes";
echo "</label>";

echo "<br/><label>";
echo "<input type='checkbox' name='checkboxDia' value = 'S' checked> Mostrar somente pedidos do próximo dia, do dia e atrasados";
echo "</label>";

echo "<br/><label>";
echo "<input type='checkbox' name='mostraMP' value = 'S'> Mostrar matérias primas, misturas e material reciclado";
echo "</label>";
echo "</div>";

echo "<input type='submit' value='Buscar'>";
echo "</div>";

if (isset ( $estado )) {
	echo "<div class='well'>";
	echo "<table class='table table-condensed'>";
	
	if ($estado == "Separacao") {
		$queryEstado = "(C5.C5_EVENTO = '5')";
	} else if ($estado == "Conferencia") {
		$queryEstado = "(C5.C5_EVENTO = '6')";
	} else {
		$queryEstado = "(C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5')";
	}
	
	if ($pedidosAteODia == 'S') {
		if ($diaSemana == "Fri") {
			$acrescimoDias = 3;
		} else {
			$acrescimoDias = 1;
		}
		$queryData = "AND C5.C5_FECENT <= $dataAtual + $acrescimoDias";
	} else {
		$queryData = NULL;
	}
	
	if ($mostraMP == 'S') {
		$queryMostraMP = '';
	} else {
		$queryMostraMP = "AND G1.G1_COMP NOT LIKE 'MP________' AND G1.G1_COMP NOT LIKE 'MS________' AND G1.G1_COMP NOT LIKE 'MR________'";
	}
	
	$retornaSQL = "DECLARE @consultaSoMeta VARCHAR(1);
							DECLARE @filial VARCHAR(4);
							DECLARE @dataMeta DATE;
							DECLARE @localEstoque VARCHAR(6);
							DECLARE @localAreaTransito VARCHAR(6);
							DECLARE @localAreaTransitoExceto VARCHAR(6);
							
							
							SET @consultaSoMeta = '1';
							--SET @dataMeta = 'DATEPART(yymmdd, CURRENT_TIMESTAMP)';
							SET @dataMeta = GETDATE();
							--SET @dataMeta = '20150304';
							SET @filial = '0101';
							SET @localEstoque = 'AP-A01';
							SET @localAreaTransito = '%TR';
							SET @localAreaTransitoExceto = 'TR-TEM';
							
							SELECT  C6.C6_PRODUTO AS Produto,
							        SUM(C6.C6_QTDVEN) AS QtdVendida,
							        SUM(C6.C6_QTDRESE) AS QtdReservada,
							        B1.B1_LOCPAD AS LocalProducao,
							        ISNULL((SELECT B2.B2_QATU - B2.B2_RESERVA 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE @localEstoque)
							         				AND B2.D_E_L_E_T_ <> '*' 
							         				AND B1.B1_COD = B2.B2_COD
							        	   )
							        ,0) AS EstoqueDisponivel,
							        ISNULL((SELECT TOP 1 B2.B2_QATU 
							         		FROM SB2010 B2 WITH (NOLOCK)
							         		WHERE 	(B2.B2_LOCAL LIKE @localAreaTransito AND B2.B2_LOCAL NOT LIKE @localAreaTransitoExceto)
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
									AND $queryEstado
									AND (C6.C6_RESERVA = '' OR C6.C6_QTDRESE < C6.C6_QTDVEN)
									AND C5.C5_FILIAL = @filial
									--AND (B1.B1_COD = 'PL04400209' OR B1.B1_COD = 'PL04402409')
									$queryData
									
							GROUP BY C6.C6_PRODUTO, B1.B1_LOCPAD, B1.B1_COD
							
							ORDER BY B1.B1_LOCPAD, B1.B1_COD";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	while ( ! $rs->EOF ) {
		
		if ((empty ( $mostraCabecalhoPrincipal ) || $mostraComponentes == 'S')) {
			echo "<tr bgcolor = '#008000'><th>Item</th><th>Produto</th>";
			echo "<th>Falta</th><th>Local Produção</th><th>Estoque</th><th>Trânsito</th><th>+</th></tr>";
			$mostraCabecalhoPrincipal = 1;
		}
		
		$produto = $fld [0]->value;
		$qtdVendida = $fld [1]->value;
		$qtdReservada = $fld [2]->value;
		$localProducao = $fld [3]->value;
		$estoque = $fld [4]->value;
		$transito = $fld [5]->value;
		$falta = $qtdVendida - $qtdReservada;
		$status = NULL;
		
		if ($evento == 6) {
			$evento = "Em Conferência";
		} else if ($evento == 5) {
			$evento = "Em Separação";
		}
		
		if ($dataPrevista == $dataAtualFormatada) {
			$corData = '#FFD700';
		} else if ($dataPrevista < $dataAtualFormatada) {
			$corData = '#FF4500';
		} else {
			$corData = '#90EE90';
		}
		
		$item ++;
		echo "<tr bgcolor = '#008000'><td>$item</td>";
		echo "<td>$produto</td><!--<td>$qtdVendida</td><td>$qtdReservada</td>--><td>$falta</td><td>$localProducao</td>";
		echo "<td>$estoque</td><td>$transito</td><td></td>";
		// echo "<td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='.bs-example-modal-lg'>+</button></td>";
		echo "</tr>";
		
		// Bloco para mostrar os componentes
		if ($mostraComponentes == 'S') {
			echo "<tr><td colspan = '7'><table class='table table-condensed'><tr><th></th><th colspan = '5' width = '10px'>Nível</th><th>Componente</th><th>Descrição</th>";
			echo "<th>Qtd Unit.</th><th>Qtd. Total Necessária</th><th>Estoque</th><th>Estoque WIP</th><th>Qtd Faltante</th><th></th></tr>";
			
			$retornaSQL1 = NULL;
			$retornaSQL1 = "SELECT 

								G1.G1_COMP, 
								G1.G1_QUANT,
								B1.B1_FANTASM,
								G1.G1_NIV,
								SUBSTRING(B1.B1_DESC, 0, 40),
										(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE B2.B2_LOCAL <> 'PP-TRA'
										AND B2.B2_LOCAL NOT LIKE '%TR'
										AND B2.B2_LOCAL NOT LIKE '%EN'
										AND B2.B2_LOCAL <> 'AP-TRA'
										AND B2.B2_LOCAL <> 'AL-TRA'
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.B2_LOCAL <> 'ENG-ES'
										AND B2.B2_LOCAL <> 'MKT-ES'
										AND B2.B2_LOCAL <> 'EXP-ES'
										AND B2.B2_LOCAL <> 'PL2102'
										AND B2.B2_LOCAL <> 'TEM-TP'
										AND B2.B2_LOCAL <> 'TP-TEM'
										AND B2.B2_LOCAL <> 'ADM-01'
										AND B2.B2_LOCAL NOT IN ('01', '02', '04', '05', '06', '21', '4', '95', '98', '99', '000001', '')
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS Estoque,
										(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE (B2.B2_LOCAL = 'PP-TRA'
										OR B2.B2_LOCAL LIKE '%TR'
										OR B2.B2_LOCAL LIKE '%EN')
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS EstoqueEmProcesso

								FROM SG1010 G1 WITH (NOLOCK)
								INNER JOIN SB1010 B1 ON G1.G1_COMP = B1.B1_COD

								WHERE G1.G1_COD = '$produto'
								AND G1.D_E_L_E_T_ <> '*'
								AND G1.G1_COMP NOT LIKE 'MOD____'
								$queryMostraMP
								ORDER BY G1.G1_COMP";
			
			$rs1 = $conn->execute ( $retornaSQL1 );
			
			$num_columns1 = $rs1->Fields->Count ();
			
			for($i1 = 0; $i1 < $num_columns1; $i1 ++) {
				$fld1 [$i1] = $rs1->Fields ( $i1 );
			}
			
			$item1 = 0;
			
			while ( ! $rs1->EOF ) {
				
				$componente1 = $fld1 [0]->value;
				$quantidadeC1 = $fld1 [1]->value;
				$fantasma = $fld1 [2]->value;
				$nivelC1 = $fld1 [3]->value;
				$descricao1 = $fld1 [4]->value;
				$estoque1 = $fld1 [5]->value;
				$estoqueEmProcesso1 = $fld1 [6]->value;
				$qtdTotalNecessariaC1 = $quantidadeC1 * $falta;
				$icone1 = NULL;
				
				if ($fantasma == 'S') {
					$corProdutoFantasma = "#708090";
					$faltas1 = '-';
				} else {
					$corProdutoFantasma = NULL;
					if ($qtdTotalNecessariaC1 < $estoque1) {
						$faltas1 = 0;
						$temEstoque1 = 1;
						$icone1 = "<img src = 'img/OK_16x16.png'>";
					} else {
						$faltas1 = $qtdTotalNecessariaC1 - $estoque1;
						if ($faltas1 <= $estoqueEmProcesso1) {
							$icone1 = "<img src = 'img/alert_16x16.png'>";
						} else {
							$icone1 = "<img src = 'img/seta-vermelha16x16.png'>";
						}
					}
				}
				
				echo "<tr bgcolor = '$corProdutoFantasma'>";
				echo "<td bgcolor = '#191970' width = '2px'></td>";
				echo "<td>$nivelC1</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td>$componente1</td>";
				echo "<td>$descricao1</td>";
				echo "<td>$quantidadeC1</td>";
				echo "<td>$qtdTotalNecessariaC1</td>";
				echo "<td>$estoque1</td>";
				echo "<td>$estoqueEmProcesso1</td>";
				echo "<td>$faltas1</td>";
				echo "<td>$icone1</td>";
				echo "</tr>";
				
				/**
				 * Bloco para mostrar os componentes de 2º nivel
				 */
				
				// echo "<tr><td colspan = '6'><table class='table table-hover'><tr><th>Nível</th><th>Componente</th>";
				// echo "<th>Qtd Unit.</th><th>Qtd. Total Necessária</th><th>Estoque</th><th>Qtd Faltante</th></tr>";
				
				$retornaSQL2 = NULL;
				$retornaSQL2 = "SELECT
					
					G1.G1_COMP,
					G1.G1_QUANT,
					B1.B1_FANTASM,
					G1.G1_NIV,
					SUBSTRING(B1.B1_DESC, 0, 40),
							(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE B2.B2_LOCAL <> 'PP-TRA'
										AND B2.B2_LOCAL NOT LIKE '%TR'
										AND B2.B2_LOCAL NOT LIKE '%EN'
										AND B2.B2_LOCAL <> 'AP-TRA'
										AND B2.B2_LOCAL <> 'AL-TRA'
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.B2_LOCAL <> 'ENG-ES'
										AND B2.B2_LOCAL <> 'MKT-ES'
										AND B2.B2_LOCAL <> 'EXP-ES'
										AND B2.B2_LOCAL <> 'PL2102'
										AND B2.B2_LOCAL <> 'TEM-TP'
										AND B2.B2_LOCAL <> 'TP-TEM'
										AND B2.B2_LOCAL <> 'ADM-01'
										AND B2.B2_LOCAL NOT IN ('01', '02', '04', '05', '06', '21', '4', '95', '98', '99', '000001', '')
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS Estoque,
										(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE (B2.B2_LOCAL = 'PP-TRA'
										OR B2.B2_LOCAL LIKE '%TR'
										OR B2.B2_LOCAL LIKE '%EN')
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS EstoqueEmProcesso
					
					FROM SG1010 G1 WITH (NOLOCK)
					INNER JOIN SB1010 B1 ON G1.G1_COMP = B1.B1_COD
					
					WHERE G1.G1_COD = '$componente1'
					AND G1.D_E_L_E_T_ <> '*'
					AND G1.G1_COMP NOT LIKE 'MOD____'
					$queryMostraMP
					ORDER BY G1.G1_COMP";
				
				$rs2 = $conn->execute ( $retornaSQL2 );
				
				$num_columns2 = $rs2->Fields->Count ();
				
				for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
					$fld2 [$i2] = $rs2->Fields ( $i2 );
				}
				
				while ( ! $rs2->EOF ) {
					
					$componente2 = $fld2 [0]->value;
					$quantidadeC2 = $fld2 [1]->value;
					$fantasma2 = $fld2 [2]->value;
					$nivelC2 = $fld2 [3]->value;
					$descricao2 = $fld2 [4]->value;
					$estoque2 = $fld2 [5]->value;
					$estoqueEmProcesso2 = $fld2 [6]->value;
					$qtdTotalNecessariaC2 = $quantidadeC2 * $qtdTotalNecessariaC1;
					$icone2 = NULL;
					
					if ($fantasma2 == 'S') {
						$corProdutoFantasma = "#708090";
						$faltas2 = '-';
					} else {
						$corProdutoFantasma = NULL;
						if ($qtdTotalNecessariaC2 < $estoque2 || $temEstoque1 == '1') {
							$faltas2 = 0;
							$temEstoque2 = 1;
							$icone2 = "<img src = 'img/OK_16x16.png'>";
						} else {
							$faltas2 = $qtdTotalNecessariaC2 - $estoque2;
							if ($faltas2 <= $estoqueEmProcesso2) {
								$icone2 = "<img src = 'img/alert_16x16.png'>";
							} else {
								$icone2 = "<img src = 'img/seta-vermelha16x16.png'>";
							}
						}
					}
					
					echo "<tr bgcolor = '$corProdutoFantasma'>";
					echo "<td bgcolor = '#0000CD' width = '2px'></td><td></td><td>$nivelC2</td><td></td><td></td><td></td><td>$componente2</td><td>$descricao2</td>";
					echo "<td>$quantidadeC2</td><td>$qtdTotalNecessariaC2</td>";
					echo "<td>$estoque2</td>";
					echo "<td>$estoqueEmProcesso2</td>";
					echo "<td>$faltas2</td><td>$icone2</td></tr>";
					
					/**
					 * Bloco para mostrar os componentes de 3º nivel
					 */
					
					// echo "<tr><td colspan = '6'><table class='table table-hover'><tr><th>Nível</th><th>Componente</th>";
					// echo "<th>Qtd Unit.</th><th>Qtd. Total Necessária</th><th>Estoque</th><th>Qtd Faltante</th></tr>";
					
					$retornaSQL3 = NULL;
					$retornaSQL3 = "SELECT
							
						G1.G1_COMP,
						G1.G1_QUANT,
						B1.B1_FANTASM,
						G1.G1_NIV,
						SUBSTRING(B1.B1_DESC, 0, 40),
						(SELECT
						
						SUM(B2.B2_QATU)
						
						FROM SB2010 B2 WITH (NOLOCK)
						
						WHERE B2.B2_LOCAL <> 'PP-TRA'
						AND B2.B2_LOCAL NOT LIKE '%TR'
						AND B2.B2_LOCAL NOT LIKE '%EN'
						AND B2.B2_LOCAL <> 'AP-TRA'
						AND B2.B2_LOCAL <> 'AL-TRA'
						AND B2.B2_LOCAL <> 'TR-TEM'
						AND B2.B2_LOCAL <> 'ENG-ES'
						AND B2.B2_LOCAL <> 'MKT-ES'
						AND B2.B2_LOCAL <> 'EXP-ES'
						AND B2.B2_LOCAL <> 'PL2102'
						AND B2.B2_LOCAL <> 'TEM-TP'
						AND B2.B2_LOCAL <> 'TP-TEM'
						AND B2.B2_LOCAL <> 'ADM-01'
						AND B2.B2_LOCAL NOT IN ('01', '02', '04', '05', '06', '21', '4', '95', '98', '99', '000001', '')
						AND B2.D_E_L_E_T_ <> '*'
						AND B2.B2_COD = G1.G1_COMP) AS Estoque,
						(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE (B2.B2_LOCAL = 'PP-TRA'
										OR B2.B2_LOCAL LIKE '%TR'
										OR B2.B2_LOCAL LIKE '%EN')
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS EstoqueEmProcesso
							
						FROM SG1010 G1 WITH (NOLOCK)
						INNER JOIN SB1010 B1 ON G1.G1_COMP = B1.B1_COD
							
						WHERE G1.G1_COD = '$componente2'
						AND G1.D_E_L_E_T_ <> '*'
						AND G1.G1_COMP NOT LIKE 'MOD____'
						$queryMostraMP
						ORDER BY G1.G1_COMP";
					
					$rs3 = $conn->execute ( $retornaSQL3 );
					
					$num_columns3 = $rs3->Fields->Count ();
					
					for($i3 = 0; $i3 < $num_columns3; $i3 ++) {
						$fld3 [$i3] = $rs3->Fields ( $i3 );
					}
					
					while ( ! $rs3->EOF ) {
						
						$componente3 = $fld3 [0]->value;
						$quantidadeC3 = $fld3 [1]->value;
						$fantasma3 = $fld3 [2]->value;
						$nivelC3 = $fld3 [3]->value;
						$descricao3 = $fld3 [4]->value;
						$estoque3 = $fld3 [5]->value;
						$estoqueEmProcesso3 = $fld3 [6]->value;
						$qtdTotalNecessariaC3 = $quantidadeC3 * $qtdTotalNecessariaC2;
						$icone3 = null;
						
						if ($fantasma3 == 'S') {
							$corProdutoFantasma = "#708090";
							$faltas3 = '-';
						} else {
							$corProdutoFantasma = null;
							if ($qtdTotalNecessariaC3 < $estoque3 || $temEstoque2 == '1') {
								$faltas3 = 0;
								$temEstoque3 = 1;
								$icone3 = "<img src = 'img/OK_16x16.png'>";
							} else {
								$faltas3 = $qtdTotalNecessariaC3 - $estoque3;
								if ($faltas3 <= $estoqueEmProcesso3) {
									$icone3 = "<img src = 'img/alert_16x16.png'>";
								} else {
									$icone3 = "<img src = 'img/seta-vermelha16x16.png'>";
								}
							}
						}
						
						echo "<tr bgcolor = '$corProdutoFantasma'>";
						echo "<td bgcolor = '#4682B4' width = '2px'></td><td></td><td></td><td>$nivelC3</td><td></td><td></td><td>$componente3</td><td>$descricao3</td>";
						echo "<td>$quantidadeC3</td><td>$qtdTotalNecessariaC3</td>";
						echo "<td>$estoque3</td>";
						echo "<td>$estoqueEmProcesso3</td>";
						echo "<td>$faltas3</td><td>$icone3</td></tr>";
						
						/**
						 * Bloco para mostrar os componentes de 4º nivel
						 */
						
						$retornaSQL4 = NULL;
						$retornaSQL4 = "SELECT
								
							G1.G1_COMP,
							G1.G1_QUANT,
							B1.B1_FANTASM,
							G1.G1_NIV,
							SUBSTRING(B1.B1_DESC, 0, 40),
							(SELECT
							
							SUM(B2.B2_QATU)
							
							FROM SB2010 B2 WITH (NOLOCK)
							
							WHERE B2.B2_LOCAL <> 'PP-TRA'
							AND B2.B2_LOCAL NOT LIKE '%TR'
							AND B2.B2_LOCAL NOT LIKE '%EN'
							AND B2.B2_LOCAL <> 'AP-TRA'
							AND B2.B2_LOCAL <> 'AL-TRA'
							AND B2.B2_LOCAL <> 'TR-TEM'
							AND B2.B2_LOCAL <> 'ENG-ES'
							AND B2.B2_LOCAL <> 'MKT-ES'
							AND B2.B2_LOCAL <> 'EXP-ES'
							AND B2.B2_LOCAL <> 'PL2102'
							AND B2.B2_LOCAL <> 'TEM-TP'
							AND B2.B2_LOCAL <> 'TP-TEM'
							AND B2.B2_LOCAL <> 'ADM-01'
							AND B2.B2_LOCAL NOT IN ('01', '02', '04', '05', '06', '21', '4', '95', '98', '99', '000001', '')
							AND B2.D_E_L_E_T_ <> '*'
							AND B2.B2_COD = G1.G1_COMP) AS Estoque,
							(SELECT 

										SUM(B2.B2_QATU) 
										
										FROM SB2010 B2 WITH (NOLOCK) 
										
										WHERE (B2.B2_LOCAL = 'PP-TRA'
										OR B2.B2_LOCAL LIKE '%TR'
										OR B2.B2_LOCAL LIKE '%EN')
										AND B2.B2_LOCAL <> 'TR-TEM'
										AND B2.D_E_L_E_T_ <> '*'
										AND B2.B2_COD = G1.G1_COMP) AS EstoqueEmProcesso
								
							FROM SG1010 G1 WITH (NOLOCK)
							INNER JOIN SB1010 B1 ON G1.G1_COMP = B1.B1_COD
								
							WHERE G1.G1_COD = '$componente3'
							AND G1.D_E_L_E_T_ <> '*'
							AND G1.G1_COMP NOT LIKE 'MOD____'
							$queryMostraMP
							ORDER BY G1.G1_COMP";
						
						$rs4 = $conn->execute ( $retornaSQL4 );
						
						$num_columns4 = $rs4->Fields->Count ();
						
						for($i4 = 0; $i4 < $num_columns4; $i4 ++) {
							$fld4 [$i4] = $rs4->Fields ( $i4 );
						}
						
						while ( ! $rs4->EOF ) {
							
							$componente4 = $fld4 [0]->value;
							$quantidadeC4 = $fld4 [1]->value;
							$fantasma4 = $fld4 [2]->value;
							$nivelC4 = $fld4 [3]->value;
							$descricao4 = $fld4 [4]->value;
							$estoque4 = $fld4 [5]->value;
							$estoqueEmProcesso4 = $fld4 [6]->value;
							$qtdTotalNecessariaC4 = $quantidadeC4 * $qtdTotalNecessariaC3;
							$icone4 = null;
							
							if ($fantasma4 == 'S') {
								$corProdutoFantasma = "#708090";
								$faltas4 = '-';
							} else {
								$corProdutoFantasma = null;
								if ($qtdTotalNecessariaC4 < $estoque4 || $temEstoque3 == '1') {
									$faltas4 = 0;
									$temEstoque4 = 1;
									$icone4 = "<img src = 'img/OK_16x16.png'>";
								} else {
									$faltas4 = $qtdTotalNecessariaC4 - $estoque4;
									if ($faltas4 <= $estoqueEmProcesso4) {
										$icone4 = "<img src = 'img/alert_16x16.png'>";
									} else {
										$icone4 = "<img src = 'img/seta-vermelha16x16.png'>";
									}
								}
							}
							
							echo "<tr bgcolor = '$corProdutoFantasma'>";
							echo "<td bgcolor = '#00BFFF' width = '2px'></td><td></td><td></td><td></td><td>$nivelC4</td><td></td><td>$componente4</td><td>$descricao4</td>";
							echo "<td>$quantidadeC4</td><td>$qtdTotalNecessariaC4</td>";
							echo "<td>$estoque4</td>";
							echo "<td>$estoqueEmProcesso4</td>";
							echo "<td>$faltas4</td><td>$icone4</td></tr>";
							
							/**
							 * Bloco para mostrar os componentes de 5º nivel
							 */
							
							$retornaSQL5 = NULL;
							$retornaSQL5 = "SELECT
							
							G1.G1_COMP,
							G1.G1_QUANT,
							B1.B1_FANTASM,
							G1.G1_NIV,
							SUBSTRING(B1.B1_DESC, 0, 40),
							(SELECT
								
							SUM(B2.B2_QATU)
								
							FROM SB2010 B2 WITH (NOLOCK)
								
							WHERE B2.B2_LOCAL <> 'PP-TRA'
							AND B2.B2_LOCAL NOT LIKE '%TR'
							AND B2.B2_LOCAL NOT LIKE '%EN'
							AND B2.B2_LOCAL <> 'AP-TRA'
							AND B2.B2_LOCAL <> 'AL-TRA'
							AND B2.B2_LOCAL <> 'TR-TEM'
							AND B2.B2_LOCAL <> 'ENG-ES'
							AND B2.B2_LOCAL <> 'MKT-ES'
							AND B2.B2_LOCAL <> 'EXP-ES'
							AND B2.B2_LOCAL <> 'PL2102'
							AND B2.B2_LOCAL <> 'TEM-TP'
							AND B2.B2_LOCAL <> 'TP-TEM'
							AND B2.B2_LOCAL <> 'ADM-01'
							AND B2.B2_LOCAL NOT IN ('01', '02', '04', '05', '06', '21', '4', '95', '98', '99', '000001', '')
							AND B2.D_E_L_E_T_ <> '*'
							AND B2.B2_COD = G1.G1_COMP) AS Estoque,
							(SELECT 

							SUM(B2.B2_QATU) 
										
							FROM SB2010 B2 WITH (NOLOCK) 
										
							WHERE (B2.B2_LOCAL = 'PP-TRA'
							OR B2.B2_LOCAL LIKE '%TR'
							OR B2.B2_LOCAL LIKE '%EN')
							AND B2.B2_LOCAL <> 'TR-TEM'
							AND B2.D_E_L_E_T_ <> '*'
							AND B2.B2_COD = G1.G1_COMP) AS EstoqueEmProcesso
							
							FROM SG1010 G1 WITH (NOLOCK)
							INNER JOIN SB1010 B1 ON G1.G1_COMP = B1.B1_COD
							
							WHERE G1.G1_COD = '$componente4'
							AND G1.D_E_L_E_T_ <> '*'
							AND G1.G1_COMP NOT LIKE 'MOD____'
							$queryMostraMP
							ORDER BY G1.G1_COMP";
							
							$rs5 = $conn->execute ( $retornaSQL5 );
							
							$num_columns5 = $rs5->Fields->Count ();
							
							for($i5 = 0; $i5 < $num_columns5; $i5 ++) {
								$fld5 [$i5] = $rs5->Fields ( $i5 );
							}
							
							
							$componente5 = NULL;
							$quantidadeC5 = NULL;
							$fantasma5 = NULL;
							$nivelC5 = NULL;
							$descricao5 = NULL;
							$estoque5 = NULL;
							$estoqueEmProcesso5 = NULL;
							$qtdTotalNecessariaC5 = NULL;
							$icone5 = NULL;
							$corProdutoFantasma = NULL;
							$faltas5 = NULL;
							$icone5 = NULL;
							
							while ( ! $rs5->EOF ) {
								
								$componente5 = $fld5 [0]->value;
								$quantidadeC5 = $fld5 [1]->value;
								$fantasma5 = $fld5 [2]->value;
								$nivelC5 = $fld5 [3]->value;
								$descricao5 = $fld5 [4]->value;
								$estoque5 = $fld5 [5]->value;
								$estoqueEmProcesso5 = $fld5 [6]->value;
								$qtdTotalNecessariaC5 = $quantidadeC5 * $qtdTotalNecessariaC4;
								$icone5 = NULL;
								
								if ($fantasma5 == 'S') {
									$corProdutoFantasma = "#708090";
									$faltas5 = '-';
								} else {
									$corProdutoFantasma = null;
									if ($qtdTotalNecessariaC5 < $estoque5 || $temEstoque4 == '1') {
										$faltas5 = 0;
										$icone5 = "<img src = 'img/OK_16x16.png'>";
									} else {
										$faltas5 = $qtdTotalNecessariaC5 - $estoque5;
										if ($faltas5 <= $estoqueEmProcesso5) {
											$icone5 = "<img src = 'img/alert_16x16.png'>";
										} else {
											$icone5 = "<img src = 'img/seta-vermelha16x16.png'>";
										}
									}
								}
								
								echo "<tr bgcolor = '$corProdutoFantasma'>";
								echo "<td bgcolor = '#B0C4DE' width = '2px'></td><td></td><td></td><td></td><td></td><td>$nivelC5</td><td>$componente5</td><td>$descricao5</td>";
								echo "<td>$quantidadeC5</td><td>$qtdTotalNecessariaC5</td>";
								echo "<td>$estoque5</td>";
								echo "<td>$estoqueEmProcesso5</td>";
								echo "<td>$faltas5</td><td>$icone5</td></tr>";
								
								$rs5->MoveNext ();
							}
							$rs5->Close ();
							$rs5 = NULL;
							
							/**
							 * Fim do bloco para mostrar os componentes de 5º nivel
							 */
							
							$rs4->MoveNext ();
						}
						$rs4->Close ();
						$rs4 = NULL;
						
						/**
						 * Fim do bloco para mostrar os componentes de 4º nivel
						 */
						
						$rs3->MoveNext ();
					}
					$rs3->Close ();
					$rs3 = NULL;
					
					/**
					 * Fim do bloco para mostrar os componentes de 3º nivel
					 */
					
					$rs2->MoveNext ();
				}
				$rs2->Close ();
				$rs2 = NULL;
				
				/**
				 * Fim do bloco para mostrar os componentes de 2º nivel
				 */
				
				$rs1->MoveNext ();
			}
			// $rs2->MoveNext ();
			// $rs2->MoveNext ();
			$rs1->Close ();
			$rs1 = NULL;
			echo "</table></td></tr>";
			
			// Fim do bloco para mostrar os componentes
		}
		
		$rs->MoveNext ();
	}
	
	// $rs->MoveNext ();
	
	echo "</table>";
	echo "</div>";
}

$rs->Close ();
$rs = NULL;

$conteudo = ob_get_contents();

ob_get_clean();

echo $conteudo;

$conn->Close ();
$conn = NULL;
?>