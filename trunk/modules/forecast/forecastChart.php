<?php
define("BADGER_ROOT", "../..");
//include charts.php to access the SendChartData function
require_once(BADGER_ROOT . "/includes/fileHeaderBackEnd.inc.php");
require_once(BADGER_ROOT . "/includes/charts/charts.php");
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$startDate= new Date();
#end date aus forecast php übergeben
#$endDate = new Date ("2006-02-15");
if (isset($_GET['endDate'])) {
	$endDate = new Date($_GET['endDate']);
}
# accountId aus forecast php übergeben
#$accountId = 1;
if (isset($_GET['account'])) {
	$accountId = $_GET['account'];
}
// get pocketMoney1 from calling file
#$savingTarget = new Amount(0);
if (isset($_GET['savingTarget'])) {
	$savingTarget = new Amount($_GET['savingTarget']);
}
// get pocketMoney1 from calling file
#$pocketMoney1 = new Amount (25);
if (isset($_GET['pocketMoney1'])) {
	$pocketMoney1 = new Amount($_GET['pocketMoney1']);
}
// get pocketMoney2 from calling file
#$pocketMoney2 = new Amount(55);
if (isset($_GET['pocketMoney2'])) {
	$pocketMoney2 = new Amount($_GET['pocketMoney2']);
}
//get daily amounts from db
$am = new AccountManager($badgerDb);
$totals = array();

$currentAccount = $am->getAccountById($accountId);
//get LowerLimit for account from db

#$currentAccount->SetLowerLimit(NULL);
if (!is_null($currentAccount->getLowerLimit()->get())){
	$lowerLimit = $currentAccount->getLowerLimit();
} else {
	//lowerLimit nicht vorhanden, wie wenn in forecast php nicht angekreuzt
	
}
#$currentAccount->SetUpperLimit(NULL);
if (!is_null($currentAccount->getUpperLimit()->get())){
	$upperLimit = $currentAccount->getUpperLimit();
} else {
	//UpperLimit nicht vorhanden, wie wenn in forecast php nicht angekreuzt
	
}

//calculate every days balance
$currentBalances = getDailyAmount($currentAccount, $startDate, $endDate);
foreach ($currentBalances as $balanceKey => $balanceVal) {
	if (isset($totals[$balanceKey])) {
		$totals[$balanceKey]->add($balanceVal);
	} else {
		$totals[$balanceKey] = $balanceVal;
	}
}
//calculate spending money, if saving target should be reached
$countDay = count($totals)-1; //get numbers of days between today & endDate
$laststanding = new Amount($totals[$endDate->getDate()]);
$endDateBalance = $laststanding; //get balance of end date
$freeMoney = new Amount($endDateBalance->sub($savingTarget)); //endDateBalance - saving target = free money to spend
$dailyPocketMoney = new Amount ($freeMoney->div($countDay)); //calculate daily pocket money = free money / count of Days

$chart['chart_data'] = array();
$chart['chart_data'][0][0] = '';
#internationalisieren
$chart['chart_data'][1][0] = 'Dispo Limit'; //lower limit of the account
$chart['chart_data'][2][0] = 'Spargrenze'; //upper limit of the account
$chart['chart_data'][3][0] = 'Geplante Transaktionen'; //account balance for every day between today & end date, if no other expenses / income than in the finished transactions 
$chart['chart_data'][4][0] = 'Verlauf mit Sparziel';
$chart['chart_data'][5][0] = 'Verlauf mit Taschengeld1';
$chart['chart_data'][6][0] = 'Verlauf mit Taschengeld2';

$day = 0;
foreach($totals as $key => $val) {
	$tmp = new Date($key);
	$chart['chart_data'][0][] = $tmp->getFormatted();
	$chart['chart_data'][1][] = $lowerLimit->get();
	$chart['chart_data'][2][] = $upperLimit->get();
	$chart['chart_data'][3][] = $val->get();
	//to keep $dailyPocketMoney
	$dailyPocketMoneyLoop = new Amount ($dailyPocketMoney->get());
	$val1 = new Amount ($val->get());
	$chart['chart_data'][4][] = $val1->sub($dailyPocketMoneyLoop->mul($day))->get();
	$PocketMoney1Loop = new Amount ($pocketMoney1->get());
	$val2 = new Amount ($val->get());
	$chart['chart_data'][5][] = $val2->sub($PocketMoney1Loop->mul($day))->get();
	$PocketMoney2Loop = new Amount ($pocketMoney2->get());
	$val3 = new Amount ($val->get());
	$chart['chart_data'][6][] = $val3->sub($PocketMoney2Loop->mul($day))->get();
	$day++;
}

//for documentation for the following code see: http://www.maani.us/charts/index.php?menu=Reference
$chart [ 'chart_type' ] = "line";
$chart [ 'axis_category' ] = array (   'skip'         =>  $countDay/12,
                                       'font'         =>  "Arial", 
                                       'bold'         =>  false, 
                                       'size'         =>  10, 
                                       'color'        =>  "000000", 
                                       'alpha'        =>  100,
                                       'orientation'  =>  "horizontal"
                                   ); 
