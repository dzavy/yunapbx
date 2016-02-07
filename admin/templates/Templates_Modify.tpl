<script type="text/javascript" src="../static/script/jquery.jqModal.js"></script>
<script type="text/javascript">
{literal}
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

<h2 style="margin-bottom: -10px;">Extension Templates</h2>

{if $Message == "CREATE_TEMPLATE"}
<p class="success_message">Successfully created extension template.</p>
{/if}

<form action="Templates_Modify.php" method="post" >
<p>
	<input type="hidden" name="Name" value="{$Template.Name}" />
	<input type="hidden" name="PK_Template" value="{$Template.PK_Template}" />
</p>

<!-- Extension Settings -->
<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/1.gif" alt="1"/></td>
				<td>
					Extension Settings
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- Template Name -->
	<tr>
		<td>Template Name</td>
		<td>{$Template.Name}</td>
	</tr>

	<!-- First Name -->
	<tr>
		<td>
			Name<br/>
			<small>Primary user of the extension</small>
		</td>
		<td>
			<label for="Name_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Name_Editable" id="Name_Editable" {if $Template.Name_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Email Address -->
	<tr>
		<td>
			Email Address<br/>
			<small>For voicemail notification</small>
		</td>
		<td>
			<label for="Email_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Email_Editable" id="Email_Editable" {if $Template.Email_Editable}checked="checked"{/if} />
		</td>
	</tr>

	<!-- Numeric Password -->
	<tr>
		<td>
			Numeric Password<br/>
			<small>For voicemail & web tool access</small>
		</td>
		<td>
			<label for="Password_Editable">User can edit</label>
			<input type="checkbox" value="1" name="Password_Editable" id="Password_Editable" {if $Template.Password_Editable}checked="checked"{/if} />
		</td>
	</tr>
</table>


<!-- Sip Phone Settings -->
<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/2.gif" alt="2"/></td>
				<td>
					SIP Phone Settings
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- DTMF Mode -->
	<tr>
		<td>
			<label for="FK_DTMFMode">DTMF Mode</label>
			<br />
		</td>
		<td>
			<select name="FK_DTMFMode" id="FK_DTMFMode">
			{foreach from=$DTMFModes item=DTMFMode}
				<option value="{$DTMFMode.PK_DTMFMode}" {if $Template.FK_DTMFMode == $DTMFMode.PK_DTMFMode }selected="selected"{/if} >{$DTMFMode.Description}</option>
			{/foreach}
			</select>
		</td>
	</tr>

	<!-- Supported Codecs -->
	<tr>
		<td>
			Supported Codecs
		</td>
		<td>
			<table>
			{foreach from=$Codecs item=Codec}
				{cycle values="<tr>,," name="tr1"}
					<td  style="font-size: 10px; padding: 2px;">
						<input type="checkbox" name="Codecs[]" value="{$Codec.PK_Codec}" id="TemplateCodec_{$Codec.PK_Codec}"
							{if $Codec.PK_Codec|in_array:$Template.Codecs } checked="checked" {/if}
						/>
						<label for="TemplateCodec_{$Codec.PK_Codec}">{$Codec.Description}</label>
						{if $Codec.Recomended == "1"}
							<small style="color: #666;">(Default)</small>
						{/if}
					</td>
				{cycle values=",,</tr>" name="tr2"}
			{/foreach}
			</table>
		</td>
	</tr>

	<!-- Phone NAT Traversal -->
	<tr>
		<td>
			Phone NAT Traversal
		</td>
		<td>
			<select name="FK_NATType" id="FK_NATType">
				{foreach from=$NATTypes item=NATType}
				<option value="{$NATType.PK_NATType}" {if $Template.FK_NATType == $NATType.PK_NATType }selected="selected"{/if}>{$NATType.Description}</option>
				{/foreach}
			</select>
		</td>
	</tr>
</table>

<!-- Enable PBX Features -->
<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/3.gif" alt="3"/></td>
				<td>
					Enable PBX Features
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
							{if $Feature.PK_Feature|in_array:$Template.Features } checked="checked" {/if}
						/>
						<label for="TemplateFeature_{$Feature.PK_Feature}">{$Feature.Name}</label>
					</td>
				{cycle values=",,</tr>" name="tr2"}
			{/foreach}
			</table>
	</td>
	</tr>
</table>

<table class="formtable">
	<tr>
	<td class="caption" colspan="3">
		<table>
			<tr>
				<td><img src="../static/images/4.gif" alt="4" /></td>
				<td>
					Outgoing Call Rules {if $Template.PK_Template != "" }for <em>{$Template.Type|lower}</em> Extension <em>{$Template.Extension}</em> {/if}
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
			<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" {if $Rule.PK_OutgoingRule|in_array:$Template.Rules}checked="checked"{/if} value="1" />
		</td>
		<td style="width: 20px;">
			<input type="radio" name="Rules[{$Rule.PK_OutgoingRule}]" {if ! $Rule.PK_OutgoingRule|in_array:$Template.Rules}checked="checked"{/if} value="0" />
		</td>
	</tr>
	{/foreach}
</table>

<!-- Extension Groups -->
<table class="formtable">
	<tr>
	<td colspan="3" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/5.gif" alt="5"/></td>
				<td>
					Extension Groups
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- Groups this Template Belongs to -->
	<tr>
		<td>
			Groups this Template Belongs to:
		</td>
		<td>
			<select name="Groups[]" id="Groups" multiple='multiple' style="width: 200px; height: 90px">
			{foreach from=$Groups item=Group}
				<option value="{$Group.PK_Group}" {if $Group.PK_Group|in_array:$Template.Groups }selected="selected"{/if} >{$Group.Name}</option>
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
	<button type="submit" name="submit" value="save">Modify Template</button>
</p>
</form>