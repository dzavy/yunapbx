<script type="text/javascript">
{literal}
function ChangeListView() {
	document.location=$('#View').val();
}
{/literal}
</script>

<h2>Manage Extensions</h2>

<p>
	<button type="button" onclick="document.location='Extensions_Create.php'">Create A New Extension</button>
</p>
<table class="fullwidth" style="margin-bottom: 20px;">
	<tr>
		<td>
			<label for="View">View</label>
			<select id="View" name="View">
				<option value="Extensions_List.php" >All Extensions</option>
				<option value="Extensions_List_Phones.php" >Only Phones</option>
				<option value="Extensions_List_IVRs.php" selected="selected" >Only IVRs</option>
				<option value="Extensions_List_Queues.php">Only Call Queues</option>
				<option value="Extensions_List_Agents.php">Only Agents</option>
			</select>
			<button type="button" onclick="ChangeListView();">Go</button>
		</td>
		<td style="text-align: right;">
			<form action="Extensions_List_IVRs.php" method="get">
			<label for="Search">Search:</label>
			<input type="text" id="Search" name="Search" value="{$Search}" />
			</form>
		</td>
	</tr>
</table>

{if $Extensions|@count}
<table class="listing fullwidth">
	<caption>All IVR Extensions ( {$Start+1} to {$End} ) of {$Total}</caption>
	<thead>
	<tr>
		<th>
			<a href="?Sort=Extension">Extension</a>
			{if $Sort == "Extension"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Name">Starting IVR Menu</a>
			{if $Sort == "Name"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Action">IVR Menu Entry Point</a>
			{if $Sort == "Action"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=DateCreated">Date Created</a>
			{if $Sort == "DateCreated"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 120px"></th>
	</tr>
	</thead>
	{foreach from=$Extensions item=Extension}
	<tr class="{if $Hilight == $Extension._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Extension.Extension}</td>
		<td>
			{$Extension.Name}
		</td>
		<td>
			{include file=IVR_Actions_Display.tpl Display='Name' Action=$Extension.Action}
		</td>
		<td>{$Extension.DateCreated_Formated}</td>
		<td style="width: 120px">
			<form method="get" action="Extensions_{$Extension.Type}_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_Extension" value="{$Extension._PK_}" />
				<button type="submit">Modify</button>
			</form>
			<form method="get" action="Extensions_{$Extension.Type}_Delete.php" style="display: inline;">
				<input type="hidden" name="PK_Extension" value="{$Extension._PK_}" />
				<button type="submit" class="important">Delete</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>
{else}
<p class="warning_message">
	There are no ivr extensions defined on this system. 
	Use the <em>Create New Extension</em> button to define one now.
</p>
{/if}

<p style="text-align: right">
{if $Start > 0}
	<a class="prev" href="?Start={$Start-$PageSize}">Previous</a>
{/if}
{if $End < $Total}
<a class="next" href="?Start={$Start+$PageSize}">Next</a>
{/if}
</p>
