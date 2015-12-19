<h2>GSM Modems</h2>

{if $Modems|@count}
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
			<a href="?Sort=AudioPort">Audio Interface</a>
			{if $Sort == "AudioPort"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=DataPort">Data Interface</a>
			{if $Sort == "DataPort"}
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
	{foreach from=$Modems item=Modem}
	<tr class="{if $Hilight == $Modem._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Modem.Name}</td>
		<td>{$Modem.AudioPort}</td>
		<td>{$Modem.DataPort}</td>
		<td>{if $Modem.CallbackExtension!=""}{$Modem.CallbackExtension}{else}No Mapping{/if}</td>
		<td>
				<form method="get" action="GSMModems_Modify.php" style="display: inline;">
					<input type="hidden" name="PK_GSMModem  " value="{$Modem._PK_}" />
					<button type="submit">Modify</button>
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


