<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
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

function AddLoginAdmin() {
	$("#AvailableAdmins").copyOptions("#Admins");
	$("#AvailableAdmins").removeOption(/./, true);
	$("#Admins").selectOptions("",true);
}

function RemoveAdmin() {
	$("#Admins").copyOptions("#AvailableAdmins");
	$("#Admins").removeOption(/./, true);
	$("#AvailableAdmins").sortOptions(true);
}

function PreSubmit() {
	$("#Members").selectOptions(/./, true);
	$("#Admins").selectOptions(/./, true);
}

function popUp(url,id_select,inName,width,height)
	{
		inName = inName.replace(/ /g, "_"); /* For stupid pos IE */
		var popup = window.open('',inName,'width='+width+',height='+height+',toolbars=0,scrollbars=1,location=0,status=0,menubar=0,resizable=1,left=200,top=200');

		// only reload the page if it contains a new url
		if (popup.closed || !popup.document.URL || (-1 == popup.document.URL.indexOf(url)))
		{
			popup.location = url+"?id_select="+id_select;
		}
		popup.focus();
		return popup;
	}


function Enable_SoundEntries() {
	PK_Folder   = $("#SoundFolders").val();
	PK_Language = $("#SoundLanguages").val();
	PK_Entry    = $("#SoundEntries_"+PK_Folder+"_"+PK_Language).val();


	$("select[id^='SoundEntries_']").css('display','none');
	$("#SoundEntries_"+PK_Folder+"_"+PK_Language).css('display','inline');

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

function Sync_SoundEntries(PK_Folder, PK_Language) {
	PK_Entry  = $("#SoundEntries_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^=SoundEntries_"+PK_Folder+"_]").val(PK_Entry);
	$("#FK_SoundEntry").val(PK_Entry);

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

function SetSoundState() {
	var PlaySound = $(":radio[name='PlaySound'][value='1']").is(":checked");
	if(PlaySound) {
		$("#SoundRow > select").removeAttr('disabled');
	} else {
		$("#SoundRow > select").attr('disabled','disabled');
	}
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

	if(Use_Admins_ByAccount) {
		$("#Admins_ByGroup select").attr('disabled','disabled');
		$("#Admins_ByAccount select").removeAttr('disabled');
	} else {
		$("#Admins_ByGroup select").removeAttr('disabled');
		$("#Admins_ByAccount select").attr('disabled','disabled');
	}

}

$(document).ready(function() {

//	PK_Entry    = $("#FK_SoundEntry").val();
//	PK_Language = $("#SoundLanguages").val();
//	{/literal}
//	{foreach from=$SoundEntries item=SoundEntry}
//		{if $SoundEntry.PK_SoundEntry == $Intercom.Param.FK_SoundEntry}
//
//	PK_Folder = {$SoundEntry.FK_SoundFolder};
//		{/if}
//	{/foreach}
//	{literal}
//
//	$("#SoundFolders > option[value='"+PK_Folder+"']").attr("selected","selected");
//	$("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr("selected","selected");
//
	Enable_SoundEntries();
	SetSoundState();
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
<p class="error_message">Intercom name may only contain alpha-numeric characters or spaces and must be 1-20 characters in length.</p>{/if}

<form action="Extensions_Intercom_Modify.php" method="post" onsubmit="PreSubmit()">
<p>
	<input type="hidden" name="PK_Extension" value="{$Intercom.PK_Extension}" />
</p>

<!-- Call Intercom Setup -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="images/1.gif"/></td>
				<td>
					Intercom Settings
				</td>
			</tr>
		</table>
	</td>
	</tr>


	<!-- Intercom Extension -->
	<tr>
		<td>
			Intercom Extension
		</td>
		<td>
			{if $Intercom.PK_Extension != "" }
			{$Intercom.Extension}
			<input type="hidden" name="Extension" value="{$Intercom.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$Intercom.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>

	<!-- Intercom Name -->
	<tr>
		<td>
			Alert-Info header<br/>
		</td>
		<td>
			<input type="text" name="Header" value="{$Intercom.Header}" {if $Errors.Header}class="error"{/if} />
		</td>
	</tr>

	<!-- Ringing Strategy -->
	<tr>
		<td>
			Timeout
		</td>
		<td>
			<select name="Timeout">
				<option value="30"  {if $Intercom.Timeout == '30' }selected="selected"{/if} >30 Seconds</option>
				<option value="60"  {if $Intercom.Timeout == '60' }selected="selected"{/if} >1 Minute</option>
				<option value="120" {if $Intercom.Timeout == '120'}selected="selected"{/if} >2  Minutes</option>
				<option value="180" {if $Intercom.Timeout == '180'}selected="selected"{/if} >3  Minutes</option>
				<option value="240" {if $Intercom.Timeout == '240'}selected="selected"{/if} >4  Minutes</option>
				<option value="300" {if $Intercom.Timeout == '300'}selected="selected"{/if} >5  Minutes</option>
				<option value="360" {if $Intercom.Timeout == '360'}selected="selected"{/if} >6  Minutes</option>
				<option value="420" {if $Intercom.Timeout == '340'}selected="selected"{/if} >7  Minutes</option>
				<option value="600" {if $Intercom.Timeout == '600'}selected="selected"{/if} >10  Minutes</option>
				<option value="0"   {if $Intercom.Timeout == '0'  }selected="selected"{/if} >Never Times Out</option>

			</select>
		</td>
	</tr>

	<!-- Dialed from an IVR  -->
	<tr>
		<td>
			Intercom / Paging <br/>
		</td>
		<td>
			<input type="radio" name="TwoWay" value="1" {if $Intercom.TwoWay=='1'}checked="checked"{/if}> 2-way Intercom
			<input type="radio" name="TwoWay" value="0" {if $Intercom.TwoWay=='0'}checked="checked"{/if}> 1-way Paging
		</td>
	</tr>
	<tr>
		<td>
			Play beep/sound first
		</td>
		<td>
			<input type="radio" name="PlaySound" value="1" {if $Intercom.PlaySound=='1'}checked="checked"{/if} onchange="SetSoundState()"> Yes
			<input type="radio" name="PlaySound" value="0" {if $Intercom.PlaySound=='0'}checked="checked"{/if} onchange="SetSoundState()"> No
		</td>
	</tr>
</table>
<p>
	Choose sound to play:
</p>
<table style="margin-left: 20px;">
	<tr>
		<td id="SoundRow">
			Folder:
				<select id="SoundFolders" name="SoundFolders" onchange="Enable_SoundEntries()">
				{foreach from=$SoundFolders item=Folder}
					<option value="{$Folder.PK_SoundFolder}"{if $Intercom.FK_Folder == $Folder.PK_SoundFolder}selected="selected"{/if}>{$Folder.Name}</option>
				{/foreach}
				</select>
			&nbsp;
			Language:
				<select id="SoundLanguages" name="SoundLanguages" onchange="Enable_SoundEntries()" name="Param[FK_SoundLanguage]">
				{foreach from=$SoundLanguages item=SoundLanguage}
					<option value="{$SoundLanguage.PK_SoundLanguage}" {if $Intercom.FK_Lang == $SoundLanguage.PK_SoundLanguage}selected="selected"{/if}>{$SoundLanguage.Name}</option>
				{/foreach}
				</select>
			&nbsp;
			Sound:
				{foreach from=$SoundFolders item=SoundFolder}
				{foreach from=$SoundLanguages item=SoundLanguage}
				<select name="SoundEntries" id="SoundEntries_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}" onchange="Sync_SoundEntries({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})" style="display: none;">
					{foreach from=$SoundEntries item=SoundEntry}
						{if $SoundEntry.FK_SoundFolder == $SoundFolder.PK_SoundFolder}
							{assign var=lang_id  value=$SoundLanguage.PK_SoundLanguage}
							{if $SoundEntry.Name.$lang_id != ""}
								<option title="{$SoundEntry.Description.$lang_id}" value="{$SoundEntry.PK_SoundEntry}"{if $Intercom.FK_Sound == $SoundEntry.PK_SoundEntry}selected="selected"{/if}>{$SoundEntry.PK_SoundEntry}-{$SoundEntry.Name.$lang_id}</option>
							{else}
								<option style="color: red" value="{$SoundEntry.PK_SoundEntry}">{$SoundEntry.Name.Default}</option>
							{/if}
						{/if}
					{/foreach}
				</select>
				{/foreach}
				{/foreach}
				<input type="hidden" name="Param[FK_SoundEntry]" id="FK_SoundEntry" value="{$Intercom.FK_Sound}" />
		</td>
	</tr>
	<tr>
		<td style="font-size: x-small; padding-top: 5px;">
			Description:
			<em><span id="SoundEntry_Description" name="SoundEntry_Description" style="font-size: x-small">{$SoundFolders.Description}</span></em>
		</td>
	</tr>
</table>



<!-- Intercom Users and Admins  -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="3" class="caption">
		<table>
			<tr>
				<td><img src="images/2.gif"/></td>
				<td>
					Intercom Users and Admins
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- Extensions authorized to use this intercom extension  -->
	<tr>
		<td>
			<b><small>Extensions authorized to use this intercom extension</small></b>
		</td>
	</tr>
	<tr>
		<td id="Admins_ByAccount">
			<!-- Use Individual Extensions -->
			<input type="radio" name="Use_Admins_ByAccount" value="1" {if $Intercom.Use_Admins_ByAccount=='1'}checked{/if} onchange="Update_ByGroupByAccount_Status()"> Use Individual Extensions

			<!-- Use Individual Extensions Admin Select -->
			<table>
				<tr>
					<td>
						<small>All Available Accounts</small>
						<br />
						<select name="AvailableAdmins" id="AvailableAdmins" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if ! $Account.PK_Extension|in_array:$Intercom.Admins}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
								{/if}
							{/foreach}
						</select>
					</td>
					<td style="vertical-align: middle;">
						<a href="javascript: AddLoginAdmin()">
						<img src="images/right-arrow.gif" alt="<<" />
						</a>
						<br />
						<a href="javascript: RemoveAdmin()">
						<img src="images/left-arrow.gif" alt=">>" />
						</a>
					</td>
					<td>
						<small>Intercom Admins</small>
						<br />
						<select name="Admins[]" id="Admins" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if $Account.PK_Extension|in_array:$Intercom.Admins}
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
		<td id="Admins_ByGroup">
			-OR-

			<!--  Use Predefined Group Intercom Admins -->
			<br /><br />
			<input type="radio" name="Use_Admins_ByAccount" value="0" {if $Intercom.Use_Admins_ByAccount=='0'}checked="checked"{/if} onchange="Update_ByGroupByAccount_Status()"> Use a Predefined Group:

			<select name="GroupsA" id="GroupsA">
				{foreach from=$Groups item=Item}
					<option {if $Item.PK_Group == $Intercom.FK_Group_Admin}selected="selected"{/if} value="{$Item.PK_Group}">{$Item.Name}</option>
				{/foreach}
			</select>

			<button type="button" onclick="javacript:popUp('Groups_Popup_Create_Param.php','GroupsA','Create Extension Group',615,500);">Create New Group</button>
		</td>
	</tr>



	<!-- Extensions that will be called -->
	<tr>
		<td>
			&nbsp; <br />
			<b><small>Extensions that will be called</small></b>
		</td>
	</tr>
	<tr>
		<td>
			<!-- Use Individual Extensions -->
			<input type="radio" name="Use_Members_ByAccount" value="1" {if $Intercom.Use_Members_ByAccount=='1'}checked="checked" {/if} onclick="Update_ByGroupByAccount_Status();"> Use Individual Extensions
		</td>
		</tr><tr>
			<td id="Members_ByAccount">
			<!-- Use Individual Extensions Select Members -->
			<table>
				<tr>
					<td>
						<small>All Available Accounts</small>
						<br />
						<select name="AvailableMembers" id="AvailableMembers"  multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if ! $Account.PK_Extension|in_array:$Intercom.Members}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"</option>
								{/if}
							{/foreach}
						</select>
					</td>
					<td style="vertical-align: middle;">
						<a href="javascript: AddLoginMember()">
						<img src="images/right-arrow.gif" alt="<<" />
						</a>
						<br />
						<a href="javascript: RemoveMember()">
						<img src="images/left-arrow.gif" alt=">>" />
						</a>
					</td>
					<td>
						<small>Intercom Members</small>
						<br />
						<select name="Members[]" id="Members" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if $Account.PK_Extension|in_array:$Intercom.Members}
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
			<input type="radio" name="Use_Members_ByAccount" value="0" {if $Intercom.Use_Members_ByAccount=='0'}checked="checked"{/if} onchange="Update_ByGroupByAccount_Status();"> Use a Predefined Group:

			<select name="GroupsM" id="GroupsM">
				{foreach from=$Groups item=Item}
					<option {if $Item.PK_Group == $Intercom.FK_Group_Member}selected="selected"{/if} value="{$Item.PK_Group}">{$Item.Name}</option>
				{/foreach}
			</select>

			<button type="button" onclick="javacript:popUp('Groups_Popup_Create_Param.php','GroupsM','Create Extension Group',615,500);">Create New Group</button>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	{if $Intercom.PK_Extension != "" }
		<button type="submit" name="submit" value="save">Modify Extension</button>
	{else}
		<button type="submit" name="submit" value="save">Create Extension</button>
	{/if}
</p>
</form>