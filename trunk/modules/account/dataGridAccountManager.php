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

define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$dataGrid = array();
$dataGrid['loadXML'] = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid['actionNew'] = "newAccount.php";
$dataGrid['actionDelete'] = "deleteAccount.php?id=";

$widgets = new WidgetEngine($tpl); 
$tpl->addCSS("Widgets/ajax_tables.css");
$tpl->addJavaScript("js/MochiKit/MochiKit.js");
$tpl->addJavaScript("js/ajax_tables.js");
$tpl->addOnLoadEvent('sortableManager.sortkey = "accountId";
        			  sortableManager.loadFromURL("xml", "'.$dataGrid['loadXML'].'");
        			  sortableManager.initialize();');
$widgets->addNavigationHead();
echo $tpl->getHeader("Seitenname");
echo $widgets->getNavigationBody();
?>
        <div>
        	<?php 
        	echo $widgets->createButton("btnNew", "Neu", "this.location.href=", "Widgets/table_add.gif");
        	?>
        </div>
        <table id="sortable_table" class="datagrid">
            <thead>
                <tr>
                    <th mochi:sortcolumn="accountId str">ID</th>
                    <th mochi:sortcolumn="currency str">Currency</th>
                    <th mochi:sortcolumn="title str">Title</th>
                    <th mochi:sortcolumn="balance">Balance</th>
                </tr>
            </thead>           
            <tfoot class="invisible">
                <tr>
                    <td colspan="0"></td>
                </tr>
            </tfoot>
            <tbody class="mochi-template">
                <tr mochi:repeat="item domains">
                    <td mochi:content="item.accountId"></td>
                    <td mochi:content="item.currency"></td>
                    <td mochi:content="item.title"></td>
                    <td mochi:content="item.balance"></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
