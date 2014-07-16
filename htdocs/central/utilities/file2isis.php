<?php
session_start();
set_time_limit(0);
include ("../config.php");
echo $db_path;
$base="folleteria";
function LlamarWxis($base,$ValorCapturado,$IsisScript,$query){
global $arrHttp,$xWxis,$wxisUrl,$OS,$db_path,$Wxis;
	include("../common/wxis_llamar.php");
	return ($contenido);
}
if ($handle = opendir('/bases_abcd/alvaro/folleteria/pdf')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            echo "<font color=darkred>$entry</font><br>";
            $size=filesize('/bases_abcd/alvaro/folleteria/pdf/'.$entry);
            $ix=strpos($entry,".pdf");
            if (!$ix===false){
	            $title=substr($entry,0,$ix);
	            echo "<font color=darkblue>$title (".$size.")</font><br>";
	            $ValorCapturado="<12>$title</12>";
				$ValorCapturado.="<800>$entry</800>";
				$ValorCapturado.="<801>$size</801>";
				$ValorCapturado=urlencode($ValorCapturado);
				$IsisScript=$xWxis."actualizar_proc.xis";
	  			$query = "&base=".$base ."&cipar=$db_path"."par/".$base.".par&login=abcd&Mfn=New&ValorCapturado=".$ValorCapturado;
				$contenido=LlamarWxis($base,$ValorCapturado,$IsisScript,$query);
				foreach ($contenido as $value) echo "	MFN: $value<br>";
			}
        }
    }
    closedir($handle);
}
?>
