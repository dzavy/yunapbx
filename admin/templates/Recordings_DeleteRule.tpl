<h2>Call Recording</h2>

<p>
	<strong>Are you sure you want to permanently delete the following Call Recording Rule:</strong>
</p>
<br />

{$RecordingRule.Label}

<br />
<p>
	<form method="post" action="Recordings_DeleteRule.php">
		<input type="hidden" name="PK_Rule" value="{$RecordingRule.PK_Rule}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Recording Rule</button>
	</form>
</p>