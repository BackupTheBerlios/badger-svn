<h1>$pageHeading</h1>
<form method="post" name="mainform" action="$FormAction?action=save" onSubmit="return validateCompleteForm(this, 'error');">
<fieldset style = "width: 20em;">
	<legend>$legend</legend>	
	<table>
	  <tr>
	    <td>$titleLabel </td>
	    <td>$titleField </td>
	  </tr>
	  <tr>
	    <td>$descriptionLabel </td>
	    <td>$descriptionField </td>
	  </tr>
	  <tr>
	    <td>$outsideCapitalLabel </td>
	    <td>$outsideCapitalField  </td>
	  </tr>
	  <tr>
	    <td>$parentLabel </td>
	    <td>$parentField  </td>
	  </tr>
	  <tr>
	    <td>$backBtn </td>
	    <td>$submitBtn </td>
	  </tr>
	</table>
</fieldset>
$hiddenID
</form>
