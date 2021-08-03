//========================
//CREACION DEL OBJETO AJAX
//========================


function objetoAjax(){
    var xmlhttp=false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }
 
    if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}
 

//==========================================================

function enviar_personas(elemento, id){  
    divResultado = document.getElementById('resultado_'+id);  
    usuario = elemento.value;  

    ajax=objetoAjax();  
    ajax.open("POST", "accion.php");  
    ajax.onreadystatechange=function() {  
        if (ajax.readyState==4) {  
            divResultado.innerHTML = ajax.responseText  
            //divResultado2.innerHTML = ajax.responseText  
        }  
    }  
    ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");  
    ajax.send("usuario="+usuario+"&id="+id)  
    //ajax.send("id="+id)  
}  

/*

function enviar_personas(){
    divResultado = document.getElementById('resultados');
	//divResultado2 = document.getElementById('resultados2');
    let usuario = document.getElementById('obj_text').value
	let id = document.getElementById('obj_text2').value
 
	 
    ajax=objetoAjax();
    ajax.open("POST", "accion.php");
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
            divResultado.innerHTML = ajax.responseText
			//divResultado2.innerHTML = ajax.responseText
        }
    }
    ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    ajax.send("usuario="+usuario+"&id="+id)
	//ajax.send("id="+id)
}




function chequear() {
  let codigos = document.querySelectorAll('input.codigo')
  let artCon = document.querySelectorAll('input.artCon')
  let caja = document.getElementById('caja')
   
  codigos.forEach(function(item, index) {
    if(item.value === caja.value) {
      artCon[index].value = 1;
    }
  })
  
  caja.value = ''
}





//==========================================================
function enviar_personas(){
    divResultado = document.getElementById('resultados');
	//divResultado2 = document.getElementById('resultados2');
    usuario = document.getElementById('obj_text').value;
	id = document.getElementById('obj_text2').value;
 
    ajax=objetoAjax();
    ajax.open("POST", "accion.php");
    ajax.onreadystatechange=function() {
        if (ajax.readyState==4) {
            divResultado.innerHTML = ajax.responseText
			//divResultado2.innerHTML = ajax.responseText
        }
    }
    ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    ajax.send("usuario="+usuario+"&id="+id)
	//ajax.send("id="+id)
}
*/




