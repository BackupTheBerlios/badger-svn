<?php

//include charts.php to access the SendChartData function
require_once(BADGER_ROOT . "/includes/charts/charts.php");

//change the chart to a bar chart
$linechart [ 'chart_type' ] = "line";

SendChartData ( $linechart );

?>
