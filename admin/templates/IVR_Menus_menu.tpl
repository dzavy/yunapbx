<br/>
<p>
	<strong>IVR Menu Name:</strong> {$IVR_Tree.Name}
</p>

<br />
<p>
	<strong>Description:</strong> {$IVR_Tree.Description}
</p>

<br />
<p>
	<button type="button" onclick="document.location='IVR_Menus_Modify.php?PK_Menu={$IVR_Tree.PK_Menu}'">Edit IVR Menu</button>
	<button class="important" type="button" onclick="document.location='IVR_Menus_Delete.php?PK_Menu={$IVR_Tree.PK_Menu}'">Delete IVR Menu</button>
</p>

<!-- Actions -->
<br /><br />
<p><strong>Actions:</strong></p>

<p>
	{foreach from=$IVR_Tree.Actions item=IVR_Action}
		<br />
		<img src='../static/images/tree_icons/{$IVR_Action.Type}.gif' alt="{$IVR_Action.Type}"/>
		{include file="IVR_Actions_Display.tpl" Display="Name" Action=$IVR_Action}
		({include file="IVR_Actions_Display.tpl" Display="Desc" Action=$IVR_Action})
	{foreachelse}
		<br />
		You currently do not have any actions for this context.
	{/foreach}
	<br />
	<img src="../static/images/tree_icons/final.gif" />
	<strong>Listen for options</strong>
</p>
<br />
<p>
	<button type="button" onclick="document.location='IVR_Actions.php?PK_Menu={$IVR_Tree.PK_Menu}'">Modify Actions</button>
</p>

<!-- Options -->
<br /><br />
<p><strong>Options:</strong></p>

<p>
	{foreach from=$IVR_Tree.Options item=IVR_Option}
		<br />
		When key <em>{$IVR_Option.Key}</em> is pressed, go to "{$IVR_Option.Name}" (from {if $IVR_Option.Order != ""}step {$IVR_Option.Order}{else}begining{/if})
		<br />
	{foreachelse}
		<br />
		You currently do not have any options for this IVR Menu.
	{/foreach}
</p>

<br />
<p>
	<button type="button" onclick="document.location='IVR_Options_List.php?PK_Menu={$IVR_Tree.PK_Menu}'">Modify Options</button>
</p>