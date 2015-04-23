<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Hang Up</h2>
{else}
	<h2>Modify Action: Hang Up</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.hang_up.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<p>
	This action does not have any options to modify. To continue, click the button below.
</p>
<br />

<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>