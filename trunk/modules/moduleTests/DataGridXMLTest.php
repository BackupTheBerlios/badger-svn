<?php
/*
 * Created on Dec 30, 2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require_once '../../core/XML/DataGridXML.class.php';

header('Content-Type: text/plain');

function printDGX($dgx) {
	$dgxResult = $dgx->getXML();
	echo gettype($dgxResult);
	echo "\n";
	print_r($dgxResult);
	echo "\n\n";
}

/* Constructor can't be tested alone because you need vaild columns for using
 * getXML() or exception will be thrown!
$dgx1 = new DataGridXML();
echo "dgx1 constructor: \n";
printDGX($dgx1);
*/

$columns1 = array ("Name", "Vorname", "Hund");
$rows1 = array (
	array ("Stets", "Niko", "Paul"),
	array ("Hetscler", "Phil", "Ferdinand")
);
$columns2 = array ("Straße", "Hausnummer");
$rows2 = array (
	array ("Wilfried Ebert Str.", "14"),
	array ("Hermann Str." , "25"),
	array ("Stefan Kunklermannn Str.", "34")
);

$dgx2 = new DataGridXML($columns1, $rows1);
echo "dgx2 constructor: \n";
printDGX($dgx2);

$dgx1 = new DataGridXML();
echo "dgx1 setData: \n";
$dgx1->setData($columns1, $rows1);
printDGX($dgx1);

echo "dgx1 setData: Overwrite \n";
$dgx1->setData($columns2, $rows2);
printDGX($dgx1);

$dgx3 = new DataGridXML();
echo "dgx3 setColumns: \n";
$dgx3->setColumns($columns1);
printDGX($dgx3);

echo "dgx3 setColumns: Overwrite \n";
$dgx3->setColumns($columns2);
printDGX($dgx3);

$dgx4 = new DataGridXML();
echo "dgx4 setRows: \n";
$dgx4->setColumns($columns1);
$dgx4->setRows($rows1);
printDGX($dgx4);

echo "dgx4 setRows: Overwrite \n";
$dgx4->setRows($rows2);
printDGX($dgx4);

$dgx5 = new DataGridXML();
echo "dgx5 addRows to empty rows: \n";
$dgx5->setColumns($columns1);
$dgx5->addRows($rows1);
printDGX($dgx5);

echo "dgx5 addRows to filled rows: \n";
$dgx5->addRows($rows1);
printDGX($dgx5);

echo "dgx5 addRow (single) to filled rows: \n";
$dgx5->addRow(array ("Hackfresse", "Hans", "Dobi"));
printDGX($dgx5);

echo "dgx5 emptyRows: \n";
$dgx5->emptyRows();
printDGX($dgx5);

$dgx6 = new DataGridXML();
echo "dgx6 getXML Exception Test \n";
try {
	$dgx6->getXML();
	echo "no exception";
} catch (Exception $ex) {
	echo $ex;
}
require_once(BADGER_ROOT . "/includes/fileFooter.php");	
?>