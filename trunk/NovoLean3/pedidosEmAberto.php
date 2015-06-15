<?php
//ob_end_flush();
$conn->open ( $connStr );
$horas = 0;
echo "<div class='well'>";
echo "<h4>Pedidos em aberto</h4><hr>";
echo "</div>";




	echo "<div class='well'>";
	
	echo "<table class='table table-hover'><tr><th>Item</th><th>Filial</th><th>Pedido</th>";
	echo "<th>Pedido Pradolux</th><th>Cliente</th><th>Razão Social</th><th></th><th>Evento</th><th>Cond. Pag.</th>";
	echo "<th>Data Prevista</th><th>%</th></tr>";
	
	$retornaSQL = "SELECT C5.C5_FILIAL AS Filial,
       C5.C5_NUM AS Pedido,
       C5.C5_XPEDORI AS PedidoPradolux,
       C5.C5_CLIENTE AS Cliente,
       A1.A1_NOME AS RazaoSocial,
       C5.C5_VEND1 AS Vendedor,
       convert(VARCHAR, convert(DATE, C5.C5_FECENT, 103), 103) AS DataPrevista,
       --(SELECT Z5.ZZ5_EVENTO FROM ZZ5010 Z51 WHERE Z51.ZZ5_PEDIDO = C5.C5_NUM AND Z51.R_E_C_N_O_ = 
       --        (SELECT MAX(Z52.R_E_C_N_O_) FROM ZZ5010 Z52 WHERE Z52.ZZ5_PEDIDO = C5.C5_NUM)) AS EVENTO,
       --Z5.R_E_C_N_O_, 
       RTRIM(ISNULL(Z5.ZZ5_EVENTO,'Não Cadastrado')) AS Evento,
	   E4.E4_DESCRI AS CondicaoPagamento,
	   (SELECT SUM(C6_QTDVEN*C6_PRCVEN) FROM SC6010 WHERE C6_NUM = C5.C5_NUM) AS ValorSemImpostos

FROM SC5010 C5

INNER JOIN SA1010 A1 ON A1.A1_COD = C5.C5_CLIENTE AND A1.A1_LOJA = C5.C5_LOJACLI
LEFT OUTER JOIN ZZ5010 Z5 ON C5.C5_NUM = Z5.ZZ5_PEDIDO AND Z5.R_E_C_N_O_ = (SELECT MAX(Z52.R_E_C_N_O_) FROM ZZ5010 Z52 WHERE Z52.ZZ5_PEDIDO = C5.C5_NUM AND Z52.D_E_L_E_T_ != '*')  -- AND Z5.ZZ5_EVENTO <> 'ORCAMENTO' AND Z5.ZZ5_EVENTO <> 'ORCAMENTO REPROVADO' AND Z5.ZZ5_EVENTO <> 'PEDIDO REPROVADO'
INNER JOIN SE4010 E4 ON C5.C5_CONDPAG = E4.E4_CODIGO AND E4.E4_FILIAL = '0101'

WHERE        C5.D_E_L_E_T_ != '*' AND A1.D_E_L_E_T_ != '*'
       AND A1.A1_MSBLQL != 1
       -- AND C5.C5_CLIENTE != '999999' AND C5.C5_CLIENTE != '999998' AND C5.C5_CLIENTE != '999997' -- Retirando clientes de venda de sucata
       -- AND C5.C5_CLIENTE != '003718' --Retirando cliente Luxparts
       -- AND C5.C5_VEND1 != '999999' AND C5.C5_VEND1 != '999998' AND C5.C5_VEND1 != '999997' AND C5.C5_VEND1 != '' --Retirando vendedores criados para outros produtos
       -- AND C5.C5_XCOMBON != 2
       AND (C5.C5_FILIAL = '0101' OR C5.C5_FILIAL = '0201')
       AND C5.C5_NOTA = ''
               AND (C5.C5_EVENTO = '2' OR C5.C5_EVENTO = '5' OR C5.C5_EVENTO = '6' OR C5.C5_EVENTO = '7' OR C5.C5_EVENTO = '7' OR C5.C5_EVENTO = '9')

GROUP BY C5.C5_NUM, C5.C5_FILIAL, C5.C5_XPEDORI, C5.C5_CLIENTE, A1.A1_NOME, C5.C5_VEND1, C5.C5_FECENT, C5.C5_NOTA, Z5.ZZ5_EVENTO, E4.E4_DESCRI";
	
	$rs = $conn->execute ( $retornaSQL );
	
	$num_columns = $rs->Fields->Count ();
	
	for($i2 = 0; $i2 < $num_columns; $i2 ++) {
		$fld [$i2] = $rs->Fields ( $i2 );
	}
	
	$produtividadeTotal = 0.00;
	$quantidadeTotal = 0;
	$tempoIdealTotal = 0;
	$item = 0;
	
	$dataAtual = date('d/m/Y');
	$metaDiaria = '115000';
	
	while ( ! $rs->EOF ) {
	
		$filial = $fld [0]->value;
		$pedido = $fld [1]->value;
		$pedidoPradolux = $fld [2]->value;
		$cliente = $fld [3]->value;
		$razaoSocial = $fld [4]->value;
		$vendedor = $fld [5]->value;
		$dataPrevista = $fld [6]->value;
		$evento = $fld [7]->value;
		$condicaoPagamento = $fld [8]->value;
		$valorSemImpostos = $fld [9]->value;
		
		$eventoFormatado = NULL;
		$corEvento = '#FFF';
		$item++;
		$porcentagemMeta = ($valorSemImpostos / $metaDiaria)*100;

		
		if ($evento == "APROVADA SEPARACAO") {
			$eventoFormatado = "Aguardando Separação";
			$corEvento = '#EEB049';
		} else if ($evento == "EM SEPARACAO") {
			$eventoFormatado = "Em Separação";
			$corEvento = '#A54200';
		} else if ($evento == "APROVADA CONFERENCIA") {
			$eventoFormatado = "Em Conferência";
			$corEvento = '#831B83';
		}else if ($evento == "AGUARDANDO FATURAMENTO") {
			$eventoFormatado = "Aguardando Faturamento";
			$corEvento = '#FFFF00';
		}else{
			$eventoFormatado = "Não cadastrado";
		}
		
		$corData = null;
		if ($dataPrevista <= $dataAtual) {
			$corData = '#EEB049';
		}
	
		echo "<tr><td>$item</td><td>$filial</td><td>$pedido</td><td>$pedidoPradolux</td>";
		echo "<td>$cliente</td><td>$razaoSocial</td><td bgcolor='$corEvento'></td><td>$eventoFormatado</td><td>$condicaoPagamento</td>";
		echo "<td bgcolor = '$corData'>$dataPrevista</td><td bgcolor = '$corData'>". number_format($porcentagemMeta, 1, ',', '.'). "%</td>";
		echo "</tr>";
		$rs->MoveNext ();
	}
	
	/*echo "<tfoot><td>Totais</td><td></td><td></td><td>$quantidadeTotal</td><td>". number_format ($tempoIdealTotal/60, 0, '.', '' ) ." min.</td>";
	echo "<td><button type='button' class='btn btn-lg btn-primary' disabled='disabled'>". number_format ($produtividadeTotal*100, 2, '.', '' ) ." %</button></td></tfoot></table>";*/
	
	$rs->MoveNext ();

echo "</tr></table>";
echo "</div>";

$rs->Close ();
$rs = null;


$conn->Close ();
$conn = null;
?>