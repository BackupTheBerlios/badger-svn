<h1>$askInsertTitle</h1>
<form name="agreeform" onSubmit="return defaultagree(this)" action="$askInsertAction" enctype="multipart/form-data" method="POST">
	<p style="color:red; background-color: white; padding: 0.3em; border: 2px solid red; text-align: center;">$askImportWarning</p>
	<p>$askImportFileUploadLabel $askImportFileUpload</p>
	<p>
		<input name="confirmUpload"  id='confirmUpload' type="checkbox" onClick="agreesubmit(this)" value="yes">
		<b>$askImportYesOptionLabel</b>
	</p>
	<p>$askImportSubmit</p>
</form>

<script>
//change two names below to your form's names
document.forms.agreeform.confirmUpload.checked=false
</script>