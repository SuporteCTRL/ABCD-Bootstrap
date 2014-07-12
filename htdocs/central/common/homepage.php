<?php
//**********************************
// script: homepage.php
// editado: 11/07/2014
// editor: Roger C. Guilherme
//*********************************


$Permiso=$_SESSION["permiso"];

if (isset($arrHttp["modulo"])) $_SESSION["modulo"]=$arrHttp["modulo"];
$lista_bases=array();
if (file_exists($db_path."bases.dat")){
	$fp = file($db_path."bases.dat");
	foreach ($fp as $linea){
		$linea=trim($linea);
		if ($linea!="") {
			$ix=strpos($linea,"|");
			$llave=trim(substr($linea,0,$ix));
			$lista_bases[$llave]=trim(substr($linea,$ix+1));
		}
	}
}
$central="";
$circulation="";
$acquisitions="";
$ixcentral=0;
foreach ($_SESSION["permiso"] as $key=>$value){
	$p=explode("_",$key);
	if (isset($p[1]) and $p[1]=="CENTRAL"){
		$central="Y";
		$ixcentral=$ixcentral+1;
	}
	if (substr($key,0,8)=="CENTRAL_")  	{
		$central="Y";
		$ixcentral=$ixcentral+1;
	}
	if (substr($key,0,4)=="ADM_"){
		$central="Y";
		$ixcentral=$ixcentral+1;
	}
	if (substr($key,0,5)=="CIRC_")  	$circulation="Y";
	if (substr($key,0,4)=="ACQ_")  		$acquisitions="Y";

}
// Se determina el nombre de la página de ayuda a mostrar

if (!isset($_SESSION["modulo"])) {
	if ($central=="Y" and $ixcentral>0) {
		$arrHttp["modulo"]="catalog";
	}else{
		if ($circulation=="Y"){
			$arrHttp["modulo"]="loan";
		}else{
			$arrHttp["modulo"]="acquisitions";
		}
	}
}else{
	$arrHttp["modulo"]=$_SESSION["modulo"];
}
switch ($arrHttp["modulo"]){
	case "catalog":
		$ayuda="homepage.html";
		$module_name=$msgstr["catalogacion"];
		$_SESSION["MODULO"]="catalog";
		break;
	case "acquisitions":
		$ayuda="acquisitions/homepage.html";
		$module_name=$msgstr["acquisitions"];
		$_SESSION["MODULO"]="acquisitions";
		break;
	case "loan":
		$ayuda="circulation/homepage.html";
		$module_name=$msgstr["loantit"];
		$_SESSION["MODULO"]="loan";
}
include("header.php");
?>

