<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Play Recorded Sound</h2>
{else}
	<h2>Modify Action: Play Recorded Sound</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.playback_rec_sound.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<table class="formtable">
	<tr>
		<td>
			Recorded Sound to Playback
		</td>
		<td>
			<select id="Var_Sound" name="Var[Sound]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Sound==$Variable}selected="selected"{/if}>{$Variable}</option>
				{/foreach}
			</select>
		</td>
	</tr>
</table>

<br />
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>