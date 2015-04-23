<h2>Sound Manager</h2>

<p>
	<strong>Are you sure you want to permanently delete the following sounds:</strong>
</p>
<br />

<ul>
{foreach from=$SoundEntries item=Entry}
	<li>{$Entry.Name}</li>
{/foreach}
</ul>

<br />
<p>
	<form method="post" action="SoundEntries_Delete.php">
		<input type="hidden" name="PK_SoundEntries" value="{$PK_SoundEntries}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete Sounds</button>
	</form>
</p>