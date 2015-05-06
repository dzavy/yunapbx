<h2>Sound Manager</h2>

<form action="SoundLanguages_Modify.php" method="post">
<p>
	<input type="hidden" name="PK_SoundLanguage" value="{$SoundLanguage.PK_SoundLanguage}" />
</p>

<table class="formtable">
	<tr>
		<td>Language Name</td>
		<td>
			<input type="text" name="Name" value="{$SoundLanguage.Name}" {if $Errors.Name }class="error"{/if} />
		</td>
	</tr>
</table>


<p>
	<br />
	{if $SoundLanguage.PK_SoundLanguage == ""}
	<button type="submit" name="submit" value="save">Create Sound Language</button>
	{else}
	<button type="submit" name="submit" value="save">Modify Sound Language</button>
	{/if}
</p>
</form>