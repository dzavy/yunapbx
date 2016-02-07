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
<p class="error_message">GroupPickup name may only contain alpha-numeric characters or spaces and must be 1-20 characters in length.</p>{/if}

<form action="Extensions_GroupPickup_Modify.php" method="post" onsubmit="PreSubmit()">
<p>
	<input type="hidden" name="PK_Extension" value="{$GroupPickup.PK_Extension}" />
</p>

<!-- Call GroupPickup Setup -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="2" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/1.gif"/></td>
				<td>
					GroupPickup Settings
				</td>
			</tr>
		</table>
	</td>
	</tr>


	<!-- GroupPickup Extension -->
	<tr>
		<td>
			GroupPickup Extension
		</td>
		<td>
			{if $GroupPickup.PK_Extension != "" }
			{$GroupPickup.Extension}
			<input type="hidden" name="Extension" value="{$GroupPickup.Extension}" />
			{else}
			<input type="text" size="5" name="Extension" value="{$GroupPickup.Extension}" {if $Errors.Extension}class="error"{/if} />
			{/if}
		</td>
	</tr>
</table>

<table class="formtable" style="margin-top: -20px;">
	<tr>
	<td colspan="3" class="caption">
		<table>
			<tr>
				<td><img src="../static/images/2.gif"/></td>
				<td>
					GroupPickup Users and Admins
				</td>
			</tr>
		</table>
	</td>
	</tr>

	<!-- Extensions authorized to use this GroupPickup extension  -->
	<tr>
		<td>
			<b><small>Extensions authorized to use this GroupPickup extension</small></b>
		</td>
	</tr>
	<tr>
		<td id="Admins_ByAccount">
			<!-- Use Individual Extensions -->
			<input type="radio" name="Use_Admins_ByAccount" value="1" {if $GroupPickup.Use_Admins_ByAccount=='1'}checked{/if} onchange="Update_ByGroupByAccount_Status()"> Use Individual Extensions

			<!-- Use Individual Extensions Admin Select -->
			<table>
				<tr>
					<td>
						<small>All Available Accounts</small>
						<br />
						<select name="AvailableAdmins" id="AvailableAdmins" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if !$Account.PK_Extension|in_array:$GroupPickup.Admins}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.Name}"</option>
								{/if}
							{/foreach}
						</select>
					</td>
					<td style="vertical-align: middle;">
						<a href="javascript: AddLoginAdmin()">
						<img src="../static/images/right-arrow.gif" alt="<<" />
						</a>
						<br />
						<a href="javascript: RemoveAdmin()">
						<img src="../static/images/left-arrow.gif" alt=">>" />
						</a>
					</td>
					<td>
						<small>GroupPickup Admins</small>
						<br />
						<select name="Admins[]" id="Admins" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if $Account.PK_Extension|in_array:$GroupPickup.Admins}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.Name}"</option>
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

			<!--  Use Predefined Group GroupPickup Admins -->
			<br /><br />
			<input type="radio" name="Use_Admins_ByAccount" value="0" {if $GroupPickup.Use_Admins_ByAccount=='0'}checked="checked"{/if} onchange="Update_ByGroupByAccount_Status()"> Use a Predefined Group:

			<select name="GroupsA" id="GroupsA">
				{foreach from=$Groups item=Item}
					<option {if $Item.PK_Group == $GroupPickup.FK_Group_Admin}selected="selected"{/if} value="{$Item.PK_Group}">{$Item.Name}</option>
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
			<input type="radio" name="Use_Members_ByAccount" value="1" {if $GroupPickup.Use_Members_ByAccount=='1'}checked="checked" {/if} onclick="Update_ByGroupByAccount_Status();"> Use Individual Extensions
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
								{if ! $Account.PK_Extension|in_array:$GroupPickup.Members}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.Name}"</option>
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
						<small>GroupPickup Members</small>
						<br />
						<select name="Members[]" id="Members" multiple="multiple" style="width: 190px; height: 200px;">
							{foreach from=$Accounts item=Account}
								{if $Account.PK_Extension|in_array:$GroupPickup.Members}
								<option value="{$Account.PK_Extension}">{$Account.Extension} "{$Account.Name}"</option>
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
			<input type="radio" name="Use_Members_ByAccount" value="0" {if $GroupPickup.Use_Members_ByAccount=='0'}checked="checked"{/if} onchange="Update_ByGroupByAccount_Status();"> Use a Predefined Group:

			<select name="GroupsM" id="GroupsM">
				{foreach from=$Groups item=Item}
					<option {if $Item.PK_Group == $GroupPickup.FK_Group_Member}selected="selected"{/if} value="{$Item.PK_Group}">{$Item.Name}</option>
				{/foreach}
			</select>

			<button type="button" onclick="javacript:popUp('Groups_Popup_Create_Param.php','GroupsM','Create Extension Group',615,500);">Create New Group</button>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	{if $GroupPickup.PK_Extension != "" }
		<button type="submit" name="submit" value="save">Modify Extension</button>
	{else}
		<button type="submit" name="submit" value="save">Create Extension</button>
	{/if}
</p>
</form>