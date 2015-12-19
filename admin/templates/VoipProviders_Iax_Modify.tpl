<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}
	function popUp(url,inName,width,height)
	{
		inName = inName.replace(/ /g, "_"); // For stupid pos IE
		var popup = window.open('',inName,'width='+width+',height='+height+',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

		// only reload the page if it contains a new url
		if (popup.closed || !popup.document.URL || (-1 == popup.document.URL.indexOf(url)))
		{
			popup.location = url;
		}
		popup.focus();
		return popup;
	}

	function Display_Advanced_Toggle() {
		$('.toggle').toggleClass('hidden');
	}

	function PreSubmit() {
		$("#Hosts").selectOptions(/./, true);
	}

	function UpdateRSAFields(){
		switch ($("#AuthType").val())		{
			case 'rsa':
				$("#IncomingPassword").attr('disabled', 'disabled');
				$("#RSA_key").removeAttr('disabled');
				$("#upload_key").removeAttr('disabled');
				$("#rsa_text_1").removeAttr('style', 'color: gray;');
				$("#rsa_text_2").removeAttr('style', 'color: gray;');
				$("#rsa_text_3").removeAttr('style', 'color: gray;');
				break;
			default:
				$("#IncomingPassword").removeAttr('disabled');
				$("#RSA_key").attr('disabled', 'disabled');
				$("#upload_key").attr('disabled', 'disabled');
				$("#rsa_text_1").attr('style', 'color: gray;');
				$("#rsa_text_2").attr('style', 'color: gray;');
				$("#rsa_text_3").attr('style', 'color: gray;');
		}
	}

	function UpdateTSFields(val){
		if  (val == 1){
				$("#LocalUser").removeAttr('disabled', 'disabled');
				$("#JabberHostname").removeAttr('disabled', 'disabled');
				$("#LocalUserTxt").removeAttr('style', 'color: gray;');
				$("#JabberTxt").removeAttr('style', 'color: gray;');
				$("#Txt1").removeAttr('style', 'color: gray;');
				$("#Txt2").removeAttr('style', 'color: gray;');
		} else{
				$("#LocalUser").attr('disabled', 'disabled');
				$("#JabberHostname").attr('disabled', 'disabled');
				$("#LocalUserTxt").attr('style', 'color: gray;');
				$("#JabberTxt").attr('style', 'color: gray;');
				$("#Txt1").attr('style', 'color: gray;');
				$("#Txt2").attr('style', 'color: gray;');

		}
	}

	function UpdateFaxFields(val){
		if  (val == 1){
				$("#MinRateFax").removeAttr('disabled', 'disabled');
				$("#MaxRateFax").removeAttr('disabled', 'disabled');
				$("#Txt3").removeAttr('style', 'color: gray;');
				$("#Txt4").removeAttr('style', 'color: gray;');
		} else{
				$("#MinRateFax").attr('disabled', 'disabled');
				$("#MaxRateFax").attr('disabled', 'disabled');
				$("#Txt3").attr('style', 'color: gray;');
				$("#Txt4").attr('style', 'color: gray;');
		}
	}

	function UpdateCallRules(val){
		if  (val == 0){
				$("#OutgoingTbl :input").removeAttr('disabled');
				$("#OutgoingTbl td").removeClass('disabled');
				$("#CallbackExtension").attr('disabled', 'disabled');
		} else{
				$("#OutgoingTbl :input").attr('disabled', 'disabled');
				$("#OutgoingTbl td").addClass('disabled');
				$("#CallbackExtension").removeAttr('disabled');
		}
	}

	function RegisterType_OnChange() {
		var RegisterType = $("#RegisterType").val();
		if(RegisterType == 'Client') {
			$("#Host").attr('disabled', 'disabled');
		} else {
			$("#Host").removeAttr('disabled');
		}
	}

$(document).ready(function() {
	UpdateRSAFields();
	{/literal}

	{if $Provider.TelesoftPBX} UpdateTSFields (1);
	{else}     		           UpdateTSFields (0);
	{/if}

	{if $Provider.ApplyIncomingRules}	UpdateCallRules(1)	;
	{else}								UpdateCallRules(0)	;
	{/if}

	{if $Errors.Hosts.Invalid} Display_Advanced_Toggle(); {/if}

	{if $Provider.ErrorCorrection} UpdateFaxFields(1);
	{else} UpdateFaxFields(0);
	{/if}

	{literal}
})
{/literal}
</script>


<h2>VOIP Providers</h2>
{if $Errors.Name.Invalid}
<p class="error_message">IAX Provider Name is required ( 1-32 characters in length).</p>
{/if}
{if $Errors.Label.Invalid}
<p class="error_message">IAX Provider label is required and may only contain alpha-numeric characters and underscores and must be 1-32 characters in length.</p>
{/if}
{if $Errors.AccountID.Invalid}
<p class="error_message">Account ID is required (1-32 characters in length, no spaces allowed).</p>
{/if}
{if $Errors.Password.Invalid}
<p class="error_message">Password is required (1-32 characters in length).</p>
{/if}
{if $Errors.CallbackExtension.Invalid}
<p class="error_message">A Callback Extension is required (3-5 digits in length).</p>
{/if}
{if $Errors.CallbackExtension.NoMatch}
<p class="error_message">That is not a valid extension in the system.</p>
{/if}
{if $Errors.Hosts.Invalid}
<p class="error_message">Invalid Hostname or IP Address in IAX Provider Host List.</p>
{/if}

<form action="VoipProviders_Iax_Modify.php" method="post" onsubmit="PreSubmit()" enctype="multipart/form-data">
<p>	<input type="hidden" name="PK_IaxProvider" value="{$Provider.PK_IaxProvider}" /> </p>

<strong>Modify IAX Provider</strong>
<table class="formtable">
	<!-- Iax Provider Name -->
	<tr>
		<td>
			IAX Provider Name
		</td>
		<td>
			<input type="text" name="Name" value="{$Provider.Name}" {if $Errors.Name}class="error"{/if} />
		</td>
	</tr>
	<tr>
		<td>
			IAX Provider Label
		</td>
		<td>
			<input type="text" name="Label" value="{$Provider.Label}" {if $Errors.Label}class="error"{/if} />
		</td>
	</tr>

	<!-- Your Account ID -->
	<tr>
		<td>
			Your Account ID
		</td>
		<td>
			<input type="text" name="AccountID" value="{$Provider.AccountID}" {if $Errors.AccountID}class="error"{/if} />
		</td>
	</tr>

	<!-- Your password -->
	<tr>
		<td>
			Your Password
		</td>
		<td>
			<input type="password" name="Password" value="{$Provider.Password}" {if $Errors.Password}class="error"{/if} />
		</td>
	</tr>

	<!-- Hostname/IP Address -->
	<tr>
		<td>
			Hostname/IP Address
		</td>
		<td>
			<input type="text" name="Host" id="Host" value="{$Provider.Host}" />
		</td>
	</tr>

	<!-- Callback Extension -->
	<tr>
		<td>
			Default Extension
		</td>
		<td>
			<input type="text" {if $Errors.CallbackExtension}class="error"{/if} id="CallbackExtension" name="CallbackExtension" value="{$Provider.CallbackExtension}" size="6" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=CallbackExtension','Select Extension',415,400);">&nbsp;</button>
		</td>
	</tr>

	<!-- Incoming Authentication Type -->
	<tr>
		<td>
			<label for="AuthType">Incoming Authentication Type</label>
		</td>
		<td>
			<select name="AuthType" id="AuthType" onclick="UpdateRSAFields()">
				<option value="md5"       {if $Provider.AuthType=='md5'} selected="selected"{/if}>MD5</option>
				<option value="rsa"       {if $Provider.AuthType=='rsa'} selected="selected"{/if}>RSA</option>
				<option value="plaintext" {if $Provider.AuthType=='plaintext'} selected="selected"{/if}>Plain Text</option>
			</select>
		</td>
	</tr>

	<!-- RSA Authentication Key -->
	<tr >
		<td>
			<span id="rsa_text_1" >RSA Authentication Key</span>
		</td>
		<td>
			<span id="rsa_text_2">Use Existing Key:  </span>
			<select name="RSA_key" id="RSA_key">
			{foreach from=$Keys item=Key}
			            <option value="{$Key.PK_Key}" id="ProviderKey_{$Key.PK_Key}"
							{if $Key.PK_Key == $Provider.Key} selected='selected'{/if}	>
									{$Key.Name}
						</option>
			{/foreach}
			</select>
			<br>
			<span id="rsa_text_3">or Upload New Key: </span>
				<input name="upload_key[]" type="file">
		</td>
	</tr>

	<!-- Caller ID Name -->
	<tr>
		<td>
			Caller ID Name
		</td>
		<td>
			<input id="CallerIDName" type="text" name="CallerIDName" value="{$Provider.CallerIDName}" />
		</td>
	</tr>

	<!-- Caller ID Number -->
	<tr >
		<td>
			Caller ID Number
		</td>
		<td>
			<input id="CallerIDNumber" type="text" name="CallerIDNumber" value="{$Provider.CallerIDNumber}" />
		</td>
	</tr>

	<!-- -------------------- -->

	<!-- Click to Show Advanced Options -->
	<tr class="toggle">
		<td></td>
		<td>
			<a href="javascript:Display_Advanced_Toggle()">
				<img src="../static/images/right-arrow.gif" alt="[+]" />
				Click to show advanced options
			</a>
		</td>
	</tr>

	<!-- Click to Hide Advanced Options -->
	<tr class="toggle hidden">
		<td></td>
		<td>
			<a href="javascript:Display_Advanced_Toggle()">
				<img src="../static/images/down-arrow.gif" alt="[-]" />
				Click to hide advanced options
			</a>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr class="toggle hidden">
		<td colspan="2" class="caption">
			<img src="../static/images/1.gif"/>
			<strong>Peer Settings</strong>
		</td>
	</tr>

	<!-- Registration Type -->
	<tr class="toggle hidden">
		<td>
			Registration Type
		</td>
		<td>
			<select name="RegisterType" id="RegisterType" onchange="RegisterType_OnChange()">
				<option {if $Provider.RegisterType=='Provider'}selected="selected"{/if} value="Provider">Provider</option>
				<option {if $Provider.RegisterType=='Client'  }selected="selected"{/if} value="Client">Client</option>
				<option {if $Provider.RegisterType=='Peer'    }selected="selected"{/if} value="Peer">Peer</option>
			</select>
		</td>
	</tr>

	<!-- ApplyIncomingRules -->
	<tr class="toggle hidden">
		<td>
			Apply Incoming Call Rules
		</td>
		<td>
			<input type="radio" value="1" id="ApplyIncomingRules_1" name="ApplyIncomingRules" {if $Provider.ApplyIncomingRules}checked="checked"{/if} onclick="UpdateCallRules(1)" />
			<label for="ApplyIncomingRules_1">Yes</label>
			&nbsp;
			<input type="radio" value="0" id="ApplyIncomingRules_0" name="ApplyIncomingRules" {if !$Provider.ApplyIncomingRules}checked="checked"{/if} onclick="UpdateCallRules(0)" />
			<label for="ApplyIncomingRules_0">No</label>
		</td>
	</tr>

	<!-- Outgoing Call Rules -->
	<tr class="toggle hidden">
		<td>
			Outgoing Call Rules
		</td>
		<td>
			<table id="OutgoingTbl">
				<tr>
					<td>Rule Name</td>
					<td>Allow    </td>
					<td>Deny     </td>
				</tr>

				{foreach from=$Rules item=Rule}
				<tr class='{cycle values="even,odd"}'>
					<td>{$Rule.Name}</td>
					<td style="width: 20px;">
						<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="{$Rule.PK_OutgoingRule}" {if $Rule.PK_OutgoingRule|in_array:$Provider.Rules}checked="checked"{/if}/>
					</td>
					<td style="width: 20px;">
						<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="0" {if !$Rule.PK_OutgoingRule|in_array:$Provider.Rules}checked="checked"{/if}/>
					</td>
				</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr class="toggle hidden">
		<td colspan="2" class="caption">
			<img src="../static/images/2.gif"/>
			<strong>Connection Settings</strong>
		</td>
	</tr>

	<!-- Primary Outbound Host -->
	<tr class="toggle hidden">
		<td>
			Primary Outbound Host
		</td>
		<td>
			<input type="text" name="PrOutbHost" value="{$Provider.PrOutbHost}" {if $Errors.PrOutbHostt} class="error"{/if} />
		</td>
	</tr>

	<!-- Secondary Outbound Host -->
	<tr class="toggle hidden">
		<td>
			Secondary Outbound Host
		</td>
		<td>
			<input type="text" name="SecOutbHost" value="{$Provider.SecOutbHost}" {if $Errors.SecOutbHost}class="error"{/if} />
		</td>
	</tr>

	<!-- Tertiary Outbound Host -->
	<tr class="toggle hidden">
		<td>
			Tertiary Outbound Host
		</td>
		<td>
			<input type="text" name="TertOutbHost" value="{$Provider.TertOutbHost}" {if $Errors.TertOutbHost} class="error"{/if} />
		</td>
	</tr>

	<!-- Incoming Password -->
	<tr class="toggle hidden">
		<td>
			Incoming Password
		</td>
		<td>
			<input type="password" name="IncomingPassword" id="IncomingPassword" value="{$Provider.IncomingPassword}" {if $Errors.IncomingPassword}class="error"{/if} />
		</td>
	</tr>

	<!-- Qualify Host -->
	<tr class="toggle hidden">
		<td>
			Qualify Host
		</td>
		<td>
			<label><input type="radio" value="1" name="Qualify" {if $Provider.Qualify} checked="checked"{/if}/>Yes</label>
			&nbsp;
			<label><input type="radio" value="0" name="Qualify" {if !$Provider.Qualify} checked="checked"{/if} />No</label>
		</td>
	</tr>

	<!-- Enable Jitterbuffer -->
	<tr class="toggle hidden">
		<td>
			Enable Jitterbuffer
		</td>
		<td>
			<label><input type="radio" value="1" name="Jitterbuffer" {if $Provider.Jitterbuffer} checked="checked"{/if}/>Yes</label>
			&nbsp;
			<label><input type="radio" value="0" name="Jitterbuffer" {if !$Provider.Jitterbuffer} checked="checked"{/if} />No</label>
		</td>
	</tr>

	<!-- Enable Trunking -->
	<tr class="toggle hidden">
		<td>
			Enable Trunking
		</td>
		<td>
			<label><input type="radio" value="1" name="Trunking" {if $Provider.Trunking} checked="checked"{/if} />Yes</label>
			&nbsp;
			<label><input type="radio" value="0" name="Trunking" {if !$Provider.Trunking} checked="checked"{/if} />No</label>
		</td>
	</tr>

	<!-- Use RSA Authentication for Outgoing -->
	<tr class="toggle hidden">
		<td>
			Use RSA Authentication for Outgoing
		</td>
		<td>
			<label><input type="radio" value="1" name="RSA_auth" {if $Provider.RSA_auth} checked="checked"{/if} />Yes</label>
			&nbsp;
			<label><input type="radio" value="0" name="RSA_auth" {if !$Provider.RSA_auth} checked="checked"{/if} />No</label>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr class="toggle hidden">
		<td colspan="2" class="caption">
			<img src="../static/images/3.gif"/>
			<strong>Call Settings</strong>
		</td>
	</tr>

	<!-- Provider Codecs -->
	<tr class="toggle hidden">
		<td>
			Provider Codecs
		</td>
		<td>
			<table>
			{foreach from=$Codecs item=Codec}
				{cycle values="<tr>,," name="tr1"}
					<td  style="font-size: 10px; padding: 2px;">
						<input type="checkbox" name="Codecs[]" value="{$Codec.PK_Codec}" id="ProviderCodec_{$Codec.PK_Codec}"
							{if $Codec.PK_Codec|in_array:$Provider.Codecs } checked="checked" {/if}
						/>
						<label for="ProviderCodec_{$Codec.PK_Codec}">{$Codec.Description}</label>
						{if $Codec.Recomended == "1"}
							<small >(Default)</small>
						{/if}
					</td>
				{cycle values=",,</tr>" name="tr2"}
			{/foreach}
			</table>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr class="toggle hidden">
		<td  colspan="2" class="caption">
			<img src="../static/images/4.gif"/>
			<strong>Fax Settings</strong>
		</td>
	</tr>
	<tr class="toggle hidden">
		<td colspan=2>
			<p class="warning_message">It is not recommended to send faxes over IAX connections.</p>
		</td>
	</tr>

	<!-- Error Correction Mode for G711 Faxes -->
	<tr class="toggle hidden">
		<td>
			Error Correction Mode for <br> G711 Faxes
		</td>
		<td>
			<label><input type="radio" name="ErrorCorrection" id="ErrorCorrection" {if $Provider.ErrorCorrection} checked="checked"{/if} value="1" onclick="UpdateFaxFields(1)"/> Enabled</label>
			&nbsp;
			<label><input type="radio" name="ErrorCorrection" id="ErrorCorrection" {if !$Provider.ErrorCorrection} checked="checked"{/if} value="0" onclick="UpdateFaxFields(0)"/> Disabled</label>
		</td>
	</tr>

	<!-- Minimum transfer rate for fax transmissions -->
	<tr class="toggle hidden">
		<td>
			<span id="Txt3">Minimum transfer rate for <br/> fax transmissions</span>
		</td>
		<td>
			<input type="text" id="MinRateFax" name="MinRateFax" value="{$Provider.MinRateFax}" {if $Errors.MinRateFax}class="error"{/if} />
		</td>
	</tr>
	<!-- Maximum transfer rate for fax transmissions -->
	<!-- Maximum transfer rate for fax transmissions -->
	<tr class="toggle hidden">
		<td>
			<span id="Txt4">Maximum transfer rate for <br> fax transmissions</span>
		</td>
		<td>
			<input type="text" id="MaxRateFax" name="MaxRateFax" value="{$Provider.MaxRateFax}" {if $Errors.MinRateFax} class="error"{/if} />
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	{if $Provider.PK_IaxProvider == ""}
	<button type="submit" name="submit" value="save">Add Iax Provider</button>
	{else}
	<button type="submit" name="submit" value="save">Modify Iax Provider</button>
	{/if}
</p>
</form>
