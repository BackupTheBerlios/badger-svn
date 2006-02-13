<form action="" method="post" enctype="multipart/form-data" name="mainform" id="Selection">
 	<div id="scroll">	
		<table border = "1" cellpadding="2" cellspacing="0">
			<tr>
			<th>$tableHeadSelect $HeadSelectToolTip</th>
			<th>$tableHeadCategory<br />$HeadCategoryToolTip</th>
			<th>$tableHeadValutaDate $HeadValueDateToolTip</th>
	   		<th>$tableHeadTitle<br />$HeadTitleToolTip</th>
	   		<th>$tableHeadAmount<br />$HeadAmountToolTip</th>
	   		<th>$tableHeadTransactionPartner $HeadTransactionPartnerToolTip</th>
	   		<th>$tableHeadDescription $HeadDescriptionToolTip</th>
	   		<th>$tableHeadPeriodical $HeadPeriodicalToolTip</th>
	   		<th>$tableHeadExceptional $HeadExceptionalToolTip</th>
	   		<th>$tableHeadOutside $HeadOutsideToolTip</th>
	   		<th>$tableHeadAccount<br />$HeadAccountToolTip</th>
	   		</tr>
	   		$tplOutput
   		</table>
	</div>
	$hiddenField 
	$buttonSubmit 
</form>