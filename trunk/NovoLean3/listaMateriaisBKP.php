<?php
// ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
$codigo = null;
echo "<div class='well'>";
echo "<h4>Listas de Materiais</h4><hr>";

echo "<form action='?pg=listaMateriais' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o produto que deseja visualizar: </b></span>";
echo "<br/>";

// $data = $_GET ['dia'];
$codigo = $_POST ['codigo'];

// Combobox do produto

/*
 * $instrucaoSQL = "SELECT TOP 4 Z8.ZZ8_DATA AS Dia, convert(VARCHAR, convert(DATE, Z8.ZZ8_DATA, 103), 103) AS DiaFormatado FROM ZZ8010 Z8 GROUP BY Z8.ZZ8_DATA ORDER BY Z8.ZZ8_DATA DESC"; $rs = $conn->execute ( $instrucaoSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); }
 */

/*echo "<select name='dia'>";
if (isset ( $data )) {
	echo "<option value='$data'>$data</option>";
} else {
	echo "<option value='-1'>Selecione a data desejada</option>";
}
while ( ! $rs->EOF ) {

	//for($i = 0; $i < $num_columns; $i ++) {
	echo "<option value=" . $fld [0]->value . ">" . $fld [1]->value . "</option>";
	//}
	$rs->MoveNext ();
}*/

echo "<select name='codigo'>";
echo "<option value='-1'>Selecione o produto desejado</option>";
echo "<option value='PL07160109'>PL07160109</option>";
echo "<option value='PL07173309'>PL07173309</option>";
echo "</select>";

/*
 * $rs->Close (); $rs = null;
 */

echo "</select><br />";

// Combobox do setor
/*
 * $instrucaoSQL = "SELECT NR.NNR_CODIGO, NR.NNR_DESCRI FROM NNR010 NR WHERE NR.NNR_DESCRI LIKE '%- TRANSITO%' AND NR.NNR_DESCRI LIKE '%PRODUCAO%' ORDER BY NR.NNR_DESCRI"; $rs = $conn->execute ( $instrucaoSQL ); $num_columns = $rs->Fields->Count (); for($i = 0; $i < $num_columns; $i ++) { $fld [$i] = $rs->Fields ( $i ); } echo "<select name='setor'>"; echo "<option value='-1'>Selecione o local</option>"; while ( ! $rs->EOF ) { echo "<option value=" . $fld [0]->value . ">" . $fld [1]->value . "</option>"; $rs->MoveNext (); } $rs->Close (); $rs = null; echo "</select>";
 */

echo "<input type='submit' value='Buscar'>";

echo "</div>";

echo "<div class='well'>";

echo "<table class='table table-hover'><tr><th>Nível</th><th>Produto</th><th>Componente</th><th>Quantidade</th><th>Perda</th><th>Observação</th></tr>";
//function imprimeLista() {
	$retornaListaDeMateriaisSQL = "SELECT -- TOP 1
								
								G1.G1_NIV,
								G1.G1_COD,
								G1.G1_COMP, 
								G1.G1_QUANT, 
								G1.G1_PERDA, 
								G1.G1_NIVINV,
								G1.G1_OBSERV,
								B12.B1_UM,
								AH.AH_DESCPO
								
								
								FROM SG1010 G1
								
								INNER JOIN SB1010 B1 ON G1.G1_COD = B1.B1_COD
								LEFT OUTER JOIN SB1010 B12 ON G1.G1_COMP = B12.B1_COD
								LEFT OUTER JOIN SAH010 AH ON B12.B1_UM = AH.AH_UNIMED

								
								WHERE G1.D_E_L_E_T_ <> '*'
								AND G1.G1_COD LIKE 'PL07160109' 
								-- AND G1.G1_COD LIKE 'KIT-CAIXAN03'
								-- AND G1.G1_NIV = '01'
			";
	
	$rs = $conn->execute ( $retornaListaDeMateriaisSQL );
	
	/*$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}*/
	
	while ( ! $rs->EOF ) {
		
		$nivel = $rs->Fields ( 0 )->value;
		$produto = $rs->Fields ( 1 )->value;
		$componente = $rs->Fields ( 2 )->value;
		$quantidade = $rs->Fields ( 3 )->value;
		$perda = $rs->Fields ( 4 )->value;
		$nivelInvertido = $rs->Fields ( 5 )->value;
		$observacao = $rs->Fields ( 6 )->value;
		$unidadeMedida = $rs->Fields ( 7 )->value;
		$unidadeMedidaDescricao = $rs->Fields ( 8 )->value;
		
		echo "<tr><td>$nivel</td><td>$produto</td><td>$componente</td><td>$quantidade $unidadeMedida ($unidadeMedidaDescricao)</td>";
		echo "<td>$perda</td><td>$observacao</td><td bgcolor='$corEvento'></td><td>$eventoFormatado</td></tr>";
		
		$rs->MoveNext ();
		
		/*if (strpos($componente,"MP") == 0)
			echo "Achou";
		else {
			echo "Não encontrado";
		}*/
		
		$pos = strpos($componente, 'M1');
		if (isset($pos)){
			echo $pos;
		} else {
			echo "Não encontrado";
		}

	}
	
	/*
	 * echo "<tfoot><td>Totais</td><td></td><td></td><td>$quantidadeTotal</td><td>". number_format ($tempoIdealTotal/60, 0, '.', '' ) ." min.</td>"; echo "<td><button type='button' class='btn btn-lg btn-primary' disabled='disabled'>". number_format ($produtividadeTotal*100, 2, '.', '' ) ." %</button></td></tfoot></table>";
	 */
	
	$rs->MoveNext ();
//}

//echo imprimeLista();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>