<script>
	function CambiarLenguaje(){
		if (document.cambiolang.lenguaje.selectedIndex>0){
               lang=document.cambiolang.lenguaje.options[document.cambiolang.lenguaje.selectedIndex].value
               self.location.href="inicio.php?reinicio=s&lang="+lang
		}
	}

	function CambiarBaseAdministrador(Modulo){
		db=""
		if (Modulo!="traducir"){
			ix=document.admin.base.selectedIndex
		    if (ix<1){
		    	alert("<?php echo $msgstr["seldb"]?>")
		    	return
		    }
		    db=document.admin.base.options[ix].value
		    ix=db.indexOf("^",2)
		    db=db.substr(2,ix-2)
		}
	    switch(Modulo){
			case 'table':
document.admin.action="../dataentry/browse.php"
break
	    	case "resetautoinc":
	    		if (db+"_CENTRAL_RESETLCN" in perms || "CENTRAL_RESETLCN" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms){
	    	   		document.admin.action="../dbadmin/resetautoinc.php";
		document.admin.target="content";
	    		}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
	    		break;
	    	case "toolbar":
	    		document.admin.action="../dataentry/inicio_main.php";
	    		document.admin.target="_top";
	    		break;
			case "utilitarios":
if (db+"_CENTRAL_DBUTILS" in perms || "CENTRAL_DBUTILS" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms ){
	document.admin.action="../dbadmin/menu_mantenimiento.php";
	document.admin.target="content";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
                break;
   			case "estructuras":
   if (db+"_CENTRAL_MODIFYDEF" in perms || "CENTRAL_MODIFYDEF" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms){
	document.admin.action="../dbadmin/menu_modificardb.php";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
                break;
    		case "reportes":
    			if (db+"_CENTRAL_PREC" in perms || "CENTRAL_PREC" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms){
	document.admin.action="../dbadmin/pft.php";
	document.admin.target="content";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
                break;
    		case "traducir":
    			if (db+"_CENTRAL_TRANSLATE" in perms || "CENTRAL_TRANSLATE" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms){
	document.admin.action="../dbadmin/menu_traducir.php";
	document.admin.target="content";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
                break;
    		case "stats":
    			if (db+"_CENTRAL_STATGEN" in perms || "CENTRAL_STATGEN" in perms || "CENTRAL_STATGEN" in perms || "CENTRAL_ALL" in perms || db+"_CENTRAL_ALL" in perms){
	document.admin.action="../statistics/tables_generate.php";
	document.admin.target="content";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
    			break;
    		case "z3950":
    			if (db+"_CENTRAL_Z3950CONF" in perms || "CENTRAL_Z3950CONF" in perms || "CENTRAL_ALL" in perms || db+"CENTRAL_ALL" in perms){
	document.admin.action="../dbadmin/z3950_conf.php";
	document.admin.target="content";
}else{
	    			alert("<?php echo $msgstr["invalidright"];?>")
	    			return;
	    		}
    			break;
	    }
		document.admin.submit();
		
	}

</script>

</head>

<body>

<?php 

	include("institutional_info.php");
	
	if (isset($msg_path))
		$path_this=$msg_path;
	else
		$path_this=$db_path;
	$a=$path_this."lang/".$_SESSION["lang"]."/lang.tab";
 	if (!file_exists($a)) {
 		$a=$path_this."lang/en/lang.tab";
 	}
 	if (!file_exists($a)){
		echo $msgstr["flang"]." ".$a;
		die;
	}
 	$a=$path_this."lang/".$_SESSION["lang"]."/lang.tab";
 	if (!file_exists($a)) {
 		$a=$path_this."lang/en/lang.tab";
 	}
 	if (!file_exists($a)){
		echo $msgstr["flang"]." ".$path_this."lang/".$_SESSION["lang"]."/lang.tab";
		die;
	}
?>


	<ol class="breadcrumb">
		<li>
			<h3><?php echo $module_name; ?></h3>
		</li>
			<span class="pull-right">
				<a href=../documentacion/ayuda.php?help=<?php echo $_SESSION["lang"]."/$ayuda"?> target=_blank>
			<span class="glyphicon glyphicon-question-sign"></span><?php echo $msgstr["help"]?></a>
	<?php
		if (isset($_SESSION["permiso"]["CENTRAL_EDHLPSYS"])){
	?>
	
	&nbsp;<a href="../documentacion/edit.php?archivo=<?php echo $_SESSION["lang"]."/$ayuda" ?>" target="_blank">
			<span class="glyphicon glyphicon-pencil"></span>
			</a>
		</span>	
	</ol>
	
<?php

}

$Permiso=$_SESSION["permiso"];
switch ($_SESSION["MODULO"]){
	case "catalog":
		AdministratorMenu();
		break;
	case "loan":
		MenuLoanAdministrator();
		break;
	case "acquisitions":
		MenuAcquisitionsAdministrator();
		break;
}

?>

	</div>
	<?php include("text-footer.php"); ?>
	<?php include("footer.php"); ?>
	<?php include ("language.php"); ?>
	</body>
</html>



<?php
	function AdministratorMenu(){
	global $msgstr,$db_path,$arrHttp,$lista_bases,$Permiso,$dirtree;
		$_SESSION["MODULO"]="catalog";
?>

	<div class="col-sm-2">
		<h4 class="menu"><span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["database"]?></h4>
			<form role="form" name="admin" action="dataentry/inicio_main.php" method="post">
	
				<div class="form-group">
					<input type="hidden" name=encabezado value="s">
					<input type="hidden" name=retorno value="../common/inicio.php">
					<input type="hidden" name=modulo value="catalog">
					<?php if (isset($arrHttp["newindow"])) ?>
					<input type="hidden" name="newindow" value="Y">

					<select class="form-control" name="base" >
					<option value="" ><?php echo $msgstr["seleccionar"]?> <?php echo $msgstr["database"]?></option>
	<?php
		$i=-1;
			foreach ($lista_bases as $key => $value) {
				$xselected="";
				$value=trim($value);
				$t=explode('|',$value);
				if (isset($Permiso["db_".$key]) or isset($_SESSION["permiso"]["db_ALL"])){
				if (isset($arrHttp["base"]) and $arrHttp["base"]==$key or count($lista_bases)==1) $xselected=" selected";
				echo "<option value=\"^a$key^badm|$value\" $xselected>".$t[0]."\n";
	}
}
?>
					</select>

		<a href="javascript:CambiarBaseAdministrador('toolbar')" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-pencil "></span> <?php echo $msgstr["dataentry"]?>
		</a>
		 </div>
		</form>


		<a href="javascript:CambiarBaseAdministrador('stats')" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["statistics"]?>
		</a>

		<a href="javascript:CambiarBaseAdministrador('reportes')" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-th-list"></span> <?php echo $msgstr["reports"]?>
		</a>

		<a href="javascript:CambiarBaseAdministrador('estructuras')" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-cog"></span> <?php echo $msgstr["updbdef"]?>
		</a>

		<a href="javascript:CambiarBaseAdministrador('utilitarios')" class="btn btn-primary  btn-block menu">
			<span class="glyphicon glyphicon-wrench"></span> <?php echo $msgstr["maintenance"]?>
		</a>

		<a href="javascript:CambiarBaseAdministrador('z3950')"  class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-cloud-download"></span> <?php echo $msgstr["z3950"]?>
		</a>

<?php

	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_CRDB"])  or isset($Permiso["CENTRAL_URDADM"])
		or isset($Permiso["CENTRAL_RESETLIN"])  or isset($Permiso["CENTRAL_TRANSLATE"])  or isset($Permiso["CENTRAL_EXDBDIR"]))
{
?>
		<h4 class="menu"><span class="glyphicon glyphicon-cog"></span> <?php echo $msgstr["admtit"]?></h4>

<?php
	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_CRDB"]) or isset($Permiso["ADM_CRDB"])){
?>
			
		<a target="content" href="../dbadmin/menu_creardb.php?encabezado=S" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-file"></span> <?php echo $msgstr["createdb"]?>
		</a>

<?php
	}
	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_URADM"]) or isset($Permiso["ADM_CRDB"])){
?>
		<a target="content" href="../dbadmin/users_adm.php?encabezado=s&base=acces&cipar=acces.par" class="btn btn-primary btn-block menu">
			<span class="glyphicon glyphicon-user"></span> <?php echo $msgstr["usuarios"]?>
		</a>

<?php
	}
	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_TRANSLATE"])){
?>

	<a href="javascript:CambiarBaseAdministrador('traducir')" class="btn btn-primary btn-block menu">
		<span class="glyphicon glyphicon-globe"></span> <?php echo $msgstr["translate"]?>
	</a>

<?php
	}

	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_RESETLIN"])){
?>
	
		<a target="content" href="../dbadmin/reset_inventory_number.php?encabezado=s" class="btn btn-warning btn-block menu">
			<span class="glyphicon glyphicon-trash"></span> <?php echo $msgstr["resetinv"]?>
		</a>

<?php
	}
	if ($dirtree==1){
	if (isset($Permiso["CENTRAL_ALL"])  or isset($Permiso["CENTRAL_EXDBDIR"])){
?>

	<a href="../dbadmin/dirtree.php?encabezado=s" class="btn btn-primary btn-block menu">
		<span></span><?php echo $msgstr["expbases"]?>
	</a>

<?php 
	}
	}
?>
</div>

<div class="col-sm-10" style="height:1500px;">
	<iframe name="content" frameborder="no" scrolling="auto" width="100%" height="100%" src="/site"></iframe>
</div>

<?php
	}
	}
// end function Administrador

function MenuAcquisitionsAdministrator(){
	include("menuacquisitions.php");
}

function MenuLoanAdministrator(){
   include("menucirculation.php");
}
?>
	<script>
<?php
echo "var perms= new Array()\n";
foreach ($_SESSION["permiso"] as $key=>$value){
	echo "perms['$key']='$value';\n";
}
?>
	</script>