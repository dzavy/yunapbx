<h2>Sound Manager</h2>

<form action="SoundFolders_Modify.php" method="post">
<p>
	<input type="hidden" name="PK_SoundFolder" value="{$SoundFolder.PK_SoundFolder}" />
</p>

<table class="formtable">
	<!-- Folder Name -->
	<tr>
		<td>Folder Name</td>
		<td>
			<input type="text" name="Name" value="{$SoundFolder.Name}" {if $Errors.Name }class="error"{/if} />
		</td>
	</tr>
	
	<!-- Folder Description -->
	<tr>
		<td>Folder Description</td>
		<td>
			<textarea name="Description" {if $Errors.Description }class="error"{/if}>{$SoundFolder.Description}</textarea>
		</td>
	</tr>
</table>


<p>
	<br />
	{if $SoundFolder.PK_SoundFolder == ""}
	<button type="submit" name="submit" value="save">Create Sound Folder</button>
	{else}
	<button type="submit" name="submit" value="save">Modify Sound Folder</button>
	{/if}
</p>
</form>