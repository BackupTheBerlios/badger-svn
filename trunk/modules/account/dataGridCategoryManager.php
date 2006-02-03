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

$widgets = new WidgetEngine($tpl); 
$tpl->addCSS("Widgets/ajax_tables.css");
$tpl->addJavaScript("js/MochiKit/MochiKit.js");
$tpl->addJavaScript("js/ajax_tables.js");
echo $tpl->getHeader("Seitenname"); //write header */

$dataGrid['loadXML'] = BADGER_ROOT."/core/XML/getDataGridXML.php?q=CategoryManager";
$dataGrid['actionNew'] = "newAccount.php";
$dataGrid['actionDelete'] = "deleteAccount.php?id=";
?>
<script>
        sortableManager.sortkey = "categoryId";
        sortableManager.loadFromURL("xml", "<?php echo $dataGrid['loadXML'] ?>");
        sortableManager.initialize();
</script>

        <div>
        	<?php 
        	echo $widgets->createButton("btnNew", "Neu", "this.location.href=", "Widgets/table_add.gif");
        	?>
        </div>
        <table id="sortable_table" class="datagrid">
            <thead>
                <tr> 
                    <th mochi:sortcolumn="categoryId str">ID</th>
                    <th mochi:sortcolumn="title str">title</th>
                    <th mochi:sortcolumn="description str">description</th>
                    <th mochi:sortcolumn="outsideCapital str">outsideCapital</th>
                </tr>
            </thead>           
            <tfoot class="invisible">
                <tr>
                    <td colspan="0"></td>
                </tr>
            </tfoot>
            <tbody class="mochi-template">
                <tr mochi:repeat="item categories">
                    <td mochi:content="item.categoryId"></td>
                    <td mochi:content="item.title"></td>
                    <td mochi:content="item.description"></td>
                    <td mochi:content="item.outsideCapital"></td>
                </tr>
            </tbody>
        </table>
    </body>
</html>
