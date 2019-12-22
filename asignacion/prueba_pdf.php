<?php
require('fpdf/fpdf.php');

// instead of "$doc = new PDF();" use "$doc = new FPDF();"

$doc = new FPDF('P', 'mm', array(100,150));
$doc -> AddPage();
$doc -> SetFont('Arial','B', 16);
$doc -> MultiCell(40,10, 'hello word!');
$doc -> OutPut('F', 'folio.pdf');