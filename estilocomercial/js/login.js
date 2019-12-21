$(document).ready(function(){

	$("#usuario").focus();

	$("#form_login").submit(function(){
		event.preventDefault();
		$.ajax({
			url:"webs/login_validar.php",
			cache:false,
			type:"POST",
			data:$(this).serialize(),
			success:function(result){
      		$("#invalido").html(result);
      		$("#usuario").val('');
      		$("#contraseña").val('');
      		$("#usuario").focus();
    		}
    	});
	});

});