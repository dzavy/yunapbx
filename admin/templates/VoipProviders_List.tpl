<script type="text/javascript">
{literal}
function AddNewSubmit() {
	var Type = $("#Type").val();
	$("#AddNewForm").attr('action', 'VoipProviders_'+Type+'_Modify.php')
	//VoipProviders_Sip_Modify.php
}
{/literal}
</script>

<h2>VOIP Providers</h2>

{if $Message == "MODIFY_RTP_RANGE"}
<p class="success_message">Successfully modified RTP port range.</p>
{/if}
{if $Message == "MODIFY_SIP_PROVIDER"}
<p class="success_message">Successfully modified SIP provider.</p>
{/if}
{if $Message == "MODIFY_IAX_PROVIDER"}
<p class="success_message">Successfully modified IAX provider.</p>
{/if}
{if $Message == "DELETE_SIP_PROVIDER"}
<p class="success_message">Successfully delete SIP provider</p>
{/if}
{if $Message == "DELETE_IAX_PROVIDER"}
<p class="success_message">Successfully delete IAX provider</p>
{/if}

{if $Message == "ERRORS_RTP_RANGE"}
<p class="error_message">Error -Invalid RTP port range</p>
{/if}

<form action="#" method="post" onsubmit="AddNewSubmit()" id="AddNewForm">
<p>
	<strong>Add New:</strong>
	<select name="Type" id="Type">
		<option value="Sip">SIP Provider</option>
		<option value="Iax">IAX Provider</option>
	</select>
	<button type="submit">Go</button>
</p>
</form>

{if $Providers|@count}
<table class="listing fullwidth">
	<caption>VOIP Providers ( {$Start+1} to {$End} ) of {$Total}</caption>
	<tr>
		<th>
			<a href="?Sort=Type">Provider Type</a>
			{if $Sort == "Type"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Name">Provider Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=AccountID">Account ID</a>
			{if $Sort == "AccountID"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Host">Hostname</a>
			{if $Sort == "Host"}
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
	{foreach from=$Providers item=Provider}
	<tr class="{if $Hilight == $Provider._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Provider.Type}</td>
		<td>{$Provider.Name}</td>
		<td>{$Provider.AccountID}</td>
		<td>{$Provider.Host}</td>
		<td>{if $Provider.CallbackExtension!=""}{$Provider.CallbackExtension}{else}No Mapping{/if}</td>
		<td>
			{if $Provider.Type == 'SIP'}
				<form method="get" action="VoipProviders_Sip_Modify.php" style="display: inline;">
					<input type="hidden" name="PK_SipProvider" value="{$Provider._PK_}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="VoipProviders_Sip_Delete.php" style="display: inline;">
					<input type="hidden" name="PK_SipProvider" value="{$Provider._PK_}" />
					<button type="submit" class="important">Delete</button>
				</form>
			{/if}
			{if $Provider.Type == 'IAX'}
				<form method="get" action="VoipProviders_Iax_Modify.php" style="display: inline;">
					<input type="hidden" name="PK_IaxProvider" value="{$Provider._PK_}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="VoipProviders_Iax_Delete.php" style="display: inline;">
					<input type="hidden" name="PK_IaxProvider" value="{$Provider._PK_}" />
					<button type="submit" class="important">Delete</button>
				</form>
			{/if}
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


