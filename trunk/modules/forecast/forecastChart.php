<?php
define("BADGER_ROOT", "../..");
//include charts.php to access the SendChartData function
require_once(BADGER_ROOT . "/includes/charts/charts.php");

//change the chart to a line chart
$chart [ 'chart_type' ] = "line";
$chart [ 'axis_category' ] = array (   'skip'         =>  0,
                                       'font'         =>  "Arial", 
                                       'bold'         =>  true, 
                                       'size'         =>  12, 
                                       'color'        =>  "ffffff", 
                                       'alpha'        =>  25,
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

$chart [ 'axis_value' ] = array (   'min'           =>  -1000,  
                                    'max'           =>  5000, 
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

$chart [ 'chart_border' ] = array (   'top_thickness'     =>  0,
                                      'bottom_thickness'  =>  1,
                                      'left_thickness'    =>  1,
                                      'right_thickness'   =>  0,
                                      'color'             =>  "000000"
                                   );

                                   
$chart [ 'chart_data' ] = array ( array ( "",         "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August"),
                                  array ( "Prognose",     1000,     1300,     1800,     2300  ,  2800, 1200, 1400, 1900),
                                  array ( "mit Taschengeld",     800,     1100,     1600,     2100  ,  2600, 1000, 1200, 1700),
                                  array ( "mit Sparziel",     900,     1200,     1700,     -2200  ,  2700, 1100, 1300, 1800)
                                );
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
                                  'negative_alpha'  =>  30
                                );
$chart [ 'chart_transition' ] = array ( 'type'      =>  "drop",
                                        'delay'     =>  1, 
                                        'duration'  =>  5, 
                                        'order'     =>  "all"                                 
                                      ); 
                               
$chart [ 'legend_rect' ] = array (   'x'               =>  5,
                                     'y'               =>  5, 
                                     'width'           =>  390, 
                                     'height'          =>  5, 
                                     'margin'          =>  5,
                                     'fill_color'      =>  "FFFFFF",
                                     'fill_alpha'      =>  100, 
                                     'line_color'      =>  "C0C0C0",
                                     'line_alpha'      =>  100, 
                                     'line_thickness'  =>  2
                                 ); 
$chart [ 'legend_label' ] = array (   'layout'  =>  "horizontal",
                                      'bullet'  =>  "circle",
                                      'font'    =>  "Arial", 
                                      'bold'    =>  true, 
                                      'size'    =>  10, 
                                      'color'   =>  "000000", 
                                      'alpha'   =>  90
                                  ); 

SendChartData ( $chart );

?>
