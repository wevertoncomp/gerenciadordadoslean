<?php
//ob_end_flush();
$conn->open ( $connStr );

$produto = $_GET['produto'];

echo "<div class='well'>";
echo "<h4>Estoque de Notas por Matéria Prima</h4><hr>";
echo "<p><a href = 'index.php?pg=custos'>Voltar à página de custos</a></p>";
echo "</div>";

	echo "<div class='well'>";
	
	echo "<table class='table table-hover'>";
	echo "<tr><th>Documento</th>";
	echo "<th>Código</th>";
	echo "<th>Emissão</th>";
	echo "<th>UM</th>";
	echo "<th>Qtde</th>";
	echo "<th bgcolor = '#FF8C00'>V Unit.</th>";
	echo "<th>V Total</th>";
	echo "<th>V IPI</th>";
	echo "<th>V ICMS</th>";
	echo "<th>2ª UM</th>";
	echo "<th>Qtd 2ª UM</th>";
	echo "<th>Custo</th>";
	echo "<th bgcolor = '#008000'>Custo Unit.</th>";
	echo "<th>Custo Rep.</th>";
	echo "<th>Qtd Devolv.</th>";
	echo "<th>Fornec.</th>";
	echo "<th>Loja</th>";
	echo "<th>Conta Contábil</th>";
	echo "<th>T.E.S.</th>";
	echo "<th>IPI</th>";
	echo "<th>ICMS</th>";
	echo "</tr>";
	
	$retornaSQL = "	SELECT 

					D1.D1_DOC, 
					D1.D1_COD,
					D1.D1_EMISSAO, 
					D1.D1_UM, 
					D1.D1_QUANT, 
					D1.D1_VUNIT, 
					D1.D1_TOTAL, 
					D1.D1_VALIPI, 
					D1.D1_VALICM,
					D1.D1_SEGUM,
					D1.D1_QTSEGUM,
					D1.D1_CUSTO,
					D1.D1_CUSRP1,
					D1.D1_QTDEDEV,
					D1.D1_FORNECE,
					D1.D1_LOJA,
					D1.D1_CONTA,
					D1.D1_TES,
					D1.D1_IPI,
					D1.D1_PICM
					
					FROM SD1010 D1 WITH (NOLOCK)
					WHERE D1.D1_COD = '$produto'
					AND D1.D1_EMISSAO>='20140101'
					AND D1.D_E_L_E_T_ <> '*'
					ORDER BY D1_EMISSAO";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$contador = 0;
	
	while ( ! $rs->EOF ) {
	
		$documento = $fld [0]->value;
		$codigo = $fld [1]->value;
		$emissao = $fld [2]->value;
		$um = $fld [3]->value;
		$qtd = $fld [4]->value;
		$vUnit = $fld [5]->value;
		$total = $fld [6]->value;
		$vIPI = $fld [7]->value;
		$vICM = $fld [8]->value;
		$segUM = $fld [9]->value;
		$qtdSegUM = $fld [10]->value;
		$custo = $fld [11]->value;
		$custoReposicao = $fld [12]->value;
		$qtdDevolvida = $fld [13]->value;
		$fornecedor = $fld [14]->value;
		$loja = $fld [15]->value;
		$contaContabil = $fld [16]->value;
		$tes = $fld [17]->value;
		$ipi = $fld [18]->value;
		$icms = $fld [19]->value;
		
		$contador++;
		$custoTotal  += $custo;
		$qtdTotal += $qtd;

	
		echo "<tr>";
		echo "<td>$documento</td>";
		echo "<td>$codigo</td>";
		echo "<td>$emissao</td>";
		echo "<td>$um</td>";
		echo "<td>$qtd</td>";
		echo "<td bgcolor = '#FF8C00'>$vUnit</td>";
		echo "<td>$total</td>";
		echo "<td>$vIPI</td>";
		echo "<td>$vICM</td>";
		echo "<td>$segUM</td>";
		echo "<td>$qtdSegUM</td>";
		echo "<td>".number_format($custo, 2, ',', '.')."</td>";
		echo "<td bgcolor = '#008000'>".number_format($custo/$qtd, 6, ',', '.')."</td>";
		echo "<td>".number_format($custoReposicao/$qtd, 6, ',', '.')."</td>";
		echo "<td>$qtdDevolvida</td>";
		echo "<td>$fornecedor</td>";
		echo "<td>$loja</td>";
		echo "<td>$contaContabil</td>";
		echo "<td>$tes</td>";
		echo "<td>$ipi</td>";
		echo "<td>$icms</td>";
		echo "</tr>";
		$rs->MoveNext ();
	}
	
			echo "<tr>";
		echo "<td colspan = '10'><h4>Custo médio: R$ ".number_format($custoTotal/$qtdTotal, 6, ',', '.')."</h4></td>";
		echo "</tr>";
	
	$rs->MoveNext ();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;


$conn->Close ();
$conn = null;
?>