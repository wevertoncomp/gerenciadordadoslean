<?php
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Rela��o Produto/Embalagem</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'>";
	echo "<tr>";
	echo "<th>Produto</th>";
	echo "<th>Qtd. Emb.</th>";
	echo "<th>Caixa Produto</th>";
	echo "<th>Caixa Estrut.</th>";
	echo "<th>Local</th>";
	echo "<th>Saquinho</th>";
	echo "<th>Descri��o Saquinho</th>";
	echo "<th>Local Kanban</th>";
	echo "<th>VD</th>";
	echo "<th>AM</th>";
	echo "<th>VM</th>";
	echo "<th>Por Kanban</th>";
	echo "<th>Total</th>";
	echo "<th>Caixas</th>";
	echo "</tr>";
	
	$retornaSQL = "SELECT RTRIM(B1.B1_COD),
					RTRIM(B1.B1_QE),
					RTRIM(B5.B5_EMB1),
					RTRIM(G1_1.G1_COMP),
					RTRIM(B1.B1_LOCPAD),
					RTRIM(G1.G1_COMP),
					RTRIM(B1_1.B1_DESC),
					RTRIM(Z9.ZZ9_LOCAL),
					RTRIM(Z9.ZZ9_KANBVD),
					RTRIM(Z9.ZZ9_KANBAM),
					RTRIM(Z9.ZZ9_KANBVM),
					RTRIM(Z9.ZZ9_QTDKAN)
					
					FROM SB1010 B1 WITH (NOLOCK)
						LEFT OUTER JOIN SB5010 B5
							ON B1.B1_COD = B5.B5_COD
						LEFT OUTER JOIN SG1010 G1
							ON B1.B1_COD = G1.G1_COD
							AND G1.G1_COMP LIKE 'MP0050____'
							AND G1.D_E_L_E_T_ <> '*'
							AND G1.G1_COMP <> 'MP00500291'
						LEFT OUTER JOIN SB1010 B1_1
							ON G1.G1_COMP = B1_1.B1_COD
						LEFT OUTER JOIN SG1010 G1_1
							ON B1.B1_COD = G1_1.G1_COD
							AND G1_1.G1_COMP LIKE 'KIT-CAIXA%'
							AND G1_1.D_E_L_E_T_ <> '*'
						LEFT OUTER JOIN ZZ9010 Z9
							ON B1.B1_COD = Z9.ZZ9_PRODUT
							AND Z9.D_E_L_E_T_ <> '*'
							
					WHERE B1.D_E_L_E_T_ <> '*'
						AND B1.B1_COD LIKE 'PL________'
						AND B1.B1_COD NOT LIKE 'PL00______'
						AND B1.B1_XTPLSPR <> '4'
						AND B1.B1_XTPLSPR <> '6'
						AND B1.B1_XTPLSPR <> '8'
						
					ORDER BY B1.B1_COD";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	
	while ( ! $rs->EOF ) {
	
		$codigoProduto 		= $fld [0]->value;
		$qtdEmbalagem 		= $fld [1]->value;
		$caixaProduto 		= $fld [2]->value;
		$caixaEstrutura 	= $fld [3]->value;
		$armazem 			= $fld [4]->value;
		$codigoSaquinho 	= $fld [5]->value;
		$descricaoSaquinho 	= $fld [6]->value;
		$localKanban 		= $fld [7]->value;
		$kanbanVD 			= $fld [8]->value;
		$kanbanAM 			= $fld [9]->value;
		$kanbanVM			= $fld [10]->value;
		$qtdPorKanban 		= $fld [11]->value;
		
		$total = NULL;
		$caixasNecessarias = NULL;
		
		$total = ($kanbanVD + $kanbanAM + $kanbanVM) * $qtdPorKanban;
		$caixasNecessarias = ($total / $qtdEmbalagem);
		

		$caixaProd = substr($caixaProduto, 0, 3);
		$caixaEstrut = substr($caixaEstrutura, 9, 12);
		
 		if ($caixaProd != $caixaEstrut) {
 			$corCaixa = "#FF8C00";
 		} else {
 			$corCaixa = NULL;
 		}
 		
 		if (empty($codigoSaquinho)) {
 			$corSaquinho = "#FF8C00";
 		} else {
 			$corSaquinho = NULL;
 		}
 		
 		if ($armazem != $localKanban) {
 			$corVerificacaoKanban = "#FF8C00";
 		} else {
 			$corVerificacaoKanban = NULL;
 		}
		
		echo "<tr>";
		echo "<td>$codigoProduto</td>";
		echo "<td>$qtdEmbalagem</td>";
		echo "<td>$caixaProduto</td>";
		echo "<td bgcolor = '$corCaixa'>$caixaEstrutura</td>";
		echo "<td>$armazem</td>";
		echo "<td bgcolor = '$corSaquinho'>$codigoSaquinho</td>";
		echo "<td bgcolor = '$corSaquinho'>$descricaoSaquinho</td>";
		echo "<td bgcolor = '$corVerificacaoKanban'>$localKanban</td>";
		echo "<td>$kanbanVD</td>";
		echo "<td>$kanbanAM</td>";
		echo "<td>$kanbanVM</td>";
		echo "<td>$qtdPorKanban</td>";
		echo "<td>$total</td>";
		echo "<td>$caixasNecessarias</td>";
		echo "</tr>";
		$rs->MoveNext ();
	}
	
	//$rs->MoveNext ();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;


$conn->Close ();
$conn = null;
?>