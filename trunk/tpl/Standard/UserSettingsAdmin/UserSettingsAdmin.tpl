<h1>$pageHeading</h1>
<form name="UserSettings" method="post" action="{BADGER_ROOT}/core/UserSettingsAdmin/UserSettingsAdmin.php">
	<fieldset style="width: 28em; height: 17em">
		<legend>$FsHeading</ledgend>
		<br>
			<table>
			<tr>
				<td>$TemplateLabel</td>
				<td style="text-align: right;">$TemplateField</td>
			</tr>
			<tr>
				<td>$LanguageLabel</td>
				<td style="text-align: right;">$LanguageField</td>
			<tr>
				<td>$DateFormatLabel</td>
				<td style="text-align: right;">$DateFormatField</td>
			</tr>
			<tr>
				<td>$SeperatorsLabel</td>
				<td style="text-align: right;">$SeperatorsField</td>
			</tr>
			<tr>
				<td>$MaxLoginLabel</td>
				<td style="text-align: right;">$MaxLoginField</td>
			</tr>
			<tr>
				<td>$LockOutTimeLabel</td>
				<td style="text-align: right;">$LockOutTimeField</td>
			</tr>
			</tr>
			<tr>
				<td>$StartPageLabel</td>
				<td style="text-align: right;">$StartPageField</td>
			</tr>
			</tr>
			<tr>
				<td>$SessionTimeLabel</td>
				<td style="text-align: right;">$SessionTimeField</td>
			</tr>
		</table>
	</fieldset>
		<br/>
	<fieldset style="width: 28em; height: 7em">
		<legend>$PWFormLabel</ledgend>
		<br/>
		<table>
			<tr>
				<td>$OldPwLabel</td>
				<td>$OldPwField</td>
			</tr>
			<tr>
				<td>$NewPwLabel</td>
				<td>$NewPwField</td>
			<tr>
				<td>$ConfPwLabel</td>
				<td>$ConfPwField</td>
			</tr>
		</table>
	</fieldset>
		<table style="clear: both;">
			<tr>
				<td></td>
				<td>$btnSubmit</td>
			</tr>
		</table>
</form>
$Feedback