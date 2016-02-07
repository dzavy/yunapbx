<h2>Extension Groups</h2>

<p>
	Grouping extensions makes it easy to apply changes and features to several extensions at once.
</p>
<form method="get" action="Groups_Modify.php">
<p>
	<button type="submit">Create a New Extension Group</button>
</p>
</form>

<br />
{if $Message == "MODIFY_GROUP"}
<p class="success_message">Successfully saved an extension group.</p>
{/if}
{if $Message == "DELETE_GROUP" }
<p class="success_message">Successfully deleted an extension group.</p>
{/if}

{if $Groups|@count}
<table class="listing fullwidth">
	<caption>Extension Groups</caption>
	<tr>
		<th>
			<a href="?Sort=Name">Group Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Members">Members</a>
			{if $Sort == "Members"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 130px"></th>
	</tr>

	{foreach from=$Groups item=Group}
	<tr class="{if $Hilight == $Group._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Group.Name}</td>
		<td>{$Group.Members}</td>
		<td>
			<form method="get" action="Groups_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_Group" value="{$Group._PK_}" />
				<button type="submit" name="submit" value="modify">Modify</button>
			</form>

			<form method="get" action="Groups_Delete.php" style="display: inline;">
				<input type="hidden" name="PK_Group" value="{$Group._PK_}" />
				<button type="submit" name="submit" value="delete" class="important">Delete</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>
{else}
<p class="warning_message">
	There are no extension groups defined on this system. 
	Use the <em>Create New Extension Group</em> button to define one now.
</p>
{/if}
