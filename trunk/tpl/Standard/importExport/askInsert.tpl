<!-- Vor Holgers Änderung
<h1>$askInsertTitle</h1>
<form action="$askInsertAction" enctype="multipart/form-data" method="POST">
	<p style="color:red; background-color: white; padding: 0.3em; border: 2px solid red; text-align: center;">$askImportWarning</p>
	<p>
		$askImportNoOption $askImportNoOptionLabel<br />
		$askImportYesOption $askImportYesOptionLabel
	</p>
	<p>$askImportFileUploadLabel $askImportFileUpload</p>
	<p>$askImportSubmit</p>
</form>
-->
<h1>$askInsertTitle</h1>
<form name="agreeform" onSubmit="return defaultagree(this)" action="$askInsertAction" enctype="multipart/form-data" method="POST">
	<p style="color:red; background-color: white; padding: 0.3em; border: 2px solid red; text-align: center;">$askImportWarning</p>
	<p>
	<p>$askImportFileUploadLabel $askImportFileUpload</p>
<input name="confirmUpload"  id='confirmUpload' type="checkbox" onClick="agreesubmit(this)" value="yes"><b>$askImportYesOptionLabel</b><br>
<input type="Submit" value="Submit!" disabled>
</form>

<script>
//change two names below to your form's names
document.forms.agreeform.confirmUpload.checked=false
</script>