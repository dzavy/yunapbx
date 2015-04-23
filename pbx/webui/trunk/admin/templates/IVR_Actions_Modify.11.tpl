<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Play Sound</h2>
{else}
	<h2>Modify Action: Play Sound</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.play_sound.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>