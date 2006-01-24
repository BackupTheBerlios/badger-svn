<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/

/**
 * Transfers data from internal DataGridHandler to the DataGrid JavaScript widge.
 * 
 * Is called by the DataGrid JavaScript widget. Evaluates calling parameters and passes
 * them to the appropriate DataGridHandler. Takes care of calling security.
 * 
 * The following calling parameters (via GET) are recognized:
 * q (query): The DataGrid handler name
 * qp (query parameters): Parameters to the DataGrid handler, passed untouched.
 * 
 * ok[0-2] (order key 0 to 2): The order keys, need to be one of the column names.
 * od[0-2] (order direction 0 to 2): The direction of the order. Valid values: 'a' (ascending; default), 'b' (descending)
 * 
 * fk[n] (filter key n): The filter key of filter n, need to be one of the column names.
 * fo[n] (filter operator n): One of the operators defined by @see isLegalOperator().
 * fv[n] (filter value n): The value to compare the key with.
 * 
 * Example calling URL: getDataGridXML.php?q=transfers&qp=7&fk0=type&fo0=eq&fv0=Fuel&fk1=text&fo1=ct&fv1=BP&ok0=date&od0=d  
 * 
 * @author Eni Kao, paraphil
 * @version $LastChangedRevision$
 */

define("BADGER_ROOT", "../.."); 
require_once BADGER_ROOT . '/includes/fileHeaderBackEnd.inc.php';
require_once BADGER_ROOT . '/core/XML/DataGridRepository.class.php';
require_once BADGER_ROOT . '/core/XML/DataGridXML.class.php';
require_once BADGER_ROOT . '/core/Amount.class.php';

//q parameter is mandatory
if (!isset($_GET['q'])){
	return 'Missing Parameter q';
}

$dgr = new DataGridRepository($badgerDb);

//Unknown DataGridHandler if no result
try{
	$handlerData = $dgr->getHandler($_GET['q']);
} catch (BadgerException $ex){
	return 'Unknown DataGridHandler';		
}

//Include file containing DataGridHandler
require_once BADGER_ROOT . $handlerData['path'];

//Pass query parameters, if available
if (isset($_GET['qp'])) {
	$handler = new $handlerData['class']($badgerDb, unescaped($_GET, 'qp'));
} else {
	$handler = new $handlerData['class']($badgerDb);
}

$order = array();

//Filter order parameters to valid entries
for ($i = 0; $i <= 2; $i++) {
	if (isset($_GET["ok$i"])) {
		if ($handler->hasField(unescaped($_GET, "ok$i"))) {
			$order[$i]['key'] = unescaped($_GET, "ok$i");

			if (isset($_GET["od$i"])) {
				switch ($_GET["od$i"]) {
					case 'a':
					default:
						$order[$i]['dir'] = 'asc';
						break;
					
					case 'd':
						$order[$i]['dir'] = 'desc';
						break;
				}
			} else {
				//no order given
				$order[$i]['dir'] = 'a';
			}
		} else {
			//unknown order key
			return 'Unknown order key: ' . $_GET["ok$i"];
		}
	} else {
		//unset order key, do not process further
		break;
	}
}

$filter = array();
$i = 0;

//Filter filter parameters to valid enties
while (isset($_GET["fk$i"]) && isset($_GET["fo$i"]) && isset($_GET["fv$i"])) {
	if ($handler->hasField(unescaped($_GET, "fk$i"))) {
		$filter[$i]['key'] = unescaped($_GET, "fk$i");
		
		if (isLegalOperator(unescaped($_GET, "fo$i"))) {
			$filter[$i]['op'] = unescaped($_GET, "fo$i");
			
			try {
				$filter[$i]['val'] = transferType($handler->getFieldType(unescaped($_GET, "fk$i")), unescaped($_GET, "fv$i"));
			} catch (TransferException $ex) {
				//Untransferable Data Type
				return 'Illegal filter value: ' . $_GET["fv$i"];
			}
		} else {
			//illegal filter operator
			return 'Illegal filter operator: ' . $_GET["fo$i"];
		}
	} else {
		//unknown filter key
		return 'Unknown filter key: ' . $_GET["fk$i"];
	}

	$i++;
}

//Prepare Handler
$handler->setOrder($order);
$handler->setFilter($filter);

//Get data
$rows = $handler->getAll();
$columns = $handler->getFieldNames();

$dgx = new DataGridXML($columns, $rows);

header('Content-Type: text/xml');

//construct XML
echo $dgx->getXML();

require_once BADGER_ROOT . "/includes/fileFooter.php";


/**
 * Indicates an unsuccessful transfer from string to target type
 */
class TransferException extends Exception {
	/**
	 * Default handler
	 */
	function TransferException($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

/**
 * Checks if $op is a legal operator.
 * 
 * Legal operators are:
 * <ul>
 *   <li>eq - equal, ==</li>
 *   <li>lt - lower than, &lt;</li>
 *   <li>le - lower or equal, &lt;=</li>
 *   <li>gt - greater than, &gt;</li>
 *   <li>ge - greater or equal, &gt;=</li>
 *   <li>ne - not equal, !=</li>
 *   <li>bw - begins with (not case sensitive)</li>
 *   <li>ew - ends with (not case sensitive)</li>
 *   <li>ct - contains (not case sensitive)</li>
 * </ul>
 * 
 * @param string $op - The string to check for operator
 * @return boolean true if $op is a legal operator, false otherwise
 */
function isLegalOperator($op) {
	$legalOperators = array (
		'eq',
		'lt',
		'le',
		'gt',
		'ge',
		'ne',
		'bw',
		'ew',
		'ct' 	
	);
	
	return in_array($op, $legalOperators, true);
}

/**
 * Casts $str to $type.
 * 
 * @param string $type - The desired target data type.
 * @param string $str - The source data
 * @throws TransferException - if an error occured while transfering
 * @returns mixed $str cast to $type.
 */
function transferType($type, $str) {
	switch ($type) {
		case 'int':
		case 'integer':
		case 'string':
		case 'boolean':
		case 'bool':
		case 'float':
		case 'double':
			if (settype($str, $type)) {
				return $str;
			} else {
				throw new TransferException();
			}
			break;
			
		case 'Amount':
			return new Amount($str);
			break;
			
		default:
			throw new TransferException();
	}
}
?>