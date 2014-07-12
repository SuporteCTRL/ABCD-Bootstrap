<?php
session_start(); 
unset($_SESSION["verifica"]);


if (isset ($_GET['lang'])) {
	if ($_GET['lang'] != "") {
		$lang = $_GET['lang'];
	}
} else {
	$lang = '';
}
include_once("lib/library.php");
$combos = load_combos($lang);
$labels = load_labels($lang);
$errors = load_errors($lang);

if (!$combos || !$errors || !$labels) {	
	die ("<center><h3>Error to load .par files</h3><h5>Check paths in config files</h5></center>");
}
?>

  <html>
      <head>
      <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">			
      <title>ODDS</title>
      <meta content="ODDS" name="title">
      <meta content="ODDS" name="description">       
      <link href="../css/estilo_odds.css" rel="stylesheet" type="text/css">	  
      <script type="text/javascript" src="js/jquery.min.js"></script>
	  <script type="text/javascript" src="js/odds.js"></script>
	  <script type="text/javascript">
	  
	function ennegrecerTexto(texto) {		
		textoNegro = texto.replace("<font color=\"red\">", "");	
		textoNegro = textoNegro.replace("<FONT color=\"red\">", "");
		textoNegro = textoNegro.replace("<font color=red>", "");
		textoNegro = textoNegro.replace("<FONT color=red>", "");		
		textoNegro = textoNegro.replace("<FONT color='red'>", "");
		textoNegro = textoNegro.replace("<font color='red'>", "");		
		textoNegro = textoNegro.replace("</font>", "");
		textoNegro = textoNegro.replace("</FONT>", "");
		textoNegro = textoNegro.replace("*", "<font color='red'>*</font>" );		
		return textoNegro;
	}
	function enrojecerTexto(texto) {		
		textoRojo = texto.replace("<font color=\"red\">", "");
		textoRojo = textoRojo.replace("<FONT color=\"red\">", "");
		textoRojo = textoRojo.replace("</font>", "");
		textoRojo = textoRojo.replace("</FONT>", "");
		textoRojo = textoRojo.replace("<font color='red'>", "");
		textoRojo = textoRojo.replace("<FONT color='red'>", "");
		textoRojo = textoRojo.replace("<font color=red>", "");
		textoRojo = textoRojo.replace("<FONT color=red>", "");		
		textoRojo = "<font color=\"red\">" + textoRojo + "</font>";		
		return textoRojo;
	}
	function validarFormulario() {   
	  var error = false;
	  // cedula
	  
      if (document.getElementById("ci").value == "" ) {
		// multi language!		
		error = "<?php echo $errors['error_id_empty'];?>";
		document.getElementById("validation_table").style.display="block";	
		document.getElementById("errorRowSmall_id").style.display="block";		
		document.getElementById("errorRowSmall_id").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error;		
		document.getElementById("lbl_id").innerHTML = enrojecerTexto(document.getElementById("lbl_id").innerHTML);		
        document.getElementById("ci").focus(); 
        document.getElementById("ci").focus(); 
        return false;        
      } else {
        enteroValidado = validarEntero(document.getElementById("ci").value);
        if (enteroValidado == "" ) {
		  error = "<?php echo $errors['error_id_digits']; ?>";  
		  document.getElementById("validation_table").style.display="block";
		  document.getElementById("errorRowSmall_id").style.display="block";
		  document.getElementById("errorRowSmall_id").innerHTML = error;
		  document.getElementById("validation_text").innerHTML = error;	
		  document.getElementById("lbl_id").innerHTML = enrojecerTexto(document.getElementById("lbl_id").innerHTML);
          document.getElementById("ci").focus(); 
          return false;
        }
      }      
	  document.getElementById("lbl_id").innerHTML = ennegrecerTexto(document.getElementById("lbl_id").innerHTML);
	  document.getElementById("errorRowSmall_id").style.display="none";
	  
      // nombre
      if (document.getElementById("nombre").value == ""){        
		error = "<?php echo $errors['error_name_empty']; ?>";  
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_name").style.display="block";		
		document.getElementById("errorRowSmall_name").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_nombre").innerHTML = enrojecerTexto(document.getElementById("lbl_nombre").innerHTML);
        document.getElementById("nombre").focus();
        return false;
      }
	  document.getElementById("lbl_nombre").innerHTML = ennegrecerTexto(document.getElementById("lbl_nombre").innerHTML);
	  document.getElementById("errorRowSmall_name").style.display="none";

      // chequeo del email
      var cor='@.';
      var vacio;
      var contA=0;
      var contP=0;
      if (document.getElementById("emailUsuario").value == "") {
        vacio = true;
		error = "<?php echo $errors['error_email_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_email").style.display="block";		
		document.getElementById("errorRowSmall_email").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error; 
		document.getElementById("lbl_email").innerHTML = enrojecerTexto(document.getElementById("lbl_email").innerHTML);
        document.getElementById("emailUsuario").focus();        
        return false;
      }
      else {
          vacio=false;
          for(e=0; e < cor.length; e++) {
            for(i=0; i < document.getElementById("emailUsuario").value.length; i++) {
              if(cor.charAt(e) == document.getElementById("emailUsuario").value.charAt(i)) {
                if(cor.charAt(e) =='.') {
                  contP = contP + 1;
                }
                else {
                  contA= contA + 1;
                }
              }
            }//fin for
          }
      }
      // cantidad de puntos
      if (((contP != 2 && contP != 3) && contP != 1) && !vacio){ 
		error = "<?php echo $errors['error_email_invalid']; ?>"; 
        document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_email").style.display="block";		
		document.getElementById("errorRowSmall_email").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error; 
        document.getElementById("emailUsuario").focus();
		document.getElementById("lbl_email").innerHTML = enrojecerTexto(document.getElementById("lbl_email").innerHTML);
        return false;
      }
      if (error) {
        if (contA != 1 && !vacio) {            
			error = "<?php echo $errors['error_email_invalid']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_email").style.display="block";		
			document.getElementById("errorRowSmall_email").innerHTML = error;			
			document.getElementById("validation_text").innerHTML = error; 			
			document.getElementById("lbl_email").innerHTML = enrojecerTexto(document.getElementById("lbl_email").innerHTML);
            document.getElementById("emailUsuario").focus(); 
            return false;
        }
      }      
	  document.getElementById("lbl_email").innerHTML = ennegrecerTexto(document.getElementById("lbl_email").innerHTML);	  
	  document.getElementById("errorRowSmall_email").style.display="none";
	  
	  // el tel es obligtorio o NOOOOO¿? ¡por ahora si!
	  if (document.getElementById("tel").value == "") {
	    error=true;
	    error = "<?php echo $errors['error_phone_empty']; ?>"; 
        document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_tel").style.display="block";		
		document.getElementById("errorRowSmall_tel").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error; 
		document.getElementById("lbl_tel").innerHTML = enrojecerTexto(document.getElementById("lbl_tel").innerHTML);
	    document.getElementById("tel").focus();
	    return false;
	  }	  
	  document.getElementById("errorRowSmall_tel").style.display="none";
	  document.getElementById("lbl_tel").innerHTML = ennegrecerTexto(document.getElementById("lbl_tel").innerHTML);	  
	  
	  if (!error) {
	    if(document.getElementById("nivelbiblio").value == "as"){
		  document.getElementById("errorRowSmall_level").style.display="none";
	      if(checkRevista()){	
			document.getElementById("lbl_nivel_biblio").innerHTML = ennegrecerTexto(document.getElementById("lbl_nivel_biblio").innerHTML);	
			return true;		    
	      }
	    } else if(document.getElementById("nivelbiblio").value == "am") {
		  document.getElementById("errorRowSmall_level").style.display="none";
	      if(checkMonog()){	
			document.getElementById("lbl_nivel_biblio").innerHTML = ennegrecerTexto(document.getElementById("lbl_nivel_biblio").innerHTML);	
			return true;	    
	      }
	    } else  {
			error = "<?php echo $errors['error_level']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_level").style.display="block";
			document.getElementById("errorRowSmall_level").innerHTML = error;
			document.getElementById("validation_text").innerHTML = error;						
			document.getElementById("lbl_nivel_biblio").innerHTML = enrojecerTexto(document.getElementById("lbl_nivel_biblio").innerHTML);	
			document.getElementById("nivelbiblio").focus();			
			return false;
		}
	  }
	  document.getElementById("lbl_nivel_biblio").innerHTML = ennegrecerTexto(document.getElementById("lbl_nivel_biblio").innerHTML);
	  document.getElementById("errorRowSmall_level").style.display="none";
	  
	  // control para no mandar campos que no corresponden
	  if(document.getElementById("nivelbiblio").value == "am") {
		document.getElementById("autorEspecifico").value = "";
		document.getElementById("tituloEspecifico").value = "";	
		document.getElementById("volumenRevista").value = "";	
		document.getElementById("numeroRevista").value = "";	
	  }
	  if(document.getElementById("nivelbiblio").value == "as") {
	  	document.getElementById("autorObra").value = "";
		document.getElementById("edicion").value = "";
		
	  }
    } 	
	
	function checkRevista() {
	  var check = true;
	  if ((document.getElementById("tituloObra").value  == "") && check){
		error = "<?php echo $errors['error_journal_title']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_titulo_obra").style.display="block";
		document.getElementById("errorRowSmall_titulo_obra").innerHTML = error;		
		document.getElementById("validation_text").innerHTML = error;
	    document.getElementById("tituloObra").focus();
		document.getElementById("lbl_titulo_obra").innerHTML = enrojecerTexto(document.getElementById("lbl_titulo_obra").innerHTML);
	    check = false;
	  } else {
		document.getElementById("lbl_titulo_obra").innerHTML = ennegrecerTexto(document.getElementById("lbl_titulo_obra").innerHTML);
		document.getElementById("errorRowSmall_titulo_obra").style.display="none";
	  }
	  
	  if ((document.getElementById("autorEspecifico").value == "")&& check){
		error = "<?php echo $errors['error_article_author']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_autor_especifico").style.display="block";
		document.getElementById("errorRowSmall_autor_especifico").innerHTML = error;				
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("autorEspecifico").focus();
		document.getElementById("lbl_autor_especifico").innerHTML = enrojecerTexto(document.getElementById("lbl_autor_especifico").innerHTML);
	    check = false;
	  } else {
		document.getElementById("lbl_autor_especifico").innerHTML = ennegrecerTexto(document.getElementById("lbl_autor_especifico").innerHTML);
		document.getElementById("errorRowSmall_autor_especifico").style.display="none";
	  }
	  
	  if ((document.getElementById("tituloEspecifico").value  == "")&& check){
		error = "<?php echo $errors['error_article_title']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_titulo_especifico").style.display="block";
		document.getElementById("errorRowSmall_titulo_especifico").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error;
	    document.getElementById("tituloEspecifico").focus();
		document.getElementById("lbl_titulo_especifico").innerHTML = enrojecerTexto(document.getElementById("lbl_titulo_especifico").innerHTML);
	    check = false;
	  } else {
		document.getElementById("lbl_titulo_especifico").innerHTML = ennegrecerTexto(document.getElementById("lbl_titulo_especifico").innerHTML);
		document.getElementById("errorRowSmall_titulo_especifico").style.display="none";
	  }
	  if ((document.getElementById("ano").value  == "") && check){
		error = "<?php echo $errors['error_year_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_ano").style.display="block";
		document.getElementById("errorRowSmall_ano").innerHTML = error;		
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_ano").innerHTML = enrojecerTexto(document.getElementById("lbl_ano").innerHTML);
	    document.getElementById("ano").focus();
	    check = false;
	  } else {
		document.getElementById("lbl_ano").innerHTML = ennegrecerTexto(document.getElementById("lbl_ano").innerHTML);
		document.getElementById("errorRowSmall_ano").style.display="none";
	  }      
      // control de ano
      if (check) {
        check = validarAno();
      }		  
	  if ((document.getElementById("pagInicial").value  == "")&& check){
		error = "<?php echo $errors['error_initpage_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_initpage").style.display="block";
		document.getElementById("errorRowSmall_initpage").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_pagina_inicial").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
	    document.getElementById("pagInicialº").focus();
	    check = false;
	  } else {
		document.getElementById("lbl_pagina_inicial").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
		document.getElementById("errorRowSmall_initpage").style.display="none";
	  }
	  
	  if ((document.getElementById("pagFinal").value  == "")&& check){
		error = "<?php echo $errors['error_endpage_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_endpage").style.display="block";
		document.getElementById("errorRowSmall_endpage").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_pagina_final").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
	    document.getElementById("pagFinal").focus();
	    check = false;
	  } else {		
		document.getElementById("lbl_pagina_final").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
		document.getElementById("errorRowSmall_endpage").style.display="none";
	  }
	  // control de paginas
	  if (check) {
		check = validarPaginas();
	  }	
	  return check;
    }

	/*--------------------------- validarPaginas */
    function validarPaginas() {
		pagInicial = validarEntero(document.getElementById("pagInicial").value);
        if (pagInicial == "") {			
			error = "<?php echo $errors['error_initpage_number']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_initpage").style.display="block";
			document.getElementById("errorRowSmall_initpage").innerHTML = error;
			document.getElementById("validation_text").innerHTML = error;  
            document.getElementById("pagInicial").focus();
			document.getElementById("lbl_pagina_inicial").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
            return false;
        } else {
			document.getElementById("lbl_pagina_inicial").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
			document.getElementById("errorRowSmall_initpage").style.display="none";
		}
		
        pagFinal = validarEntero(document.getElementById("pagFinal").value);
        if (pagFinal == "") {
			error = "<?php echo $errors['error_endpage_number']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_endpage").style.display="block";
			document.getElementById("errorRowSmall_endpage").innerHTML = error;								
			document.getElementById("validation_text").innerHTML = error; 
            document.getElementById("pagFinal").focus();
			document.getElementById("lbl_pagina_final").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
            return false;
        } else {
			document.getElementById("lbl_pagina_final").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
			document.getElementById("errorRowSmall_endpage").style.display="none";
		}
		
        if (pagFinal < pagInicial) {
            error = "<?php echo $errors['error_pages_consistency']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("validation_text").innerHTML = error; 
            document.getElementById("pagInicial").focus();
			document.getElementById("lbl_pagina_inicial").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
			document.getElementById("lbl_pagina_final").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
			document.getElementById("errorRowSmall_endpage").style.display="block";
			document.getElementById("errorRowSmall_endpage").innerHTML = error;	
            return false;
        } else {
			document.getElementById("lbl_pagina_inicial").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
			document.getElementById("lbl_pagina_final").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
			document.getElementById("errorRowSmall_endpage").style.display="none";
		}
        return true;            
    }

	/*--------------------------- validarAno */
    function validarAno() { 
        ano = validarEntero(document.getElementById("ano").value);
        anoString = trim(document.getElementById("ano").value);
        if (ano == "") {
            error = "<?php echo $errors['error_year_number']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_ano").style.display="block";
			document.getElementById("errorRowSmall_ano").innerHTML = error;	
			document.getElementById("validation_text").innerHTML = error; 
            document.getElementById("ano").focus();
			document.getElementById("lbl_ano").innerHTML = enrojecerTexto(document.getElementById("lbl_ano").innerHTML);
            return false;
        }  else {
			document.getElementById("lbl_ano").innerHTML = ennegrecerTexto(document.getElementById("lbl_ano").innerHTML);
			document.getElementById("errorRowSmall_ano").style.display="none";
		}
        if (anoString.length != 4) {
            error = "<?php echo $errors['error_year_length']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_ano").style.display="block";
			document.getElementById("errorRowSmall_ano").innerHTML = error;
			document.getElementById("validation_text").innerHTML = error; 
            document.getElementById("ano").focus(); 
			document.getElementById("lbl_ano").innerHTML = enrojecerTexto(document.getElementById("lbl_ano").innerHTML);
            return false;
        } else {
			document.getElementById("lbl_ano").innerHTML = ennegrecerTexto(document.getElementById("lbl_ano").innerHTML);
			document.getElementById("errorRowSmall_ano").style.display="none";
		}		
        fecha = new Date(); 
        if (ano < 1850 || ano > fecha.getFullYear()) {
			error = "<?php echo $errors['error_year_consistency']; ?>"; 
			document.getElementById("validation_table").style.display="block";
			document.getElementById("errorRowSmall_ano").style.display="block";
			document.getElementById("errorRowSmall_ano").innerHTML = error;
			document.getElementById("validation_text").innerHTML = error; 
            document.getElementById("ano").focus();
			document.getElementById("lbl_ano").innerHTML = enrojecerTexto(document.getElementById("lbl_ano").innerHTML);
            return false;
        } else {
			document.getElementById("lbl_ano").innerHTML = ennegrecerTexto(document.getElementById("lbl_ano").innerHTML);
			document.getElementById("errorRowSmall_ano").style.display="none";
		}		
        return true;
    }
    
	/*--------------------------- checkMonog */
	function checkMonog() {
	  var check = true;
	  if ((document.getElementById("autorObra").value  == "") && check){
		error = "<?php echo $errors['error_book_author']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_autor_obra").style.display="block";
		document.getElementById("errorRowSmall_autor_obra").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error; 
	    document.getElementById("autorObra").focus();
		document.getElementById("lbl_autor_obra").innerHTML = enrojecerTexto(document.getElementById("lbl_autor_obra").innerHTML);
	    check = false;		
	  } else {
		document.getElementById("lbl_autor_obra").innerHTML = ennegrecerTexto(document.getElementById("lbl_autor_obra").innerHTML);
		document.getElementById("errorRowSmall_autor_obra").style.display="none";
	  }
	  if (document.getElementById("tituloObra").value  == "" && check) {
		error = "<?php echo $errors['error_book_title']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_titulo_obra").style.display="block";
		document.getElementById("errorRowSmall_titulo_obra").innerHTML = error;
		document.getElementById("validation_text").innerHTML = error; 
	    document.getElementById("tituloObra").focus();
		document.getElementById("lbl_titulo_obra").innerHTML = enrojecerTexto(document.getElementById("lbl_titulo_obra").innerHTML);
	    check = false;		
	  } else {
		document.getElementById("lbl_titulo_obra").innerHTML = ennegrecerTexto(document.getElementById("lbl_titulo_obra").innerHTML);
		document.getElementById("errorRowSmall_titulo_obra").style.display="none";
	  }	  
	  if ((document.getElementById("ano").value  == "")&& check){
	    error = "<?php echo $errors['error_year_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_ano").style.display="block";
		document.getElementById("errorRowSmall_ano").innerHTML = error;		
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_ano").innerHTML = enrojecerTexto(document.getElementById("lbl_ano").innerHTML);
	    document.getElementById("ano").focus();
	    check = false;
	  } else {
		document.getElementById("lbl_ano").innerHTML = ennegrecerTexto(document.getElementById("lbl_ano").innerHTML);
		document.getElementById("errorRowSmall_ano").style.display="none";
	  }	  
      // control de ano
      if (check) {
        check = validarAno();
      }
	  if ((document.getElementById("pagInicial").value  == "") && check){
	    error = "<?php echo $errors['error_initpage_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_initpage").style.display="block";
		document.getElementById("errorRowSmall_initpage").innerHTML = error;	
		document.getElementById("validation_text").innerHTML = error;		
		document.getElementById("lbl_pagina_inicial").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
	    document.getElementById("pagInicial").focus();
	    check = false;
	  }
	  else {
		document.getElementById("lbl_pagina_inicial").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_inicial").innerHTML);
		document.getElementById("errorRowSmall_initpage").style.display="none";
	  }
	  
	  if ((document.getElementById("pagFinal").value  == "") && check){
	    error = "<?php echo $errors['error_endpage_empty']; ?>"; 
		document.getElementById("validation_table").style.display="block";
		document.getElementById("errorRowSmall_endpage").style.display="block";
		document.getElementById("errorRowSmall_endpage").innerHTML = error;	
		document.getElementById("validation_text").innerHTML = error;
		document.getElementById("lbl_pagina_final").innerHTML = enrojecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
	    document.getElementById("pagFinal").focus();
	    check = false;
	  }
      else {
		document.getElementById("lbl_pagina_final").innerHTML = ennegrecerTexto(document.getElementById("lbl_pagina_final").innerHTML);
		document.getElementById("errorRowSmall_endpage").style.display="none";
	  }
	  // control de paginas
      if (check) {
        check = validarPaginas();
      }	  
	  return check;
    }	  
	
	$(document).ready(function() {
		$('#nivelbiblio').change(function() {  		
			if ($(this).val() == "as" || $(this).val() == "am") { 
			  document.getElementById("div_pagina_inicial").style.display = "block";
			  document.getElementById("div_pagina_final").style.display = "block";
			  $('#lbl_pagina_inicial').html("<?php echo $labels['initialpage']; ?>");
			  $('#lbl_pagina_final').html("<?php echo $labels['endpage']; ?>");	
			  document.getElementById("div_titulo_obra").style.display = "block";
			  document.getElementById("div_fuente").style.display = "block";
			  $('#lbl_fuente').html("<?php echo $labels['reference_source']; ?>");
			  <?php 
			  if (count($combos["topicarea"])>0) { 
			  ?>
				document.getElementById("div_area").style.display = "block";
			  <?php 
				}
			  ?>
			  document.getElementById("div_ano").style.display = "block";
			  $('#lbl_ano').html("<?php echo $labels['year']; ?>");
			  document.getElementById("errorRowSmall_level").style.display="none";
			  document.getElementById("lbl_nivel_biblio").innerHTML = ennegrecerTexto(document.getElementById("lbl_nivel_biblio").innerHTML);
			}
			
			if ($(this).val() == "as") {			  
			  document.getElementById("div_edicion").style.display = "none";			  
			  $('#lbl_titulo_especifico').html("<?php echo $labels['articletitle']; ?>"); 
			  $('#lbl_titulo_obra').html("<?php echo $labels['journaltitle']; ?>");
			  $('#lbl_autor_especifico').html("<?php echo $labels['articleauthor']; ?>");
			  document.getElementById("div_volumen_numero").style.display = "block";
			  document.getElementById("div_autor_obra").style.display = "none";
			  $('#lbl_volumen').html("<?php echo $labels['journalvolume']; ?>");		  
			  $('#lbl_numero').html("<?php echo $labels['journalnumber']; ?>");
			  document.getElementById("div_titulo_especifico").style.display = "block";
			  document.getElementById("div_autor_especifico").style.display = "block";
			  
			} else if ($(this).val() == "am") {
			  document.getElementById("div_edicion").style.display = "block";			  
			  $('#lbl_edicion').html("<?php echo $labels['edition']; ?>");			  
			  $('#lbl_titulo_obra').html("<?php echo $labels['booktitle']; ?>"); 			  
			  $('#lbl_autor_obra').html("<?php echo $labels['bookauthor']; ?>");
			  document.getElementById("div_titulo_especifico").style.display = "none";
			  document.getElementById("div_autor_especifico").style.display = "none";
			  document.getElementById("div_volumen_numero").style.display = "none";
			  document.getElementById("div_autor_obra").style.display = "block";			  
			} else {
			  document.getElementById("div_volumen_numero").style.display = "none";
			  document.getElementById("div_ano").style.display = "none";
			  document.getElementById("div_edicion").style.display = "none";			  
			  document.getElementById("div_pagina_inicial").style.display = "none";
			  document.getElementById("div_pagina_final").style.display = "none";			  
			  document.getElementById("div_titulo_especifico").style.display="none";
			  document.getElementById("div_autor_especifico").style.display= "none";
			  document.getElementById("div_titulo_obra").style.display="none";  
			  document.getElementById("div_autor_obra").style.display="none";			  
			  document.getElementById("div_fuente").style.display = "none";				  
			  <?php 
			  if (count($combos["topicarea"])>0) { 
			  ?>
				document.getElementById("div_area").style.display = "none";
			  <?php 
				}
			  ?>

			}
		});
		});
	</script>
    </head>
	
