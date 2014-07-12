<?php

//**********************************
// script: homepage.php
// editado: 11/07/2014
// editor: Roger C. Guilherme
//*********************************

// ==================================================================================================
// GERA ESTATISTICAS
// ==================================================================================================
//

session_start();
require ("../config.php");
include("../common/get_post.php");
include ("../lang/admin.php");
include ("../lang/statistics.php");

//SE EXTRAE EL NOMBRE DE LA BASE DE DATOS
if (strpos($arrHttp["base"],"|")===false){

}   else{
		$ix=strpos($arrHttp["base"],'^b');
		$arrHttp["base"]=substr($arrHttp["base"],2,$ix-2);
}

if (!isset($arrHttp["Opcion"]))$arrHttp["Opcion"]="";

if (isset($arrHttp["encabezado"]))
	$encabezado="&encabezado=S";
else
	$encabezado="";

// SE LEE EL MÁXIMO MFN DE LA BASE DE DATOS
$IsisScript=$xWxis."administrar.xis";
$query = "&base=".$arrHttp["base"] . "&cipar=$db_path"."par/".$arrHttp["base"].".par&Opcion=status";
include("../common/wxis_llamar.php");
$ix=-1;
foreach($contenido as $linea) {
	$ix++;
	if ($ix>1) {
		if (trim($linea)!=""){
	   		$a=explode(":",$linea);
	   		$tag[$a[0]]=$a[1];
	  	}
	}
}


//HEADER DEL LA PÁGINA HTML Y ARCHIVOS DE ESTIVO
include("../common/header.php");
?>
<script language="javascript1.2" src="../dataentry/js/lr_trim.js"></script>
<style type="text/css">

td{
	font-size:12px;
}

div#useextable{

	display: none;
	margin: 0px 20px 0px 20px;
	font-size: 12px;
	color: #000000;
}

div#createtable{
<?php if ($arrHttp["Opcion"]!="new") echo "display: none;\n"?>

	margin: 0px 20px 0px 20px;
	font-size: 12px;
	color: #000000;
}

div#generate{
	display: none;
	margin: 0px 20px 0px 20px;
	font-size: 12px;
	color: #000000;
}

div#pftedit{
	display: none;
	margin: 0px 20px 0px 20px;
	font-size: 12px;
	color: #000000;
}

div#configure{
	display: none;
	margin: 0px 20px 0px 20px;
	font-size: 12px;
	color: #000000;
}
</style>

<script>
TipoFormato=""
C_Tag=Array()

function AbrirVentana(Archivo){
	xDir=""
	msgwin=window.open(xDir+"ayudas/"+Archivo,"Ayuda","menu=no, resizable,scrollbars")
	msgwin.focus()
}

function EsconderVentana( whichLayer ){
var elem, vis;
	if( document.getElementById ) // this is the way the standards work
		elem = document.getElementById( whichLayer );
	else if( document.all ) // this is the way old msie versions work
		elem = document.all[whichLayer];
	else if( document.layers ) // this is the way nn4 works
		elem = document.layers[whichLayer];
	vis = elem.style;
	// if the style.display value is blank we try to figure it out here
	if( vis.display == '' && elem.offsetWidth != undefined && elem.offsetHeight != undefined )
		vis.display = 'none';
	vis.display =  'none';
}

function toggleLayer( whichLayer ){
	var elem, vis;

	switch (whichLayer){
		case "createtable":
<?php
		echo '
			EsconderVentana("useextable")
			break
			';

?>
		case "useextable":
			EsconderVentana("createtable")
			break
	}
	if( document.getElementById ) // this is the way the standards work
		elem = document.getElementById( whichLayer );
	else if( document.all ) // this is the way old msie versions work
		elem = document.all[whichLayer];
	else if( document.layers ) // this is the way nn4 works
		elem = document.layers[whichLayer];
	vis = elem.style;
	// if the style.display value is blank we try to figure it out here
	if( vis.display == '' && elem.offsetWidth != undefined && elem.offsetHeight != undefined )
		vis.display = ( elem.offsetWidth != 0 && elem.offsetHeight != 0 ) ? 'block':'none';
	vis.display = ( vis.display == '' || vis.display == 'block' ) ? 'none':'block';
}



function BorrarRango(){
	document.forma1.Mfn.value=''
	document.forma1.to.value=''
}

function BorrarExpresion(){
	document.forma1.Expresion.value=''
}

