<script type="text/javascript">
{literal}
function RefreshTemplate() {
	if ($('#Type').val() != 'SipPhone' && $('#Type').val() != 'SccpPhone' && $('#Type').val() != 'Virtual') {
		$('#FK_Template_tr').hide();
	} else {
		$('#FK_Template_tr').show();
	}

	if ($('#Type').val() != 'Feature') {
		$('#Feature_tr').hide();
	} else {
		$('#Feature_tr').show();
	}
}

$(document).ready(function() {
	RefreshTemplate();
})

{/literal}
</script>

<h2>Manage Extensions</h2>
{if $Errors.Type}
<p class="error_message">That is an invalid extension type.</p>
{/if}

<form method="post" action="Extensions_Create.php">
<table class="formtable">
	<!-- Extension Type -->
	<tr>
		<td>
			Extension Type
		</td>
		<td>
			<select name="Type" id="Type" onchange="javascript: RefreshTemplate();">
				<optgroup label="Standard">
				<option {if $Type == "SipPhone"}selected="selected"{/if} value="SipPhone">SIP Phone</option>
                <option {if $Type == "SccpPhone"}selected="selected"{/if} value="SccpPhone">Cisco SCCP Phone</option>
				<option {if $Type == "Virtual"}selected="selected"{/if} value="Virtual">Virtual Extension</option>
				</optgroup>
				<optgroup label="Features">
				<option {if $Type == "ParkingLot"}selected="selected"{/if} value="ParkingLot">Call Parking</option>
                <option {if $Type == "ConfCenter"}selected="selected"{/if} value="ConfCenter">Conference Center</option>
				<option {if $Type == "DialTone"}selected="selected"{/if} value="DialTone">Dial Tone</option>
				<option {if $Type == "Feature"}selected="selected"{/if} value="Feature">Feature Code</option>
                <option {if $Type == "GroupPickup"}selected="selected"{/if} value="GroupPickup">Group Pickup</option>
                <option {if $Type == "IVR"}selected="selected"{/if} value="IVR">IVR</option>
				<option {if $Type == "Voicemail"}selected="selected"{/if} value="Voicemail">Voicemail Access</option>
				</optgroup>
				<optgroup label="Call Center">
				<option {if $Type == "Queue"}selected="selected"{/if} value="Queue">Call Queue</option>
				<option {if $Type == "Agent"}selected="selected"{/if} value="Agent">Call Queue Agent</option>
				<option {if $Type == "AgentLogin"}selected="selected"{/if} value="AgentLogin">Agent Login</option>
				</optgroup>
			</select>
		</td>
	</tr>

	<!-- Extension Template -->
	<tr id="FK_Template_tr">
		<td>
			Extension Template
		</td>
		<td>
			<select name="FK_Template">
			{foreach from=$Templates item=Template}
				<option {if $Template.PK_Template == $FK_Template}selected="selected"{/if} value="{$Template.PK_Template}">{$Template.Name}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- Feature Code -->
	<tr id="Feature_tr">
		<td>
			Feature Type
		</td>
		<td>
			<select name="Feature">
				<option {if $Feature == "FC_Voicemail"       }selected="selected"{/if} value="FC_Voicemail">Go To Voicemail</option>
				<option {if $Feature == "FC_DirectedPickup"  }selected="selected"{/if} value="FC_DirectedPickup">Directed Pickup</option>
				<option {if $Feature == "FC_CallMonitor"     }selected="selected"{/if} value="FC_CallMonitor">Call Monitor</option>
			</select>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="create">Create A New Extension</button>
</p>
</form>