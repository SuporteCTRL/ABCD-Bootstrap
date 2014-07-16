<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
include("../lang/admin.php");
include("../lang/importdoc.php");
include("../lang/soporte.php");
include("../common/get_post.php");
//foreach ($arrHttp as $var=>$value)  echo "$var=$value<br>";
//echo "URL=" . $arrHttp["fURL"] . "<BR>";
//echo "Tag=" . $arrHttp["Tag"] . "<BR>";
$URL=$arrHttp["fURL"];
$Tag=$arrHttp["Tag"];
$storein=$arrHttp["storein"];
if (substr($storein,0,1)=="/" ) $storein=substr($storein,1);


$lang=$_SESSION["lang"];
if ( substr(PHP_OS,0,3) == 'WIN') {
	$target="pc";
	$copycmd="copy ";
}else{
	$target="linux";
	$copycmd="cp ";
}
//$target="linux";
//$copycmd="cp ";
//setting cisis_ver;
if (file_exists($db_path."abcd.def")){
	$def = parse_ini_file($db_path."abcd.def");
	$cisis_ver=trim($def[$arrHttp["base"]]);}
	else {
	if (isset($arrHttp["base"]))
	if (isset($def[$arrHttp["base"]]))
 		$cisis_ver=$def[$arrHttp["base"]]."/";
}
//echo $cisis_ver . "<BR>";
$tipo=$arrHttp["Tipo"];
$dbname=$arrHttp["base"];
$files=$_FILES['userfile'];
$cipar = " cipar=" . $db_path . "par/".$dbname . ".par";

$max=(int)get_cfg_var ("upload_max_filesize") * 1000000;
//echo "upload_max_filesize = ".$max."<br>";

