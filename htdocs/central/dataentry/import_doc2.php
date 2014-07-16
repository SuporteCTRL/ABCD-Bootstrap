<?php
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}
include ("../config.php");
include("../lang/admin.php");
include("../lang/importdoc.php");
include("../lang/soporte.php");
include("../common/get_post.php");
$lang=$_SESSION["lang"];
        if ( substr(PHP_OS,0,3) == 'WIN') { 
					$target="pc";
					$copycmd="copy ";
				}else{
					$target="linux";
					$copycmd="cp ";
//echo "OS=" . $target . "<BR>";
        }
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
if($files['name']=="")
{
echo $msgstr["uploaderror"];
die;
}
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
			$name = ereg_replace("[^a-z0-9._]", "",
        			str_replace(" ", "_", str_replace("%20", "_", strtolower($name)))
			);
//setting folder to keep original documents
			if (file_exists($db_path.$arrHttp["base"]."/dr_path.def")){
			$def = parse_ini_file($db_path.$arrHttp["base"]."/dr_path.def");
			$dr_path=trim($def["ROOT"]);
			}else $dr_path=getenv("DOCUMENT_ROOT")."/bases/".$arrHttp["base"]."/";
      //echo 'dr_path=' . $dr_path . '<BR>';
			$files = $_FILES['userfile'];
			//if (!ereg("/$", $dr_path)) $dr_path = $dr_path."/";
//$store_in="";
//if (isset($arrHttp["storein"]))
//if (!ereg("/$", $arrHttp["storein"])) $store_in = $arrHttp["storein"]."/";
//echo "storein=".$store_in . "<BR>";
			// copy the original file in the htdocs/bases domain in the database folder
			$destpath = $dr_path ;
			$destpath = str_replace('\\','/',$destpath);
      //echo "destpath=".$destpath. "<BR>";
			$s=explode("/" ,$destpath);
			$ix=count($s);
			$destpath = "/bases/" . $s[$ix-2] . $s[$ix-1].'/';
      //if ($target = 'pc') $destpath = str_replace('/', '\\',$destpath); 
      //echo "destpath=".$destpath. "<BR>";
	//die;
			// create folder if not yet existing 
//if (!file_exists($destfile)) 
//exec("mkdir ".$destfile, $content,$resultado);
			$destfile = $dr_path .$name;
			//echo "destfile=".$destfile . "<BR>";
			//die;
			//if ($target=='linux') $destfile = str_replace('\\', '/', $destfile); 
				// THERE IS A PHP FUNCTION CALLED move_uploaded_file but often doesn't work for some reason
			//$res=move_uploaded_file($files['tmp_name'][$key],$destfile);
				// so we use a simple copy cmd for the time being... but this is only for local network
			$cmd=$copycmd . $files['tmp_name'][$key] . ' '. $destfile; 
			//echo $files['tmp_name'][$key].'<BR>'. $cmd . '<BR>';
			$res=exec($cmd,$content,$result);
			if ($result==0) echo "File uploaded to " . $destfile. "<BR>"; else echo "Problem uploading file". "<BR>";
			if ($target=='linux') $mx_path = str_replace('\\', '/', $mx_path); 
			// creating the upload window
			echo "<html>\n";
			echo "<title>".$msgstr["uploadfile"]."</title>\n";
			echo "<script language=javascript src=js/lr_trim.js></script>\n";
			echo "<body>\n";
			echo "<font face=verdana>\n";
			//echo $mx_path . "<BR>";
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
				$converterpdf = $mx_path0 ."pdftohtml.exe";
				$convertertika= $mx_path0 . "tika.jar";
				if ($target=='linux')  $convertertika = str_replace('\\', '/', $convertertika); 
				//echo "convertertika=".$convertertika. "<BR>";				
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
				//echo $xcmd."<BR>";
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
//				$proc0 = " \"proc='d".$arrHttp["fURL"] . "', 'a".$arrHttp["fURL"] . "~" . $destpath . "/" . $destfilename . "~'\" ";
					$proc0 = " \"proc='a".$arrHttp["fURL"] . "~" . $destpath  . $destfilename . "~'\" ";
//	 			$proc1 = "\"proc='d".$arrHttp["Tag"] . "', 'Gload/".$arrHttp["Tag"]."=$name'\"";
		 			$proc1 = "\"proc='Gload/".$arrHttp["Tag"]."=$name'\"";
					$proc = $proc1 . $proc0; 
					if (filesize($name)<512000) $indexupd = 1; else $indexupd = 0;
					//if (isset($indexupd)) $ifupd = " fst=@".$dbname.".fst fullinv/ansi=" . $dbname . " ";  else $ifupd = "";  
					if (isset($indexupd)) $ifupd = " fst=@".$dbname.".fst ifupd=" . $dbname . " ";  else $ifupd = "";  
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
				echo "<H4><font color=green>" .$msgstr["documentfield"]." ".$arrHttp["Tag"]."<br>";
				echo $msgstr["urlfield"]." ".$arrHttp["fURL"]."<br>";
				//echo "<script>setTimeout('self.close()',4000)</script>";
				if ($arrHttp["Mfn"]=="New")
					echo "<script>window.opener.top.maxmfn++;window.opener.top.Menu('ultimo')</script>";
				else
					echo "<script>window.opener.top.Menu('same')</script>";
			}
				else echo "<H4><font color=red>".$msgstr["importdocfail"]."</font>";
    }		
}
	echo "</body>\n";
	echo "</html>\n";
?>
