<h1>$askInsertTitle</h1>
<form name="agreeform" onSubmit="return defaultagree(this)" action="$askInsertAction" enctype="multipart/form-data" method="POST">
<fieldset>
	<legend>$legend</legend>
	<p style="color:red; background-color: white; padding: 0.3em; border: 2px solid red; text-align: center;">$askImportWarning</p>
	<p>$askImportFileUploadLabel $askImportFileUpload</p>
	<p>$askImportVersionInfo<br />$askImportCurrentVersionInfo $versionInfo</p>
	<p>
		$confirmUploadField
		<b>$askImportYesOptionLabel</b>
	</p>
	<p>$askImportSubmit</p>
</form>

<script>
//change two names below to your form's names
document.forms.agreeform.confirmUpload.checked=false
</script>

</fieldset>