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
define("BADGER_ROOT", "..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addAutoCompleteJS();
echo $tpl->getHeader("Seitenname"); //write header */
?>

<table width="129%" border="0">
  <tr> 
    <td width="61%"><img src="/trunk/img/badger_comic.gif" width="595" height="502"></td>
    <td width="39%"><table width="101%" height="467" border="0">
        <tr> 
          <td width="21%"><a href="#">Modul 1</a></td>
        </tr>
        <tr> 
          <td>&nbsp; &nbsp; &nbsp; <a href="#">Modul 2</a></td>
        </tr>
        <tr> 
          <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href="#">Modul 3</a></td>
        </tr>
        <tr> 
          <td>&nbsp; &nbsp; &nbsp; <a href="#">Modul 4</a></td>
        </tr>
        <tr> 
          <td><a href="#">Modul 5</a></td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
