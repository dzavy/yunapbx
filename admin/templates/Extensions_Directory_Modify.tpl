<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}

function AddLoginMember() {
	$("#AvailableMembers").copyOptions("#Members");
	$("#AvailableMembers").removeOption(/./, true);
	$("#Members").selectOptions("",true);
}

function RemoveMember() {
	$("#Members").copyOptions("#AvailableMembers");
	$("#Members").removeOption(/./, true);
	$("#AvailableMembers").sortOptions(true);
}
///???
function AddLoginAdmin() {
	$("#AvailableAdmins").copyOptions("#Admins");
	$("#AvailableAdmins").removeOption(/./, true);
	$("#Admins").selectOptions("",true);
}
///???
function RemoveAdmin() {
	$("#Admins").copyOptions("#AvailableAdmins");
	$("#Admins").removeOption(/./, true);
	$("#AvailableAdmins").sortOptions(true);
}

function PreSubmit() {
	$("#Members").selectOptions(/./, true);
	$("#Admins").selectOptions(/./, true);
}

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

function Update_ByGroupByAccount_Status() {
	var Use_Members_ByAccount = $(":radio[name='Use_Members_ByAccount'][value='1']").is(":checked");
	var Use_Admins_ByAccount  = $(":radio[name='Use_Admins_ByAccount'][value='1']").is(":checked");


	if(Use_Members_ByAccount) {
		$("#Members_ByGroup select").attr('disabled','disabled');
		$("#Members_ByAccount select").removeAttr('disabled');
	} else {
		$("#Members_ByGroup select").removeAttr('disabled');
		$("#Members_ByAccount select").attr('disabled','disabled');
	}	
}

$(document).ready(function() {
	//Enable_SoundEntries();
	//SetSoundState();
	Update_ByGroupByAccount_Status();
});

{/literal}
</script>



<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 3-5 digits in length.</p>{/if}

{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>{/if}

{if $Errors.Name.Invalid}
<p class="error_message">Directory name may only contain alpha-numeric characters or spaces and must be 1-20 characters in length.</p>{/if}

<form action="Extensions_Directory_Modify.php" method="post" onsubmit="PreSubmit()">
<p>	<input type="hidden" name="PK_Extension" value="{$Directory.PK_Extension}" /> </p>

<br>
<!-- Call Directory Setup -->
<table class="formtable" style="margin-top: -20px;" class="nostyle">
	<!-- Directory Extension -->
	<tr>
		<td>
			Directory Extension
			{if $Directory.PK_Extension != "" }
			{$Directory.Extension}
			<input type="hidden" name="Extension" value="{$Directory.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Directory.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
			<br>
			<small>3 digits in length </small>
		</td>
	</tr>	
	<tr>
		<td>
			<input type="checkbox" name="IVRDial" id="IVRDial" value="1" {if $Directory.IVRDial==1} checked='checked'{/if} />  
			This extension can be dialed from an IVR. 		
		</td>
	</tr>
	<tr>
		<td>
			If a caller presses the "0" digit they will exit the directory and be forwarded to extension
			<input type="text" {if $Errors.CallbackExtension}class="error"{/if} 
			id="CallbackExtension" name="CallbackExtension" value="{$Provider.CallbackExtension}" size="6" />
			<button type="button" class="users" 
			onclick="javacript:popUp('Extensions_Popup.php?FillID=CallbackExtension','Select Extension',415,400);">
			&nbsp;</button>			
		</td>
	</tr>
	<tr>
		<td>
			Let callers search the directory by member's 
				<select name="Which_Name">
					<option value="LastName"  {if $Directory.Which_Name=="LastName" } selected="selected"{/if}>Last  Name</option>
					<option value="FirstName" {if $Directory.Which_Name!="LastName"} selected="selected"{/if}>First Name</option>
				</select>			
			<hr>
		</td>
	</tr>
	
	<tr>
		<td>
			<b>Directory Members:</b><br>
			<!-- Use Individual Extensions -->
			<input type="radio" name="Use_Members_ByAccount" value="1" {if $Directory.Use_Members_ByAccount=='1'}checked="checked" {/if} onclick="Update_ByGroupByAccount_Status();"> Use Individual Extensions
		</td>
	</tr>
	<tr>
			<td id="Members_ByAccount">
			<!-- Use Individual Extensions Select Members -->
			<table >
				<tr>
					<td>
						<small>All Available Accounts</small>
						<br />
						<select name="AvailableMembers" id="AvailableMembers"  multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if ! $Account.PK_Extension|in_array:$Directory.Members}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
								{/if}
							{/foreach}
						</select>
					</td>
					<td style="vertical-align: middle;">
						<a href="javascript: AddLoginMember()">
						<img src="../static/images/right-arrow.gif" alt="<<" />
						</a>
						<br />
						<a href="javascript: RemoveMember()">
						<img src="../static/images/left-arrow.gif" alt=">>" />
						</a>
					</td>
					<td>
						<small>Directory Members</small>
						<br />
						<select name="Members[]" id="Members" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if $Account.PK_Extension|in_array:$Directory.Members}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
								{/if}
							{/foreach}
						</select>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id="Members_ByGroup">
			-OR-

			<br /><br />
			<input type="radio" name="Use_Members_ByAccount" value="0" {if !$Directory.Use_Members_ByAccount}checked="checked"{/if} onchange="Update_ByGroupByAccount_Status();"> Use a Predefined Group:

			<select name="GroupsM" id="GroupsM">
				{foreach from=$Groups item=Item}
					<option {if $Item.PK_Group == $Directory.FK_Group_Member}selected="selected"{/if} value="{$Item.PK_Group}">{$Item.Name}</option>
				{/foreach}
			</select>

			<button type="button" onclick="javacript:popUp('Groups_Popup_Create_Param.php','GroupsM','Create Extension Group',615,500);">Create New Group</button>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	{if $Directory.PK_Extension != "" }
		<button type="submit" name="submit" value="save">Modify Extension</button>
	{else}
		<button type="submit" name="submit" value="save">Create Extension</button>
	{/if}
</p>
</form>