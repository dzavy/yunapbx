<script type="text/javascript">
{literal}
function Update_UseVariable_Status() {
	if ($("#Param_Digits").val() != "") {
		$("#UseVariable_0").attr('checked','checked');
		$("#UseVariable_0").removeAttr('disabled');
		$("#UseVariable_1").removeAttr('checked','checked');
		$("#UseVariable_1").attr('disabled','disabled');
		$("#Var_Digits").attr('disabled','disable');
	} else {
		$("#UseVariable_1").attr('checked','checked');
		$("#UseVariable_1").removeAttr('disabled');
		$("#UseVariable_0").removeAttr('checked','checked');
		$("#UseVariable_0").attr('disabled','disabled');
		$("#Var_Digits").removeAttr('disabled');
	}
}

$(document).ready(function() {
	Update_UseVariable_Status();
})
{/literal}
</script>

<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Say Digits/Letters</h2>
{else}
	<h2>Modify Action: Say Digits/Letters</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.say_digits.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<table class="formtable">
	<tr>
		<td>
			<input type="radio" name="UseVariable" id="UseVariable_1" value="1" />
			From Variable
		</td>
		<td>
			<select id="Var_Digits" name="Var[Digits]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Digits==$Variable}selected="selected"{/if}>{$Variable}</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td>
			<input type="radio" name="UseVariable" id="UseVariable_0" value="0" />
			Manualy Entered
		</td>
		<td>
			<input type="text" id="Param_Digits" name="Param[Digits]" value="{$Action.Param.Digits}"  onkeyup="Update_UseVariable_Status()"/>
		</td>
	</tr>
</table>

<br />
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>