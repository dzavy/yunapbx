<h2>Manage Extensions</h2>
<br />
<p style="font-weight: bold;">
	Are you sure you want to permanently delete this user's extension ({$Extension.Extension})?
</p>
<br />

<p style="margin-left: 10px;" class="warning_message">Deleting this extension will delete all voicemail in this extension's voice mailbox.</p>

<p>
	To move a user from one extension to another, before deleting this extension, make the user's new extension and then forward all of their old voicemail from this extension to the new extension.
</p>
<br />
<p>
	To archive the voicemail files, click the "Download Voicemail Archive" button before deleting this extension.
</p>
<br />

<form method="post" action="Extensions_SipPhone_Delete.php">
<p>
		<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Extension</button>
		&nbsp;&nbsp;&nbsp;<button type="button">Download Voicemail Archive</button>
</p>
</form>