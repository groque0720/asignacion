$(".rep_sucursal").click(function(event) {
	/* Act on the event */
	referencias=$(this).attr('data-datos');
	var datos = referencias.split('/');
	url = "filtro_resumen_pdf.php?desde="+datos[0]+"&hasta="+datos[1]+"&suc="+datos[2]+"&insc="+datos[3];
	window.open(url, '_blank');
});

$(".rep_provincia").click(function(event) {
	/* Act on the event */
	referencias=$(this).attr('data-datos');
	var datos = referencias.split('/');
	url = "filtro_detalle_pdf.php?desde="+datos[0]+"&hasta="+datos[1]+"&suc="+datos[2]+"&insc="+datos[3];
	window.open(url, '_blank');
});