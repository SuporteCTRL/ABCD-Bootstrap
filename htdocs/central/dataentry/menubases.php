<?php
//error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
global $valortag;
session_start();
if (!isset($_SESSION["permiso"])){
	header("Location: ../common/error_page.php") ;
}

include("../common/get_post.php");
include("../config.php");


if (isset($arrHttp["cambiolang"]))  $_SESSION["lang"]=$arrHttp["lang"];
include ("../lang/admin.php");
include ("../lang/lang.php");
include("leerregistroisispft.php");

$arrHttp["IsisScript"]="ingreso.xis";
$arrHttp["Mfn"]=$_SESSION["mfn_admin"];

$fp = file($db_path."bases.dat");
if (!$fp){
	echo "falta el archivo bases.dat";
	die;
}
echo "<script>
top.listabases=Array()\n";
foreach ($fp as $linea){
	if (trim($linea)!="") {
		$ix=strpos($linea,"|");
		$llave=trim(substr($linea,0,$ix));
		$lista_bases[$llave]=trim(substr($linea,$ix+1));
		echo "top.listabases['$llave']='".trim(substr($linea,$ix+1))."'\n";
	}

}
echo "</script>\n";
include("../common/header.php");
?>
<script>
lang='<?php echo $_SESSION["lang"]?>'

function AbrirAyuda(){
	msgwin=window.open("ayudas/"+lang+"/menubases.html","Ayuda","status=yes,resizable=yes,toolbar=no,menu=no,scrollbars=yes,width=750,height=500,top=10,left=5")
	msgwin.focus()
}

Entrando="S"

function VerificarEdicion(Modulo){
	 if(top.xeditar=="S"){
		alert("<?php echo $msgstr["aoc"]?>")
		return
	}
}

function CambiarBase(){
	tl=""
   	nr=""
   	top.img_dir=""
  	i=document.OpcionesMenu.baseSel.selectedIndex
  	top.ixbasesel=i
   	if (i==-1) i=0
  	abd=document.OpcionesMenu.baseSel.options[i].value
  	top.base=abd
	if (abd.substr(0,2)=="--"){
		alert("<?php echo $msgstr["seldb"]?>")
		return
	}
	ix=abd.indexOf("^b");
	if (ix>0){		base=abd.substr(2,ix-2)
	}else{		base=abd.substr(2)	}
	top.base=base
	if (document.OpcionesMenu.baseSel.options[i].text==""){
		return
	}
	abd=abd.substr(ix+2)
	ix=abd.indexOf("^c");
	if (ix>0){
		top.db_copies=abd.substr(ix+2)
	}else{
		top.db_copies=""
	}

	cipar=base+".par"
	top.nr=nr
	document.OpcionesMenu.base.value=base
   	document.OpcionesMenu.cipar.value=cipar
	document.OpcionesMenu.tlit.value=tl
	document.OpcionesMenu.nreg.value=nr
	top.base=base
	top.cipar=cipar
	top.mfn=0
	top.maxmfn=99999999
	top.browseby="mfn"
	top.Expresion=""
	top.Mfn_Search=0
	top.Max_Search=0
	top.Search_pos=0
	switch (top.ModuloActivo){
		case "dbadmin":

			top.menu.location.href="../dbadmin/index.php?base="+base

            break;
		case "catalog":
			i=document.OpcionesMenu.baseSel.selectedIndex
			document.OpcionesMenu.baseSel.options[i].text
			//if (top.NombreBase==document.OpcionesMenu.baseSel.options[i].text) return
			top.NombreBase=document.OpcionesMenu.baseSel.options[i].text
			top.menu.location.href="../dataentry/menu_main.php?Opcion=continue&inicio=s&cipar=acces.par&base="+base
			top.menu.document.forma1.ir_a.value=""
			i=document.OpcionesMenu.baseSel.selectedIndex
			break
		case "Capturar":

			break
	}
}


</script>
</head>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><i class="fa fa-university"></i> <?php echo $institution_name?></a>
    </div>
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
	<form class="navbar-form navbar-left"  name="OpcionesMenu">
		<input type="hidden" name="base" value="">
		<input type="hidden" name="cipar" value="">
		<input type="hidden" name="marc" value="">
		<input type="hidden" name="tlit" value="">
		<input type="hidden" name="nreg" value="">
		<select class="form-control" name=baseSel onchange=CambiarBase() onclick=VerificarEdicion() >
		<option value=""></option>
<?php
$i=-1;
$hascopies="";
foreach ($lista_bases as $key => $value) {
	$xselected="";
	$t=explode('|',$value);
	if (isset($_SESSION["permiso"]["db_".$key]) or isset($_SESSION["permiso"]["db_ALL"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]) or  isset($_SESSION["permiso"][$key."_CENTRAL_ALL"])){
		if (isset($arrHttp["base_activa"])){
			if ($key==$arrHttp["base_activa"]) 	{				$xselected=" selected";
				if (isset($t[1])) $hascopies=$t[1];			}

		}
		if (!isset($t[1])) $t[1]="";
		echo "<option value=\"^a$key^badm^c".$t[1]."\" $xselected>".$msgstr["bd"]." ".$t[0]."\n";
	}
}
echo "</select></td></table>" ;
if ($hascopies=="Y" and (isset($_SESSION["permiso"]["CENTRAL_ADDCO"]) or isset($_SESSION["permiso"]["CENTRAL_ALL"]) or  isset($_SESSION["permiso"][$arrHttp["base"]."_CENTRAL_ALL"]) or isset($_SESSION["permiso"][$arrHttp["baser"]."_CENTRAL_ADDCO"]))){	echo "\n<script>top.db_copies='Y'\n</script>\n";}
?>

</form>
	<ul class="nav navbar-nav navbar-right">
        <li>
        <a><?php echo $_SESSION["nombre"]?>, <?php echo $_SESSION["profile"]?> |
		<?php  $dd=explode("/",$db_path);
               if (isset($dd[count($dd)-2])){
			   		$da=$dd[count($dd)-2];
			   		echo " (".$da.") ";
				}
		?> </a>
		</li>
        <li style="margin-right: 10px;">
<?php

if (isset($_SESSION["newindow"]) or isset($arrHttp["newindow"])){
	?>
	<a class="btn btn-danger off" href='javascript:top.location.href="../dataentry/logout.php";top.close()' xclass="button_logout"><i class="glyphicon glyphicon-off"></i></a>

<?php}else{
?>
	<a class="btn btn-danger off" href="../dataentry/logout.php" xclass="button_logout"><i class="glyphicon glyphicon-off"></i></a>
	
<?php}
?>                
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
<script>
<?php
if (isset($arrHttp["inicio"]))
	$inicio="&inicio=s";
else
	$inicio="";
echo "top.menu.location.href=\"menu_main.php?base=\"+top.base+\"$inicio\"\n";?>
</script>
</body>
</html>

