     <style>
            html {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>		

<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><i class="fa fa-university"></i> <?php echo $institution_name?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse">
		<?php include("modules.php")?>    
    
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
		
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Atalhos<span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/site" target="_blank">Site</a></li>
            <li><a href="http://www.loc.gov/marc/" target="_blank">MARC Standards</a></li>
            <li><a href="http://oraculo.inf.br" target="_blank">Oráculo</a></li>
           </ul>
        </li>
 
        <li>
 
<?php

if (isset($_SESSION["newindow"]) or isset($arrHttp["newindow"])){
	?>
	<a  class="btn btn-danger off" href='javascript:top.location.href="../dataentry/logout.php";top.close()' xclass="button_logout"><i class="glyphicon glyphicon-off"></i></a>

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


