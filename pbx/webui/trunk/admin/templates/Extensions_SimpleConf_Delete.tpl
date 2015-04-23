<h2>Manage Extensions</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete the simple conf access extension "{$Extension.Extension}" ?
</p>
<br />

<p style="margin-left: 10px;" class="warning_message">Without at least one voicemail extension users will not be able to check their voicemail.</p>

<form method="post" action="Extensions_SimpleConf_Delete.php">
<p>
		<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Extension</button>
</p>
</form>