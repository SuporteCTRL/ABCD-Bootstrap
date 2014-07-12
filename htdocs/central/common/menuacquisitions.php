<?php
$_SESSION["MODULO"]="acquisitions";
global $arrHttp,$msgstr,$db_path,$valortag,$lista_bases;
	include ("../lang/acquisitions.php");
?>
	<div class="col-sm-2">

	<h4 class="menu"><span class="glyphicon glyphicon-comment"></span> <?php echo $msgstr["suggestions"]?></h4>	

	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/overview.php?encabezado=s">
		<span class="glyphicon glyphicon-eye-open"></span> <?php echo $msgstr["overview"]?>
	</a>
	
<?php
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_NEWSUGGESTIONS"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/suggestions_new.php?encabezado=s&base=suggestions&cipar=suggestions.par">
	<span class="glyphicon glyphicon-file"></span> <?php echo $msgstr["newsugges"]?>
	</a>
<?php }
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_APPROVREJECT"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/suggestions_status.php?base=suggestions&cipar=suggestions.par&sort=TI&encabezado=s">
	<span class="glyphicon glyphicon-flag"></span> <?php echo $msgstr["approve"]."/".$msgstr["reject"]?>
	</a>
<?php }
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_BIDDING"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/bidding.php?base=suggestions&sort=DA&encabezado=s&menu=s" >
	<span class="glyphicon glyphicon-filter"></span> <?php echo $msgstr["bidding"]?>
	</a>
<?php }
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_DECISION"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/decision.php?base=suggestions&sort=DA&encabezado=s&menu=s">
	<span class="glyphicon glyphicon-bell"></span> <?php echo $msgstr["decision"]?>
	</a>
<?php }?>
					
	<h4 class="menu"><span class="glyphicon glyphicon-shopping-cart"></span>  <?php echo $msgstr["purchase"]?></h4>						
			
<?php
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_CREATEORDER"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/order_new_menu.php?base=suggestions&sort=PV&encabezado=s" >
	<span class="glyphicon glyphicon-shopping-cart"></span>  <?php echo $msgstr["createorder"]?>
	</a>

	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/order.php?base=suggestions&sort=PV&encabezado=s">
	<span class="glyphicon glyphicon-briefcase"></span> <?php echo $msgstr["generateorder"]?>
	</a>
<?php }?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/pending_order.php?base=purchaseorder&sort=PV&encabezado=s">
	<span class="glyphicon glyphicon-star"></span> <?php echo $msgstr["pendingorder"]?>
	</a>
<?php if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_RECEIVING"])){
?>
	<a target="content" class="btn btn-primary btn-block menu" href="../acquisitions/receive_order.php?encabezado=s" class="defaultButton multiLine receivingButton">
	<span class="glyphicon glyphicon-download-alt"></span> <?php echo $msgstr["receiving"]?>
	</a>
<?php }?>

<?php
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_ACQDATABASES"])){
?>

	<h4 class="menu"><span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["basedatos"]?></h4>						

	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=suggestions&modulo=acquisitions">
	<span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["suggestions"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=providers&modulo=acquisitions">
	<span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["providers"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=purchaseorder&modulo=acquisitions">
	<span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["purchase"]?>
	</a>
	<a target="content" class="btn btn-primary btn-block menu" href="../dataentry/browse.php?base=copies&modulo=acquisitions">
	<span class="glyphicon glyphicon-hdd"></span> <?php echo $msgstr["copies"]?>
	</a>

<?php  }
if (isset($_SESSION["permiso"]["ACQ_ACQALL"]) or isset($_SESSION["permiso"]["ACQ_RESETCN"])){
?>

	<h4 class="menu"><span class="glyphicon glyphicon-stats"></span> <?php echo $msgstr["admin"]?></h4>						

	<a target="content" class="btn btn-warning btn-block menu" href="../acquisitions/resetautoinc.php?base=suggestions" class="defaultButton multiLine resetButton">
	<span class="glyphicon glyphicon-trash"></span> <?php echo $msgstr["resetctl"]; ?>
	</a>

</div><!--COL SM 2-->

<div class="col-sm-10" style="height:1000px;">
<iframe name="content" frameborder="no" width="100%" height="100%" src="/site"></iframe>
</div>
<?php }?>