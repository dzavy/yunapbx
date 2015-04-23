<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Email Recorded Sound</h2>
{else}
	<h2>Modify Action: Email Recorded Sound</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.email_rec_sound.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<table class="formtable">
	<tr>
		<td>Recorded Sound</td>
		<td>
			<select id="Var_Sound" name="Var[Sound]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Sound==$Variable}selected="selected"{/if}>{$Variable}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>Email Address</td>
		<td>
			<input type="text" name="Param[Email]" value="{$Action.Param.Email}" />
		</td>
	</tr>
</table>

<br />
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>