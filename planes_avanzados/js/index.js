document.addEventListener("DOMContentLoaded", function() {

    setInterval(function(){
        location.reload();
    }, 300000);

    // Selecciona todos los inputs que necesitan formateo de moneda
    const currencyInputs = document.querySelectorAll("#plus, #venta, #cuota_promedio, #costo, #cuotas_pagadas_monto, #valor_unidad, #monto_reserva, #derecho_adjudicacion, #integracion");

    // Función para formatear el valor como moneda
    function formatCurrency(value) {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',
            minimumFractionDigits: 2
        }).format(value);
    }

    // Función que se ejecutará en el evento input
    function handleInput(event) {
        let input = event.target;
        let value = input.value.replace(/\D/g, ""); // Elimina todo lo que no sea dígito
        value = (value / 100).toFixed(2); // Convierte a decimal
        input.value = formatCurrency(value); // Formatea y asigna el valor formateado
    }

    // Formatea el valor inicial de cada campo si ya tiene uno
    currencyInputs.forEach(function(input) {
        if (input.value) {
            input.value = formatCurrency(parseFloat(input.value));
        }
        // Asigna el evento input a cada campo
        input.addEventListener("input", handleInput);
    });



    // Selecciona el input que solo debe aceptar números
    const cantidadInput = document.getElementById("cuotas_pagadas_cantidad");

    // Función que se ejecutará en el evento input
    cantidadInput.addEventListener("input", function(event) {
        let input = event.target;
        input.value = input.value.replace(/\D/g, ''); // Elimina todo lo que no sea un dígito
    });
});



