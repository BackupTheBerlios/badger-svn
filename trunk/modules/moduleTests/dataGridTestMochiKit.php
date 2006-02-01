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

$tpl->addCSS("Widgets/ajax_tables.css");
$tpl->addJavaScript("js/MochiKit/MochiKit.js");
$tpl->addJavaScript("js/ajax_tables.js");
echo $tpl->getHeader("Seitenname"); //write header */

?>
        <div>
            Load data: [
            <a href="../../core/XML/getDataGridXML.php?q=AccountManager" mochi:dataformat="xml">AccountManager</a>
            ]
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
