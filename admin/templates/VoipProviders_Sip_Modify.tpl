<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
    {literal}
    function popUp(url, inName, width, height)
    {
        inName = inName.replace(/ /g, "_"); /* For stupid pos IE */
        var popup = window.open('', inName, 'width=' + width + ',height=' + height + ',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

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

    function CallerID_Enable() {
        $('#CallerIDName').removeAttr("disabled");
        $('#CallerIDNumber').removeAttr("disabled");
        $('#CallerIDNumber').removeClass("disabled");
        $('#CallerIDName').removeClass("disabled");
    }
    function CallerID_Disable() {
        $('#CallerIDName').attr("disabled", "disabled");
        $('#CallerIDNumber').attr("disabled", "disabled");
        $('#CallerIDNumber').addClass("disabled");
        $('#CallerIDName').addClass("disabled");
    }

    function AddHost() {
        $("#Hosts").addOption($("#SipHost").val(), $("#SipHost").val(), false);
        $("#SipHost").val('');
    }
    function DeleteHost() {
        $("#Hosts").removeOption(/./, true);
    }

    function PreSubmit() {
        $("#Hosts").selectOptions(/./, true);
    }

    function UpdateTSFields(val) {
        if (val == 1) {
            $("#LocalUser_TD").removeClass('disabled');
            $("#LocalUser_TD input").removeAttr('disabled');
            $("#JabberHostname").removeAttr('disabled', 'disabled');
        } else {
            $("#LocalUser_TD").addClass('disabled');
            $("#LocalUser_TD input").attr('disabled', 'disabled');
            $("#JabberHostname").attr('disabled', 'disabled');
        }
    }

    function UpdateCallRules(val) {
        if (val == 0) {
            $("#OutgoingTbl :input").removeAttr('disabled');
            $("#OutgoingTbl td").removeClass('disabled');
            $("#CallbackExtension").attr('disabled', 'disabled');
        } else {
            $("#OutgoingTbl :input").attr('disabled', 'disabled');
            $("#OutgoingTbl td").addClass('disabled');
            $("#CallbackExtension").removeAttr('disabled');
        }
    }

    function UpdateFaxFields(val) {
        if (val == 1) {
            $("#MinRateFax").removeAttr('disabled', 'disabled');
            $("#MaxRateFax").removeAttr('disabled', 'disabled');
            $("#RSP_fax").removeAttr('disabled', 'disabled');
            $("#RIP_fax").removeAttr('disabled', 'disabled');
            $("#MaxDelayFax").removeAttr('disabled', 'disabled');
        } else {
            $("#MinRateFax").attr('disabled', 'disabled');
            $("#MaxRateFax").attr('disabled', 'disabled');
            $("#RSP_fax").attr('disabled', 'disabled');
            $("#RIP_fax").attr('disabled', 'disabled');
            $("#MaxDelayFax").attr('disabled', 'disabled');
        }
    }

    $(document).ready(function () {

    {/literal}
    {if $Provider.TelesoftPBX} UpdateTSFields (1);
    {else}
    UpdateTSFields (0);
    {/if}

    {if $Provider.ApplyIncomingRules}	UpdateCallRules(1);
    {else}
    UpdateCallRules(0);
    {/if}

    {if $Provider.ErrorCorrection} UpdateFaxFields(1);
    {else}
    UpdateFaxFields(0);
    {/if}

    {literal}
            })

    {/literal}
</script>

<h2>VOIP Providers</h2>
{if $Errors.Name.Invalid}
    <p class="error_message">SIP Provider Name is required (1-32 characters in length).</p>
{/if}
{if $Errors.Password.Invalid}
    <p class="error_message">Password is required (1-32 characters in length).</p>
{/if}
{if $Errors.AccountID.Invalid}
    <p class="error_message">Account ID is required (1-32 characters in length, no spaces allowed).</p>
{/if}
{if $Errors.Host.Invalid}
    <p class="error_message">A Hostname/IP Address is required (1-64 characters in length).</p>
{/if}
{if $Errors.CallbackExtension.Invalid}
    <p class="error_message">A Callback Extension is required (3-5 digits in length).</p>
{/if}
{if $Errors.CallbackExtension.NoMatch}
    <p class="error_message">That is not a valid extension in the system.</p>
{/if}
{if $Errors.Hosts.Invalid}
    <p class="error_message">Invalid Hostname or IP Address in SIP Provider Host List.</p>
{/if}

<form action="VoipProviders_Sip_Modify.php" method="post" onsubmit="PreSubmit()">
    <p>
        <input type="hidden" name="PK_SipProvider" value="{$Provider.PK_SipProvider}" />
    </p>

    {if $Provider.PK_SipProvider == ""}
        <strong>Add a New SIP Provider</strong>
    {else}
        <strong>Modify SIP Provider</strong>
    {/if}
    <table class="formtable">
        <!-- Sip Provider Name -->
        <tr>
            <td>
                Provider Name
            </td>
            <td>
                <input type="text" name="Name" value="{$Provider.Name}" {if $Errors.Name}class="error"{/if} />
            </td>
        </tr>

        <!-- Your Account ID -->
        <tr>
            <td>
                Account ID
            </td>
            <td>
                <input type="text" name="AccountID" value="{$Provider.AccountID}" {if $Errors.AccountID}class="error"{/if} />
            </td>
        </tr>

        <!-- Your password -->
        <tr>
            <td>
                Password
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
                <input type="text" name="Host" value="{$Provider.Host}" {if $Errors.Host}class="error"{/if} />
            </td>
        </tr>

        <!-- Callback Extension -->
        <tr>
            <td>
                Default Voice Extension
            </td>
            <td>
                <input type="text" {if $Errors.CallbackExtension}class="error"{/if} id="CallbackExtension" name="CallbackExtension" value="{$Provider.CallbackExtension}" size="6" />
                <button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=CallbackExtension', 'Select Extension', 415, 330);">&nbsp;</button>
            </td>
        </tr>

        <!-- Default Fax Extension -->
        <tr>
            <td>
                Default Fax Extension
            </td>
            <td>
                <input type="text" {if $Errors.DefFaxExtension}class="error"{/if} id="DefFaxExtension" name="DefFaxExtension" value="{$Provider.DefFaxExtension}" size="6" />
                <button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=DefFaxExtension', 'Select Extension', 415, 330);">&nbsp;</button>
            </td>
        </tr>

        <!-- DTMF Mode -->
        <tr>
            <td>
                <label for="FK_DTMFMode">DTMF Mode</label>
            </td>
            <td>
                <select name="FK_DTMFMode" id="FK_DTMFMode">
                    {foreach from=$DTMFModes item=DTMFMode}
                        <option value="{$DTMFMode.PK_DTMFMode}" {if $Provider.FK_DTMFMode == $DTMFMode.PK_DTMFMode }selected="selected"{/if} >{$DTMFMode.Description}</option>
                    {/foreach}
                </select>
            </td>
        </tr>

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

        <!-- Host Type -->
        <tr class="toggle hidden">
            <td>
                Host Type
            </td>
            <td>
                <select name="HostType">
                    <option {if $Provider.HostType=='Provider'}selected="selected"{/if} value="Provider">Provider</option>
                    <option {if $Provider.HostType=='Peer'}selected="selected"{/if} value="Peer">Peer</option>
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
                <strong>Caller ID Settings</strong>
            </td>
        </tr>

        <!-- Support Changing Caller ID -->
        <tr class="toggle hidden">
            <td>
                Support Changing Caller ID
            </td>
            <td>
                <label><input onclick="CallerID_Enable()" type="radio" value="1" name="CallerIDChange" {if $Provider.CallerIDChange}checked="checked"{/if} />Yes</label>
                &nbsp;
                <label><input onclick="CallerID_Disable()" type="radio" value="0" name="CallerIDChange" {if ! $Provider.CallerIDChange}checked="checked"{/if} />No</label>
            </td>
        </tr>

        <!-- Caller-ID method -->
        <tr class="toggle hidden">
            <td>
                Caller ID method
            </td>
            <td>
                <select name="CallerIDMethod">
                    <option {if $Provider.CallerIDMethod=='FromHeader'} selected="selected"{/if}
                                                                        value="FromHeader"> From Header</option>
                    <option {if $Provider.CallerIDMethod=='P-Asserted-Identity'} selected="selected"{/if}
                                                                                 value="P-Asserted-Identity"> P-Asserted-Identity</option>
                    <option {if $Provider.CallerIDMethod=='Remote-Party-ID'} selected="selected"{/if}
                                                                             value="Remote-Party-ID"> Remote-Party-ID</option>
                </select>
            </td>
        </tr>

        <!-- Caller ID Name -->
        <tr class="toggle hidden">
            <td>
                Caller ID Name
            </td>
            <td>
                <input {if ! $Provider.CallerIDChange}disabled="disabled" class="disabled"{/if} id="CallerIDName" type="text" name="CallerIDName" value="{$Provider.CallerIDName}" />
            </td>
        </tr>

        <!-- Caller ID Number -->
        <tr class="toggle hidden">
            <td>
                Caller ID Number
            </td>
            <td>
                <input {if ! $Provider.CallerIDChange}disabled="disabled" class="disabled"{/if} id="CallerIDNumber" type="text" name="CallerIDNumber" value="{$Provider.CallerIDNumber}" />
            </td>
        </tr>
    </table>

    <table class="formtable">
        <tr class="toggle hidden">
            <td colspan="2" class="caption">
                <img src="../static/images/3.gif"/>
                <strong>Connection Settings</strong>
            </td>
        </tr>

        <!-- Sip Port -->
        <tr class="toggle hidden">
            <td>
                SIP Port
            </td>
            <td>
                <input type="text" name="SipPort" value="{$Provider.SipPort}" />
            </td>
        </tr>

        <!-- Sip Expiry -->
        <tr class="toggle hidden">
            <td>
                Sip Expiry <small>(in seconds)</small>
            </td>
            <td>
                <input type="text" name="SipExpiry" value="{$Provider.SipExpiry}" />
            </td>
        </tr>

        <!-- Proxy Host -->
        <tr class="toggle hidden">
            <td>
                Proxy Host
            </td>
            <td>
                <input type="text" name="ProxyHost" value="{$Provider.ProxyHost}" />
            </td>
        </tr>

        <!-- Proxy Host -->
        <tr class="toggle hidden">
            <td>
                Authentication User
            </td>
            <td>
                <input type="text" name="AuthUser" value="{$Provider.AuthUser}" />
            </td>
        </tr>

        <!-- Always Trust this Provider -->
        <tr class="toggle hidden">
            <td>
                Always Trust this Provider
            </td>
            <td>
                <label><input type="radio" value="1" name="AlwaysTrust" {if $Provider.AlwaysTrust}checked="checked"{/if} />Yes</label>
                &nbsp;
                <label><input type="radio" value="0" name="AlwaysTrust" {if ! $Provider.AlwaysTrust}checked="checked"{/if} />No</label>
            </td>
        </tr>

        <!-- Qualify Host -->
        <tr class="toggle hidden">
            <td>
                Qualify Host
            </td>
            <td>
                <input type="radio" value="1" name="Qualify" id="Qualify_1" {if $Provider.Qualify==1}checked="checked"{/if} />
                <label for="Qualify_1">Yes</label>
                &nbsp;
                <input type="radio" value="0" name="Qualify" id="Qualify_0" {if $Provider.Qualify!=1}checked="checked"{/if} />
                <label for="Qualify_0">No</label>
            </td>
        </tr>

        <!-- Include user=phone in SIP -->
        <tr class="toggle hidden">
            <td>
                Include user=phone in SIP
            </td>
            <td>
                <input type="radio" value="1" name="UserEqPhone" id="UserEqPhone_1" {if $Provider.UserEqPhone==1} checked="checked"{/if} />
                <label for="UserEqPhone_1">Yes</label>
                &nbsp;
                <input type="radio" value="0" name="UserEqPhone" id="UserEqPhone_0" {if $Provider.UserEqPhone!=1} checked="checked"{/if} />
                <label for="UserEqPhone_0">No</label>
            </td>
        </tr>

        <!-- Use Local Address in From Header -->
        <tr class="toggle hidden">
            <td>
                Use Local Address in From Header
            </td>
            <td>
                <input type="radio" value="1" name="LocalAddrFrom" id="LocalAddrFrom_1" {if $Provider.LocalAddrFrom==1} checked="checked"{/if}/>
                <label for="LocalAddrFrom_1">Yes</label>
                &nbsp;
                <input type="radio" value="0" name="LocalAddrFrom" id="LocalAddrFrom_0" {if $Provider.LocalAddrFrom!=1} checked="checked"{/if} />
                <label for="LocalAddrFrom_0">No</label>
            </td>
        </tr>

        <!-- SIP Provider Host List -->
        <tr class="toggle hidden">
            <td>
                SIP Provider Host List
            </td>
            <td>
                <input type="text" name="SipHost" id="SipHost" style="width: 180px;"/>
                <a href="javascript:AddHost()">Add New Host</a>
                <br />
                <select {if $Errors.Hosts}class="error"{/if} multiple="multiple" id="Hosts" name="Hosts[]" style="width: 180px; height: 150px;">
                    {foreach from=$Provider.Hosts item=Host}
                        <option value="{$Host}">{$Host}</option>
                    {/foreach}
                </select>
                <a href="javascript:DeleteHost()">Delete Host</a>
            </td>
        </tr>
    </table>

    <table class="formtable">
        <tr class="toggle hidden">
            <td colspan="2" class="caption">
                <img src="../static/images/4.gif"/>
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
                                <small style="color: #666;">(Default)</small>
                            {/if}
                        </td>
                        {cycle values=",,</tr>" name="tr2"}
                    {/foreach}
                </table>
            </td>
        </tr>

        <!-- DTMF Dial -->
        <tr class="toggle hidden">
            <td>
                Dial Using DTMF Tones
            </td>
            <td>
                <label><input type="radio" value="1" name="DTMFDial" {if $Provider.DTMFDial}checked="checked"{/if} />Yes</label>
                &nbsp;
                <label><input type="radio" value="0" name="DTMFDial" {if ! $Provider.DTMFDial}checked="checked"{/if} />No</label>
            </td>
        </tr>



        <!-- Map Distinctive Rings -->
        <tr class="toggle hidden">
            <td>
                Map Distinctive Rings
            </td>
            <td>
                <table class="nostyle">
                    {assign var=NrMapRing value=1}
                    {foreach from=$Provider.MapRings item=MapRing}
                        <tr>
                            <td>Ring #{$NrMapRing} maps to number
                                <input type="text" name="MapRing[{$NrMapRing-1}]" value="{$MapRing}" />
                            </td>
                            {assign var=NrMapRing value=$NrMapRing+1}
                        </tr>
                    {/foreach}
                </table>
            </td>
        </tr>

        <!-- Enable Jitterbuffer -->
        <tr class="toggle hidden">
            <td>
                Enable Jitterbuffer
            </td>
            <td>
                <select name="Jitterbuffer">
                    <option value="never"
                            {if $Provider.Jitterbuffer == 'never'} selected="selected"{/if} >
                        Never
                    </option>
                    <option value="yes"
                            {if $Provider.Jitterbuffer == 'yes'} selected="selected"{/if} >
                        When Needed
                    </option>
                    <option value="always"
                            {if $Provider.Jitterbuffer == 'always'} selected="selected"{/if} >
                        Always
                    </option>
                    <option value="fixed"
                            {if $Provider.Jitterbuffer == 'fixed'} selected="selected"{/if} >
                        When Needed with Fixed Window
                    </option>
                    <option value="alwaysfixed"
                            {if $Provider.Jitterbuffer == 'alwaysfixed'} selected="selected"{/if} >
                        Always with Fixed Window
                    </option>
                </select>
            </td>
        </tr>

        <!-- Allow Reinvite -->
        <tr class="toggle hidden">
            <td>
                Allow Reinvite
            </td>
            <td>
                <select name="Reinvite" >
                    <option value="no"
                            {if $Provider.Reinvite == 'no'} selected="selected"{/if} >
                        Never
                    </option>
                    <option value="yes"
                            {if $Provider.Reinvite == 'yes'} selected="selected"{/if} >
                        Always
                    </option>
                    <option value="nonat"
                            {if $Provider.Reinvite == 'nonat'} selected="selected"{/if} >
                        Non-NAT Connections Only
                    </option>
                    <option value="update"
                            {if $Provider.Reinvite == 'update'} selected="selected"{/if} >
                        Always, Using the UPDATE method
                    </option>
                    <option value="nonat-update"
                            {if $Provider.Reinvite == 'nonat-update'} selected="selected"{/if} >
                        Non-NAT Connections Only, Using the UPDATE method
                    </option>
                </select>
            </td>
        </tr>

        <!-- Always Send Early Media -->
        <tr class="toggle hidden">
            <td>
                Always Send Early Media
            </td>
            <td>
                <label><input type="radio" value="1" name="SendEarlyMedia" {if $Provider.SendEarlyMedia}checked="checked"{/if} />Yes</label>
                &nbsp;
                <label><input type="radio" value="0" name="SendEarlyMedia" {if !$Provider.SendEarlyMedia}checked="checked"{/if} />No</label>
            </td>
        </tr>
    </table>

    <table class="formtable">
        <tr class="toggle hidden">
            <td colspan="2" class="caption">
                <img src="../static/images/5.gif"/>
                <strong>Fax Settings</strong>
            </td>
        </tr>
        <tr class="toggle hidden">
            <td colspan="2">
                <p class="warning_message">It is recommended to only send T.38 faxes over SIP.</p>
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
                Minimum transfer rate for <br> fax transmissions
            </td>
            <td>
                <input type="text" id="MinRateFax" name="MinRateFax" value="{$Provider.MinRateFax}" {if $Errors.MinRateFax}class="error"{/if} />
            </td>
        </tr>

        <!-- Maximum transfer rate for fax transmissions -->
        <tr class="toggle hidden">
            <td>
                Maximum transfer rate for <br> fax transmissions
            </td>
            <td>
                <input type="text" id="MaxRateFax" name="MaxRateFax" value="{$Provider.MaxRateFax}" {if $Errors.MinRateFax} class="error"{/if} />
            </td>
        </tr>

        <!-- Redundant signal packets for T38 fax sessions -->
        <tr class="toggle hidden">
            <td>
                Redundant signal packets for T38 fax sessions
            </td>
            <td>
                <input type="text" id="RSP_fax" name="RSP_fax" value="{$Provider.RSP_fax}" {if $Errors.RSP_fax} class="error"{/if} />
            </td>
        </tr>

        <!-- Redundant image packets for T38 fax sessions -->
        <tr class="toggle hidden">
            <td>
                Redundant image packets for T38 fax sessions
            </td>
            <td>
                <input type="text" id="RIP_fax" name="RIP_fax" value="{$Provider.RIP_fax}" {if $Errors.RIP_fax} class="error"{/if} />
            </td>
        </tr>

        <!-- Maximum expected T38 packet delay -->
        <tr class="toggle hidden">
            <td>
                Maximum expected T38 packet delay
            </td>
            <td>
                <input type="text" id="MaxDelayFax" name="MaxDelayFax" value="{$Provider.MaxDelayFax}" {if $Errors.MaxDelayFax} class="error"{/if} />
            </td>
        </tr>
    </table>

    <!-- Submit -->
    <p>
        <br />
        {if $Provider.PK_SipProvider == ""}
            <button type="submit" name="submit" value="save">Add SIP Provider</button>
        {else}
            <button type="submit" name="submit" value="save">Modify SIP Provider</button>
        {/if}
    </p>
</form>
