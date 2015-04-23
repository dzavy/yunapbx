<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
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

function AddAdmin() {
	$("#Accounts").copyOptions("#Admins");
	$("#Accounts").removeOption(/./, true);
	$("#Admins").selectOptions("",true);
}

function RemoveAdmin() {
	$("#Admins").copyOptions("#Accounts");
	$("#Admins").removeOption(/./, true);
	$("#Accounts").sortOptions(true);
}

function PreSubmit() {
	$("#Admins").selectOptions(/./, true);
}
{/literal}
</script>

<h2>Conference Setup</h2>
{if $Message != ""}
<p class="success_message">{if $Message == "MODIFY_CONFERENCE"}Successfully saved your conference settings.{/if}</p>
{/if}

{if $Errors.Number.Format}
<p class="error_message">PIN must be 5 digits in length and can not start with a zero.</p>{/if}

{if $Errors.Operator.Format}
<p class="error_message">Exit Extension must be 3 to 5 digits in length.</p>{/if}

<!-- General Settings -->
<form method="post" action="ConferenceSetup.php" onsubmit="PreSubmit()">
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td class="caption">
		<table>
			<tr>
				<td><img src="images/1.gif"/></td>
				<td>
					General Settings
					<a href="#" class="help">What is this for?</a>
				</td>
			</tr>
		</table>
	</td>
	</tr>
	
	<!-- Conference Room Number -->
	<tr>
		<td>
			Your Conference Room Number:
			<input type="text" name="Number" size="5" value="{$Conference.Number}" {if $Errors.Number}class="error"{/if} />
			<img src="../images/fill_number.gif" />
		</td>
	</tr>
	
	<tr>
		<td>
			<input type="checkbox" name="PlayEnterSound" id="PlayEnterSound" value="1" {if $Conference.PlayEnterSound}checked="checked"{/if} />
			<label for="PlayEnterSound">Play sound when people enter/leave</label>
		</td>
	</tr>

	<tr>
		<td>
			<input type="checkbox" name="PlayMOH" id="PlayMOH" value="1" {if $Conference.PlayMOH}checked="checked"{/if} />
			<label for="PlayMOH">Play Music On Hold when only 1 member is in the conference</label>
		</td>
	</tr>

	<tr>
		<td>
			Conference members may press # and be sent to extension
			<input type="text" id="Operator" name="Operator" size="3" value="{$Conference.Operator}" {if $Errors.Operator}class="error"{/if} />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Operator','Select Extension',415,400);">&nbsp;</button>
		</td>
	</tr>
</table>

<!-- Admin Settings -->
<table class="formtable">
	<tr>
	<td class="caption" colspan="3">
		<table>
			<tr>
				<td><img src="images/2.gif"/></td>
				<td>
					Admin Settings
					<a href="#" class="help">Tell me more about admins</a>
				</td>
			</tr>
		</table>
	</td>
	</tr>
	
	<tr>
		<td>
			<small>All Available Accounts</small><br />
			<select name="Accounts" id="Accounts" multiple="multiple" style="width: 190px; height: 200px;">
				{foreach from=$Accounts item=Account}
					{if ! $Account.PK_Extension|in_array:$Conference.Admins}
					<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
					{/if}
				{/foreach}			
			</select>
		</td>
		<td style="vertical-align: middle;">
			<a href="javascript: AddAdmin()">
				<img src="images/right-arrow.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: RemoveAdmin()">
				<img src="images/left-arrow.gif" alt=">>" />
			</a>
		</td>
		<td>
			<small>Conference Admins</small><br />
			<select name="Admins[]" id="Admins" multiple="multiple" style="width: 190px; height: 200px;">
				{foreach from=$Accounts item=Account}
					{if $Account.PK_Extension|in_array:$Conference.Admins}
					<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
					{/if}
				{/foreach}
			</select>
		</td>
	</tr>
</table>
<table class="formtable">
	<tr>
		<td colspan="3">
			<input type="checkbox" name="OnlyAdminTalk" id="OnlyAdminTalk" value="1" {if $Conference.OnlyAdminTalk}checked="checked"{/if} />
			<label for="OnlyAdminTalk">Only allow conference admins to talk</label>		
		</td>
	</tr>
	
	<tr>
		<td colspan="3">
			<input type="checkbox" name="HangupIfNoAdmin" id="HangupIfNoAdmin" value="1" {if $Conference.HangupIfNoAdmin}checked="checked"{/if} />
			<label for="HangupIfNoAdmin">Hang up conference when all conference admins leave</label>		
		</td>
	</tr>
	
	<tr>
		<td colspan="3">
			<input type="checkbox" name="NoTalkTillAdmin" id="NoTalkTillAdmin" value="1" {if $Conference.NoTalkTillAdmin}checked="checked"{/if} />
			<label for="NoTalkTillAdmin">Users can not talk until a conference admin is in the conference room</label>		
		</td>
	</tr>
</table>

<br />
<button type="submit" name="submit" value="save">Save Conference Settings</button>
</form>