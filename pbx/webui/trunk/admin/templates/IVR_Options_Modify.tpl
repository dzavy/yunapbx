<script type="text/javascript">
{literal}
function UpdateActions() {
	FK_Menu_Entry = $("#FK_Menu_Entry").val();
	$("select[id^='Menu_']").css('display','none');
	$("#Menu_"+FK_Menu_Entry+"_Actions").css('display','inline');

	FK_Action_Entry = $("#Menu_"+FK_Menu_Entry+"_Actions").val();
	$("#FK_Action_Entry").val(FK_Action_Entry);
}

$(document).ready(function() {
	FK_Menu_Entry = 0;
	FK_Action_Entry = 0;
	{/literal}

	{if $Option.PK_Option != ""}
	FK_Menu_Entry   = "{$Option.FK_Menu_Entry}";
	FK_Action_Entry = "{$Option.FK_Action_Entry}";
	{/if}
	{literal}

	$("#FK_Menu_Entry").val(FK_Menu_Entry);
	$("#Menu_"+FK_Menu_Entry+"_Actions").val(FK_Action_Entry);

	UpdateActions();
})
{/literal}
</script>

<h2>IVR Editor</h2>

<form action="IVR_Options_Modify.php" method="post">
<input type="hidden" name="PK_Option" value="{$Option.PK_Option}" />
<input type="hidden" name="FK_Menu"   value="{$Option.FK_Menu}" />
<table class="formtable">
	<tr>
		<td>Option Number</td>
		<td>
			<select name="Key">
				<option value="1" {if $Option.Key == '1'}selected="selected"{/if} {if '1'|in_array:$UsedKeys}disabled="disabled"{/if} >1</option>
				<option value="2" {if $Option.Key == '2'}selected="selected"{/if} {if '2'|in_array:$UsedKeys}disabled="disabled"{/if} >2</option>
				<option value="3" {if $Option.Key == '3'}selected="selected"{/if} {if '3'|in_array:$UsedKeys}disabled="disabled"{/if} >3</option>
				<option value="4" {if $Option.Key == '4'}selected="selected"{/if} {if '4'|in_array:$UsedKeys}disabled="disabled"{/if} >4</option>
				<option value="5" {if $Option.Key == '5'}selected="selected"{/if} {if '5'|in_array:$UsedKeys}disabled="disabled"{/if} >5</option>
				<option value="6" {if $Option.Key == '6'}selected="selected"{/if} {if '6'|in_array:$UsedKeys}disabled="disabled"{/if} >6</option>
				<option value="7" {if $Option.Key == '7'}selected="selected"{/if} {if '7'|in_array:$UsedKeys}disabled="disabled"{/if} >7</option>
				<option value="8" {if $Option.Key == '8'}selected="selected"{/if} {if '8'|in_array:$UsedKeys}disabled="disabled"{/if} >8</option>
				<option value="9" {if $Option.Key == '9'}selected="selected"{/if} {if '9'|in_array:$UsedKeys}disabled="disabled"{/if} >9</option>
				<option value="0" {if $Option.Key == '0'}selected="selected"{/if} {if '0'|in_array:$UsedKeys}disabled="disabled"{/if} >0</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>
			Go To
		</td>
		<td>
			<input type="hidden" name="FK_Action_Entry" id="FK_Action_Entry" value="{$Options.FK_Action_Entry}" />
			<select name="FK_Menu_Entry" id="FK_Menu_Entry" onchange="UpdateActions()">
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
					{$IVR_Action.Order}.{include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Name'} ({include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Desc'})
				</option>
				{/foreach}
			</select>
			{/foreach}
		</td>
	</tr>
	<tr>
		<td colspan="2">
		{if $Option.PK_Option != ""}
			<button type="submit" name="submit" value="save">Modify Option</button>
		{else}
			<button type="submit" name="submit" value="save">Create New Option</button>
		{/if}
		</td>
	</tr>
</table>
</form>