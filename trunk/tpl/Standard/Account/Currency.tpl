<h1>$pageHeading</h1>
<form method="post" name="mainform" action="$FormAction?action=save" onSubmit="return validateCompleteForm(this, 'error');">
<fieldset style = "width: 20em;">
	<legend>$legend</legend>
	<table>
	  <tr>
	    <td>$symbolLabel </td>
	    <td>$symbolField </td>
	  </tr>
	  <tr>
	    <td>$longnameLabel </td>
	    <td>$longnameField </td>
	  </tr>
	  <tr>
	    <td>$backBtn </td>
	    <td>$submitBtn </td>
	  </tr>
	</table>
</fieldset>
$hiddenID
</form>