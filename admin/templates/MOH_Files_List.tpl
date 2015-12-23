<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script language="javascript">
{literal}
function select_all()  { 
	$('input[type=checkbox][name^=Files]').attr('checked', 'checked');
	$('#img_select_all').toggle();
	$('#img_select_none').toggle();
}

function select_none() { 
	$('input[type=checkbox][name^=Files]').removeAttr('checked'); 
	$('#img_select_all').toggle();
	$('#img_select_none').toggle();
};

function ChangeView() {
	document.location = $("#View").val();
}

function AddNew() {
	document.location = $("#New").val();
}

function popUp(url,inName,width,height)
{
	inName = inName.replace(/ /g, "_"); /* For stupid pos IE */
	var popup = window.open('',inName,'width='+width+',height='+height+',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

	// only reload the page if it contains a new url
	if (popup.closed || !popup.document.URL || (-1 == popup.document.URL.indexOf(url)))
	{
		popup.location = url;
	}
	popup.focus();
	return popup;
}
{/literal}
</script>

<h2>Music On Hold </h2>
{if $Errors.EmptySelection}
	<p class="error_message">No file was selected.</p>
{/if}

{if $Errors.Delete.File}
	<p class="error_message">Error - Delete file from disk!</p>
{/if}
{if $Errors.Delete.RenameFiles}
	<p class="error_message">Error - rename after delete file! </p>
{/if}
{if $Errors.Move.InSameGroup}
	<p class="error_message">Can't move in same group! </p>
{/if}
{if $Errors.Move.File}
	<p class="error_message">Error - move file! </p>
{/if}
{if $Errors.Move.ReorderFile}
	<p class="error_message">Error - rename after move file! </p>
{/if}
{if $Errors.Copy.SameDirectory}
	<p class="error_message">Error - copy in same directory</p>
{/if}
{if $Errors.Copy.DuplicateFile}
	<p class="error_message">Error - duplicate file</p>
{/if}

{if $Errors.Move.DuplicateFile}
	<p class="error_message">Error - duplicate file</p>
{/if}

{if $Errors.Copy.FileToDisk}
	<p class="error_message">Error - copy to disk</p>
{/if}

{if $Message == "COPY_SUCCESSFULLY"}
	<p class="success_message"> Successfully copied file to new group</p>
{/if}
{if $Message == "MOVE_SUCCESSFULLY"}
	<p class="success_message">  Successfully moved file to new group</p>
{/if}
{if $Message == "DELETE_SUCCESSFULLY"}
	<p class="success_message">Succesfully deleted file.</p>
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
				<option value="MOH_Files_List.php" >All Songs</option>
				<option value="MOH_Groups_List.php">All Groups</option>
				<optgroup label="Group Listings">
					{foreach from=$Groups item=Group}
					<option value="MOH_Files_ListGroup.php?PK_Group={$Group.PK_Group}" 
						{if $Group.PK_Group == $selectedGroup}	selected {/if}>					
						{$Group.Name}
					</option>
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

<hr />

{if $Files|@count}
<p>
<form action="MOH_Files_List.php" method="post" name="MOH_Files_List">
	<input type="hidden" name="PK_Group" value="{$selectedGroup}" />
	<button type="submit" class="important" name="submit" value="delete"> Delete </button>	
	&nbsp;&nbsp;
	<button type="submit" name="submit" value="move">Move To :</button>
	<select name="move_group">
		{foreach from=$Groups item=Group}		
			{if $Group.PK_Group != $selectedGroup}		
			<option value="{$Group.PK_Group}"  name="{$Group.Name}">
				{$Group.Name}
			</option>
			{/if}
		{/foreach}
		
	</select>
	
	&nbsp;&nbsp;

	<button type="submit"  name="submit" value="copy">Copy To :</button>
	<select name="copy_group">
		
		{foreach from=$Groups item=Group}							
			{if $Group.PK_Group != $selectedGroup}		
			<option value="{$Group.PK_Group}" name="{$Group.Name}">
				{$Group.Name}
			</option>
			{/if}
		{/foreach}
	</select>
</p>

<br />
<br />

<form action="MOH_Files_List.php" method="post" name="MOH_Files_List">
<table class="listing fullwidth">
	<caption>Music On Hold Songs ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th>
			<img src="../static/images/select_all.gif" onclick="select_all()" id="img_select_all" alt="0" title="Select All"/>
			<img src="../static/images/select_none.gif" onclick="select_none()" id="img_select_none" alt="0" title="Select None" style="display: none"/>
		</th>
		<th>
			<a href="?Sort=Filename">File Name</a>
			{if $Sort == "Filename"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Group">Group Name</a>
			{if $Sort == "Group"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Order">Play order</a>
			{if $Sort == "Order"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=DateCreated">Date created</a>
			{if $Sort == "DateCreated"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>		
		<th style="width: 50px;">Play</th>
	</tr>
	
	{foreach from=$Files item=File}
	<tr class="{cycle values='odd,even'}">
		<td style="width:20px;">
			<input type="checkbox" name="Files[]" value="{$File._PK_}" />
		</td>
		<td>{$File.Filename}.{$File.Fileext}</td>
		<td>{$File.Group}</td>
		<td>{$File.Order}</td>
		<td>{$File.DateCreated}</td>
		<td style="width: 50px;">				
				<button type="button" onclick="javacript:popUp('MOH_Files_Play.php?PK_File={$File._PK_}','Play MOH File',300,100);">Play</button>
		</td>
	</tr>
	{/foreach}
</table>
</form>
{else}
<p class="warning_message">
	There are no music on hold files uploaded on this system. 
	Use the <em>Upload Song</em> option to add one now.
</p>
{/if}
