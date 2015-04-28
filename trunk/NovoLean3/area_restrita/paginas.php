<?

$var = "principal.php";
$pg = "$_GET[pg].php";
if(empty($_SERVER["QUERY_STRING"])) {
include($var);
} else {
include("$pg");
} 
?>