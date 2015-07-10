<?php 
$conn->open ( $connStr );

$senha = NULL;
$senha = $_POST['password'];

echo "<div class='well'>";
echo "<h4>OPs abertas por área</h4><hr />";
echo "Senha: $senha";
echo "</div>";


echo "<div class='well'>";
echo "<table class='table table-hover'>";
echo "<tr>";
echo "<th>Item</th>";
echo "<th>OP</th>";
echo "<th>Produto</th>";
echo "<th>Qtd Pedida</th>";
echo "<th>Data</th>";
echo "<th>Hora Lib.</th>";
echo "<th>Perda</th>";
echo "<th>Tipo OP</th>";
echo "<th>Evento</th>";
echo "<th>Usuário</th>";
echo "<th>Qtd Feita</th>";
echo "<th>+</th>";
echo "</tr>";

$retornaSQL = "	SELECT

					RTRIM(C2.C2_NUM) AS OP,
					RTRIM(C2.C2_PRODUTO) AS Codigo,
					RTRIM(C2.C2_QUANT) AS QtdPedida,
					convert(VARCHAR, convert(DATE, C2.C2_EMISSAO, 103), 103) AS Data,
					RTRIM(C2.C2_HORA) AS Hora,
					RTRIM(C2.C2_PERDA) AS Perda,
					RTRIM(C2.C2_TPOP) AS TipoOP,
					RTRIM(C2.C2_ZEVENTO) AS Evento,
					RTRIM(C2.C2_ZUSER) AS Usuario,
					RTRIM(C2.C2_QUJE) AS QtdFeita
			
					FROM SC2010 C2
			
					WHERE C2.C2_LOCAL = 'INJ-TR'
					AND C2.C2_ZEVENTO <> '5'
					AND C2.D_E_L_E_T_ <> '*'
		
					ORDER BY C2.C2_ZEVENTO, C2.C2_EMISSAO";

$rs = $conn->execute ( $retornaSQL );

$num_columns = $rs->Fields->Count ();

for($i = 0; $i < $num_columns; $i ++) {
	$fld [$i] = $rs->Fields ( $i );
}


while ( ! $rs->EOF ) {

	$item++;
	$eventoPorExtenso = NULL;
	$corEvento = NULL;
	$op = $fld [0]->value;
	$codigo = $fld [1]->value;
	$qtdPedida = $fld [2]->value;
	$data = $fld [3]->value;
	$hora = $fld [4]->value;
	$perda = $fld [5]->value;
	$tipoOP = $fld [6]->value;
	$evento = $fld [7]->value;
	$usuario = $fld [8]->value;
	$qtdFeita = $fld [9]->value;

	switch ($evento) {
		case '1':
			$eventoPorExtenso = "Espera";
			$corEvento = "#FF8C00";
			break;
		case '2':
			$eventoPorExtenso = "Liberada";
			$corEvento = "#4682B4";
			break;
		case '4':
			$eventoPorExtenso = "Produção";
			$corEvento = "#32CD32";
			break;
		case '5':
			$eventoPorExtenso = "Finalizada";
			$corEvento = "#DC143C";
			break;
	}


	echo "<tr>";
	echo "<td>$item</td>";
	echo "<td>$op</td>";
	echo "<td>$codigo</td>";
	echo "<td>$qtdPedida</td>";
	echo "<td>$data</td>";
	echo "<td>$hora</td>";
	echo "<td>$perda</td>";
	echo "<td>$tipoOP</td>";
	echo "<td bgcolor = '$corEvento'>$eventoPorExtenso</td>";
	echo "<td>$usuario</td>";
	echo "<td>$qtdFeita</td>";
	echo "<td>";
		if ($evento == '1') {
			echo "<button type='button' class='btn btn-primary' data-toggle='modal' href = '#registro01' data-target='#myModal' value = '123'>Liberar OP</button>";
		}
	echo "</td>";
	echo "</tr>";
	$rs->MoveNext ();
}

echo "</table>";
echo "</div>";

$rs->Close ();
$rs = null;

$conn->Close ();
$conn = null;
?>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Liberar OP</h4>
      </div>
      <div class="modal-body">
      
        <form action='?pg=opsAbertasPorArea' method = 'post'>
			<input type='hidden' name='acao' value='enviar'>
			<div class='form-group'>
			<label for='password'>Informe sua senha de liberação</label>
			<input type='password' class='form-control' name='password' placeholder='Senha'>
			</div>
		<input type='submit' value='Enviar'>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>