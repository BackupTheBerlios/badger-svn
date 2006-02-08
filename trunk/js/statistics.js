function submitSelect() {
	accountsField = document.getElementById('accounts');
	
	accountIds = dgGetAllIds();
	first = true;

	for (i = 0; i < accountIds.length; i++) {
		if (accountIds[i]) {
			if (!first) {
				accountField.value += ';';
			} else {
				first = false;
			}
			accountField.valu += accountIds[i];
		}
	}
	
	form = document.getElementById('selectForm');
	
	alert(form);

	form.submit();
}