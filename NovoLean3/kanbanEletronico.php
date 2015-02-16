<?php
$conn->open ( $connStr );

//$setor = 0;
echo "<div class='well'>";
echo "<h4>Kanban Eletrônico</h4><hr>";

echo "<form action='?pg=kanbanEletronico' method = 'post'>";
echo "<span><b>Informe o local que deseja visualizar: </b></span>";
echo "<br/>";

$setor = $_POST ['setor'];
//echo $setor;

// Combobox da data

$instrucaoSQL = "SELECT 
				 NR.NNR_CODIGO AS CODIGO, 
				 NR.NNR_DESCRI AS DESCRICAO
				 FROM NNR010 NR
				 WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%'
				 AND NR.NNR_DESCRI LIKE '%PRODUCAO%'
				 --AND NR.NNR_DESCRI LIKE '%MONTAGEM 2%'
				 AND NR.NNR_CODIGO <> 'SOM-TR'
				 ORDER BY NR.NNR_DESCRI";
$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

echo "<select name='setor'>";
if (isset ( $setor )) {
	echo "<option value='$setor'>$setor</option>";
} else {
	echo "<option value='-1'>Selecione o local</option>";
}
while ( ! $rs->EOF ) {

	//for($i = 0; $i < $num_columns; $i ++) {
		echo "<option value=" . $fld [0]->value . ">" . substr ( $fld [1]->value, 0, strpos ( $fld [1]->value, " -" ) ) . "</option>";
	//}
	$rs->MoveNext ();
}

echo "</select>";

$rs->Close ();
$rs = null;

echo "</select><br />";

// Combobox do setor
/*
 * $instrucaoSQL = "SELECT NR.NNR_CODIGO, NR.NNR_DESCRI FROM NNR010 NR WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%' AND NR.NNR_DESCRI LIKE '%PRODUCAO%' ORDER BY NR.NNR_DESCRI"; $rs = $conn->execute ( $instrucaoSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); } echo "<select name='setor'>"; echo "<option value='-1'>Selecione o local</option>"; while ( ! $rs->EOF ) { echo "<option value=" . $fld [0]->value . ">" . $fld [1]->value . "</option>"; $rs->MoveNext (); } $rs->Close (); $rs = null; echo "</select>";
*/

echo "<input type='submit' value='Buscar'>";
echo "</div>";

/*$instrucaoSQL = "SELECT 

				Z9.ZZ9_PRODUT,
				Z9.ZZ9_LOCAL,
				Z9.ZZ9_CLABC, 
				Z9.ZZ9_KANBVD, 
				Z9.ZZ9_KANBAM, 
				Z9.ZZ9_KANBVM,
				Z9.ZZ9_QTDKAN,
				Z9.ZZ9_TEMPOP,
				Z9.ZZ9_TEMPOT,
				Z9.ZZ9_CXKANB,	
				(SELECT B2.B2_QATU FROM SB2010 B2
				WHERE (B2.B2_LOCAL LIKE 'AP-A01')
				AND B2.B2_LOCAL NOT LIKE '%-TRA' AND B2.B2_COD =  Z9.ZZ9_PRODUT) AS ESTOQUE,
				B1.B1_LOCPAD
				
				FROM ZZ9010 Z9
				
				INNER JOIN SB1010 B1 ON Z9.ZZ9_PRODUT = B1.B1_COD
				
				WHERE Z9.ZZ9_LOCAL = '$setor'
		
				ORDER BY Z9.ZZ9_CLABC";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$contador = 0;

echo "<table><tr>";
while ( ! $rs->EOF ) {
	
	$produto = 				$fld [0]->value;
	$local = 				$fld [1]->value;
	$classificacaoABC = 	$fld [2]->value;
	$kanbanVD = 			$fld [3]->value;
	$kanbanAM = 			$fld [4]->value;
	$kanbanVM = 			$fld [5]->value;
	$qtdKanban = 			$fld [6]->value;
	$tempoUnitario = 		$fld [7]->value;
	$tempoTotal = 			$fld [8]->value;
	$qtdCaixas = 			$fld [9]->value;
	$estoque = 				$fld [10]->value;
	$localPadrao = 			$fld [11]->value;
	$qtdTotal = 			($kanbanVD + $kanbanAM + $kanbanVM) * $qtdKanban;
	
	$porcentagem =			($estoque / $qtdTotal) * 100;
	$qtdFaltante =			$qtdTotal - $estoque; //Calcula quantidade do produto no quadro
	
	$porcentagemNoEstoque = ($qtdFaltante / $qtdTotal) * 100;
	$porcentagemNoQuadro = (($qtdTotal - $estoque) / $qtdTotal) * 100;
	
	if ($porcentagemNoQuadro < 0) {
		$porcentagemNoQuadro = 0;
	}
	
	if ($porcentagemNoQuadro >= 66.66) {
		$corBotao = "btn-danger";
		$porcentagemNoVerde = 33.33;
		$porcentagemNoAmarelo = 33.33;
		$porcentagemNoVermelho = $porcentagemNoQuadro - 66.666;
	} else if ($porcentagemNoQuadro >= 33.33 && $porcentagemNoQuadro < 66.66) {
		$corBotao = "btn-warning";
		$porcentagemNoVerde = 33.33;
		$porcentagemNoAmarelo = $porcentagemNoQuadro - 33.33;
		$porcentagemNoVermelho = 0;
	} else {
		$corBotao = "btn-success";
		$porcentagemNoVerde = $porcentagemNoQuadro;
		$porcentagemNoAmarelo = 0;
		$porcentagemNoVermelho = 0;
	}
	
	if ($contador%7==0){
		echo "</tr><tr><td><br /></td></tr><tr>";
	}
	$contador++;*/

	/***
	 * Montagem do Kanban
	 */
	/*echo "<td>";
	echo "<table class='table-bordered' style='background-color: #f7f7f7'><tr>";
	echo "<td colspan=2 style='text-align: center;'>$produto</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td colspan=2 style='text-align:left; min-width:160px; max-width:160px; height:10px;'>";
	echo "<div class='progress'>";
		echo "<div class='bar bar-success' style='width: $porcentagemNoVerde%;'></div>";
		echo "<div class='bar bar-warning' style='width: $porcentagemNoAmarelo%;'></div>";
		echo "<div class='bar bar-danger' style='width: $porcentagemNoVermelho%;'></div>";
	echo "<span class='progress-value'>" . ( int ) $porcentagemNoQuadro . "%</span></div>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr>";
		echo "<td style='min-width:80px; text-align: center;'><button type='button' class='btn btn-mini $corBotao $disabled' style='min-width:80px;'>" . number_format ( $tempoProducaoProdutosEmHoras, 1, '.', '' ) . " Horas</button></td>";
		echo "<td style='min-width:80px; text-align:center;'><button type='button' class='btn btn-mini $corBotao $disabled' style='min-width:80px;'>$localPadrao</button></td>";
	echo "</tr>";
	
			echo "</table>";
	
	echo "</td>";

	$rs->MoveNext ();
}
echo "</tr></table>";


$rs->Close ();
$rs = null;
echo "<br /><br /><br />";*/