<body  onload="comienzo()">	
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
<table width="94%" border="0" cellpadding="0" cellspacing="0">
  <tbody>
	  <tr>
		<td valign="top" class="cuerpoCuad">&nbsp;</td>
	    <td>
			<div class='welcome'>
				<?php
				echo $labels['welcome'];
				?>
			</div>
		</td>
	  </tr>
  
  <tr>
	<td valign="top" class="cuerpoCuad">&nbsp;</td>
    <td colspan="2" valign="top" class="cuerpoText1">       
    <table width="790" border="0" cellspacing="0" cellpadding="1" bordercolor="#cccccc" class="textNove">
      <tbody>
      <tr>
	    <td height="12">
		<!-- TITULOS -->				
		<?php
		echo "<h2 class='main_title'>";
		echo $labels['title']; echo "<br>";
		echo "</h2>";
		echo "<i>";
		echo $labels['subtitle'];
		?>
		</i>		
	    </td>
      </tr>
      <tr>
	    <td>
	    &nbsp;
	    </td>
      </tr>

	  <!-- El archivo que procesa los datos del form -->
      <tr>
	    <td>		  
		  <form method = "post" name="forma1">	
	      <!-- subtitle USER -->		  
	      <span class="textNove">
		  <b>
		  <?php 
		  echo $labels['subtitle_user']
		  ?> 
		  </b>
		  </span>	      
	    </td>
      </tr>

      <!-- Error validation -->
      <tr>
	    <td>		
	    <table border="0" class="errorRow" style="display:none" id="validation_table">
	    <tbody>
	      <tr>
		  <div id="validation">
			<td width="30">
			<img src='../images/icon/defaultButton_cancel_odds_1.png' />
			</td>		  
			<td width="790">
				<div id="validation_text"></div>
				</div>
			</td>
	      </tr>
	    </tbody>
	    </table>
	    </td>
      </tr>	  
	  
      <!-- Cédula -->
      <tr>
	    <td>		
	    <table border="0" class="textNove" width="790">
	    <tbody>
	      <tr>
	      <td width="210"><label id="lbl_id"><?php echo $labels['id']." :"; ?> </label> <br>
			<div class = 'subtitle'><?php echo $labels['subid']; ?></div>
	      </td>
	      <td valign="top" width="120">
			<input type="text" id="ci" name="tag630" size="10" maxlength="10">
	      </td>
		  <td valign="top">
			<div style="display:none" id="errorRowSmall_id" class='errorRowSmall'></div>		  
		  </td>
	      </tr>
	    </tbody>
	    </table>
	    </td>
      </tr>

    <!-- Nombre-->
    <tr>
	<td>
	  <table border="0" class="textNove" width="790">
	    <tbody>
	      <tr>
		    <td width="210"><label id="lbl_nombre"><?php echo $labels['name']." :"; ?></label><br>
		    <div class = 'subtitle'><?php echo $labels['subname'].":"; ?></div>
		    </td>
		    <td valign="top" width="260">
		      <input type="text" id="nombre" name="tag510" size="35" maxlength="35">
		    </td>
			<td valign="top">
			  <div style="display:none" id="errorRowSmall_name" class='errorRowSmall'></div>		  
			</td>
	      </tr>
	    </tbody>
	    </table>
	  </td>
	</tr>

	<!-- Categoría -->
	<tr>
	  <td>
	    <table border="0" class="textNove" width="790">
	      <tbody>
	      <tr>
		    <td width="210"><?php echo $labels['category']." :"; ?> <br>
		    </td>
		    <td>
		      <select name="tag520" id="categoria">
				  <?php					
					$first = true;
					foreach ($combos["categoria"] as $key => $value) {
						if ($first) {
							echo "<option value=\"".$key."\" selected>".$value."</option>\n";
							$first = false;
						} else {
							echo "<option value=\"".$key."\">".$value."</option>\n";
						}
					}
				  ?>			  
		      </select>
		  </td>
	      </tr>
	      </tbody>
	    </table>
	  </td>
	</tr>

	<!-- Email -->
    <tr>
	  <td>	
	    <table border="0" class="textNove" width=790>
	      <tbody>
	      <tr>
		    <td width="210"><label id="lbl_email"><?php echo $labels['email']." :"; ?></label>
		    </td>
		    <td width="260">
		      <input type="text" name="tag528" id="emailUsuario"  size="35" maxlength="35">
		    </td>
			<td valign="top">
			  <div style="display:none" id="errorRowSmall_email" class='errorRowSmall'></div>  
			</td>			
	      </tr>
          </tbody>
	  </table>
        </td>
	</tr>

	<!-- Tel -->
	<tr>
	  <td>
	    <table border="0" class="textNove" width=790>
	      <tbody>
	      <tr>
		    <td width="210" valign="top"><label id="lbl_tel"><?php echo $labels['phone']." :"; ?></label>
		    </td>
            <td valign="top" width="260">
		    <input type="text" id="tel" name="tag512" size="35" maxlength="35">		
			<div class="pre-spoiler" style="margin: 2px 0 3px 0">
				<a href ='#' style="width: 80px; font-size: 11px; font-family: verdana; color:#000000;"
				onclick="if(this.parentNode.getElementsByTagName('div')[0].style.display != ''){this.parentNode.getElementsByTagName('div')[0].style.display = '';this.value = 'Ocultar'; document.getElementById('tel_2').focus();}else{this.parentNode.getElementsByTagName('div')[0].style.display = 'none'; this.value = 'Ver más'; document.getElementById('notas').focus();}">
				<?php echo $labels['addphone']; ?></a><div class="spoiler" style="display: none; padding: 5 0 0 0;">
				<input type="text" id="tel_2" name="tag512_additional" size="35" maxlength="35">
			</div>
			</div>
		    </td>
			<td valign="top">
			  <div style="display:none" id="errorRowSmall_tel" class='errorRowSmall'></div>		  
			</td>			

			</tr>   
	      </tbody>		  
	  </table>
	</td>
	</tr>   

	<!-- Notas del solicitante -->
	<tr>
	  <td>
	    <table border="0" class="textNove">
	      <tbody>
	      <tr>
		    <td width="210" valign="top" style="padding:5 0 0 0">
		    <?php echo $labels['comments']." :"; ?>
		    </td>
            <td valign="top" style="padding:0 0 7 0">
		    <textarea  id="notas" cols="50" rows="5"  name="tag068" style="overflow:hidden; resize:none; family: Verdana; font-size: 9 pt; height: 85; font-family: Verdana; background-color: #FFFFFF; color: #000000; maxlegth='100' size='80'"></textarea> 
		    </td>
	      </tr>
	      </tbody>
	  </table>
	</td>
	</tr>   

    <!-- DATOS DE LA BUSQUEDA -->
    <tr>
      <td><span class="textNove"><b><?php echo $labels['subtitle_request']." :"; ?></b></span></td>
    </tr>	
	
	<!-- as|articulo de revista - am|capitulo de libro -->
	<tr>
	  <td>
	    <table border="0" class="textNove">
	    <tbody>
	    <tr>
	      <td width="210">
			<label id="lbl_nivel_biblio"><?php echo $labels['level']." :"; ?></label>
			<br>
	      </td>	      
	      <td width="120">
		  <select name="tag006"  id="nivelbiblio">
			<option value="" selected><?php echo $labels['selectlevel']; ?></option>
			<?php
			foreach ($combos["nivelbiblio"] as $key => $value) {
				echo "<option value=\"".$key."\">".$value."</option>\n";
			}
		  ?>
		  </select>
	      </td>
		  <td valign="top">
		    <div style="display:none" id="errorRowSmall_level" class='errorRowSmall'></div>
		  </td>	
	    </tr>
	    </tbody>
		</table>
	  </td>
	</tr>
	
	<!-- area temática  -->
	<?php
		if (count($combos["topicarea"])>0) {
	?>	
	<tr>
	  <td>
		<div style="display: none" id="div_area">
	    <table border="0" class="textNove">
	    <tbody>
	    <tr>
	      <td width="210">
			<label id="lbl_area_temática"><?php echo $labels['tematicarea']." :"; ?></label>
			<br>
	      </td>	      
	      <td>
		  <select name="tag525"  id="areatematica">			
			<?php
			$i = 0;
			foreach ($combos["topicarea"] as $key => $value) {
				if ($i == 0) {
					echo "<option selected value=\"".$key."\">".$value."</option>\n";
				} else {
					echo "<option value=\"".$key."\">".$value."</option>\n";
				}
				$i++;
			}
		  ?>
		  </select>
	      </td>
	    </tr>
	    </tbody>
		</table>
		</div>
	  </td>
	</tr>
	<?php	
		}
	?>

    <!-- Autor -->    
    <tr>
	  <td>
	  <div style="display: none" id="div_autor_obra">
	  <table border="0" class="textNove">
	    <tbody>
	    <tr>
	      <td width="210"> 
	      <label id="lbl_autor_obra">Autor de la obra</label>
	      </td>	
	      <td align="left" width="390">
		    <input type="text" id="autorObra" name="tag016" size="57" maxlength="70">
	      </td>
		  <td valign="top">
		    <div style="display:none" id="errorRowSmall_autor_obra" class='errorRowSmall'></div>
		  </td>			  
	  </tr>
	  </tbody>
	  </table>
	</td>
	</div>
    </tr>
    
    <!-- Titulo -->
    <tr>
      <td>
      <div style="display: none" id="div_titulo_obra">
	  <table border="0" class="textNove">
	  <tbody>
	  <tr>
	    <td width="210"> 
	    <label id="lbl_titulo_obra">Título de la obra</label>
	    </td>
	    <td align="left" width="390">
	      <input type="text" name="tag012" id="tituloObra" size="57" maxlength="100">
	    </td>
		<td valign="top">
		  <div style="display:none" id="errorRowSmall_titulo_obra" class='errorRowSmall'></div>
		</td>
	  </tr>
	  </tbody>
	  </table>
	  </div>
   </td>
   </tr>

   <tr>
	  <td>
	  <div style="display: none" id="div_autor_especifico">
	  <table border="0" class="textNove">
	    <tbody>
	    <tr>
	      <td width="210"> 
	      <label id="lbl_autor_especifico">Autor específico</label>
	      </td>	
	      <td align="left" width="390">
		    <input type="text" id="autorEspecifico" name="tag010" size="57" maxlength="70">
	      </td>
	  </tr>
	  </tbody>
	  </table>
	</td>
	<td valign="top">
	  <div style="display:none" id="errorRowSmall_autor_especifico" class='errorRowSmall'></div>
	</td>
	</div>
    </tr>

    <!-- Titulo libro, tesis, revista -->
    <tr>
    <td>
    <div style="display: none" id="div_titulo_especifico">
	<table border="0" class="textNove">
	  <tbody>
	  <tr>
	    <td width="210">
	    <label id="lbl_titulo_especifico">Título específico</label>
	    </td>
	    <td align="left" width="390">
	      <input type="text" name="tag018" id="tituloEspecifico" size="57" maxlength="100">
	    </td>
		<td valign="top">
		  <div style="display:none" id="errorRowSmall_titulo_especifico" class='errorRowSmall'></div>
		</td>
	  </tr>
	  </tbody>
	</table>
	</div>
    </td>
	</tr> 

    <!-- Año  -->
    <tr>
    <td>
    <div style="display: none" id="div_ano">
	<table border="0" class="textNove">
	  <tbody>
	  <tr>
	    <td width="210">
	    <label id="lbl_ano">Año</label>
	    </td>
	    <td align="left" width="120">
	      <input type="text" id="ano"  name="tag064" size="5" maxlength="4">
	    </td>
		<td valign="top">
		  <div style="display:none" id="errorRowSmall_ano" class='errorRowSmall'></div>
		</td>
	  </tr>
	  </tbody>
	</table>
	</div>
    </td>
    </tr> 

    <!-- Edición  -->
    <tr>
    <td>
    <div style="display: none" id="div_edicion">
	<table border="0" class="textNove">
	  <tbody>
	  <tr>
	    <td width="210">
	    <label id="lbl_edicion">Edición</label>
	    </td>
	    <td align="left">
	      <input type="text" name="tag065" id="edicion" size="5" maxlength="4">
	    </td>
	  </tr>
	  </tbody>
	</table>
	</div>
    </td>
   </tr> 

    <!-- volumen numero SOLO REVISTA -->    
    <tr>
      <td>
        <div style="display: none" id="div_volumen_numero">
        <table border="0" class="textNove">
          <tbody
          <tr>
            <td width="210">
			<label id="lbl_volumen">Volumen de revista</label>
            </td>
            <td align="left">
              <input type="text" id="volumenRevista"  name="tag031" size="5" maxlength="5">
            </td>
            <td width="130">
			<label id="lbl_numero">Número de la revista</label>             
            </td>
            <td align="left">
              <input type="text" id="numeroRevista"  name="tag032" size="5" maxlength="5">
            </td>
          </tr>
          </tbody>
        </table>
        </div>
      </td>
    </tr>  

	<!-- pag inicial  -->
    <tr>
	<td>
      <div style="display: none" id="div_pagina_inicial">
	  <table border="0" class="textNove">
	    <tbody>
	    <tr>	
	      <td width="210">
		  <label id="lbl_pagina_inicial">Página inicial</label>
	      </td>
	      <td align="left" width="120">
		  <input type="text" id="pagInicial" name="tag020" size="7" maxlength="10">		  
	      </td>
		  <td valign="top">
		  <div style="display:none" id="errorRowSmall_initpage" class='errorRowSmall'></div>
		  </td>
	    </tr>
      </tbody>
	  </table>	  
	  </div>
	  </td>
      </tr>

    <!-- pag final -->
    <tr>
	<td>
      <div style="display: none" id="div_pagina_final">
	  <table border="0" class="textNove">
	    <tbody>
	    <tr>	
	      <td width="210">
		  <label id="lbl_pagina_final">Página final</label>
	      </td>
	      <td align="left"  width="120">
		    <input type="text" name="tag021" id="pagFinal"  size="7" maxlength="10">			
	      </td>
		  <td valign="top">
			<div style="display:none" id="errorRowSmall_endpage" class='errorRowSmall'></div>
		  </td>		  
	    </tr>
      </tbody>
	  </table>	  
	  </div>
	  </td>
      </tr>
	  
    <!-- Fuente  -->
    <tr>
    <td>
    <div style="display: none" id="div_fuente">
	<table border="0" class="textNove">
	  <tbody>
	  <tr>
	    <td width="210">
	    <label id="lbl_fuente">Fuente</label><br>
		  <div class = 'subtitle'><?php echo $labels['reference_source_subtitle']; ?></div> 
	    </td>
	    <td align="left">
	      <input type="text" name="tag900" id="fuente" size="57" maxlength="70">
	    </td>
	  </tr>
	  </tbody>
	</table>
	</div>
    </td>
   </tr> 
	  

    <!-- Botones para enviar -->
    <tr>
      <td align="center" class="txt1">	    
		<table border=0>
		  <tr>
		    <td align=center style="padding: 10 20 0 0"><a href="javascript:enviarForm()">
			<img src='../dataentry/img/barSave.png' border=0 alt="Enviar solicitud"></a></td>
			<td align=center style="padding: 10 20 0 0"><a href="javascript:cancelarEnvio()">
			<img src='../dataentry/img/barCancelEdit.png' border=0 alt="Limpiar formulario"></a>
			</td>
		  </tr>
		</table>
		<input type=hidden name=IsisScript value=ingreso.xis>
		<input type=hidden name=Opcion value="crear"> 
		<input type=hidden name=lang value="<?php  echo $lang; ?>"> 
		<input type=hidden name=ValorCapturado value="">
		<input type=hidden name=check_select value="">		
		<input type=hidden name=ver value=S>
		<input type=hidden name=Formato value='odds'>		
		<input type=hidden name=tag094 value='0'>
		</form> 
      </td>
    </tr>
    </tbody>
    </table>

</td></tr>
</tbody>
</table>
<!-- ----------------------------------------------------- -->
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
