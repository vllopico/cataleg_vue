<?php

require_once dirname(__FILE__) . '/TCPDF-main/tcpdf.php'; 
$comandes = json_decode(file_get_contents('php://input'), true);

class MYPDF extends TCPDF {
		
	public function Header() {
		$this->setXY(15, 15);
		$this->SetFont('helvetica', 'B', 20);
		$this->Cell(0, 15, 'Sistema de comanda', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}
	public function Footer() {
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		$this->Cell(0, 10, 'Pag.'.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Vicent Llopico');
$pdf->SetTitle('Comanda');
$pdf->SetSubject('Sistema comandes');


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->AddPage();

$pdf->setXY(178, 21);
$hui = date('d/m/Y H:i:s');
$pdf->Cell(20, 5, $hui,0,0,'R',0,'',0,false,'T','M');
$pdf->SetFont('helvetica', 'BI', 10);



$x = 10;
$y = 33;
$pdf->setXY($x, $y);

$pdf->Cell(30, 5,'Referencia',0,0,'L',0,'',0,false);
$x = $x+30;
$pdf->setX($x);
$pdf->Cell(40, 5,'Producte',0,0,'L',0,'',0,false);
$x = $x+40;
$pdf->setX($x);
$pdf->Cell(30, 5,'Quantitat',0,0,'C',0,'',0,false);
$x = $x+30;
$pdf->setX($x);
$pdf->Cell(19, 5,'Preu',0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,'Iva %',0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,'Base',0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,'Iva €',0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,'Total',0,0,'C',0,'',0,false);

$sLinia = array('width' => 0.07, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(40, 40, 40));
$pdf->Line(10, 38, 200, 38, $sLinia);

$pdf->SetFont('helvetica', 'I', 10);

$x = 10;
$y = 40;
$pdf->setXY($x,$y);


$totalbase = 0;
$totaliva = 0;
$total = 0;

foreach($comandes as $c)
{
	$pdf->Cell(30, 5,$c['referencia'],0,0,'L',0,'',0,false);
	$x = $x+30;
	$pdf->setX($x);
	$pdf->Cell(40, 5,$c['producte'],0,0,'L',0,'',0,false);
	$x = $x+40;
	$pdf->setX($x);
	$pdf->Cell(30, 5,$c['quantitat'],0,0,'C',0,'',0,false);
	$x = $x+30;
	$pdf->setX($x);
	$pdf->Cell(19, 5,$c['preu'],0,0,'C',0,'',0,false);
	$x = $x+19;
	$pdf->setX($x);
	$pdf->Cell(19, 5,$c['iva'],0,0,'C',0,'',0,false);
	$x = $x+19;
	$pdf->setX($x);
	$pdf->Cell(19, 5,$c['base'],0,0,'C',0,'',0,false);
	$x = $x+19;
	$pdf->setX($x);
	$pdf->Cell(19, 5,$c['totaliva'],0,0,'C',0,'',0,false);
	$x = $x+19;
	$pdf->setX($x);
	$pdf->Cell(19, 5,$c['total'],0,0,'C',0,'',0,false);
	
	$y = $y+7;
	$x = 10;
	$pdf->setXY($x, $y);
	
	$totalbase += floatval($c['base']);
	$totaliva += floatval($c['totaliva']);
	$total += floatval($c['total']);
}

//Última fila
$x = 148;
$y = $pdf->getY() + 3;
$pdf->setXY($x,$y);

$pdf->SetTextColor(210,40,40);
$pdf->SetFont('helvetica', 'BI', 11);

$pdf->Cell(19, 5,$totalbase,0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,$totaliva,0,0,'C',0,'',0,false);
$x = $x+19;
$pdf->setX($x);
$pdf->Cell(19, 5,$total,0,0,'C',0,'',0,false);


ob_clean();
$arxiu = "comanda_" . time() . ".pdf";
$pdf->Output('/var/www/html/cataleg_vue/pdfs/'.$arxiu, 'F');
echo json_encode(array("file"=>$arxiu));
