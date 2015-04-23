{literal}
<script type="text/javascript">
function select_all()  { 
	$('input[type=checkbox][name^=Message]').attr('checked', 'checked');
	$('#img_select_all').toggle();
	$('#img_select_none').toggle();
} 
function select_none() { 
	$('input[type=checkbox][name^=Message]').removeAttr('checked'); 
	$('#img_select_all').toggle();
	$('#img_select_none').toggle();
}; 
</script>
{/literal}
<h2>Mailbox</h2>
<p>
	Organize your voicemail as easily as you organize your email.
	Put voicemails into folders, forward voicemail messages, and delete voicemails from the system.
</p>

<br />

<p>
	<form action="Voicemail.php" method="get">
	<label for="ActiveFolder">View Folder:</label>
	
	<select name="Path" id="Path">
		{foreach from=$Folders item=Folder}
		<option value="{$Folder.name}" {if $Folder.name == $Path}selected="selected"{/if}>{$Folder.name} ({$Folder.size})</option>
		{/foreach}
	</select>
	
	<button type="submit">Go</button>
	</form>
</p>
<form action="Voicemail.php" method="get" >
<br />
<hr />
<br />
	<button class="important" type="submit" name="action" value="delete">Delete</button>
	&nbsp;
	<button type="submit" name="action" value="move">Move To Folder:</button>
	<select name="MoveFolder">
		{foreach from=$Folders item=Folder}
		{if $Folder.name != $Path}
			<option value="{$Folder.name}">{$Folder.name} ({$Folder.size})</option>
		{/if}
		{/foreach}
	</select>
	&nbsp;
	<button type="submit" name="action" value="forward">Forward To:</button>
	<select></select>
<br /><br />

<table class="listing fullwidth">
	<caption>Voicemail in  {$Path} folder ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th>
			<img src="images/select_all.gif" onclick="select_all()" id="img_select_all" alt="0" title="Select All"/>
			<img src="images/select_none.gif" onclick="select_none()" id="img_select_none" alt="0" title="Select None" style="display: none"/>
		</th>
		<th>
			<a href="?Sort=no">Msg #</a>
			{if $Sort == "no"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=origmailbox">Original Mailbox</a>
			{if $Sort == "origmailbox"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		
		<th>
			<a href="?Sort=callerid">Caller ID</a>
			{if $Sort == "callerid"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		
		<th>
			<a href="?Sort=origtime">Date</a>
			{if $Sort == "origtime"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>

		<th>
			<a href="?Sort=duration">Duration</a>
			{if $Sort == "duration"}
				<img src="images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		
		<th style="width: 70px">Play</th>
	</tr>
	
	{foreach from=$Messages item=Message}
	<tr class="{if $Hilight == $Message.no}hilight{/if} {cycle values="odd,even"}">
		<td>
			<input type="checkbox" name="Messages[]" value="{$Message.no}" />
		</td>
		<td>{$Message.no}</td>
		<td>{$Message.origmailbox}</td>
		<td>{$Message.callerid}</td>
		<td>{$Message.origdate}</td>
		<td>{$Message.duration}s</td>
		<td>
			<a href="Voicemail_GetFile.php?Folder={$Path}&File={$Message.no}">
				<img src="images/play.gif" /> Play
			</a>
		</td>
	</tr>
	{/foreach}
</table>
</form>