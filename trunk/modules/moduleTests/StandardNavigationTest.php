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

require_once ('../../core/navi/StandardNavigation.class.php');

$navi1 = array (
	array (
		'type' => 'menu',
		'name' => 'Datei',
		'menu' => array (
			array (
				'type' => 'item',
				'name' => 'Neumachen macht viel Laune',
				'icon' => 'navi/account.png',
				'command' => 'http://www.heise.de/'
			),
			array (
				'type' => 'item',
				'name' => 'Der Niko ist der Obermeister!!!!!',
				'icon' => 'navi/account.png',
				'command' => 'http://www.slashdot.org/'
			),
			array (
				'type' => 'item',
				'name' => 'Speichern',
				'command' => 'http://www.spiegel.de/'
			),
			array (
				'type' => 'separator'
			),
			array (
				'type' => 'item',
				'name' => 'Beenden',
				'icon' => 'navi/account.png',
				'command' => 'javascript:alert("Feddisch is!")'
			)
		)
	),
	array (
		'type' => 'menu',
		'name' => 'Bearbeiten',
		'menu' => array (
			array (
				'type' => 'menu',
				'name' => 'Einfuegen als',
				'menu' => array (
					array (
						'type' => 'item',
						'name' => 'Text',
						'command' => 'javascript:alert("Text!")'
					),
					array (
						'type' => 'item',
						'name' => 'HTML',
						'command' => 'http://w3.org/'
					)
				)
			)
		)
	),
	array (
		'type' => 'menu',
		'name' => 'Konto',
		'tooltip' => 'Alle das Konto betreffende Optionen',
		'icon' => 'navi/account.png',
		'menu' => array (
			array (
				'type' => 'item',
				'name' => 'Abbuchen',
				'tooltip' => 'Beträge auf ein anderes Konto überweisen',
				/* icon haben wir hier keins */
				'command' => 'modules/account/transfer.php'
			)
		)
	),
	array (
		'type' => 'separator',
	),
	array (
		'type' => 'item',
		'name' => 'Ausloggen',
		'tooltip' => 'Loggt disch aus',
		'icon' => 'navi/logout.png',
		'command' => 'core/session.php?logout'
	)
);

$naviObj = new StandardNavigation();

$naviObj->setStructure($navi1);
?>
<html>
<head>
	<?php echo $naviObj->getHeader();?>
	<link rel="stylesheet" type="text/css" href="../../js/jsDOMenuBar/themes/office_xp/office_xp.css" />
	<script type="text/javascript" src="../../js/jsDOMenuBar/jsdomenu.js"></script>
	<script type="text/javascript" src="../../js/jsDOMenuBar/jsdomenubar.js"></script></head>

<body>
	<?php echo $naviObj->getHTML();?>
	<p>Erste Zeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Zwischenzeile</p>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<p>Letzte Zeile</p>
</body>
</html>
<?php
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>