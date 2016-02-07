<script type="text/javascript">
{literal}
function AddNewSubmit() {
	var Type = $("#Type").val();
	$("#AddNewForm").attr('action', 'VoipProviders_Modify.php')
	//VoipProviders_Sip_Modify.php
}
{/literal}
</script>

<h2>VoIP Providers</h2>

{if $Message == "MODIFY_SIP_PROVIDER"}
<p class="success_message">Successfully modified VoIP provider.</p>
{/if}
{if $Message == "DELETE_SIP_PROVIDER"}
<p class="success_message">Successfully delete VoIP provider</p>
{/if}

<form action="#" method="post" onsubmit="AddNewSubmit()" id="AddNewForm">
<p>

	<button type="submit">Add New VoIP Provider</button>
</p>
</form>

{if $Providers|@count}
<table class="listing fullwidth">
	<caption>VoIP Providers</caption>
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
				<form method="get" action="VoipProviders_Modify.php" style="display: inline;">
					<input type="hidden" name="PK_SipProvider" value="{$Provider._PK_}" />
					<button type="submit">Modify</button>
				</form>
				<form method="get" action="VoipProviders_Delete.php" style="display: inline;">
					<input type="hidden" name="PK_SipProvider" value="{$Provider._PK_}" />
					<button type="submit" class="important">Delete</button>
				</form>
		</td>
	</tr>
	{/foreach}
</table>
{else}
<p class="warning_message">
	There are no VoIP providers defined on this system. 
	Use the <em>Add New Provider</em> form to define one now.
</p>
{/if}
