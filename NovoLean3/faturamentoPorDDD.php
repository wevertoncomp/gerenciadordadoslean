<?php
//ob_end_flush();
$conn->open ( $connStr );

$produto = $_GET['produto'];

echo "<div class='well'>";
echo "<h4>Faturamento por região do estado (DDD)</h4><hr>";

echo "<form action='?pg=faturamentoPorDDD' method = 'post' class='form-inline'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe o intervalo de datas que deseja visualizar: </b></span>";
echo "<br/>";

$dataInicial = $_POST ['dataInicial'];
$dataFinal = $_POST ['dataFinal'];

$dataInicial = str_replace('-', '', $dataInicial);
$dataFinal = str_replace('-', '', $dataFinal);

$dataInicialFormatada = substr($dataInicial, 6, 2) ."/". substr($dataInicial, 4, 2) ."/". substr($dataInicial, 0, 4);
$dataFinalFormatada = substr($dataFinal, 6, 2) ."/". substr($dataFinal, 4, 2) ."/". substr($dataFinal, 0, 4);;

?>
<div class="form-group">
<label for="dataInicial">Data Inicial</label>
<input type="date" class="form-control" name="dataInicial" placeholder="Data Inicial" min="2015-01-01">
</div>
<div class="form-group">
<label for="dataFinal">Data Final</label>
<input type="date" class="form-control" name="dataFinal" placeholder="Data Final">
</div>
<?php 

echo "<br />";

echo "<input type='submit' value='Buscar'>";
echo "</form>";

echo "<h4>Mostrando dados de $dataInicialFormatada até $dataFinalFormatada</h4>";

echo "</div>";

	echo "<div class='well'>";
	
	echo "<table class='table table-hover'>";
	
	$retornaSQL = "	SELECT

					F2.F2_VEND1,
					A1.A1_DDD,
					SUM(ROUND((F2_VALMERC),2)) AS totalCompletos, 
					A3.A3_NOME
					
					FROM SF2010 AS F2
					INNER JOIN SA1010 A1 ON F2.F2_CLIENTE = A1.A1_COD
					INNER JOIN SA3010 A3 ON F2.F2_VEND1 = A3.A3_COD
					
					WHERE F2.D_E_L_E_T_ <> '*' 
					AND (F2_FILIAL = '0101' OR F2_FILIAL = '0201')
					AND F2_CLIENTE != '003718'
					AND F2_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
					AND (SELECT COUNT(E1_PARCELA) FROM SE1010 WHERE E1_NUM = F2.F2_DOC) > 0 AND F2_SERIE = '1' 
					--AND (F2_VEND1 = '000128' OR F2_VEND2 = '000128')
					
					GROUP BY F2.F2_VEND1, A1.A1_DDD, A3.A3_NOME
					ORDER BY F2.F2_VEND1, A1.A1_DDD";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	$contador = 0;
	
	while ( ! $rs->EOF ) {
	
		$codigoVendedor = $fld [0]->value;
		$DDD = $fld [1]->value;
		$valorLiquido = $fld [2]->value;
		$representante = $fld [3]->value;

		echo "<tr bgcolor = '#FF8C00'><th>Cod. Rep.</th>";
		echo "<th>Representante</th>";
		echo "<th>DDD</th>";
		echo "<th>Valor Liquido</th>";
		echo "</tr>";
		
		echo "<tr bgcolor = '#FF8C00'>";
		echo "<td>$codigoVendedor</td>";
		echo "<td>$representante</td>";
		echo "<td>$DDD</td>";
		echo "<td>R$ ". number_format($valorLiquido, 2, ',', '.') ."</td></tr>";
		
		// Bloco para mostrar os clientes
		echo "<tr><td colspan = '4'><table class='table table-hover'>";
		echo "<tr><th>Codigo</th>";
		echo "<th>Cliente</th>";
		echo "<th>Valor Liquido</th>";
		echo "<th>Cidade</th>";
		echo "<th>Estado</th>";
		echo "</tr>";
		
		$retornaSQL2 = "	SELECT
		
		A1.A1_COD,
		A1.A1_NOME,
		ROUND((F2_VALMERC),2) AS totalCompletos,
		A1.A1_MUN,
		A1.A1_EST
			
		FROM SF2010 AS F2
		INNER JOIN SA1010 A1 ON F2.F2_CLIENTE = A1.A1_COD
		INNER JOIN SA3010 A3 ON F2.F2_VEND1 = A3.A3_COD
			
		WHERE F2.D_E_L_E_T_ <> '*'
		AND (F2_FILIAL = '0101' OR F2_FILIAL = '0201')
		AND F2_CLIENTE != '003718'
		AND F2_EMISSAO BETWEEN '$dataInicial' AND '$dataFinal'
		AND (SELECT COUNT(E1_PARCELA) FROM SE1010 WHERE E1_NUM = F2.F2_DOC) > 0 AND F2_SERIE = '1'
		AND (F2_VEND1 = '$codigoVendedor' OR F2_VEND2 = '$codigoVendedor')
		AND A1.A1_DDD = '$DDD'
			
		--GROUP BY A1.A1_COD, A1.A1_NOME, A1.A1_DDD, A3.A3_NOME, F2_VALMERC
		ORDER BY A1.A1_COD, A1.A1_NOME, A1.A1_DDD";
		
		$rs2 = $conn->execute ( $retornaSQL2 );
		
		$num_columns2 = $rs2->Fields->Count ();
		
		for($i2 = 0; $i2 < $num_columns2; $i2 ++) {
		$fld2 [$i2] = $rs2->Fields ( $i2 );
		}
		
		while ( ! $rs2->EOF ) {
		
			$codigoCliente = $fld2 [0]->value;
			$nome = $fld2 [1]->value;
			$valor = $fld2 [2]->value;
			$cidade = $fld2 [3]->value;
			$estado = $fld2 [4]->value;
		
		
			echo "<tr>";
			echo "<td>$codigoCliente</td>";
			echo "<td>$nome</td>";
			echo "<td>R$ ". number_format($valor, 2, ',', '.') ."</td>";
			echo "<td>$cidade</td>";
			echo "<td>$estado</td>";
		
			$rs2->MoveNext ();
		}
		
		echo "</tr></table>";
		// Fim do bloco para mostrar os clientes
		
		echo "</td></tr></tr>";
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