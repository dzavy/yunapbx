{if $IVR_Tree}
<a href="#menu_{$IVR_Tree.PK_Menu}" id="menu_{$IVR_Tree.PK_Menu}" onclick="SelectTreeNode('menu', {$IVR_Tree.PK_Menu})">
{if $Key > -1}
	<img src="images/tree_icons/folder.gif" alt="folder" /> ( {$Key} )
{else}
	<img src="images/tree_icons/base.gif"  alt="base"/>
{/if}
{$IVR_Tree.Name}
</a>

<ul {if $Key==""}id="treeview"{/if}>
	{foreach from=$IVR_Tree.Actions item=IVR_Action}
	<li {if $IVR_Action.Disabled}class="disabled"{/if}>
		<a href="#action_{$IVR_Action.PK_Action}" id="action_{$IVR_Action.PK_Action}" onclick="SelectTreeNode('action', {$IVR_Action.PK_Action})">
		<img src='images/tree_icons/{$IVR_Action.Type}.gif' alt="{$IVR_Action.Type}"/>
		{include file="IVR_Actions_Display.tpl" Display="Name" Action=$IVR_Action}
		</a>
	</li>
	{/foreach}
	<li>
		<a href="javascript:void()">
		<img src='images/tree_icons/final.gif' alt="final"/>
		Listen for Options
		</a>
	</li>

	{foreach from=$IVR_Tree.Options key=Key item=IVR_Option}
		<li>
		{include file="IVR_Menus_treeview.tpl" IVR_Tree=$IVR_Option Key=$Key}
		</li>
	{/foreach}

	{foreach from=$IVR_Tree.Visited key=Key item=IVR_Option}
		<li>
			<img src="images/tree_icons/page.gif" alt="page" />
			<a href="#" onclick="SelectTreeNode('menu', {$IVR_Option.PK_Menu})">
			( {$Key} ) {$IVR_Option.Name}
			</a>
		</li>
	{/foreach}
</ul>
{/if}
