<form class="navbar-form navbar-left" role="form" name="frmModulo" method="post" action="../common/change_module.php">
<?php
$central="";
$circulation="";
$acquisitions="";
$ixcentral=0;
foreach ($_SESSION["permiso"] as $key=>$value){	if (substr($key,0,8)=="CENTRAL_")  {		$central="Y";
		$ixcentral=$ixcentral+1;
	}
	if (substr($key,0,5)=="CIRC_")  $circulation="Y";
	if (substr($key,0,4)=="ACQ_")  $acquisitions="Y";}
if ($central=="Y" and $ixcentral>1 and ($circulation=="Y" or $acquisitions=="Y")){
	?>
 
	<select class="form-control" name="modulo"  onchange="CambiarModulo()">
		<option value="" selected><?php echo $msgstr["modulo"]; ?> <?php echo $module_name; ?></option>
		<option value="catalog"><?php echo $msgstr["catalogacion"]?>
<?php
if ($catalog=="Y") echo "<option value=catalog>".$msgstr["catalogacion"]."\n";
if ($circulation=="Y") echo "<option value=loan>".$msgstr["prestamo"]."\n";
if ($acquisitions=="Y") echo "<option value=acquisitions>".$msgstr["acquisitions"]."\n";
echo "	</select>";
}
?>

</form>
<script>
function CambiarModulo(){	ix=document.frmModulo.modulo.selectedIndex
	if (ix<1){		return	}
	document.frmModulo.submit()}
</script>