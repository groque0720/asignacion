<?php
/*
 * Endpoint de exportación a PDF (apaisado, para imprimir) del filtro actual.
 * Wrapper fino: silencia errores (FPDF viejo) + bootstrap + helpers + acción.
 */
// FPDF es viejo: sus deprecations/notices romperían el binario. Silenciar + bufferizar.
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', '0');
ob_start();
require __DIR__ . '/config/config_app.php';    // sesión + auth (redirect) + $con
require __DIR__ . '/funciones/consulta.php';   // cp_where / cp_fetch_todo / cp_fecha …
require __DIR__ . '/actions/exportar_pdf.php'; // streamea el PDF y termina
