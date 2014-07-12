<?php
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include("../common/get_post.php");
include("../config.php");
//foreach ($arrHttp as $var=>$value) echo "$var=$value<br>";

include("../lang/admin.php");
echo "<html><title>Test FDT</title>
<link rel=stylesheet href=../styles/basic.css type=text/css>\n";
echo "<font size=1 face=arial> &nbsp; &nbsp; Script: default_update.php<BR>";
global $ValorCapturado;
include("actualizarregistro.php");
require_once ('plantilladeingreso.php');


function VariablesDeAmbiente($var,$value){
global $arrHttp,$variables;

		if (substr($var,0,3)=="tag") {
			$ixpos=strpos($var,"_");
			if ($ixpos!=0) {
				$occ=explode("_",$var);
				if (trim($value)!=""){
					if (isset($occ[2])) $value="^".trim($occ[2]).$value;
					$var=$occ[0]."_".$occ[1];
					if (is_array($value)) {
						$value = implode("\n", $value);
					}
				}
				if (isset($arrHttp[$var])){
					$arrHttp[$var].=$value;
				}else{
					if (trim($value)!="") $arrHttp[$var]=$value;
				}
			}else{
				if (is_array($value)) {
			   		$value = implode("\n", $value);
				}
				if (isset($arrHttp[$var])){
					$arrHttp[$var].="\n".$value;
				}else{
					if (trim($value)!="") $arrHttp[$var]=$value;
				}
			}
		}else{
			if (trim($value)!="") {
				$arrHttp[$var]=$value;
			}
		}
}

$arrHttp=Array();
foreach ($_GET as $var => $value) {
	if (trim($value)!="") VariablesDeAmbiente($var,$value);
}
if (count($arrHttp)==0){
	foreach ($_POST as $var => $value) {
		if (trim($value)!="") VariablesDeAmbiente($var,$value);
	}
}


foreach ($arrHttp as $var => $value) {
	if (substr($var,0,3)=="tag" ){
		$tag=explode("_",$var);
		if (substr($tag[0],3)>3000 and substr($tag[0],3)<4000){  //IF LEADER, REFORMAT THE FIELD FOR ELIMINATING |
			$v=explode('|',$value);
			$value=$v[0];
		}
		if (isset($variables[$tag[0]])){
			$variables[$tag[0]].="\n".$value;
			$valortag[substr($tag[0],3)].="\n".$value;
		}else{
			$variables[$tag[0]]=$value;
			$valortag[substr($tag[0],3)]=$value;
		}
   	}

}
//foreach ($variables as $key => $value) echo "$key=$value<br>";die;

$base=$arrHttp["base"];
$arrHttp["cipar"]="$base.par";
$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].".fdt";
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].".fdt";
$fp=file($archivo);
global $vars;
$ix=-1;
foreach ($fp as $value){

	$ix=$ix+1;
	$vars[$ix]=$value;
}
$default_values="S";
ActualizarRegistro();
$_SESSION["valdef"]=$ValorCapturado;
echo "<br><br><center><h1>".$msgstr["valdef"]." ".$msgstr["actualizados"]."</h1>";
//echo $ValorCapturado;




?>