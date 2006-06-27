function showBadgerHelp() {
	chapter = "";
	try {
		chapter = badgerHelpChapter;
	} catch(ex) {}

	window.open(badgerHelpRoot + "/help_" + badgerHelpLang + ".html#" + chapter, "badgerHelp");
}