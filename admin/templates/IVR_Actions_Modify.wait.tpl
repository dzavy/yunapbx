<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Wait</h2>
{else}
	<h2>Modify Action: Wait</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.wait.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<p>
	Wait time in seconds:
	<input type="text" name="Param[Time]" value="{$Action.Param.Time}" />
</p>
<br />

<p>
	&nbsp;<input type="checkbox" id="Intrerruptible" name="Param[Intrerruptible]" {if $Action.Param.Intrerruptible}checked="checked"{/if} value="1"/>
	<label for="Intrerruptible">This wait is interruptible</label>
</p>
<br />

<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>