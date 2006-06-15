<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
echo $tpl->getHeader("Seitenname");
echo $widgets->addToolTipLayer();
?>
   <form name="testform" method="post" action="process.php" onSubmit="return validateStandard(this, 'error');">
        Name: <input type="text" required="1" regexp="/^\w*$/" name="name"><br />
        Password: <input type="password" required="1" minlength="3" maxlength="8" name="password"><br />
        Alter: <input type="text" name="age" id="age" maxlength="3" size="3" required="1" minvalue="10" maxvalue="90" /><br />
        Asche: <input type="text" required="1" regexp="JSVAL_RX_MONEY" name="asche"><br />
        
        <input type="submit" value="test">

blablabla