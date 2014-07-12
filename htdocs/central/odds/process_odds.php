<?php
	// venimos del formulario?	
	$reload = true;	
	session_start();  	
	if (!isset($_SESSION["verifica"])) {
		$reload = false;
		$_SESSION["verifica"] = true;  
	} 	
	//$reload = false;
	include_once("../../central/config.php");	
	
	$message_error = false;	
	if (isset($_POST['tag094']) == false) {
		$message_error = true;
	}	
	if (isset ($_GET['lang'])) {
		if ($_GET['lang'] != "") {
			$lang_param = $_GET['lang'];
		}
	} else if (isset ($_POST['lang'])) {
		if ($_POST['lang'] != "") {
			$lang_param = $_POST['lang'];
		}	
	} else {
		$lang_param = '';
	}	
	if ($lang_param != "") {
		// carga el lang desde la configuración
		$lang = $lang_param;
	}
	
	include_once("lib/library.php");
	$requests = load_request_message($lang_param);	
	if ($message_error) {
		$message = $requests['notice_error'];	
	} else {
		if ($reload === false) {
			// para obtener las configs	
			$base  = "odds";
			$cipar = "odds.par";
			$mfn = "new";	
			$cn = get_cn($base, $db_path);
	 		if ($cn == "" or $cn == false){
				$fatal_cn = "Could not generate the control number";
	 		} /* ¿¿VAMOS A GRABAR ESTE DATO??
			else {
				$key = $arrHttp["autoincrement"];
			}
			*/
			$tags = _flattenPOST($_POST);
			if ($tags != "") {
				$contenido = _saveData($xWxis, $tags, $db_path, $base, $cipar, $mfn, $Wxis, $xWxis);
			} else {
				die("error flatten");
			}
			if (is_null($contenido)) {
				$message = $requests['notice_success'];
			} else {
				$message = $requests['notice_error'];	
			}	
		// RELOAD (el usuario recargó la página)
		} else {
			$message = $requests['notice_reload'];	
		}
	}
	/********************************************************************/
	function _execute($query, $db_path, $Wxis, $xwxis) {
		/* como manejar LOCKS!?
		if (isset($arrHttp["lock"]) and $arrHttp["lock"]=="S"){
			$query.="&lock=S";
		}
		*/
		$IsisScript=$xwxis."actualizar.xis";		
	 	exec("\"".$Wxis."\" IsisScript=$IsisScript", $result);	
		include("../common/wxis_llamar.php");		
		
		//Ex LOG: c:/abcd/www/bases/log
		if (is_dir($db_path."log")){
			$fp=fopen($db_path."log/log_".date("Ymd").".log","a");
			//TODO: Si es Linux, \n para salto de linea! 
			fwrite($fp,"**".date('l jS \of F Y h:i:s A')."\r\n");
			fwrite($fp,$_SERVER["PHP_SELF"]." ".$IsisScript." ".urldecode($query)."\r\n");
			fclose($fp);
		}
	} // fin execute()
	
	function _saveData($xWxis, $tags, $db_path, $base, $cipar, $mfn, $wxis, $xwxis) {		
		$query = "&base=".$base ."&cipar=".$db_path."par/".$cipar."&Mfn=" .$mfn."&Opcion=crear&ValorCapturado=".$tags;		
		$contenido = _execute($query, $db_path, $wxis, $xwxis);
		return $contenido;
	}
	
	function _flattenPOST($post) {
		$ValorCapturado = "";		
		$post["tag100"] = date("Ymd");		
		$processed_tags = array();
		foreach ($post as $key => $line) {	
			if (substr($key, 0, 3) == "tag") {
				$key=trim(substr($key,3));
				$lin=stripslashes($line);
				if (strpos($key, "_additional") !== false) {					
					$key = str_replace("_additional", "", $key);					
				}
				if (strlen($key)==1) $key="000".$key;
				if (strlen($key)==2) $key="00".$key;
				if (strlen($key)==3) $key="0".$key;	
				// repetibles 
				if (isset($processed_tags[$key])) {
					$new_line = $processed_tags[$key] . "\n" .  $line;
					$processed_tags[$key] = $new_line;
				} else { 				
					$processed_tags[$key] = (string)$line;
				}
			}
		}
		foreach ($processed_tags as $key => $line) {			
			$value = explode("\n", $line);
			//var_dump($value);echo "<hr>";
			foreach ($value as $v) {
				$ValorCapturado.=$key.trim($v)."\n";			
			}			
		} // fin foreach
		return $ValorCapturado;
	}	
?>

<html>
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">			
    <title>ODDS data</title>      
    <link href="../css/estilo_odds.css" rel="stylesheet" type="text/css">	 

<body>	
<?php		
	
	include("lib/header.php");	
?>	

<div class="middle homepage">
	<div class="mainBox" onmouseover="this.className = 'mainBox mainBoxHighlighted';" onmouseout="this.className = 'mainBox';">
		<div class="boxTop">
			<div class="btLeft">&#160;</div>
			<div class="btRight">&#160;</div>
		</div>
		<div class="boxContent toolSection ">
	<!-- ----------------------------------------------------- -->
	
<table width="100%" border="0" cellpadding="0" cellspacing="0" > 
  <tbody>
  <tr>
    <td valign="top" class="cuerpoCuad">&nbsp;</td>
    <td colspan="2" valign="top" class="cuerpoText1">       
    <table width="780" border="0" cellspacing="0" cellpadding="1" bordercolor="#cccccc" class="textNove">
      <tbody>
      <tr>
	    <td height="12">
		<!-- SUBTITULO -->
		<?php
			echo $message;
		?>		
		</td>
	  </tr>
	  </tbody>	  
	</table>
	</td>
  </tr>
  </tbody>
</table>


</div>	
<div class="spacer">&nbsp;</div>
<div class="boxBottom">
<div class="bbLeft">&#160;</div>
<div class="bbRight">&#160;</div>
</div>
</div>	
</div>	
<?php		
	include("lib/footer.php");
?>	
</body></html>
