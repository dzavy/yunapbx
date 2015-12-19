<script language="javascript">
{literal}
function SetDisabledFields() {
	Voicemail_UseExternal = $("#Voicemail_UseExternal").is(':checked');

	if (Voicemail_UseExternal) {
		$("#Voicemail_From").attr("disabled","diabled");
		$("#Voicemail_SMTP_Server").attr("disabled","diabled");
		$("#Voicemail_SMTP_User").attr("disabled","diabled");
		$("#Voicemail_SMTP_Pass").attr("disabled","diabled");
		$("#Voicemail_AllowLogin").attr("disabled","diabled");
		$("#Voicemail_OperatorExtension").attr("disabled","diabled");
		$("#Voicemail_EmailTemplate").attr("disabled","diabled");
		$("#Test_Email").attr("disabled","diabled");
		$("#Voicemail_PK_SipProvider").removeAttr("disabled");
	} else {
		$("#Voicemail_From").removeAttr("disabled");
		$("#Voicemail_SMTP_Server").removeAttr("disabled");
		$("#Voicemail_SMTP_User").removeAttr("disabled");
		$("#Voicemail_SMTP_Pass").removeAttr("disabled");
		$("#Voicemail_AllowLogin").removeAttr("disabled");
		$("#Voicemail_OperatorExtension").removeAttr("disabled");
		$("#Voicemail_EmailTemplate").removeAttr("disabled");
		$("#Test_Email").removeAttr("disabled");
		$("#Voicemail_PK_SipProvider").attr("disabled","diabled");

	}
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

$(document).ready(function(){
	SetDisabledFields();
});
{/literal}
</script>


<h2>Voicemail Settings</h2>
{if     $Message == "SAVED_EMAIL_SETTINGS"}    <p class="success_message">Successfully saved voicemail email settings.</p>
{elseif $Message == "SAVED_ROUTING_SETTINGS"}  <p class="success_message">Successfully update your voicemail routing settings.</p>
{elseif $Message == "SAVED_EMAIL_TEMPLATE"}    <p class="success_message">Successfully updated your voicemai email template.</p>
{elseif $Message == "RESTORE_EMAIL_TEMPLATE"}  <p class="success_message">Successfully restored your voicemail email template back to the original template.</p>
{elseif $Message == "SAVED_EXTERNAL_SETTINGS"} <p class="success_message">Successfully saved external voicemail settings.</p>
{/if}

{if $Errors.Voicemail_OperatorExtension.Invalid}
<p class="error_message">Invalid extension for voicemail routing.</p>
{/if}

<form action="VoicemailSettings.php" method="post" name="voicemail" >
<table class="formtable" style="margin-top: -20px;">
	<tr>
		<td colspan="2" class="caption">
			<table>
				<tr>
					<td><img src="../static/images/1.gif"/></td>
					<td>
					Customize the settings for voicemail notification emails
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			Voicemail Notification Email "From" Address
		</td>
		<td colspan="1">
			<input type="text" size="40" name="Voicemail_From" id="Voicemail_From" value="{$Settings.Voicemail_From}"  {if $Errors.Voicemail_From}class="error"{/if} />

		</td>
	</tr>
	<tr>
		<td>
			Outbound SMTP Server
		</td>
		<td colspan="1">
			<input type="text" size="20" id="Voicemail_SMTP_Server" name="Voicemail_SMTP_Server" value="{$Settings.Voicemail_SMTP_Server}" {if $Errors.Voicemail_SMTP_Server}class="error"{/if} />

		</td>
	</tr>
	<tr>
		<td>
			Outbound SMTP Username
		</td>
		<td colspan="1">
			<input type="text" size="20" id="Voicemail_SMTP_User" name="Voicemail_SMTP_User" value="{$Settings.Voicemail_SMTP_User}"  {if $Errors.Voicemail_SMTP_User}class="error"{/if} />

		</td>
	</tr>
	<tr>
		<td>
			Outbound SMTP Password
		</td>
		<td colspan="1">
			<input type="password" size="20" id="Voicemail_SMTP_Pass" name="Voicemail_SMTP_Pass" value="{$Settings.Voicemail_SMTP_Pass}" {if $Errors.Voicemail_SMTP_Pass}class="error"{/if} />

		</td>
	</tr>
	<tr>
		<td>
			<button type="submit" name="submit" value="save_email_settings">Save Email Settings</button>
		</td>
	</tr>

</table>

<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/2.gif"/></td>
				<td>
				Voicemail Notification Email Diagnostics
				</td>
			</tr>
		</table>
	</td>
	</tr>
	<tr>
		<td>
			<p>Are users not receiving voicemail notification emails?</p>
		</td>
	</tr>
	<tr>
		<td>
			Email Address to Send a Test Email To
			<input type="text" size="40" id="Test_Email" name="Test_Email" value="" {if $Errors.Test_Email}class="error"{/if} />

		</td>
	</tr>
	<tr>
		<td>
			<button type="submit" name="submit" value="save">Run Diagnostic Tool</button>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/3.gif"/></td>
				<td>
				Voicemail Routing
				</td>
			</tr>
		</table>
	</td>
	</tr>
	<tr>
		<td>
			<input type="checkbox" name="Voicemail_AllowLogin" id="Voicemail_AllowLogin" value="1" {if $Settings.Voicemail_AllowLogin}checked="checked"{/if} />
			During a user's voicemail greeting, callers may push the "*" key to log into the current voicemail box.

			<br /><br />

			During a user's voicemail greeting, callers may push the "0" key to exit and be forwarded to extension
			<input type="text" size="5" id="Voicemail_OperatorExtension" name="Voicemail_OperatorExtension" value="{$Settings.Voicemail_OperatorExtension}" {if $Errors.Voicemail_OperatorExtension}class="error"{/if} />

			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Voicemail_OperatorExtension','Select Extension',415,400);">&nbsp;</button>

		</td>
	</tr>
	<tr>
		<td>
			<button type="submit" name="submit" value="save_routing_settings">Save Routing Settings</button>
		</td>
	</tr>
</table>

<table class="formtable">
	<tr>
		<td colspan="2" class="caption">
			<table>
				<tr>
					<td><img src="../static/images/4.gif"/></td>
					<td>
					Voicemail Email Template
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		Below you can the customize the email template that gets sent to the user when they recieve a voicemail email.
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="Voicemail_EmailTemplate" id="Voicemail_EmailTemplate" rows="25" style="width: 100%">{$Settings.Voicemail_EmailTemplate}</textarea>
		</td>
	</tr>
	<tr>
		<td>
		You can use variables in your template that will be substitued with the real values when the email gets sent. Below is a list of the variables and a description of their substituted value.
		</td>
	</tr>
	<tr>
		<td>
			<div style="background: #FBFBBB; font-size: x-small; padding: 5px; border: 1px solid #999">
				<p><b>%VM_NAME% </b>- Recipient's firstname and lastname</p>
				<p><b>%VM_DUR% </b>- The duration of the voicemail message</p>
				<p><b>%VM_MAILBOX% </b>- The recipient's extension</p>
				<p><b>%VM_CALLERID% </b>- The caller id of the person who left the message</p>
				<p><b>%VM_DATE% </b>- The date and time the message was left</p>
				<p><b>%VM_MSGNUM% </b>- The message number in your mailbox</p>
				<p><b>%DOWNLOAD_LINK% </b>- A URL to the web admin to download the message</p>
				<p><b>%DOWNLOAD_LINK_AND_MARK_READ% </b>- A URL to the web admin to download the message and mark it as read in the recipient's mailbox.</p>
				<p><b>%DOWNLOAD_LINK_AND_MARK_DELETED% </b>- A URL to the web admin to download the message and delete it from the recipient's mailbox</p>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center" >
			<button type="submit" name="submit" value="save_voicemail_template">Save Voicemail Template</button>
			<button type="submit" name="submit" value="restore_original_template" class="important">Restore to Original Template</button>
		</td>
	</tr>
</table>

</form>