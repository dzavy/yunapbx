<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
<script language="javascript">

{literal}
function LookupExternalIP() {
	$("#LookupExternalIPButton").attr('disabled', 'disabled');
	$("#LookupExternalIPButton").addClass('disabled');
	$.post('NetworkSettings_Ajax.php',
	{
		Action: "LookupExternalIP"
	},
	LookupExternalIP_Callback,
	"json");
}

function LookupExternalIP_Callback(data) {
	$("#Network_ExternalAddress").val(data['IP']);
	$("#LookupExternalIPButton").removeAttr('disabled');
	$("#LookupExternalIPButton").removeClass('disabled');
}

function ToggleAdvancedOptions() {
	$('.advanced2').toggleClass('hidden');
}

function UpdateProtocolFields(var_static) {
	if (var_static == 'static') {
		$("#Network_Address").removeAttr('disabled');
		$("#Network_Mask").removeAttr('disabled');
		$("#Network_Gateway").removeAttr('disabled');
		$("#Network_DNS_0").removeAttr('disabled');
		$("#Network_DNS_1").removeAttr('disabled');
	}
	else if (var_static == 'dhcp'){
		$("#Network_Address").attr('disabled', 'disabled');
		$("#Network_Mask").attr('disabled', 'disabled');
		$("#Network_Gateway").attr('disabled', 'disabled');
		$("#Network_DNS_0").attr('disabled', 'disabled');
		$("#Network_DNS_1").attr('disabled', 'disabled');
	}
}

function UpdateNatFields(nat_enabled) {
	if (nat_enabled) {
		$("#LookupExternalIPButton").removeAttr('disabled');
		$("#Network_ExternalAddress").removeAttr('disabled');
	} else {
		$("#LookupExternalIPButton").attr('disabled', 'disabled');
		$("#Network_ExternalAddress").attr('disabled', 'disabled');
	}
}

function AddLAN() {
		$("#Network_Additional_LAN").addOption($("#LAN").val(), $("#LAN").val(), false);
		$("#LAN").val('');
}

function DeleteLAN() {
		$("#Network_Additional_LAN").removeOption(/./, true);
}

function PreSubmit() {
	$("#Network_Additional_LAN").selectOptions(/./, true);
}

$(document).ready(function(){
	UpdateNatFields({/literal}{$Settings.Network_UseNAT}{literal});
	UpdateProtocolFields({/literal}'{$Settings.Network_Protocol}'{literal});
});
{/literal}

</script>

<h2>Network Settings</h2>

{if $Message == "SAVED_NETWORK_SETTINGS"}
	<p class="success_message">Successfully saved network settings.</p>
{/if}
{if $Errors.Network_Additional_LAN}
<p class="error_message">Some aditional local networks does not seam to be valid:
{foreach from=$Errors.Network_Additional_LAN item=Lan}<br /> * {$Lan}{/foreach}
</p>
{/if}

{if $Errors.Network_ExternalAddress}
<p class="error_message">You need to enter a valid external ip address</p>
{/if}

<p class="warning_message">
Changing your network settings can cause all active calls to be dropped. We do not recommend making changes to your network settings during business hours.
</p>

<form action="NetworkSettings.php" method="post" name="Network" onsubmit="PreSubmit()">
<table class="formtable">
	<tr>
		<td style="width:200px;">
			Method
		<td>
			<input type="radio" name="Network_Protocol" value="dhcp" id="DHCP" onclick="UpdateProtocolFields('dhcp')"{if $Settings.Network_Protocol=='dhcp'} checked="checked"{/if} / >
			<label for="DHCP">DHCP</label>
			<input type="radio" name="Network_Protocol" value="static" id="STATIC" onclick="UpdateProtocolFields('static')"{if $Settings.Network_Protocol=='static'} checked="checked"{/if} />
			<label for="STATIC">Static</label>
		</td>
	</tr>
	<tr>
		<td>
			IP Address
		</td>
		<td>
			<input type="text" id="Network_Address" name="Network_Address" value="{$Settings.Network_Address}"/>
		</td>
	</tr>
	<tr>
		<td>
			Network Mask
		</td>
		<td>
			<input type="text" id="Network_Mask" name="Network_Mask" value="{$Settings.Network_Mask}" />
		</td>
	</tr>
	<tr>
		<td style="width:200px;">
			Gateway
		</td>
		<td>
			<input type="text" name="Network_Gateway" id="Network_Gateway" value="{$Settings.Network_Gateway}" 				{if $Errors.Network_Gateway} class="error"{/if} />
		</td>
	</tr>
	<tr>
		<td>
			DNS Servers
		</td>
		<td>
			<input type="text" size="20" id="Network_DNS_0" name="Network_DNS[]" value="{$Settings.Network_DNS[0]}" {if $Errors.Network_DNS0} class="error"{/if} /> <BR>
			<input type="text" size="20" id="Network_DNS_1" name="Network_DNS[]" value="{$Settings.Network_DNS[1]}" {if $Errors.Network_DNS1} class="error"{/if} /> <BR>
		</td>
	</tr>
	<tr>
		<td>
			Use NAT
		</td>
		<td>
			<p>
				<input name="Network_UseNAT" value="1" {if $Settings.Network_UseNAT == 1}checked="checked"{/if} onclick="UpdateNatFields(1)" type="radio">
				Yes
				&nbsp;&nbsp;
				<input name="Network_UseNAT" value="0" {if $Settings.Network_UseNAT == 0}checked="checked"{/if} onclick="UpdateNatFields(0)" type="radio">
				No
			</p>
		</td>
	</tr>
	<tr>
		<td>
			External IP Address
		</td>
		<td>
			<input type="text" size="20" id="Network_ExternalAddress" name="Network_ExternalAddress" value="{$Settings.Network_ExternalAddress}" {if $Errors.Network_ExternalAddress} class="error"{/if} />
			<button type="button" onclick='LookupExternalIP()' id="LookupExternalIPButton">Look Up External IP</button>
		</td>
	</tr>

	<tr>
		<td>
			Additional Local Networks
		</td>
		<td>
			<input type="text" name="LAN" id="LAN" />
			<button type="button" onclick='AddLAN()' id="AddLANButton">Add New Network</button>
			<br />
			<select name="Network_Additional_LAN[]" id="Network_Additional_LAN" style="width: 160px; height: 150px;" multiple="multiple" {if $Errors.Network_Additional_LAN} class="error"{/if} />
				{foreach from=$Settings.Network_Additional_LAN item=LAN}
					<option label="" value="{$LAN}">{$LAN}</option>
				{/foreach}
			</select>

			<button type="button" onclick='DeleteLAN()' id="DeleteLANButton">Delete Network</button>
		</td>
	</tr>
</table>

<br />
<button type="submit" name="submit" value="update_network_settings">Update Network Settings</button>
</form>