foreach ($files['name'] as $key=>$name)
{
	if (((int)$files['size'][$key]==0) || ((int)$files['size'][$key]>=(int)$max))
		{
			echo "size of file =" . $files['size'][$key];
			echo $msgstr["maxfilesiz"];
			die;
		} else
		if ($files['size'][$key])
		{
			// clean up file name to make it easy to process
			$name = preg_replace("/[^a-z0-9._]/", "",
        			str_replace(" ", "_", str_replace("%20", "_", strtolower($name)))
			);

//setting folder to keep original documents
			if (file_exists($db_path.$arrHttp["base"]."/dr_path.def")){
				$def = parse_ini_file($db_path.$arrHttp["base"]."/dr_path.def");
				if (isset($def["ROOT"]))
					$dr_path=trim($def["ROOT"]).$storein;
				else
					$dr_path=getenv("DOCUMENT_ROOT")."/bases/".$arrHttp["base"];
			}
			$files = $_FILES['userfile'];
			// copy the original file in the htdocs/bases domain in the database folder
			$destpath = $dr_path ;
			$destpath = str_replace('\\','/',$destpath);
			$s=explode("/" ,$destpath);
			$ix=count($s);
			$destpath =  $s[$ix-2] . $s[$ix-1];
			$destfile = $dr_path .$name;
			echo "<strong>destfile=</strong>".$destfile . "<BR>";
				// THERE IS A PHP FUNCTION CALLED move_uploaded_file but often doesn't work for some reason
		//	$res=move_uploaded_file($files['tmp_name'][$key],$destfile);
				// so we use a simple copy cmd for the time being... but this is only for local network
//			if ($target = 'pc'){
//				$destfile = str_replace('/', '\\',$destfile);
//			}else{
//				$destfile = str_replace('\\', '/',$destfile);
//			}

			$cmd=$copycmd . $files['tmp_name'][$key] . ' '. $destfile;

			echo $cmd."<br>";

			$res=exec($cmd,$content,$result);
			if (!file_exists($destfile)) {
				echo "<p><font color=red>".$destfile. " file not loaded<p>";
				die;
			}
			echo $res."<p>" ;
			//if ($res==0) echo "File uploaded to " . $destfile. "<BR>"; else echo "Problem uploading file with errno." . $res . "<BR>";
			if ($target=='linux') $mx_path = str_replace('\\', '/', $mx_path);
			// creating the upload window
			echo "<html>\n";
			echo "<title>".$msgstr["uploadfile"]."</title>\n";
			echo "<script language=javascript src=js/lr_trim.js></script>\n";
			echo "<body>\n";
			echo "<font face=verdana>\n";
			echo  "mx path:".$mx_path . "<BR>";
			switch($tipo){
			case "B":
				$s=explode("/" ,$mx_path);
				$ix=count($s)-1;
				$mx_path0 = '';
                		for ($i=0; $i<$ix; $i++)
				{
                   		$mx_path0 = $mx_path0.$s[$i].'/';
				 }
				$mx_path0 = $mx_path0.$cisis_ver.'/';
				if ($target=='pc') $mx_path0 = str_replace('/', '\\', $mx_path0);
				//echo "mx_path=". $mx_path0 . "<BR>";
				$destfilename = $name;
				//echo "destfilename=".$destfilename."<BR>";
				//die;
				$s=explode("." ,$name);
				$ix=count($s)-1;
				$name = '';
                		for ($i=0; $i<$ix; $i++)
				{
                   		$name = $name.$s[$i];
				 }
                		$name=$db_path."wrk/".$name.".html";
				if ($target=='pc') $name = str_replace('/', '\\', $name);
				//echo "name=" . $name."<BR>";
				$redir="";
				//if ($target == 'pc') 
				$converterpdf = $mx_path0 ."pdftohtml.exe"; 
				//else $converterpdf = '';
				$convertertika= $mx_path0 . "tika.jar";
				if ($target=='linux') {
				$converterpdf = str_replace('\\', '/', $converterpdf);
				$convertertika= str_replace('\\', '/',$convertertika);				
				//echo "convertertika=".$convertertika. "<BR>";
				}
				// checking existence of pdftohtml.exe
				if (file_exists($converterpdf) && $s[$ix]== "pdf")
				{
					$converter=$converterpdf." -noframes -i ";
				}
				else
				if (file_exists($convertertika))
				{
					$converter="java -jar ".$convertertika." -h ";
					$redir = ">";
				}
				else {
				echo "converter missing in the cgi-bin folder";
				die;
				}
				//the actual command to convert temporary file to html extraction
				$xcmd = $converter.$files['tmp_name'][$key]."  ".$redir.$name;
				echo $xcmd."<BR>";
				//die;
				$res=exec($xcmd,$content,$result);
	  			if ($result==0)
				{
					$s=explode("." ,$name);
					$ix=count($s)-1;
					$name=$s[0].".html";
	  			}
				else
				{
	  				echo "<font color=red><p>conversion failed";
	  				echo " with error code = " . $result;
					die;
	  			}
// next part does the loading of the converted html into the ISIS-database
	  			$database=$db_path.$dbname.'/data/'.$dbname;
	  			if (!file_exists($name))
				{
	  				echo "$name not available";
	  				die;
	  			}
//$command= $mx_path.$cisis_ver . "/mx.exe ".$cipar." ".$dbname.' count=1';
				if (filesize($name)<999999)
				{
					if ($arrHttp["Mfn"]=="New") $appendcopy=" append="; else $appendcopy = " from=".$arrHttp["Mfn"] . " copy=";
//				$proc0 = " \"proc='d".$URL . "', 'a".$URL . "~" . $destpath . "/" . $destfilename . "~'\" ";

					$proc0 = " \"proc='a".$URL . "~" . $destpath . '/' . $destfilename . "~'\" ";
//	 			$proc1 = "\"proc='d".$arrHttp["Tag"] . "', 'Gload/".$arrHttp["Tag"]."=$name'\"";
		 			$proc1 = "\"proc='Gload/".$Tag ."=$name'\"";
					$proc = $proc1 . $proc0;
					if (filesize($name)<512000) $indexupd = 1; else $indexupd = 0;
					//if (isset($indexupd)) $ifupd = " fst=@".$dbname.".fst fullinv/ansi=" . $dbname . " ";  else $ifupd = "";
					if (isset($indexupd)) $ifupd = " fst=@".$dbname.".fst fullinv/ansi=" . $dbname . " ";  else $ifupd = "";
//$mx_path="/abcd/www/cgi-bin/ffi/mx.exe";
	 				$command = $mx_path0 ."mx ". $cipar . " null " . $proc . $ifupd . $appendcopy .$dbname. " count=1 lw=999 now -all";
					//if ($target=='pc') $command = str_replace('/','\\',$command);
					echo $command . "<BR>";
					//die;
 					$res=exec($command,$content,$result);
				}
  				else {
  				echo "<p> File too big, not possible to load into ISIS-record..."; die;
  				}
  				break;
		}
            if ($result==0)
			{
				echo "<H4><font color=green>" .$msgstr["documentfield"]." ".$Tag."<br>";
				echo $msgstr["urlfield"]." ".$URL."<br>";
				echo "<p><a href=javascript:VerDocumento()>".$msgstr["continuar"]."</a>";
				//echo "<script>setTimeout('self.close()',4000)</script>";
				if ($arrHttp["Mfn"]=="New")
					echo "<script>
							function VerDocumento(){
								top.maxmfn++;top.Menu('ultimo')
							}
						</script>";
				else
					echo "<script>
						function VerDocumento(){
							top.Menu('same')
						}
						</script>";
			}
				else echo "<H4><font color=red>".$msgstr["importdocfail"]."</font>";
    }
}
	echo "</body>\n";
	echo "</html>\n";
?>
