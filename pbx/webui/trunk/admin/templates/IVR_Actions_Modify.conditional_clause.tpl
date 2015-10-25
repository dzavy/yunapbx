<script type="text/javascript">
{literal}
function UpdateActions() {
	PK_Menu = $("#Param_Menu").val();
	$("select[id^='Menu_']").css('display','none');
	$("#Menu_"+PK_Menu+"_Actions").css('display','inline');

	PK_Action = $("#Menu_"+PK_Menu+"_Actions").val();
	$("#Param_Action").val(PK_Action);
}

$(document).ready(function() {
	PK_Menu = 0;
	PK_Action = 0;
	{/literal}
	{foreach from=$Menus item=Menu}
	{foreach from=$Menu.Actions item=IVR_Action}
	{if $IVR_Action.PK_Action == $Action.Param.Action}

	PK_Menu   = {$Menu.PK_Menu};
	PK_Action = {$IVR_Action.PK_Action};
	{/if}
	{/foreach}
	{/foreach}
	{literal}

	$("#Param_Menu").val(PK_Menu);
	$("#Menu_"+PK_Menu+"_Actions").val(PK_Action);
	UpdateActions();
})
{/literal}
</script>

<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Conditional Clause</h2>
{else}
	<h2>Modify Action: Conditional Clause</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.conditional_clause.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />


<table class="formtable">
	<tr>
		<td>
			When
		</td>
		<td>
			<select id="Var_Value1" name="Var[Value1]">
				{foreach from=$Variables item=Variable}
				<option value="{$Variable}" {if $Action.Var.Value1==$Variable}selected="selected"{/if}>{$Variable}</option>
				{/foreach}
			</select>

			<select name="Param[Operator]">
				<option value="eq"  {if $Action.Param.Operator=='eq'}selected="selected"{/if}>equals</option>
				<option value="neq" {if $Action.Param.Operator=='neq'}selected="selected"{/if}>does not equal</option>
				<option value="gt"  {if $Action.Param.Operator=='gt'}selected="selected"{/if}>is greater than</option>
				<option value="lt"  {if $Action.Param.Operator=='lt'}selected="selected"{/if}>is less than</option>
			</select>

			<input type="text" name="Param[Value2]" value="{$Action.Param.Value2}" />
		</td>
	</tr>
	<tr>
		<td>
			Go To
		</td>
		<td>
			<input type="hidden" name="Param[Action]" id="Param_Action" value="{$Action.Param.Action}" />
			<select name="Param[Menu]" id="Param_Menu" onchange="UpdateActions()">
				{foreach from=$Menus item=Menu}
				<option value="{$Menu.PK_Menu}">{$Menu.Name}</option>
				{/foreach}
			</select>

			at

			{foreach from=$Menus item=Menu}
			<select name="Menu_{$Menu.PK_Menu}_Actions" id="Menu_{$Menu.PK_Menu}_Actions" style="display:none" onchange="UpdateActions()">
				<option value="0">IVR Menu Begining</option>
				{foreach from=$Menu.Actions item=IVR_Action}
				<option value="{$IVR_Action.PK_Action}">
					{$IVR_Action.Order}.{include file="IVR_Actions_Display.tpl" Action=$IVR_Action Display="Name"} ({include file="IVR_Actions_Display.tpl" Action=$IVR_Action Display="Desc"})
				</option>
				{/foreach}
			</select>
			{/foreach}
		</td>
	</tr>
</table>

<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>