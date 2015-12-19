<script type="text/javascript" src="../static/script/calendar.js"></script>
<script type="text/javascript" src="../static/script/calendar-setup.js"></script>
<script type="text/javascript" src="../static/script/calendar-en.js"></script>
<script type="text/javascript" src="../static/script/jquery.tooltip.js"></script>

<h2>Call Log</h2>

<p>
	See what calls have been made on the phone system. Use the form below to narrow down the information that you view in the logs in order to derive a more meaningful and specific call report.
</p>
<br />
<form method="get" action="CallLog.php" />
<table>
	<tr>
		<td>From Date:</td>
		<td>{dhtml_calendar format='%m/%d/%Y' name="StartDate" style="width: 100px" value=$Filter.StartDate}</td>
		<td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</td>
		<td>To Date:</td>
		<td>{dhtml_calendar format='%m/%d/%Y' name="EndDate" style="width: 100px" value=$Filter.EndDate}</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<small>(mm/dd/yyyy)</small>
		</td>
		<td></td>
		<td></td>
		<td>
			<small>(mm/dd/yyyy)</small>
		</td>
	</tr>
</table>

<br />
<p>
	<button type="submit">View Report</button>
	<button>Export to Excel</button>
</p>
</form>

<br />
<hr />
<br />

{if $CDRs|@count}
<table class="listing fullwidth">
	<caption>Call Log ( {$Start+1} to {$End} ) of {$Total}</caption>
	<thead>
	<tr>
		<th>
			<a href="?Sort=StartDate">Call Date</a>
			{if $Sort == "StartDate"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=CallerNumber">Call From</a>
			{if $Sort == "CallerNumber"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=CalledNumber">Call To</a>
			{if $Sort == "CalledNumber"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=CallType">Call Type</a>
			{if $Sort == "CallType"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Duration">Call Time</a>
			{if $Sort == "Duration"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=BillSec">Talk Time</a>
			{if $Sort == "BillSec"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 90px">Call Details</th>
	</tr>
	</thead>
	{foreach from=$CDRs item=CDR}
	<tr class="{cycle values="odd,even"}">
		<td>{$CDR.StartDate_Formated}</td>
		<td>
			{if $CDR.CallerName != "" }
				{$CDR.CallerName} &lt;{$CDR.CallerNumber}&gt;
			{else}
				{$CDR.CallerNumber}
			{/if}
		</td>
		<td>
			{if $CDR.CalledName != "" }
				{$CDR.CalledName} &lt;{$CDR.CalledNumber}&gt;
			{else}
				{$CDR.CalledNumber}
			{/if}
		</td>
		<td>
			{if     $CDR.CallType == 'IN'}<img src="../static/images/incoming.png" />
			{elseif $CDR.CallType == 'OUT'}<img src="../static/images/outgoing.png" />
			{elseif $CDR.CallType == 'LOCAL'}<img src="../static/images/internal.png" />
			{/if}
		</td>
		<td>{if $CDR.Duration/60 > 1}{$CDR.Duration/60|intval}m, {/if}{$CDR.Duration%60}s</td>
		<td>{if $CDR.BillSec/60 > 1}{$CDR.BillSec/60|intval}m, {/if}{$CDR.BillSec%60}s</td>
		<td>
			<a href="CallLog_Details.php?PK_CallLog={$CDR.PK_CallLog}&width=400" id="ToolTip_{$CDR.PK_CallLog}" class="jTip" id="six" name="Call Details">Call Details</a>
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
{else}
<p class="warning_message">
	There are no call logs for the selected time period.
</p>
{/if}
