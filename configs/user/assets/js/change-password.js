function valid() {
    const password = document.form1.newpass.value;
    const confirmPassword = document.form1.confirmpassword.value;

    // Expresión regular (Como minimo = 6 caracteres,1 mayuscula,1 minuscula, 1 numero y un caracter especial: @$!%*?&. )
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{6,}$/;

    if (!passwordRegex.test(password)) {
        alert("La contraseña debe tener al menos 6 caracteres e incluir: una letra mayúscula, una letra minúscula, un número y un carácter especial.");
        document.form1.newpass.focus();
        return false;
    } 

    if (password !== confirmPassword) {
        alert("La contraseña y la confirmación no coinciden.");
        document.form1.confirmpassword.focus();
        return false;
    }

    return true;
}