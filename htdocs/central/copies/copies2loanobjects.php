<?php
session_start();
set_time_limit(0);
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
if (!isset($_SESSION["lang"]))  $_SESSION["lang"]="en";
include("../common/get_post.php");
include ("../config.php");
$lang=$_SESSION["lang"];
$arrHttp["base"]="copies";

$IsisScript=$xWxis."imprime.xis";
$Formato="V1'|',v10'|^i',v30,'|^t'v50,'|^v'v60,'|',v63,'|',ref(['libros']l(['libros']'CN_'v1),v3)/";
$query ="&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=rango&from=1&to=99999&Formato=".urlencode($Formato);
//echo $query;
include("../common/wxis_llamar.php");
foreach ($contenido as $value){
	//echo "$value<br>";
	if (!isset($inven[$x[0]])){

foreach ($inven as $key=>$value){
	if ($value!=""){
		$ValorCapturado="0001".$control."\n0010libros";
		$obj=explode("\n",$value);
		foreach ($obj as $lobje) {
			$a=explode('|',$lobje);
			$inven=$a[2];
			$signa=strtoupper($a[6]);
			$copia=strtoupper($a[5]);
			$ValorCapturado.="\n0959".$inven;
			$tipo=strtoupper(substr($inven,2,1));
			if ($tipo=="T"){
				$tipo='^oT';
			}else{
					$tipo="^oR";
				}else{
					if (!$ix==false){
			$ValorCapturado.=$tipo;
			if ($a[3]!="^t")
			   $ValorCapturado.=$a[3];
			if ($a[4]!="^v")
			   $ValorCapturado.=$a[4];
			//$ValorCapturado.="\n";
		echo "<xmp>$ValorCapturado</xmp>";
		$IsisScript=$xWxis."actualizar.xis";
		$ValorCapturado=urlencode($ValorCapturado);
  		$query = "&base=loanobjects&cipar=$db_path"."par/loanobjects.par&login=abcd&Mfn=New&Opcion=crear&ValorCapturado=".$ValorCapturado;
		include("../common/wxis_llamar.php");
		foreach ($contenido as $linea) echo "$linea<br>";
	}
?>