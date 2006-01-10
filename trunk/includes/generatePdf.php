<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://badger.berlios.org 
*
**/
 	/**
	 * horizontal resolution used during the page rendering process
	 * 
	 * @var int
	 */	
	$pdfPixels = 800;
	
	/**
	 * When a page contains both device-dependent (in pixels) 
	 * 	and device-independent (in points) dimensions, we're in 
	 * 	trouble. For example, imagine a page containing absolute-positioned 
	 * 	text inside the image; in this case, as display and paper have 
	 * 	different sizes, point/pixel ratio will be different in the browser 
	 * 	window and file rendered by the script, possilibly breaking the page 
	 * 	layout. To prevent this, the scalepoint option is used.
	 * 	If this parameter is set, the HTML page "points" are scaled to keep 
	 * 	the mentioned ratio, so if you print the resulting file and measure 
	 * 	fonts, they will be smaller than you expect, but the page layout 
	 * 	will be exactly the same as you see in browser window (well, 
	 * 	close to it). If you want fonts to have their real size, disable 
	 * 	this option.
	 * 
	 * @var int
	 */	
	$pdfScalePoints =1;
	
 	/**
	 * If this option is set, images will be displayed
	 * 
	 * @var int
	 */
	$pdfRenderImages = 1;
	
	/**
	 * If this option is set, links will be activated in the PDF
	 * 
	 * @var int
	 */
	$pdfRenderLinks = 1;
	
	/**
	 * If this option is set, special fields will be rendered
	 * 
	 * @var int
	 */
	$pdfRenderFields = 1;
	
	/**
	 * Defines Media (Letter, A4, A5, ...) 
	 * 
	 * @var String
	 */
	$pdfMedia = "A4";
	
	/**
	 * Defines CSS media (Print, Screen,...)
	 * 
	 * @var String
	 */
	$pdfCssMedia = "Print";
	
	/**
	 * Page margins (millimeters)
	 * 
	 * @var int
	 */	
	 $pdfLeftMargin = 30;
	 $pdfRightMargin = 15;
	 $pdfTopMargin = 15;
	 $pdfBottomMargin = 15;
	
	/**
	 * text encoding (here autodetect)
	 * 
	 * @var String
	 */
	 $pdfEncoding = "";
	
	/**
	 * Output method
	 * 
	 * @var String
	 */
	 $pdfMethod = "fpdf";	
	 
	/**
	 * PDF Version
	 * 
	 * @var String
	 */
	 $pdfPdfVersion = "1.3";	
	 
	 /**
	 * Select output source
	 * 
	 * @var String
	 */
	 $pdfOutput = "1";
	 	  

/**
 * function creates link for creating pdf
 * 
 * @param string $url 
 * @param string $linkText 
 * @param string $image Image displayed as link
 * @param string $fileName
 * @return String Link to create PDF
 */
function generatePdf ($url, $linkText, $fileName, $image = NULL){
	if ($url == ""){
		throw new badgerException('html2pdf.missing_url'); 
	}
	if ($linkText == ""){
		$linkText = "Generated PDF"; 
	}
	if ($fileName == ""){
		$fileName = "Badger Export"; 
	}
	global $pdfPixels, $pdfScalePoints, $pdfRenderImages, $pdfRenderLinks, 
		$pdfRenderFields, $pdfMedia, $pdfCssMedia, $pdfLeftMargin, $pdfRightMargin,
		$pdfTopMargin, $pdfBottomMargin, $pdfEncoding, $pdfMethod, $pdfMethod, 
		$pdfPdfVersion, $pdfOutput;
	
	/**
	 * options HTML2PS/PDF
	 * 
	 * @var String
	 */
	$pdfString="
	URL=" . $url . "&
	pixels=" . $pdfPixels . "&
	scalepoints=". $pdfScalePoints. "&
	renderimages=" . $pdfRenderImages . "&
	renderlinks=" . $pdfRenderLinks . "&
	renderfields=". $pdfRenderFields . "&
	media=" . $pdfMedia . "&
	cssmedia=" .$pdfCssMedia. "&
	leftmargin=" . $pdfLeftMargin . "&
	rightmargin=" . $pdfRightMargin . "&
	topmargin=" . $pdfTopMargin . "&
	bottommargin=" . $pdfBottomMargin . "&
	encoding=" . $pdfEncoding . "&
	method=" . $pdfMethod . "&
	pdfversion=" . $pdfPdfVersion . "&
	output=" . $pdfOutput . "&
	badgerFileName=".$fileName;
	if ($image){
		$pdfLink = "<a href = \"" . BADGER_ROOT . "/includes/html2pdf/html2ps.php?" . $pdfString . "\"><img src=\"" . $image . "\" alt = \"" . $linkText ."\" /></a>";		
	} else {
		$pdfLink = "<a href = \"".BADGER_ROOT."/includes/html2pdf/html2ps.php?" . $pdfString . "\">" . $linkText . "</a>";
	}
	return $pdfLink;
	// <a href = "/includes/html2pdf/html2ps.php?url=...">Linktext</a>
}

?>