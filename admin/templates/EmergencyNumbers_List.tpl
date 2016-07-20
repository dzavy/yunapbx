<script type="text/javascript" src="../static/script/jquery.checkboxes.js"></script>
<script type="text/javascript">
{literal}
function ViewList() {
	document.location=$('#ListPage').val();
}

function ViewAdd() {
	document.location=$('#AddPage').val();
}

function GetSelectedEntries() {
	var cbox = $('input[name=PK_SoundEntry_Checkbox]:checked');
	var PK_Entries = '';
	
	for(i=0; i<cbox.length ; i++) {
		if (PK_Entries == '') {
			PK_Entries = PK_Entries+cbox[i].value;
		} else {
			PK_Entries = PK_Entries+','+cbox[i].value;
		}
	}
	return PK_Entries;
}

function DeleteEntries() {
	PK_Entries = GetSelectedEntries();
	if (PK_Entries != '') {
		document.location = 'SoundEntries_Delete.php?PK_SoundEntries='+PK_Entries;
	}
}

function MoveEntries() {
	PK_Entries = GetSelectedEntries();
	PK_Folder  = $('#PK_Folder_Move').val();
	if (PK_Entries != '') {
		document.location = 'SoundEntries_Move.php?PK_SoundEntries='+PK_Entries+'&PK_Folder='+PK_Folder;
	}
}
{/literal}
</script>

<h2>Sound Manager</h2>

{if $Message!= ""}
<p class="success_message">
{if     $Message == "MODIFY_ENTRY" } Successfully recorded and saved sound.
{elseif $Message == "DELETE_ENTRY" } Successfully deleted sound.
{/if}
</p>
{/if}

<p>
	Create and manage the recordings for your IVRs and call queues.
</p>

<table class="fullwidth">
	<tr>
		<td>
			<small>View:</small>
			<select id="ListPage" onchange="ViewList()">
				<option {if $PK_SoundFolder==''}selected="selected"{/if} value="SoundEntries_List.php?PK_SoundFolder=">All Sounds</option>
				<option value="SoundFolders_List.php">All Folders</option>
				<option value="SoundLanguages_List.php">All Languages</option>
				<optgroup label="Folder Listings">
				{foreach from=$SoundFolders item=Folder}
					<option {if $PK_SoundFolder==$Folder.PK_SoundFolder}selected="selected"{/if}  value="SoundEntries_List.php?PK_SoundFolder={$Folder.PK_SoundFolder}">{$Folder.Name} Folder ({$Folder.Quantity} files)</option>
				{/foreach}
				</optgroup>
			</select>
		</td>
		<td style="text-align: right;">
			<small>Add New:</small>
			<select id="AddPage">
				<option value="SoundFiles_Modify.php">Sound</option>
				<option value="SoundFolders_Modify.php">Folder</option>
				<option value="SoundLanguages_Modify.php">Language</option>
			</select>
			<button onclick="ViewAdd()">Go</button>
		</td>
	</tr>
</table>

<br />
<hr style="background-color: #ccc"/>
<br />
<p>
	<button type="button" class="important" onclick="DeleteEntries()">Delete</button>
	<button type="button" onclick="MoveEntries()">Move To:</button>
	<select id="PK_Folder_Move">
	{foreach from=$SoundFolders item=Folder}
		<option value="{$Folder.PK_SoundFolder}">{$Folder.Name} ({$Folder.Quantity})</option>
	{/foreach}
	</select>
</p>
<br />

<table class="listing fullwidth">
	<caption>Sound Folders ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th></th>
		<th>
			<a href="?Sort=Name">Sound Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Folder">Folder Name</a>
			{if $Sort == "Folder"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Language">Languages</a>
			{if $Sort == "Languages"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Type">Sound Type</a>
			{if $Sort == "Type"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 100px"></th>
	</tr>
	
	{foreach from=$SoundEntries item=Entry}
	<tr class="{if $Hilight == $Entry._PK_}hilight{/if} {cycle values="odd,even"}" title="{$Entry.Description}">
		<td>
			<input type="checkbox" value="{$Entry._PK_}" name="PK_SoundEntry_Checkbox" />
		</td>
		<td>{$Entry.Name}</td>
		<td>{$Entry.Folder}</td>
		<td>{$Entry.Language}</td>
		<td>{$Entry.Type}</td>
		<td  style="width: 100px">
			<form method="get" action="SoundEntries_Modify.php">
				<input type="hidden" name="PK_SoundEntry" value="{$Entry.PK_SoundEntry}" />
				<button type="submit">Modify / Play</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>

<p style="text-align: right">
{if $Start > 0} 
	<a class="prev" href="?Start={$Start-$PageSize}">Previous</a>
{/if}
{if $End < $Total}
<a class="next" href="?Start={$Start+$PageSize}">Next</a>
{/if}
</p>
