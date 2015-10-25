<script type="text/javascript">
{literal}
function UpdateActions() {
	FK_Menu = $("#FK_Menu").val();
	$("select[id^='Menu_']").css('display','none');
	$("#Menu_"+FK_Menu+"_Actions").css('display','inline');

	FK_Action = $("#Menu_"+FK_Menu+"_Actions").val();
	$("#FK_Action").val(FK_Action);
}

$(document).ready(function() {
	FK_Menu = 0;
	FK_Action = 0;
	{/literal}

	{if $Extension.PK_Extension != ""}
	FK_Menu   = "{$Extension.FK_Menu}";
	FK_Action = "{$Extension.FK_Action}";
	{/if}
	{literal}

	$("#FK_Menu").val(FK_Menu);
	$("#Menu_"+FK_Menu+"_Actions").val(FK_Action);

	UpdateActions();
})
{/literal}
</script>

<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 4 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}
<form action="Extensions_IVR_Modify.php" method="post">
<p>
	<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
	<input type="hidden" name="FK_Action" value="{$Extension.FK_Action}" />
</p>
<table class="formtable">
	<!-- Extension -->
	<tr>
		<td>
			Extension
		</td>
		<td>
			{if $Extension.PK_Extension != "" }
			{$Extension.Extension}
			<input type="hidden" name="Extension" value="{$Extension.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Extension.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<tr>
		<td>
			IVR Menu Name <br />
			<small>Which IVR should this extension play?</small>
		</td>
		<td>
			<input type="hidden" name="FK_Action" id="FK_Action" value="{$Extension.FK_Action}" />
			<select name="FK_Menu" id="FK_Menu" onchange="UpdateActions()">
				{foreach from=$Menus item=Menu}
				<option value="{$Menu.PK_Menu}">{$Menu.Name}</option>
				{/foreach}
			</select>
		</td>
	</tr>

	<tr>
		<td>
			IVR Menu Entry Point <br />
			<small>Which action should it start playing?</small>
		</td>
		<td>
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

	<tr>
		<td colspan="2">
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Extension.IVRDial=='1'}checked="checked"{/if} />
			<label for="IVRDial">This extension can be dialed from an IVR.</label>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		{if $Extension.PK_Extension != ""}
			<button type="submit" name="submit" value="save">Modify Extension</button>
		{else}
			<button type="submit" name="submit" value="save">Create Extension</button>
		{/if}
		</td>
	</tr>
</table>
</form>