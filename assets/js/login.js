/* Webarch Admin Dashboard 
-----------------------------------------------------------------*/ 
$(document).ready(function() {		
	$('#login-form').validate({

                focusInvalid: false, 
                ignore: "",
                rules: {
                    txtusername: {
                        minlength: 2,
                        required: true
                    },
                    txtpassword: {
                        required: true,
                    }
                },

                invalidHandler: function (event, validator) {
					//display error alert on form submit    
                },

                errorPlacement: function (label, element) { // render error placement for each input type   
					$('<span class="error"></span>').insertAfter(element).append(label)
                    var parent = $(element).parent('.input-with-icon');
                    parent.removeClass('success-control').addClass('error-control');  
                },

                highlight: function (element) { // hightlight error inputs
					
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    
                },

                success: function (label, element) {
					var parent = $(element).parent('.input-with-icon');
					parent.removeClass('error-control').addClass('success-control'); 
                },
			    submitHandler: function(form) {
						form.submit();
				}
            });	

});

//Validaciones 
function checkpass() {
    /*Validar numero
    var numero =  document.signup.phone.value;
    const regex = /^\+56\d{9}$/;

    if (!regex.test(numero) ){
        alert('Debe ser un numero valido');
        document.signup.phone.focus();
        return false;
    }*/

    //validar contraseñas
    if (document.signup.password.value != document.signup.cpassword.value) {
        alert('Los campos contraseña y confirmar contraseña no coinciden');
        document.signup.cpassword.focus();
        return false;
    }
    return true;
}