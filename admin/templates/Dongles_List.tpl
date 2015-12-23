<script type="text/javascript">
{literal}
function AddNewSubmit() {
	var Type = $("#Type").val();
	$("#AddNewForm").attr('action', 'Dongles_Modify.php')
	//VoipProviders_Sip_Modify.php
}
{/literal}
</script>

<h2>GSM Modems</h2>
<form action="#" method="post" onsubmit="AddNewSubmit()" id="AddNewForm">
<p>

	<button type="submit">Add New VOIP Provider</button>
</p>
</form>
{if $Dongles|@count}
<table class="listing fullwidth">
	<caption>GSM Modems ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th>
			<a href="?Sort=Name">Modem Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=IMEI">IMEI</a>
			{if $Sort == "IMEI"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=IMSI">IMSI</a>
			{if $Sort == "IMSI"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=CallbackExtension">Default Extension</a>
			{if $Sort == "CallbackExtension"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 130px"></th>
	</tr>
	{foreach from=$Dongles item=Dongle}
	<tr class="{if $Hilight == $Dongle._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Dongle.Name}</td>
		<td>{$Dongle.IMEI}</td>
		<td>{$Dongle.IMSI}</td>
		<td>{if $Modem.CallbackExtension!=""}{$Modem.CallbackExtension}{else}No Mapping{/if}</td>
		<td>
				<form method="get" action="Dongles_Modify.php" style="display: inline;">
					<input type="hidden" name="PK_Dongle" value="{$Dongle._PK_}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="Dongles_Delete.php" style="display: inline;">
					<input type="hidden" name="PK_Dongle" value="{$Dongle._PK_}" />
					<button type="submit" class="important">Delete</button>
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
{else}
<p class="warning_message">
	There are no voip providers defined on this system. 
	Use the <em>Add New Provider</em> form to define one now.
</p>
{/if}

