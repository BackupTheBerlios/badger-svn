<?php

define ('BADGER_ROOT', '../..');

function testLink($text, $link) {
	$link = BADGER_ROOT . '/core/XML/getDataGridXML.php?' . $link;

	echo "<dt>$text</dt><dd><a href='$link'>$link</a></dd>\n";
}
?>
<html>
<head>
	<title>getDataGridXML Test</title>
</head>

<body>
	<dl>
		<?php
			testLink('Alle Accounts', 'q=AccountManager');
			testLink('Alle Accounts, geordnet nach Kontostand aufsteigend', 'q=AccountManager&amp;ok0=balance&amp;od0=a');
			testLink('Alle Accounts, geordnet nach Titel absteigend', 'q=AccountManager&amp;ok0=title&amp;od0=d');
			testLink('Alle Accounts, die ein X enthalten', 'q=AccountManager&amp;fk0=title&amp;fo0=ct&amp;fv0=x');
			testLink('Alle Accounts, die mit X aufh&ouml;ren', 'q=AccountManager&amp;fk0=title&amp;fo0=ew&amp;fv0=x');
			testLink('Alle Accounts, die ein X enthalten und positiven Kontostand aufweisen', 'q=AccountManager&amp;fk0=title&amp;fo0=ct&amp;fv0=x&amp;fk1=balance&amp;fo1=gt&amp;fv1=0');
			testLink('Alle Accounts, die ein X enthalten und negativen Kontostand aufweisen, nach Kontostand absteigend sortiert', 'q=AccountManager&amp;fk0=title&amp;fo0=ct&amp;fv0=x&amp;fk1=balance&amp;fo1=lt&amp;fv1=0&amp;ok0=balance&amp;od0=d');
		?>
	</dl>
</body>
</html>