$chart [ 'axis_ticks' ] = array (   'value_ticks'      =>  true, 
                                    'category_ticks'   =>  true, 
                                    'position'         =>  "centered", 
                                    'major_thickness'  =>  2, 
                                    'major_color'      =>  "000000", 
                                    'minor_thickness'  =>  1, 
                                    'minor_color'      =>  "000000",
                                    'minor_count'      =>  4
                                ); 

$chart [ 'axis_value' ] = array (   'min'           =>  0, //automatically adjusted  
                                    'max'           =>  0, //automatically adjusted
                                    'steps'         =>  10,  
                                    'prefix'        =>  "", 
                                    'suffix'        =>  "", 
                                    'decimals'      =>  0,
                                    'decimal_char'  =>  ".",
                                    'separator'     =>  "", 
                                    'show_min'      =>  true, 
                                    'font'          =>  "Arial", 
                                    'bold'          =>  false, 
                                    'size'          =>  10, 
                                    'color'         =>  "000000", 
                                    'alpha'         =>  75,
                                    'orientation'   =>  "horizontal"
                                   );

$chart [ 'chart_border' ] = array (   'top_thickness'     =>  1,
                                      'bottom_thickness'  =>  1,
                                      'left_thickness'    =>  1,
                                      'right_thickness'   =>  1,
                                      'color'             =>  "000000"
                                   );

 /*                                  
$chart [ 'chart_data' ] = array ( array ( "",         "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August"),
                                  array ( "Prognose",     1000,     1300,     1800,     2300  ,  2800, 1200, 1400, 1900),
                                  array ( "Dispo-Limit",     -800,     -800,     -800,     -800  ,  -800, -800, -800, -800),
                                  array ( "Investieren!",     2000,     2000,     2000,     2000  ,  2000, 2000, 2000, 2000),
                                  array ( "mit Taschengeld",     800,     1100,     1600,     2100  ,  2600, 1000, 1200, 1700),
                                  array ( "mit Sparziel",     900,     1200,     1700,     -2200  ,  2700, 1100, 1300, 1800)
                                );
*/
$chart [ 'chart_pref' ] = array (   'line_thickness'  =>  1,  
                                    'point_shape'     =>  "none", 
                                    'fill_shape'      =>  false
                                  ); 

$chart [ 'chart_grid_h' ] = array (   'thickness'  =>  1,
                                      'color'      =>  "000000",
                                      'alpha'      =>  15,
                                      'type'       =>  "solid"
                                   );
$chart [ 'chart_grid_v' ] = array (   'thickness'  =>  1,
                                      'color'      =>  "000000",
                                      'alpha'      =>  5,
                                      'type'       =>  "dashed"
                                   );
$chart [ 'chart_rect' ] = array ( 'x'=>50,
                                  'y'=>50,
                                  'width'=>700,
                                  'height'=>300,
                                  'positive_color'  =>  "ffffff",
                                  'negative_color'  =>  "000000",
                                  'positive_alpha'  =>  100,
                                  'negative_alpha'  =>  10
                                );
$chart [ 'chart_value' ] = array (  'prefix'         =>  "", 
                                    'suffix'         =>  "", 
                                    'decimals'       =>  0,
                                    'decimal_char'   =>  ".",  
                                    'separator'      =>  "",
                                    'position'       =>  "cursor",
                                    'hide_zero'      =>  true, 
                                    'as_percentage'  =>  false, 
                                    'font'           =>  "Arial", 
                                    'bold'           =>  false, 
                                    'size'           =>  10, 
                                    'color'          =>  "000000", 
                                    'alpha'          =>  90
                                  ); 
$chart [ 'chart_transition' ] = array( 'type'      =>  "none",
                                        'delay'     =>  1, 
                                        'duration'  =>  1, 
                                        'order'     =>  "all"                                 
                                      ); 
                               
$chart [ 'legend_rect' ] = array (   'x'               =>  50,
                                     'y'               =>  5, 
                                     'width'           =>  700, 
                                     'height'          =>  5, 
                                     'margin'          =>  5,
                                     'fill_color'      =>  "FFFFFF",
                                     'fill_alpha'      =>  100, 
                                     'line_color'      =>  "000000",
                                     'line_alpha'      =>  100, 
                                     'line_thickness'  =>  1
                                 ); 
$chart [ 'legend_label' ] = array (   'layout'  =>  "horizontal",
                                      'bullet'  =>  "circle",
                                      'font'    =>  "Arial", 
                                      'bold'    =>  false, 
                                      'size'    =>  11, 
                                      'color'   =>  "000000", 
                                      'alpha'   =>  90
                                  ); 
$chart [ 'legend_transition' ] = array ( 'type'      =>  "none",
                                         'delay'     =>  1, 
                                         'duration'  =>  1 
                                       ); 
$chart [ 'series_color' ] = array (  "FF0000", 
									 "00FF00",									 
									 "0000FF",
									 "FF8000",
									 "404040",
									 "800040"
									);                                       
SendChartData ( $chart );

?>
