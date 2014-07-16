<script>
function VerificarInicializacion(){	if (confirm("Quiere inicializar las transacciones de préstamo")){		self.frames['content'].location.href="../circulation/initialize_trans.php?encabezado=s"	}}
</script>


<div class="col-sm-2">

<?php
$_SESSION["MODULO"]="loan";
global $arrHttp,$msgstr,$db_path,$valortag,$lista_bases;
?>

	<h4 class="menu"><span class="glyphicon glyphicon-transfer"></span> <?php echo $msgstr["trans"]?></h4>
	
<?php
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_LOAN"])){
?>

	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/prestar.php?encabezado=s">
	<span class="glyphicon glyphicon-transfer"></span> </span><?php echo $msgstr["loan"]?>
	</a>
<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RESERVE"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/estado_de_cuenta.php?encabezado=s&reserve=S" class="menuButton reserveButton">
	<span class="glyphicon glyphicon-calendar"></span> <?php echo $msgstr["reserve"]?>
	</a>
<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RETURN"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/devolver.php?encabezado=s" class="menuButton returnButton">
	<span class="glyphicon glyphicon-repeat"></span> <?php echo $msgstr["return"]?>
	</a>
<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_RENEW"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/renovar.php?encabezado=s" class="menuButton renewButton">
	<span class="glyphicon glyphicon-refresh"></span> <?php echo $msgstr["renew"]?>
	</a>
<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_SUSPEND"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/sanctions.php?encabezado=s" class="menuButton sanctionsButton">
	<span class="glyphicon glyphicon-usd"></span> <?php echo $msgstr["suspend"]."/".$msgstr["fine"]?>
	</a>
<?php }?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/situacion_de_un_objeto.php?encabezado=s" class="menuButton statusitemButton">
	<span class="glyphicon glyphicon-book"></span> <?php echo $msgstr["ecobj"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/estado_de_cuenta.php?encabezado=s">
			
	<span class="glyphicon glyphicon-info-sign"></span> <?php echo $msgstr["statment"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/borrower_history.php?encabezado=s">
	<span class="glyphicon glyphicon-folder-open"></span> <?php echo $msgstr["bo_history"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../output_circulation/menu.php">
	<span class=" glyphicon glyphicon-th-list"></span> <?php echo $msgstr["reports"]?>
	</a>

<?php
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCDATABASES"])){
?>

	<h4 class="menu"><span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["basedatos"]?></h4>

	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=users&modulo=loan">
	<span class="glyphicon glyphicon-user"></span> <?php echo $msgstr["users"]?>
	</a>
	
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=trans&modulo=loan">
	<span class="glyphicon glyphicon-th-list"></span> <?php echo $msgstr["trans"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=suspml&modulo=loan">
	<span class="glyphicon glyphicon-th-list"></span> <?php echo $msgstr["suspen"]."/".$msgstr["multas"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=reserve&modulo=loan">
	<span class="glyphicon glyphicon-th-list"></span> <?php echo $msgstr["reservas"]?>
	</a>

<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCFG"])
	or isset($_SESSION["permiso"]["CIRC_CIRCREPORTS"]) or isset($_SESSION["permiso"]["CIRC_CIRCSTAT"])){
?>
	<h4 class="menu"><span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["admin"]?></h4>

<?php
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCFG"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../circulation/configure_menu.php?encabezado=s" class="menuButton toolsButton">
	<span class="glyphicon glyphicon-cog"></span> <?php echo $msgstr["configure"]?>
	</a>
<?php
}
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCSTAT"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../statistics/tables_generate.php?base=users&encabezado=s" class="menuButton statisticsusersButton">
	<span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["stat_users"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../statistics/tables_generate.php?base=trans&encabezado=s" class="menuButton statButton">
	<span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["stat_trans"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../statistics/tables_generate.php?base=suspml&encabezado=s" class="menuButton statisticsanctionsButton">
	<span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["stat_suspml"]?>
	</a>
	
<?php
}

?>


<?php
if (isset($_SESSION["permiso"]["CENTRAL_ALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCALL"]) or isset($_SESSION["permiso"]["CIRC_CIRCREPORTS"])){
?>


	<a class="btn btn-warning btn-block menu" href="javascript:VerificarInicializacion()">
	<span class="glyphicon glyphicon-trash"></span> <?php echo $msgstr["init_trans"]?>
	</a>
<?php
}
}
?>

</div><!--COL SM 2-->
<div class="col-sm-10" style="height:1000px;">
<iframe name="content" frameborder="no" width="100%" height="100%" src="/site"></iframe>
</div>	
	