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

    $(document).ready(function () {

    {/literal}

    {if $Dongle.ApplyIncomingRules}	UpdateCallRules(1);
    {else}
    UpdateCallRules(0);
    {/if}

    {literal}
    })

    {/literal}
</script>

<h2>3G Dongles</h2>
{if $Errors.Name.Invalid}
    <p class="error_message">Dongle Name is required (1-32 characters in length).</p>
{/if}
{if $Errors.IMEI.Invalid}
    <p class="error_message">IMEI is required (15-16 characters in length).</p>
{/if}
{if $Errors.IMSI.Invalid}
    <p class="error_message">IMSI is required (14-15 characters in length).</p>
{/if}
{if $Errors.CallbackExtension.Invalid}
    <p class="error_message">A Callback Extension is required (3-5 digits in length).</p>
{/if}
{if $Errors.CallbackExtension.NoMatch}
    <p class="error_message">That is not a valid extension in the system.</p>
{/if}

<form action="Dongles_Modify.php" method="post" onsubmit="PreSubmit()">
    <p>
        <input type="hidden" name="PK_Dongle" value="{$Dongle.PK_Dongle}" />
    </p>

    {if $Dongle.PK_Dongle == ""}
        <strong>Add a New 3G Dongle</strong>
    {else}
        <strong>Modify 3G Dongle</strong>
    {/if}
    <table class="formtable">
        <!-- Sip Provider Name -->
        <tr>
            <td>
                Dongle Name
            </td>
            <td>
                <input type="text" name="Name" value="{$Dongle.Name}" {if $Errors.Name}class="error"{/if} />
            </td>
        </tr>

        <!-- Your Account ID -->
        <tr>
            <td>
                IMEI
            </td>
            <td>
                <input type="text" name="IMEI" value="{$Dongle.IMEI}" {if $Errors.IMEI}class="error"{/if} />
            </td>
        </tr>

        <!-- Your password -->
        <tr>
            <td>
                IMSI
            </td>
            <td>
                <input type="text" name="IMSI" value="{$Dongle.IMSI}" {if $Errors.IMSI}class="error"{/if} />
            </td>
        </tr>

        <!-- Callback Extension -->
        <tr>
            <td>
                Default Voice Extension
            </td>
            <td>
                <input type="text" {if $Errors.CallbackExtension}class="error"{/if} id="CallbackExtension" name="CallbackExtension" value="{$Dongle.CallbackExtension}" size="6" />
                <button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=CallbackExtension', 'Select Extension', 415, 330);">&nbsp;</button>
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
        <!-- ApplyIncomingRules -->
        <tr class="toggle hidden">
            <td>
                Apply Incoming Call Rules
            </td>
            <td>
                <input type="radio" value="1" id="ApplyIncomingRules_1" name="ApplyIncomingRules" {if $Dongle.ApplyIncomingRules}checked="checked"{/if} onclick="UpdateCallRules(1)" />
                <label for="ApplyIncomingRules_1">Yes</label>
                &nbsp;
                <input type="radio" value="0" id="ApplyIncomingRules_0" name="ApplyIncomingRules" {if !$Dongle.ApplyIncomingRules}checked="checked"{/if} onclick="UpdateCallRules(0)" />
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
                                <input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="{$Rule.PK_OutgoingRule}" {if $Rule.PK_OutgoingRule|in_array:$Dongle.Rules}checked="checked"{/if}/>
                            </td>
                            <td style="width: 20px;">
                                <input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="0" {if !$Rule.PK_OutgoingRule|in_array:$Dongle.Rules}checked="checked"{/if}/>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </td>
        </tr>
    </table>


    <!-- Submit -->
    <p>
        <br />
        {if $Dongle.PK_Dongle == ""}
            <button type="submit" name="submit" value="save">Add 3G Dongle</button>
        {else}
            <button type="submit" name="submit" value="save">Modify 3G Dongle</button>
        {/if}
    </p>
</form>
