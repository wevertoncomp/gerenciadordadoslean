<?php
//ob_end_flush();
$conn->open ( $connStr );
echo "<div class='well'>";
echo "<h4>Titulo</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'><tr><th>Item</th><th>Filial</th></tr>";
	
	$retornaSQL = "";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i = 0; $i < $num_columns; $i ++) {
		$fld [$i] = $rs->Fields ( $i );
	}
	
	
	while ( ! $rs->EOF ) {
	
		$filial = $fld [0]->value;
		$pedido = $fld [1]->value;

	
		echo "<tr><td>$item</td><td>$filial</td></tr>";
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