<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

$destpath = ' \abcd\www\cgi-bin\ffi\\';
$convertertika = '\abcd\www\cgi-bin\ffi\tika.jar';
					if (file_exists($convertertika)){
						$converter='"\Program Files\Java\jre7\bin\java" -jar '.$convertertika.' -h ';
//$converter='java -jar -Xms512M -Xmx1024M '.$convertertika.' -h ';
						$redir = ' >';
$name = 'tikaresult.html';
					}else {
						echo "converter missing in the cgi-bin folder";
						die;}
				//the actual command to convert temporary file to html extraction
//				$xcmd = $converter.$files['tmp_name'][$key]."  ".$redir.$name;
				$xcmd = str_replace('/','\\',$converter. $destpath.'calendrier.rtf  '.$redir. $destpath.$name);
//$xcmd=urlencode($xcmd);
				echo 'command='.$xcmd."<BR>";
//				echo "<hr>";
				//die;
				$content=array();
				$res=exec($xcmd,$content,$result);
				echo "<p>result: $result<p>";
					$texto="";
					foreach ($content as $key=>$value){
						$value=strip_tags($value,"<br>");
						$value=str_replace(" <br>","<br>",$value);
						while (strpos($value,"<br><br>"))
							$value=str_replace("<br><br>","<~~>",$value);
						$value=str_replace("<br>","~",$value);
						//$value=str_replace("\n","",$value);
					   	if (trim($value)!="" )
					   		$texto.=$value;
					}
echo $texto;
die;
?>