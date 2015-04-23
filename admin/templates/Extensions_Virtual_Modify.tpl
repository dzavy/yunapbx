<script src="../script/jquery.jqModal.js"></script>
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
{/literal}

</script>

<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>
{/if}
{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>
{/if}
{if $Errors.Password.Invalid}
<p class="error_message">A valid password is required and must only consist of digits and must be 3-10 digits in length.</p>
{/if}
{if $Errors.Password.Match}
<p class="error_message">Your two numeric passwords you entered did not match each other.</p>
{/if}
{if $Errors.FirstName.Invalid}
<p class="error_message"> First name is required and must be 1 - 32 characters in length.</p>
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
				<td><img src="images/1.gif"/></td>
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
			Extensions
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
			First Name<br/>
			<small>Primary user of the extension</small>
		</td>
		<td>
			<input type="text" name="FirstName" value="{$Extension.FirstName}" {if $Errors.FirstName }class="error"{/if} />&nbsp;
			<label for="FirstName_Editable">User can edit</label>
			<input type="checkbox" value="1" name="FirstName_Editable" id="FirstName_Editable" {if $Extension.FirstName_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Last Name -->
	<tr>
		<td>Last Name</td>
		<td>
			<input type="text" name="LastName" value="{$Extension.LastName}" />&nbsp;
			<label for="LastName_Editable">User can edit</label>
			<input type="checkbox" value="1" name="LastName_Editable" id="LastName_Editable" {if $Extension.LastName_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Email Address -->
	<tr>
		<td>
			Email Address<br/>
			<small>For voicemail notification</small>
		</td>
		<td>
			<input type="text" name="Email" value="{$Extension.Email}" />&nbsp;
			<label for="Email_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Email_Editable" id="Email_Editable" {if $Extension.Email_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Numeric Password -->
	<tr>
		<td>
			Numeric Password<br/>
			<small>For voicemail & web tool access</small>
		</td>
		<td>
			<input type="password" name="Password" value="{$Extension.Password}" {if $Errors.Extension}class="error"{/if} />&nbsp;
			<label for="Password_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Password_Editable" id="Password_Editable" {if $Extension.Password_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Retype Numeric Password -->
	<tr>
		<td>
			Retype Numeric Password<br/>
			<small>Must match password above</small>
		</td>
		<td>
			<input type="password" name="Password_Retype" {if $Errors.Extension}class="error"{/if} />&nbsp;
		</td>
	</tr>
</table>

<!-- Extension Settings Advanced -->
<table class="formtable" id="ExtensionSettings_Advanced_0">
	<tr><td>
	<a href="javascript:ExtensionSettings_Advanced_Toggle()">
		<img src="images/right-arrow.gif" alt="[+]" />
		Click to show advanced options
	</a>
	</td></tr>
</table>

<table class="formtable advanced hidden" id="ExtensionSettings_Advanced_1">
	<tr>
		<td>
			<a href="javascript:ExtensionSettings_Advanced_Toggle()">
				<img src="images/down-arrow.gif" alt="[-]" />
				Click to hide advanced options
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Extension.IVRDial=='1'}checked="checked"{/if} />
			<label for="IVRDial">This extension can be dialed from an IVR.</label>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr>
	<td class="caption" colspan="3">
		<table>
			<tr>
				<td><img src="images/3.gif" alt="3" /></td>
				<td>
					Outgoing Call Rules {if $Extension.PK_Extension != "" }for <em>{$Extension.Type|lower}</em> Extension <em>{$Extension.Extension}</em> {/if}
				</td>
			</tr>
		</table>
	</td>
	</tr>
	<tr>
		<td><b>Rule Name</b></td>
		<td><b>Allow</b></td>
		<td><b>Deny</b></td>
	</tr>

	{foreach from=$Rules item=Rule}
	<tr class='{cycle values="even,odd"}'>
		<td>{$Rule.Name}</td>
		<td style="width: 20px;">
			<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" {if $Rule.PK_OutgoingRule|in_array:$Extension.Rules}checked="checked"{/if} value="1" />
		</td>
		<td style="width: 20px;">
			<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" {if ! $Rule.PK_OutgoingRule|in_array:$Extension.Rules}checked="checked"{/if} value="0" />
		</td>
	</tr>
	{/foreach}
</table>

<!-- Enable PBX Features -->
<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="images/4.gif"/></td>
				<td>
					Enable PBX Features {if $Extension.PK_Extension != "" }for <em>{$Extension.Type|lower}</em> Extension <em>{$Extension.Extension}</em> {/if}
				</td>
			</tr>
		</table>
	</td>
	</tr>
	<tr>
	<td>
			<table>
			{foreach from=$Features item=Feature}
				{cycle values="<tr>,," name="tr1"}
					<td>
						<input type="checkbox" name="Features[]" value="{$Feature.PK_Feature}" id="TemplateFeature_{$Feature.PK_Feature}"
							{if $Feature.PK_Feature|in_array:$Extension.Features } checked="checked" {/if}
						/>
						<label for="TemplateFeature_{$Feature.PK_Feature}">{$Feature.Name}</label>
					</td>
				{cycle values=",,</tr>" name="tr2"}
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
				<td><img src="images/5.gif"/></td>
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