<h2>IVR Editor</h2>

{if $IVR_Actions || $IVR_Options || $IVR_Extensions}
<div style="background-color: #ddd; border: 1px solid #999; padding: 5px; font-size: 11px;">
	<strong>You cannot delete this IVR Menu yet because it is still in use somewhere in your system.</strong>
	<br /><br />
	<p>Please see the list below of where this IVR Menu is still in use and remove them. Once removed, you will be allowed to remove this IVR Menu.</p>
</div>

<br />
<table class="listing">
	<caption>Other actions pointing to this Menu</caption>
    {if $IVR_Actions}
        <tr>
			<th><a href="#">Action of Menu</a></th>
			<th><a href="#">Order</a></th>
			<th><a href="#">Action Type</a></th>
			<th><a href="#">GoTo Menu</a></th>
			<th>Modify Option</th>
		</tr>
		{foreach from=$IVR_Actions item=IVR_Action}
		<tr class="{cycle values='odd,even' name='actions'}">
			<td>{$IVR_Action.Parent}</td>
			<td>{$IVR_Action.Order}</td>
			<td>
				{if $IVR_Action.Type == 'conditional_clause'}
					Conditional Clause
				{elseif $IVR_Action.Type == 'goto_context'}
					Go To
				{else}
					{$IVR_Action.Type}
				{/if}
			</td>
			<td><strong>{$IVR_Action.Child}**</strong></td>
			<td>
				<form method="get" action="IVR_Actions_Modify.{$IVR_Action.Type}.php" style="display:inline">
					<input type="hidden" name="PK_Action" value="{$IVR_Action.PK_Action}" />
					<button type="submit">Modify</button>
				</form>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr>
			<td style="width: 300px">No Data Available </td>
		</tr>
	{/if}
</table>
{if $IVR_Actions}
<p>
	<small>** Signifies the current IVR Menu you are deleting</small>
</p>
{/if}

<br /><br />
<table class="listing">
	<caption>Options pointing to this Menu</caption>
    {if $IVR_Options}
		<tr>
			<th><a href="#">Option of Menu</a></th>
			<th><a href="#">Option #</a></th>
			<th><a href="#">GoTo Menu</a></th>
			<th>Modify Option</th>
		</tr>
		{foreach from=$IVR_Options item=IVR_Option}
		<tr class="{cycle values='odd,even' name='options'}">
			<td>{$IVR_Option.Parent}</td>
			<td>{$IVR_Option.Key}</td>
			<td><strong>{$IVR_Option.Child}**</strong></td>
			<td>
				<form method="get" action="IVR_Options_Modify.php" style="display:inline">
					<input type="hidden" name="PK_Option" value="{$IVR_Option.PK_Option}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="IVR_Options_Delete.php" style="display:inline">
					<input type="hidden" name="PK_Option" value="{$IVR_Option.PK_Option}" />
					<button type="submit" class="important">Delete</button>
				</form>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr>
			<td style="width: 300px">No Data Available </td>
		</tr>
	{/if}
</table>
{if $IVR_Options}
<p>
	<small>** Signifies the current IVR Menu you are deleting</small>
</p>
{/if}

<br /><br />
<table class="listing">
	<caption>IVR Extensions pointing to this Menu</caption>
    {if $IVR_Extensions}
		<tr class="{cycle values='odd,even' name='extensions'}">
			<th><a href="#">Extension</a></th>
			<th><a href="#">Menu</a></th>
			<th><a href="#">Date Created</a></th>
			<th>Modify / Delete</th>
		</tr>
		{foreach from=$IVR_Extensions item=IVR_Extension}
		<tr>
			<td>{$IVR_Extension.Extension}</td>
			<td><strong>{$IVR_Extension.Name}**</strong></td>
			<td>{$IVR_Extension.DateCreated}</td>
			<td>
				<form method="get" action="Extensions_IVR_Modify.php" style="display:inline">
					<input type="hidden" name="PK_Extension" value="{$IVR_Extension.PK_Extension}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="Extensions_IVR_Delete.php" style="display:inline">
					<input type="hidden" name="PK_Extension" value="{$IVR_Extension.PK_Extension}" />
					<button type="submit" class="important">Delete</button>
				</form>
			</td>
		</tr>
		{/foreach}
	{else}
		<tr>
			<td style="width: 300px">No Data Available </td>
		</tr>
	{/if}
</table>
{if $IVR_Extensions}
<p>
	<small>** Signifies the current IVR Menu you are deleting</small>
</p>
{/if}

{else}
<p>
	<strong>Are you sure you want to permanently delete the IVR Menu "{$IVR_Menu.Name}"?</strong>
</p>
<br />

<br />
<p>
	<form method="post" action="IVR_Menus_Delete.php">
		<input type="hidden" name="PK_Menu" value="{$IVR_Menu.PK_Menu}" />
		<button type="submit" name="submit" value="delete_confirm" class="important">Yes, Delete IVR Menu</button>
	</form>
</p>
{/if}