function EnviarForma(){
	de=Trim(document.forma1.Mfn.value)
  	a=Trim(document.forma1.to.value)
  	if (de!="" || a!="") {
	  	document.forma1.Opcion.value="rango"
  		Se=""
		var strValidChars = "0123456789";
		blnResult=true
   	//  test strString consists of valid characters listed above
   		for (i = 0; i < de.length; i++){
    		strChar = de.charAt(i);
    		if (strValidChars.indexOf(strChar) == -1){
    			alert("<?php echo $msgstr["inv_mfn"]?>")
	    		return
    		}
    	}
    	for (i = 0; i < a.length; i++){
    		strChar = a.charAt(i);
    		if (strValidChars.indexOf(strChar) == -1){
    			alert("<?php echo $msgstr["inv_mfn"]?>")
	    		return
    		}
    	}
    	de=Number(de)
    	a=Number(a)
    	if (de<=0 || a<=0 || de>a ||a><?php echo $tag["MAXMFN"]?>){
	    	alert("<?php echo $msgstr["inv_mfn"]?>")
	    	return
		}
	}
    if (Trim(document.forma1.Expresion.value)=="" && (Trim(document.forma1.Mfn.value)=="" )){
		alert("<?php echo $msgstr["selreg"]?>")
		return
	}
	if (Trim(document.forma1.Expresion.value)!="" && (Trim(document.forma1.Mfn.value)!="" )){
		alert("<?php echo $msgstr["selreg"]?>")
		return
	}
	if (document.forma1.tables.selectedIndex>0 ){
		if (document.forma1.rows.selectedIndex>0 || document.forma1.cols.selectedIndex>0){
			alert("<?php echo $msgstr["seltab"]?>")
			return
		}
	}
	if (document.forma1.tables.selectedIndex || document.forma1.rows.selectedIndex>0 || document.forma1.cols.selectedIndex>0){
	  	document.forma1.submit()
	  	return
	}
	document.forma1.submit();
}

function Buscar(){
	base=document.forma1.base.value
	cipar=document.forma1.cipar.value
  	Url="../dataentry/buscar.php?Opcion=formab&Target=s&Tabla=stats&base="+base+"&cipar="+cipar
  	msgwin=window.open(Url,"Buscar","menu=no, resizable,scrollbars,width=750,height=400")
	msgwin.focus()
}

function Configure(Option){
	if (document.configure.base.value==""){
		alert("<?php echo $msgstr["seldb"]?>")
		return
	}
	switch (Option){
		case "stats_var":
			document.configure.action="config_vars.php"
			break
		case "stats_tab":
			document.configure.action="tables_cfg.php"
			break
	}
	document.configure.submit()
}
</script>


<?php
if (isset($arrHttp["encabezado"])){
	$encabezado="&encabezado=s";
}
?>



	<ol class="breadcrumb">
	<li><h4><?php echo $msgstr["stats"].": ".$arrHttp["base"]?></h4></li>

<span class="pull-right">
<a href="../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]?>/stats/stats_tables_generate.html" target="_blank"><span class="glyphicon glyphicon-question-sign"></span><?php echo $msgstr["help"]?></a>

<?php
if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"]))
?>
	<a href="../documentacion/edit.php?archivo=<?php echo $_SESSION['lang'];?>/stats/stats_tables_generate.html" target="_blank">&nbsp;<span class="glyphicon glyphicon-pencil"></span></a>
</span>
	</ol>

<div class="panel-group" id="accordion">
<form name="forma1" method="post" action="tables_generate_ex.php" onsubmit="Javascript:return false">
<input type="hidden" name="base" value="<?php echo $arrHttp["base"]; ?>">
<input type="hidden" name="cipar" value="<?php echo $arrHttp["base"]; ?>.par">
<input type="hidden" name="Opcion">

<?php if (isset($arrHttp["encabezado"])) 
	echo "<input type=hidden name=encabezado value=s>\n";
?>



<!--//USAR UNA TABLA YA EXISTENTE-->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#exist_tb">
	<?php echo $msgstr["exist_tb"]; ?>
        </a>
      </h4>
    </div>
    <div id="exist_tb" class="panel-collapse collapse">
      <div class="panel-body">


	<label><?php echo $msgstr["tab_list"]; ?> </label>
	<select class="form-control" name="tables">
	<option value=""></option>
