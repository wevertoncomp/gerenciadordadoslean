
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      
      
    </div>
  </div>
</div>


<?php
// ob_end_flush();
$conn->open ( $connStr );
$mostrar = "Faltantes";
$item = 0;

echo "<div class='well'>";
echo "<h4>Faltas em Pedidos que estão em Separação e Conferência</h4><hr>";

//echo "<form action='index.php?area=produtividadeDiaria&data=$dia&setor=$setor' method = 'get'>";
echo "<form action='?pg=faltasEmPedidos' method = 'post'>";
echo "<input type='hidden' name='acao' value='enviar'>";
echo "<span><b>Informe a ordem que deseja visualizar: </b></span>";
echo "<br/>";

$ordenacao = $_POST ['ordenacao'];

echo "<select name='ordenacao'>";
if (isset ( $ordenacao )) {
	echo "<option value='$ordenacao'>$ordenacao</option>";
} else {
	echo "<option value='-1'>Selecione a ordem</option>";
}

	echo "<option value='Local'>Local</option>";
	echo "<option value='Pedido'>Pedido</option>";
	echo "<option value='Produto'>Produto</option>";

echo "</select><br />";

echo "<span><b>Informe o evento que deseja visualizar: </b></span><br />";
echo "<select name='estado'>";
if (isset ( $estado )) {
	echo "<option value='$estado'>$estado</option>";
} else {
	echo "<option value='-1'>Selecione o evento</option>";
}

echo "<option value='Separação'>Separação</option>";
echo "<option value='Conferência'>Conferência</option>";
echo "<option value='Todos'>Todos</option>";

echo "</select><br />";

echo "<input type='submit' value='Buscar'>";
echo "</div>";



//$ordenacao = "local";

if ($ordenacao == "Local") {
	$orderBy = "ORDER BY B1.B1_LOCPAD, C5.C5_NUM";
} else if ($ordenacao == "Pedido") {
	$orderBy = "ORDER BY C5.C5_NUM, B1.B1_LOCPAD";
} else {
	$orderBy = "ORDER BY C6.C6_PRODUTO";
}


if ($estado == "Separação") {
	$queryEstado = "(C5.C5_EVENTO = '5')";
} else if ($estado == "Conferência") {
	$queryEstado = "(C5.C5_EVENTO = '6')";
} else {
	$queryEstado = "(C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '5')";
}


echo "<div class='well'>";
echo "<table class='table table-hover'><tr><th>Item</th><th>Pedido</th><th>Cliente</th><th>Data Prevista</th><th>Evento</th><th>Produto</th>";
echo "<!--<th>Qtd Vendida</th><th>Qtd Reservada</th>--><th>Falta</th><th>Local Produção</th><th>Status</th><th>Estoque</th><th>Trânsito</th><th>+</th></tr>";

$retornaProdutividadeSQL = "SELECT 

								C5.C5_NUM AS Pedido, 
								C5.C5_CLIENTE AS Cliente,
								A1.A1_NOME AS RazaoSocial,
								convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
								--ZB.ZZB_EVENTO AS Evento,
								C5.C5_EVENTO AS Evento,
								C6.C6_PRODUTO AS Produto,
								C6.C6_QTDVEN AS QtdVendida,
								C6.C6_RESERVA AS Reserva,
								C6.C6_QTDRESE AS QtdReservada,
								B1.B1_LOCPAD AS LocalProducao,
								(SELECT B2.B2_QATU FROM SB2010 B2
								WHERE (B2.B2_LOCAL LIKE 'AP-A01')
								--OR B2.B2_LOCAL LIKE 'AS-A01')
								AND B2.D_E_L_E_T_ <> '*' AND B1.B1_COD = B2.B2_COD) AS Estoque,
								(SELECT TOP 1 B2.B2_QATU FROM SB2010 B2
								WHERE (B2.B2_LOCAL LIKE '%TR')
								AND B2.D_E_L_E_T_ <> '*' AND B1.B1_COD = B2.B2_COD) AS Transito
								
								FROM SC5010 C5
								INNER JOIN SC6010 C6 ON C5.C5_NUM = C6.C6_NUM
								INNER JOIN SB1010 B1 ON C6.C6_PRODUTO = B1.B1_COD
								INNER JOIN SA1010 A1 ON C5.C5_CLIENTE = A1.A1_COD
								
								WHERE $queryEstado
								--AND ZB.ZZB_DTMETA = '20150107'
								AND A1.D_E_L_E_T_ <> '*' AND C6.D_E_L_E_T_ <> '*'
								
								$orderBy";

$rs = $conn->execute ( $retornaProdutividadeSQL );

$num_columns = $rs->Fields->Count ();

for($i2 = 0; $i2 < $num_columns; $i2 ++) {
	$fld [$i2] = $rs->Fields ( $i2 );
}

while ( ! $rs->EOF ) {
	
	$pedido = $fld [0]->value;
	$cliente = $fld [1]->value;
	$razaoSocial = $fld [2]->value;
	$dataPrevista = $fld [3]->value;
	$evento = $fld [4]->value;
	$produto = $fld [5]->value;
	$qtdVendida = $fld [6]->value;
	$reserva = $fld [7]->value;
	$qtdReservada = $fld [8]->value;
	$localProducao = $fld [9]->value;
	$estoque = $fld [10]->value;
	$transito = $fld [11]->value;
	$falta = $qtdVendida - $qtdReservada;
	$status = NULL;
	
	if ($evento == 6) {
		$evento = "Em Conferência";
	} else if ($evento == 5) {
		$evento = "Em Separação";
	}
	
	if ($qtdVendida == $qtdReservada) {
		$status = "ok_16x16";
	} else {
		$status = "alert_16x16";
	}
	
	if ($mostrar == "Todos") {
		$item ++;
		echo "<tr><td>$item</td><td>$pedido</td><td>$cliente - $razaoSocial</td><td>$dataPrevista</td>";
		echo "<td>$evento</td><td>$produto</td><!--<td>$qtdVendida</td><td>$qtdReservada</td>--><td>$falta</td><td>$localProducao</td>";
		echo "<td><img src = 'img/$status.png'></td><td>$estoque</td><td>$transito</td></tr>";
	} else if ($mostrar == "Faltantes") {
		if ($status == "alert_16x16") {
			$item ++;
			echo "<tr><td>$item</td><td>$pedido</td><td>$cliente - $razaoSocial</td><td>$dataPrevista</td>";
			echo "<td>$evento</td><td bgcolor = '#00FF00'>$produto</td><!--<td>$qtdVendida</td><td>$qtdReservada</td>--><td bgcolor = '#00FF00'>$falta</td><td bgcolor = '#00FF00'>$localProducao</td>";
			echo "<td><img src = 'img/$status.png'></td><td>$estoque</td><td>$transito</td>";
			echo "<td><button type='button' class='btn btn-primary' data-toggle='modal' data-target='.bs-example-modal-lg'>+</button></td></tr>";
		}
	}
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