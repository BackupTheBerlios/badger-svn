function submitSelect() {
	accountsField = document.getElementById('accounts');
	
	accountsField.value = '';
	
	accountIds = dgGetAllIds();
	first = true;

	for (i = 0; i < accountIds.length; i++) {
		if (accountIds[i]) {
			if (!first) {
				accountsField.value += ';';
			} else {
				first = false;
			}
			accountsField.value += accountIds[i];
		}
	}
	
	form = document.getElementById('selectForm');
	
	form.submit();
}