$instrucaoSQL = "SELECT
				Z9.ZZ9_PRODUT,
				Z9.ZZ9_CLABC,
				Z9.ZZ9_KANBVD,
				Z9.ZZ9_KANBAM,
				Z9.ZZ9_KANBVM,
				Z9.ZZ9_QTDKAN,
				Z9.ZZ9_TEMPOP,
				Z9.ZZ9_TEMPOT,
				Z9.ZZ9_QTDOPE,
				Z9.ZZ9_CXKANB,
				(SELECT B2.B2_QATU FROM SB2010 B2
				WHERE (B2.B2_LOCAL LIKE 'AP-A01')
				AND B2.B2_LOCAL NOT LIKE '%-TRA' AND B2.B2_COD =  Z9.ZZ9_PRODUT) AS ESTOQUE,
				(SELECT B3.B3_MEDIA FROM SB3010 B3 WHERE B3.B3_COD = Z9.ZZ9_PRODUT AND B3.B3_FILIAL = '0101') AS DEMANDA
				
				FROM ZZ9010 Z9
				
				WHERE 
				
				Z9.ZZ9_LOCAL = '$setor'
				
				ORDER BY Z9.ZZ9_CLABC, Z9.ZZ9_PRODUT";

$rs = $conn->execute ( $instrucaoSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}

$estoqueMaximo = 0;
$estoqueTotal = 0;

echo "<table class='table table-hover'><tr><th>Produto</th><th>ABC</th><th>VD</th><th>AM</th><th>VM</th><th>Por Kanban</th><th>Tempo Unit.</th><th>Tempo Total</th><th>Operadores</th><th>Qtd Caixas</th><th>Qtd Total</th><th>Estoque</th><th>%</th></tr>";
while ( ! $rs->EOF ) {

	$produto = 				$fld [0]->value;
	$classificacaoABC = 	$fld [1]->value;
	$kanbanVD = 			$fld [2]->value;
	$kanbanAM = 			$fld [3]->value;
	$kanbanVM = 			$fld [4]->value;
	$qtdKanban = 			$fld [5]->value;
	$tempoUnitario = 		$fld [6]->value;
	$tempoTotal = 			$fld [7]->value;
	$qtdOperadores = 		$fld [8]->value;
	$qtdCaixas = 			$fld [9]->value;
	$estoque = 				$fld [10]->value;
	$qtdTotal = 			($kanbanVD + $kanbanAM + $kanbanVM) * $qtdKanban;
	$porcentagem = 			($estoque / $qtdTotal)*100;
	$estoqueMaximo +=		$qtdTotal;
	$estoqueTotal +=		$estoque;
	
	$kanbanVDQuadro = 0;
	$kanbanAMQuadro = 0;
	$kanbanVMQuadro = 0;
	

	echo "<tr>";
	echo "<td>$produto</td>";
	echo "<td>$classificacaoABC</td>";
	echo "<td>$kanbanVDQuadro($kanbanVD)</td>";
	echo "<td>$kanbanAMQuadro($kanbanAM)</td>";
	echo "<td>$kanbanVMQuadro($kanbanVM)</td>";
	echo "<td>$qtdKanban</td>";
	echo "<td>$tempoUnitario</td>";
	echo "<td>$tempoUnitario</td>";
	echo "<td>$qtdOperadores</td>";
	echo "<td>$qtdCaixas</td>";
	echo "<td>$qtdTotal</td>";
	echo "<td>$estoque</td>";
	echo "<td>". number_format ($porcentagem, 0, '.', '' ) . "%</td>";
	echo "</tr>";

	$rs->MoveNext ();
}
echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><b>$estoqueMaximo</b></td><td><b>$estoqueTotal</b></td>";
echo "<td><b>" . number_format ((($estoqueTotal/$estoqueMaximo)*100), 0, '.', '' ) . " %</b></td></tr>";
echo "</table>";

$rs->Close ();
$rs = null;
	

?>