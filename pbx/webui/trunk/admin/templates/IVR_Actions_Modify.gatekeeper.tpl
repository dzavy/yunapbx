<script type="text/javascript" src="../script/jquery.autocomplete.js"></script>
<script type="text/javascript">
{literal}
$(document).ready(function() {
	$("#Name").autocompleteArray(
		{/literal}
			[{foreach from=$Variables key=Key item=Variable}
				{if $Key!=0},{/if}"{$Variable}"
			{/foreach}]
		{literal}
	,
	{
		matchContains:true,
		autoFill: true,
		cacheLength: 1000
	}
	);
})
{/literal}
</script>



<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h2>New Action: Gate Keeper</h2>
{else}
	<h2>Modify Action: Gate Keeper</h2>
{/if}

<form method="post" action="IVR_Actions_Modify.gatekeeper.php" />
<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

<table class="formtable">
	<tr>
		<td>Gate Variable Name</td>
		<td>
			<input type="text" id="Name" name="Param[Name]" value="{$Action.Param.Name}" />
		</td>
	</tr>
</table>
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>