<?php
 unset($fp);
	$file=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/tabs.cfg";
	if (!file_exists($file)) $file=$db_path.$arrHttp["base"]."/def/".$lang_db."/tabs.cfg";
	if (!file_exists($file)){
		$error="S";
	}else{
		$fp=file($file);
		$fields="";
		foreach ($fp as $value) {
			$value=trim($value);
			if ($value!=""){
				$t=explode('|',$value);
				echo "<option value=".urlencode($value).">".trim($t[0])."</option>";
			}
		}
	}
?>
			</select>

      </div>
    </div>
  </div>
<!-- CONSTRUIR UNA TABLA SELECCIONANDO FILAS Y COLUMNAS  -->

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#create_tb">
	<?php echo $msgstr["create_tb"]?>
        </a>
      </h4>
    </div>
    <div id="create_tb" class="panel-collapse collapse">
      <div class="panel-body">
	<label><?php echo $msgstr["rows"]?></label>
	<select class="form-control" name="rows" >
	<option value=""></option>
 <?php
 	unset($fp);
	$file=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/stat.cfg";
	if (!file_exists($file)) $file=$db_path.$arrHttp["base"]."/def/".$lang_db."/stat.cfg";
	if (!file_exists($file)){
		$error="S";
	}else{
		$fp=file($file);
		foreach ($fp as $value) {
			$value=trim($value);
			if ($value!=""){
				$t=explode('|',$value);
				echo "<option value=".urlencode($value).">".trim($t[0])."</option>";
			}
		}
	}
?>
	</select>

	<label><?php echo $msgstr["cols"]?></label>
	<select class="form-control" name="cols">
	<option value=""></option>

 <?php
		foreach ($fp as $value) {
			$value=trim($value);
			if ($value!=""){
				$t=explode('|',$value);
				echo "<option value=\"".$value."\">".trim($t[0])."</option>";
			}
		}
?>
	</select>
      </div>
    </div>
  </div>

<!-- SELECIONAR REGISTROS  -->
		
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#gen_output">

	<?php echo $msgstr["gen_output"]?>
        </a>
      </h4>
    </div>
    <div id="gen_output" class="panel-collapse collapse">
      <div class="panel-body">
	<h5><?php echo $msgstr["bymfn"]?></h5>
		<div class="form-group">
			<label><?php echo $msgstr["from"]?></label>
			<input class="form-control col-md-12" type="text" name="Mfn" value="1">&nbsp; &nbsp; 
		 </div>
		<div class="form-group">
			<label><?php echo $msgstr["to"]?></label>
			<input class="form-control" type="text" name="to" size="10" value="<?php echo $tag["MAXMFN"]?>">
		 </div>
		 		<a class="btn btn-danger"  href="javascript:BorrarRango()"><?php echo $msgstr["clear"]?></a> (
		<?php echo $msgstr["maxmfn"].": ".$tag["MAXMFN"]?>)
	
	<h5><?php echo $msgstr["bysearch"]?></h5>
		<a href="javascript:Buscar()"><span class="glyphicon glyphicon-search btn-lg"></span></a>
		<textarea class="form-control" rows="2" cols="100" name="Expresion"><?php if (isset($Expresion )) echo $Expresion?></textarea>
		<a class="btn btn-danger" href="javascript:BorrarExpresion()" ><?php echo $msgstr["clear"]?></a></td>
		<button class="btn btn-primary" type="submit" onclick="EnviarForma()"><?php echo $msgstr["send"]?></button>
      </div>
    </div>
  </div>

<?php
if (isset($_SESSION["permiso"]["CENTRAL_STATCONF"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]))
{
?>

<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#stats_conf">
          <?php echo $msgstr["stats_conf"]?>
        </a>
      </h4>
    </div>
    <div id="stats_conf" class="panel-collapse collapse">
      <div class="panel-body">
    	<ul>
		<li><a href=javascript:Configure("stats_var")><?php echo $msgstr["var_list"]?></a></li>
		<li><a href=javascript:Configure("stats_tab")><?php echo $msgstr["tab_list"]?></a></li>
    	</ul>
<?php } ?>

</div>
</div>
</form>

<form name="configure" onSubmit="return false">
	<input type="hidden" name="Opcion" value="update">
	<input type="hidden" name="from" value="statistics">
	<input type="hidden" name="base" value=<?php echo $arrHttp["base"]?>>
	<?php if (isset($arrHttp["encabezado"])) echo "<input type=hidden name=encabezado value=s>";?>
</form>

      </div>
    </div>
  </div>
</div>
<?php
include("../common/footer.php");
?>
