<script language="javascript">
{literal}
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
<h2>Call Recording</h2>
<p>
	Setup and manage which extensions's calls you want recorded in your system.
</p>
<br />
<p>
	<form action="Recordings_ModifyRule.php" method="get" style="display:inline">
		<button type="submit">Schedule a New Call Recording</button>
	</form>
</p>

<br />
<br />
<table class="listing fullwidth">
	<caption>Scheduled Call Recordings ( {$Rules.Start+1} to {$Rules.End} ) of {$Rules.Total}</caption>
	<tr>
		<th>
			<a href="?Rules[Sort]=Label">Recording Tag</a>
			{if $Rules.Sort == "Label"}
				<img src="images/{$Rules.Order}.gif" alt="{$Rules.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Rules[Sort]=Type">Extension Type</a>
			{if $Rules.Sort == "Type"}
				<img src="images/{$Rules.Order}.gif" alt="{$Rules.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Rules[Sort]=End">Record For</a>
			{if $Rules.Sort == "End"}
				<img src="images/{$Rules.Order}.gif" alt="{$Rules.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Rules[Sort]=Keep">Keep For</a>
			{if $Rules.Sort == "Keep"}
				<img src="images/{$Rules.Order}.gif" alt="{$Rules.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Rules[Sort]=DateCreated">Date Created</a>
			{if $Rules.Sort == "DateCreated"}
				<img src="images/{$Rules.Order}.gif" alt="{$Rules.Order}" />
			{/if}
		</th>
		<th style="width: 120px;"></th>
	</tr>

	{foreach from=$Rules.Fields item=Rule}
	<tr class="{if $Hilight == $Rule.PK_Rule}hilight{/if} {cycle values="odd,even"}">
		<td>{$Rule.Label}</td>
		<td>{$Rule.Type}</td>
		<td>
			{if $Rule.EndCount}
				{$Rule.EndCount} calls
			{elseif $Rule.EndDate}
				Until {$Rule.EndDate}
			{/if}
		</td>
		<td>
			{if $Rule.KeepCount}
				{$Rule.KeepCount} calls then {if $Rule.Backup}backup{else}delete{/if}
			{elseif $Rule.KeepSize}
				{$Rule.KeepSize} megs then {if $Rule.Backup}backup{else}delete{/if}
			{else}
				Forever
			{/if}
		</td>
		<td>{$Rule.DateCreated}</td>
		<td style="width: 120px;">
			<form style="display:inline" method="get" action="Recordings_ModifyRule.php">
				<input type="hidden" name="PK_Rule" value="{$Rule.PK_Rule}" />
				<button type="submit">Modify</button>
			</form>
			<form style="display:inline" method="get" action="Recordings_DeleteRule.php">
				<input type="hidden" name="PK_Rule" value="{$Rule.PK_Rule}" />
				<button type="submit" class="important">Delete</button>
			</form>
		</td>
	</tr>
	{/foreach}
</table>


<br /><br />
<h2>Recorded Calls</h2>
<div style="float: left; width: 150px;">
	<br /><br /><br />
	<button class="important">Delete Checked</button>
</div>
<div style="float: left; width: 200px;">&nbsp;</div>
<div style="float: left; border: 1px solid #a1a1a1; padding: 5px 10px; background-color: #EFEFEF; width: 350px;">
	<span style="font-size: 11px">Your disk is {$Disk.Used.Percent|string_format:'%.1f'}% full ({$Disk.Used.HSize} / {$Disk.Size.HSize})</span>
	<div style="border: 1px solid #a1a1a1; padding: 2px; background-color: #fff; margin: 0px;">
		<div title="{$Disk.Rec.Percent|string_format:'%.1f'}% ({$Disk.Rec.HSize})"     style="background-color:#CC0000; width: {$Disk.Rec.Percent|string_format:'%d'}%  ; float: left; height: 13px; overflow: hidden; cursor: help">&nbsp;</div>
		<div title="{$Disk.Other.Percent|string_format:'%.1f'}% ({$Disk.Other.HSize})" style="background-color:#075181; width: {$Disk.Other.Percent|string_format:'%d'}%; float: left; height: 13px; overflow: hidden; cursor: help">&nbsp;</div>
		<div style="clear: both"></div>
	</div>

	<div style="font-size:8px; line-height: 8px; margin-bottom: 3px">
		<div style="width: 33%; float: left; text-align: left">0%</div>
		<div style="width: 33%; float: left; text-align: center">50%</div>
		<div style="width: 33%; float: left; text-align: right">100%</div>
		<div style="clear: both; width: 1px"></div>
	</div>

	<span style="height: 10px; width: 10px; background-color: #CC0000; font-size:10px; font-family: monospace">&nbsp;&nbsp;</span>
	<span style="font-size: 11px">Recorded calls disk usage</span>
	&nbsp;
	<span style="height: 10px; width: 10px; background-color: #075181; font-size:10px; font-family: monospace">&nbsp;&nbsp;</span>
	<span style="font-size: 11px">Other disk usage</span>
</div>
<div style="clear: both"></div>

<br/>
<table class="listing fullwidth">
	<caption>Recorded Calls ( {$Calls.Start+1} to {$Calls.End} ) of {$Calls.Total}</caption>
	<tr>
		<th>
			<a href="?Calls[Sort]=Label">Recording Tag</a>
			{if $Calls.Sort == "Label"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Calls[Sort]=RecordedNumber">Recorded Account</a>
			{if $Calls.Sort == "RecordedNumber"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Calls[Sort]=CallerNumber">From Caller</a>
			{if $Calls.Sort == "CallerNumber"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Calls[Sort]=CalledNumber">To Caller</a>
			{if $Calls.Sort == "CalledNumber"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Calls[Sort]=Duration">Duration</a>
			{if $Calls.Sort == "Duration"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th>
			<a href="?Calls[Sort]=StartDate">Date Created</a>
			{if $Calls.Sort == "StartDate"}
				<img src="images/{$Calls.Order}.gif" alt="{$Calls.Order}" />
			{/if}
		</th>
		<th></th>
	</tr>

	{foreach from=$Calls.Fields item=Call}
	<tr class="{cycle values="odd,even"}">
		<td>{$Call.Label}</td>
		<td>
			{if $Call.RecordedName != "" }
				{$Call.RecordedName} &lt;{$Call.RecordedNumber}&gt;
			{else}
				{$Call.RecordedNumber}
			{/if}
		</td>
		<td>
			{if $Call.CallerName != "" }
				{$Call.CallerName} &lt;{$Call.CallerNumber}&gt;
			{else}
				{$Call.CallerNumber}
			{/if}
		</td>
		<td>
			{if $Call.CalledName != "" }
				{$Call.CalledName} &lt;{$Call.CalledNumber}&gt;
			{else}
				{$Call.CalledNumber}
			{/if}
		</td>
		<td>{if $Call.Duration/60 > 1}{$Call.Duration/60|intval}m, {/if}{$Call.Duration%60}s</td>
		<td>{$Call.DateCreated}</td>
		<td>
			<button type="button" onclick="javacript:popUp('Recordings_Play.php?ID={$Call.FK_CallLog}','Play Recordings File',350,200);">Play</button>
		</td>
	</tr>
	{/foreach}
</table>