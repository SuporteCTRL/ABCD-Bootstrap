	
	function trim (myString) {
		return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
	}

	function comienzo(){	  
		document.getElementById("ci").focus();
	}  	
   
	function cancelarEnvio() {
	  document.getElementById("validation_table").style.display="none";
	  document.getElementById("validation").innerHTML = "";
      document.getElementById("tel").value = "";
      document.getElementById("emailUsuario").value = "";
      document.getElementById("nombre").value = "";
      document.getElementById("ci").value = "";
      document.getElementById("edicion").value = "";
      document.getElementById("tituloEspecifico").value = "";
      document.getElementById("autorEspecifico").value = "";
      document.getElementById("tituloObra").value = "";
      document.getElementById("volumenRevista").value = "";
      document.getElementById("numeroRevista").value = "";
      document.getElementById("autorObra").value = "";
      document.getElementById("pagInicial").value = "";
      document.getElementById("notas").value = "";
      document.getElementById("pagFinal").value  = "";
      document.getElementById("ci").focus();
	}
	
	function enviarForm(){
		if (validarFormulario()) {	
			document.forma1.action="process_odds.php";
			document.forma1.submit();
		}
	}
	
	function validarEntero(valor){
	  //intento convertir a entero. 
	  //si era un entero no le afecta, si no lo era lo intenta convertir
	  valor = parseInt(valor)
	  //Compruebo si es un valor numérico
	  if (isNaN(valor)) {
	    //entonces (no es numero) devuelvo el valor cadena vacia
	    return "";
	  } 
	  else {
	    //En caso contrario (Si era un número) devuelvo el valor
	    return valor;
	  }
	}		
	

