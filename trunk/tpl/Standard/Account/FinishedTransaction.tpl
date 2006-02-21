<h1>$pageHeading</h1>
<form method="post" name="mainform" action="$FormAction?action=save&accountID=$AccountID" onSubmit="return validateCompleteForm(this, 'error');">
<table>
  <tr>
    <td>$AccountLabel</td>
    <td>$hiddenAccID </td>
  </tr>
  <tr>
    <td>$titleLabel </td>
    <td>$titleField </td>
  </tr>
  <tr>
    <td>$descriptionLabel </td>
    <td>$descriptionField </td>
  </tr>
  <tr>
    <td>$valutaDateLabel </td>
    <td>$valutaDateField </td>
  </tr>
  <tr>
    <td>$amountLabel </td>
    <td>$amountField  </td>
  </tr>
  <tr>
    <td>$outsideCapitalLabel </td>
    <td>$outsideCapitalField  </td>
  </tr>
  <tr>
    <td>$transactionPartnerLabel </td>
    <td>$transactionPartnerField  </td>
  </tr>
  <tr>
    <td>$categoryLabel </td>
    <td>$categoryField  </td>
  </tr>
  <tr>
    <td>$exceptionalLabel </td>
    <td>$exceptionalField $exceptionalToolTip </td>
  </tr>
  <tr>
    <td>$periodicalLabel </td>
    <td>$periodicalField  $periodicalToolTip</td>
  </tr>
  <tr>
    <td>$backBtn </td>
    <td>$submitBtn </td>
  </tr>
</table>
$hiddenID
$hiddenType
</form>