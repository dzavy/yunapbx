{literal}
<script type="text/javascript">
function ShowSelectRow() {
	$("#SIP_SelectRow").css('display', 'none');
	$("#DONGLE_SelectRow").css('display', 'none');
	$("#GROUP_SelectRow").css('display', 'none');

	var Type = $('#ProviderType').val();
	$("#"+Type+"_SelectRow").css('display', 'table-row');
}

$(document).ready(function() {
	ShowSelectRow();
});

</script>
{/literal}

<h2>Outgoing Calls</h2>
{if $Errors.Name           } <p class="error_message">Missing Outgoing Rule Name</p> {/if}
{if $Errors.RestBetweenLow } <p class="error_message">Missing Minimum Length of Number in Pattern</p> {/if}
{if $Errors.RestBetweenHigh} <p class="error_message">Missing Maximum Length of Number in Pattern</p> {/if}
{if $Errors.TrimFront      } <p class="error_message">Trim digits must be 1-99.</p> {/if}
{if $Errors.PrependDigits  } <p class="error_message">Prepend value must consist of only digits,*,+,or # and may only be 1-20 characters in length</p> {/if}
{if $Errors.BeginWith      } <p class="error_message">Invalid "Number Begins With" in pattern. Must be a digit,*,#, or special expression character ")(|^" and must be 1 to 24 characters in length</p> {/if}

<form action="OutgoingCalls_Rule_Modify.php" method="post">
<p>
	<input type="hidden" name="PK_OutgoingRule" value="{$Rule.PK_OutgoingRule}" />
</p>


{if $Rule.PK_OutgoingRule == "" }
<strong>Create A New Outgoing Call Rule</strong>
{else}
<strong>Modify Outgoing Rule</strong>
{/if}

<table class="formtable">
	<!-- Rule Name -->
	<tr>
		<td>Rule Name:</td>
		<td>
			<input type="text" name="Name" value="{$Rule.Name}" {if $Errors.Name }class="error"{/if} />
		</td>
	</tr>

	<!-- Is this rule final? -->
	<tr>
		<td>
			Is this rule final?:
		</td>
		<td>
			<input type="radio" name="Final" id="Final_1" value="1" {if $Rule.Final=='1'}checked="checked"{/if} />
			<label for="Final_1">Yes</label>
			&nbsp;&nbsp;
			<input type="radio" name="Final" id="Final_0" value="0" {if $Rule.Final=='0'}checked="checked"{/if} />
			<label for="Final_0">No</label>
		</td>
	</tr>

	<!-- Pattern to match -->
	<tr>
		<td>
			Pattern to match:
		</td>
		<td>
			Number begins with the digits <input type="text" name="BeginWith" value="{$Rule.BeginWith}" {if $Errors.BeginWith }class="error"{/if}/>.
			<br />

			The rest of the number must be between
			<input type="text" name="RestBetweenLow" size="3" value="{$Rule.RestBetweenLow}" {if $Errors.RestBetweenLow }class="error"{/if}/>
			and
			<input type="text" name="RestBetweenHigh" size="3" value="{$Rule.RestBetweenHigh}" {if $Errors.RestBetweenHigh }class="error"{/if} />
			digits in length.
			<br />

			Before connecting the call, trim
			<input type="text" name="TrimFront" size="3" value="{$Rule.TrimFront}" {if $Errors.TrimFront }class="error"{/if}/>
			digits from the front,
			<br />
			and then prepend the digits
			<input type="text" name="PrependDigits" size="10" value="{$Rule.PrependDigits}" {if $Errors.PrependDigits }class="error"{/if} />
			to the number.
		</td>
	</tr>

	<!-- Call Trough -->
	<tr>
		<td>Call Trough:</td>
		<td>
			<select name="ProviderType" id="ProviderType" onchange="ShowSelectRow()">
				<option value="SIP"      {if $Rule.ProviderType=='SIP'     }selected="selected"{/if}>VoIP Provider</option>
				<option value="DONGLE"   {if $Rule.ProviderType=='DONGLE'     }selected="selected"{/if}>3G Dongle</option>
				<option value="GROUP"    {if $Rule.ProviderType=='GROUP'   }selected="selected"{/if}>Channel Group</option>
				<option value="INTERNAL" {if $Rule.ProviderType=='INTERNAL'}selected="selected"{/if}>Internal</option>
			</select>
		</td>
	</tr>

	<!-- SIP Provider -->
	<tr id="SIP_SelectRow" style="display:none">
		<td>VoIP Provider:</td>
		<td>
			<select name="ProviderID[SIP]">
			{foreach from=$SipProviders item=SipProvider}
				<option {if $Rule.ProviderID == $SipProvider.PK_SipProvider}selected="selected"{/if} value="{$SipProvider.PK_SipProvider}">{$SipProvider.Name}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- IAX Provider -->
	<tr id="DONGLE_SelectRow" style="display:none">
		<td>3G Dongle:</td>
		<td>
			<select name="ProviderID[DONGLE]">
			{foreach from=$Dongles item=Dongle}
				<option {if $Rule.ProviderID == $Dongle.PK_Dongle}selected="selected"{/if} value="{$Dongle.PK_Dongle}">{$Dongle.Name}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- Channel Group -->
	<tr id="GROUP_SelectRow" style="display:none">
		<td>Channel Group:</td>
		<td>
			<select name="ProviderID[GROUP]">
			{foreach from=$SipProviders item=SipProvider}
				<option {if $Rule.ProviderID == $SipProvider.PK_SipProvider}selected="selected"{/if} value="{$SipProvider.PK_SipProvider}">{$SipProvider.Name}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- Allow all existing extensions to use this rule ? -->
	{if $Rule.PK_OutgoingRule == "" }
	<tr>
		<td colspan="2">
			Allow all existing extensions to use this rule ?
			&nbsp;
			<input type="radio" name="Allow" id="Allow_1" value="1" {if $Rule.Allow=='1'}checked="checked"{/if} />
			<label for="Allow_1">Yes</label>
			&nbsp;&nbsp;
			<input type="radio" name="Allow" id="Allow_0" value="0" {if $Rule.Allow=='0'}checked="checked"{/if} />
			<label for="Allow_0">No</label>
		</td>
	</tr>
	{/if}
</table>

<p>
	<br />
	<button type="submit" name="submit" value="save">Save Changes</button>
</p>

</form>