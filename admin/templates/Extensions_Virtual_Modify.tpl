<script src="../static/script/jquery.jqModal.js"></script>
<script>
{literal}
	function ExtensionSettings_Advanced_Toggle() {
		$('#ExtensionSettings_Advanced_0').toggleClass('hidden');
		$('#ExtensionSettings_Advanced_1').toggleClass('hidden');
	}

	function popUp(url,inName,width,height)
	{
		inName = inName.replace(/ /g, "_"); /* For stupid pos IE */
		var popup = window.open('',inName,'width='+width+',height='+height+',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

		// only reload the page if it contains a new url
		if (popup.closed || !popup.document.URL || (-1 == popup.document.URL.indexOf(url)))
		{
			popup.location = url;
		}
		popup.focus();
		return popup;
	}
    
    function UpdateCallRules(val) {
        if (val == 0) {
            $("#OutgoingTbl :input").removeAttr('disabled');
            $("#OutgoingTbl td").removeClass('disabled');
            $("#CallbackExtension").attr('disabled', 'disabled');
            $("#InternalExtensionRow").css('display', 'none');
            $("#ExternalNumberRow").css('display', 'table-row');
        } else {
            $("#OutgoingTbl :input").attr('disabled', 'disabled');
            $("#OutgoingTbl td").addClass('disabled');
            $("#CallbackExtension").removeAttr('disabled');
           	$("#InternalExtensionRow").css('display', 'table-row');
            $("#ExternalNumberRow").css('display', 'none');
        }
    }

    $(document).ready(function () {

    {/literal}

    {if $Extension.IsInternal}	UpdateCallRules(1);
    {else}
    UpdateCallRules(0);
    {/if}

    {literal}
    })

    {/literal}
</script>    

<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}
{if $Errors.Name.Invalid}
<p class="error_message"> Name is required and must be 1 - 32 characters in length.</p>
{/if}

<form action="Extensions_Virtual_Modify.php" method="post" >
<p>
	<input type="hidden" name="PK_Extension" value="{$Extension.PK_Extension}" />
</p>

<!-- Extension Settings -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/1.gif"/></td>
				<td>
					Extension Settings {if $Extension.PK_Extension != "" } for <em>{$Extension.Type|lower}</em> Extension <em>{$Extension.Extension}</em> {/if}
				</td>
			</tr>
		</table>
	</td>
	</tr>


	<!-- Extension -->
	<tr>
		<td>
			Extension
		</td>
		<td>
			{if $Extension.PK_Extension != "" }
			{$Extension.Extension}
			<input type="hidden" name="Extension" value="{$Extension.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Extension.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<!-- First Name -->
	<tr>
		<td>
			Name
		</td>
		<td>
			<input type="text" name="Name" value="{$Extension.Name}" {if $Errors.Name }class="error"{/if} />&nbsp;
		</td>
	</tr>

	<tr>
		<td>
			Target Type
		</td>
		<td>
            <input type="radio" value="1" id="IsInternal_1" name="IsInternal" {if $Extension.IsInternal}checked="checked"{/if} onclick="UpdateCallRules(1)" />
            <label for="IsInternal_1">Internal Extension</label>
            &nbsp;
            <input type="radio" value="0" id="IsInternal_0" name="IsInternal" {if !$Extension.IsInternal}checked="checked"{/if} onclick="UpdateCallRules(0)" />
            <label for="IsInternal_0">External Number</label>
		</td>
	</tr>

	<tr id="InternalExtensionRow" style="display:none">
		<td>
			Target Extension
		</td>
		<td>
            <input type="text" {if $Errors.TargetExtension}class="error"{/if} id="TargetExtension" name="TargetExtension" value="{$Extension.TargetExtension}" size="6" />
            <button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=TargetExtension', 'Select Extension', 415, 330);">&nbsp;</button>
		</td>
	</tr>

	<tr id="ExternalNumberRow" style="display:none">
		<td>
			Target Number
		</td>
		<td>
            <input type="text" name="TargetNumber" value="{$Extension.TargetNumber}" {if $Errors.TargetNumber }class="error"{/if} />&nbsp;
		</td>
	</tr>
    
    <!-- Outgoing Call Rules -->

    <tr>
        <td>
            External Call Rules
        </td>
        <td>
            <table id="OutgoingTbl">
                <tr>
                    <th>Rule Name</th>
                    <th>Allow    </th>
                    <th>Deny     </th>
                </tr>

                {foreach from=$Rules item=Rule}
                    {if $Rule.ProviderType!='INTERNAL'}
                    <tr class='{cycle values="even,odd"}'>
                        <td>{$Rule.Name}</td>
                        <td style="width: 20px;">
                            <input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="{$Rule.PK_OutgoingRule}" {if $Rule.PK_OutgoingRule|in_array:$Extension.Rules}checked="checked"{/if}/>
                        </td>
                        <td style="width: 20px;">
                            <input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" value="0" {if !$Rule.PK_OutgoingRule|in_array:$Extension.Rules}checked="checked"{/if}/>
                        </td>
                    </tr>
                    {/if}
                {/foreach}
            </table>
        </td>
    </tr>    
    
</table>

<!-- Extension Groups -->
<table class="formtable">
	<tr>
	<td colspan="3" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/2.gif"/></td>
				<td>
					Extension Groups {if $Extension.PK_Extension != "" }for <em>{$Extension.Type|lower}</em> Extension <em>{$Extension.Extension}</em> {/if}
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- Groups this Template Belongs to -->
	<tr>
		<td>
			Groups this extension belongs to:
		</td>
		<td>
			<select name="Groups[]" id="Groups" multiple='multiple' style="width: 200px; height: 90px">
			{foreach from=$Groups item=Group}
				<option value="{$Group.PK_Group}" {if $Group.PK_Group|in_array:$Extension.Groups }selected="selected"{/if} >{$Group.Name}</option>
			{/foreach}
			</select>
			<br />
			<small>Hold down CTRL to select multiple groups</small>
		</td>
		<td>
			<button type="button" onclick="javacript:popUp('Groups_Popup_Create.php','Create Extension Group',615,500);">Create New Group</button>
		</td>
	</tr>
</table>
<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Save Extension Settings</button>
</p>
</form>