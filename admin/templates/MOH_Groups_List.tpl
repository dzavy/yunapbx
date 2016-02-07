<script type="text/javascript">
{literal}
function ChangeView() {
	document.location = $("#View").val();
}

function AddNew() {
	document.location = $("#New").val();
}
{/literal}
</script>

<h2>Music On Hold</h2>

{if $Message != ""}
<p class="success_message">
{if     $Message == "MODIFY_MOH_GROUP"   }  Successfully modified music on hold group.
{elseif $Message == "DELETE_MOH_GROUP"   }  Successfully deleted  music on hold group.
{elseif $Message == "ADD_MOH_GROUP"      }  Successfully created music on hold group.
{/if}
</p>
{/if}

<p>
	The music on hold manager lets you create groups of music, upload new songs, manage the play order, and more.
</p>
<br />
<table class="fullwidth">
	<tr>
		<td>
			View:
			<select id="View">
				<option value="MOH_Files_List.php">All Songs</option>
				<option value="MOH_Groups_List.php" selected="selected">All Groups</option>
				<optgroup label="Group Listings">
					{foreach from=$Groups item=Group}
					<option value="MOH_Files_ListGroup.php?PK_Group={$Group.PK_Group}">{$Group.Name}</option>
					{/foreach}
				</optgroup>
			</select>
			<button type="button" onclick="ChangeView()">Go</button>
		</td>
		
		<td style="text-align: right">
			Add New:
			<select id="New">
				<option value="MOH_Files_Modify.php" >Song</option>
				<option value="MOH_Groups_Modify.php">Group</option>
			</select>
			<button type="button" onclick="AddNew()">Go</button>
		</td>
	</tr>
</table>
<br />
<table class="listing fullwidth">
	<caption>Music On Hold Groups</caption>
	<tr>
		<th>
			<a href="?Sort=Name">Group Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Volume">Playback Volume</a>
			{if $Sort == "Volume"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Ordered">Playback Order</a>
			{if $Sort == "Ordered"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 120px"></th>
	</tr>
	
	{foreach from=$Groups item=Group}
	<tr class="{if $Hilight == $Group.PK_Group}hilight{/if} {cycle values="odd,even"}">
		<td>{$Group.Name}</td>
		<td>{$Group.Volume}%</td>
		<td>{if $Group.Ordered == "1"}normal{else}random{/if}</td>
		<td style="width: 120px">
			<form method="get" action="MOH_Groups_Modify.php" style="display:inline">
				<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
				<button type="submit">Modify</button>
			</form>
			{if $Group.Protected}
				<button type="button" class="important_disabled">Delete</button>
			{else}
				<form method="get" action="MOH_Groups_Delete.php" style="display:inline">
					<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
					<button type="submit" class="important">Delete</button>
				</form>
			{/if}
		</td>
	</tr>
	{/foreach}
</table>

