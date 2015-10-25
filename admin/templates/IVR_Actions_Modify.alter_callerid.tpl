<script type="text/javascript">
{literal}
function Update_UseVariable_Status() {
	if ($("#Param_Text").val() != "") {
		$("#UseVariable_0").attr('checked','checked');
		$("#UseVariable_0").removeAttr('disabled');
		$("#UseVariable_1").removeAttr('checked','checked');
		$("#UseVariable_1").attr('disabled','disabled');
		$("#Var_Text").attr('disabled','disable');
	} else {
		$("#UseVariable_1").attr('checked','checked');
		$("#UseVariable_1").removeAttr('disabled');
		$("#UseVariable_0").removeAttr('checked','checked');
		$("#UseVariable_0").attr('disabled','disabled');
		$("#Var_Text").removeAttr('disabled');
	}
}

$(document).ready(function() {
	Update_UseVariable_Status();
})
{/literal}
</script>

<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Alter Caller ID</h2>
{else}
	<h2>Modify Action: Alter Caller ID</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.alter_callerid.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<table class="formtable">
	<tr>
		<td colspan="2">
			Alter Caller ID
			<select name="Param[Section]">
				<option value="name"   {if $Action.Param.Section=='name'  }selected="selected"{/if} >name</option>
				<option value="number" {if $Action.Param.Section=='number'}selected="selected"{/if} >number</option>
			</select>
			by
			<select name="Param[Method]">
				<option value="prepend" {if $Action.Param.Method=='prepend'}selected="selected"{/if} >prepending</option>
				<option value="append"  {if $Action.Param.Method=='append' }selected="selected"{/if} >appending</option>
				<option value="replace" {if $Action.Param.Method=='replace'}selected="selected"{/if} >replacing</option>
			</select>
			the text with:
		</td>
	</tr>
	<tr>
		<td>
			<input type="radio" name="UseVariable" id="UseVariable_1" value="1" />
			From Variable
		</td>
		<td>
			<select id="Var_Number" name="Var[Text]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Text==$Variable}selected="selected"{/if}>{$Variable}</option>
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
			<input type="text" id="Param_Text" name="Param[Text]" value="{$Action.Param.Text}"  onkeyup="Update_UseVariable_Status()"/>
		</td>
	</tr>
</table>

<br />
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>