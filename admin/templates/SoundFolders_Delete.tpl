<h2>Sound Manager</h2>

<p>
	<strong>Are you sure you want to permanently delete this sound folder ({$SoundFolder.Name})?</strong>
</p>
<br />
<p style="margin-left: 10px;" class="warning_message">Deleting this folder will also permanently delete all of the sounds in it.</p>
<br />
<p>
	<strong>Sounds that will be removed:</strong>
	<table style="margin-left: 10px; ">
		{foreach from=$Group.Extensions item=Extension}
			<tr>
				<td>{$Extension.Extension}</td>
				<td>"{$Extension.FirstName} {$Extension.LastName}"</td>
			</tr>
		{/foreach}
	</table>
</p>
<br />
<p>
	<form method="post" action="SoundFolders_Delete.php">
		<input type="hidden" name="PK_SoundFolder" value="{$SoundFolder.PK_SoundFolder}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Folder</button>
	</form>
</p>