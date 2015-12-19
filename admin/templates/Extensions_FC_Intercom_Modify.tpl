<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">

{literal}
function Enable_SoundEntries() {
	PK_Folder   = $("#SoundFolders").val();
	PK_Language = $("#SoundLanguages").val();
	PK_Entry    = $("#SoundEntries_"+PK_Folder+"_"+PK_Language).val();


	$("select[id^='SoundEntries_']").css('display','none');
	$("#SoundEntries_"+PK_Folder+"_"+PK_Language).css('display','inline');

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

function ToggleExtGr(val) {
	$('.advanced'+val).toggleClass('hidden');
}

function HtmlInputs(AdminOrMember, id) {
	var type = $("#"+AdminOrMember+"_type").val();
	var val  = $("#"+AdminOrMember+"_"+type).val() || [];
	//console.info (val);
	var str = "";
	var j = 0; //pt a genera id-uri unice
	for (i in val) {
		str += '<input type="hidden"';
		str += 'name   ="'+AdminOrMember+'['+id+']'+'['+type+']'+'['+j+']"';
		str += 'id     ="'+AdminOrMember+'['+id+']'+'['+type+']'+'['+j+']"';
		str +=  'value ='+val[i]+' />';
		j++;
	}
	return str;
}

function HtmlDisplay(AdminOrMember, id) {
	var type = $("#"+AdminOrMember+"_type") .val();
	var val  = $("#"+AdminOrMember+"_"+type).val() || [];

	var str = "";
	if (type == "Extension") {
		str += "<b>Extensions</b><br/>";
		str += "<input type='hidden' id='"+id+AdminOrMember+"' value='pk_ext'>";
	} else                     {
		str += "<b>Groups</b><br/>"    ;
		str += "<input type='hidden' id='"+id+AdminOrMember+"' value='pk_group'>";
	}

	for (i in val) {
		name = $("#"+AdminOrMember+"_"+type+" option[value="+val[i]+"]").html();
		str += name+'<br />';
	}
	return str;
}

function DeleteRow(id, message) {
	$('button').removeClass('disabled');
	$('button').removeAttr('disabled');

	if (message == "delete") {var speed="slow";}
	else                     {var speed="fast";}

	Row = $("#row" + id);
	if (window.confirm("Are you sure you want to "+message+" this extension permission group?"))  {
		CancelEdit();
        Row.fadeOut(speed, function()  {
			Row.remove();
			redrawTable();
		});
	}
	redrawTable();
}

function redrawTable(){
	$("#listing tr").removeClass();
	$("#listing tr:even").addClass("even");
	$("#listing tr:odd").addClass("odd");

	if ($("#listing tr").size() == 2) {
		$("#nomapping").css('display', 'table-row');
	} else {
		$("#nomapping").css('display', 'none');
	}
}


function CancelEdit(){
	$('button').removeClass('disabled');
	$('button').removeAttr('disabled');

	$('.editbox').addClass('hidden');
	$('.grayx').removeClass('grayx');
	generateButton = "<button type='button' name='submit' onclick='javascript:GenerateGroups(0);' >Create Intercom Group</button>";
	$("#zoneedit").html(generateButton);

	redrawTable();
}

function GenerateGroups(id) {
	if (id != 0) {
		DeleteRow(id, "modify");
		generateButton = "<button type='button' name='submit' onclick='javascript:GenerateGroups(0);' >Create Intercom Group</button>";
		$("#options tr:eq(4) td.template_gen_group").html(generateButton);
	}

	var myDate = new Date();
	var newId = myDate.getTime();

	inputs_admin  = HtmlInputs ("Admin", newId);
 	display_admin = HtmlDisplay("Admin", newId);
	if (!inputs_admin) {
		alert ("You have not selected any extension that can use Intercom ");
		return;
	}

	inputs_member  = HtmlInputs ("Member",newId);
	display_member = HtmlDisplay("Member",newId);
	if (!inputs_member) {
		alert ("You have not selected any extension that can they pickup ");
		return;
	}


	editbox = "<div id=\"divedit"+newId+"\" class=\"editbox hidden\"> <img src=\"../static/images/alert.gif\">Currently editing &nbsp;&nbsp;&nbsp;<a href=\"javascript:CancelEdit();\">Cancel Edit</a></div>";

	str = $("#template_tr").html();
	$("#listing tr:first").before('<tr id="row'+newId+'" style="display: none">'+str+'</tr>');
	$("#listing tr:first").fadeIn();


	$("#listing tr:first td.template_td_Admin") .html(inputs_admin + ' ' + display_admin);
	$("#listing tr:first td.template_td_Member").html(editbox+' ' + inputs_member + ' ' + display_member);

	$("#generatedID").attr("id",newId);

	var GrExt1 = $("#"+newId+"Admin" ).val();
	var GrExt2 = $("#"+newId+"Member").val();
	var editButton = "<button id=\"editButton"+newId+"\" type='button' onclick='javascript:EditRow("+newId+",\""+GrExt1+"\",\""+GrExt2+"\");' >Edit</button>";
	$("#listing tr:first td.template_td_edit").html(editButton);

	var deleteButton = "<button id=\"deleteButton"+newId+"\" type='button' onclick='javascript:DeleteRow("+newId+",\"delete\");' class='important'>Delete</button>";
	$("#listing tr:first td.template_td_delete").html(deleteButton);

	$("#listing tr:first").show();

	redrawTable();
}

function EditRow(id, GrExt1, GrExt2) {
	$('button').removeClass('disabled');
	$('button').removeAttr('disabled');

	$('.editbox').addClass('hidden');
	$('#divedit'+id+'.editbox').removeClass('hidden');
	$('#divedit'+id+'.editbox').removeClass('hidden');

	$('#editButton'+id).addClass('disabled');
	$('#deleteButton'+id).addClass('disabled');

	$('#editButton'+id).attr('disabled', 'disabled');
	$('#deleteButton'+id).attr('disabled', 'disabled');

	$("#listing tr").removeClass('grayx');
	$('#row'+id).addClass('grayx');


	//generate edit-buttons
	var editButtons = "";
	editButtons += "<button type='button' name='submit' onclick='javascript:GenerateGroups("+id+");' >Save Changes</button> &nbsp;&nbsp;";
	editButtons += "<button type='button' name='submit' onclick='javascript:CancelEdit();' class='important'> Cancel Edit</button>";

	$("#zoneedit").html(editButtons);

	//unselect previous selections
	$('#Admin_Group option').attr('selected', false);
	$('#Admin_Extension option').attr('selected', false);
	$('#Member_Group option').attr('selected', false);
	$('#Member_Extension option').attr('selected', false);

	//restore admin zone
	if (GrExt1 == "pk_group") {
			var gr_ext_1 = "Group";
			$("#Admin_type").selectOptions("Group");
			$("#Admin_Group").removeClass('advanced1 hidden');
            $("#Admin_Group").addClass('advanced1');
			$("#Admin_Extension").addClass('advanced1 hidden');
	}else {
			var gr_ext_1 = "Extension";
			$("#Admin_type").selectOptions("Extension");
			$("#Admin_Extension").removeClass('advanced1 hidden');
            $("#Admin_Extension").addClass('advanced1');
			$("#Admin_Group").addClass('advanced1 hidden');
	}

	//restore members zone
	if (GrExt2 == "pk_group") {
			var gr_ext_2 = "Group";
			$("#Member_type").selectOptions("Group");
			$("#Member_Group").removeClass('advanced2 hidden');
            $("#Member_Group").addClass('advanced2');
			$("#Member_Extension").addClass('advanced2 hidden');
	}else {
			var gr_ext_2 = "Extension";
			$("#Member_type").selectOptions("Extension");
			$("#Member_Extension").removeClass('advanced2 hidden');
            $("#Member_Extension").addClass('advanced2');
			$("#Member_Group").addClass('advanced2 hidden');
	}

	// select Admins from hidden inputs
	var adminVal = true;
	for (i = 0; adminVal; i++ ) {
		var idSelected =  "Admin["+id+"]["+gr_ext_1+"]["+i+"]";// input type hidden ids
		adminVal = document.getElementById(idSelected);//echivalentul jquery nu merge la []
		if (adminVal != null)
			{ $("#Admin_" + gr_ext_1).selectOptions(adminVal.value); 	}
	}

	// select Members from hidden inputs
	var memberVal = true;
	for (i = 0; memberVal; i++ ) {
		var idSelected =  "Member["+id+"]["+gr_ext_2+"]["+i+"]";
		memberVal = document.getElementById(idSelected);
		if (memberVal != null)
			{ $("#Member_" + gr_ext_2).selectOptions(memberVal.value); }
	}
}

function ValidateMappings() {
	if ($("#listing tr").size() == 2) {
		alert('Nobody is allowed to use this feature code. You must create at least 1 pickup group.');
		return false;
	} else {
		return true;
	}
}

$(document).ready(function(){
	$("option:first", "select#Admin_Extension").attr("selected","selected");
	$("option:first", "select#Admin_Group").attr("selected","selected");
	$("option:first", "select#Member_Extension").attr("selected","selected");
	$("option:first", "select#Member_Group").attr("selected","selected");

	redrawTable();
});



{/literal}
</script>


<h2>Manage Extensions</h2>
{if $Errors.Extension.Invalid}
<p class="error_message">Extension may only consist of digits and must be 2 digits in length.</p>{/if}

{if $Errors.Extension.Duplicate}
<p class="error_message">An extension with that number already exists. Please try another extension.</p>{/if}

{if $Errors.Name.Invalid}
<p class="error_message">Intercom name may only contain alpha-numeric characters or spaces and must be 1-20 characters in length.</p>{/if}

<form action="Extensions_FC_Intercom_Modify.php" method="post" onsubmit="return ValidateMappings()">


<!-- Call Intercom Setup -->
<table class="formtable" style="margin-top: -20px;">
	<tr>
		<td colspan="2">
			<div><br>This feature allows an extension to directly dial another extension's intercom.</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="caption">
			<img src="../static/images/1.gif"/>  <b>Intercom Settings</b>
		</td>
	</tr>

	<!-- Intercom Extension -->
	<tr>
		<td> Intercom Extension	</td>
		<td>
			{if $FC_Intercom.PK_Extension != "" }
				*{$FC_Intercom.Extension}
				<input type="hidden" name="PK_Extension" value="{$FC_Intercom.PK_Extension}" />
			{else}
				<input type="text" size="5" name="Extension" value="{$FC_Intercom.Extension}"
				{if $Errors.Extension} class="error"{/if} />
			{/if}
		</td>
	</tr>

	<!-- Intercom Name -->
	<tr>
		<td>
			Alert-Info header<br/>
		</td>
		<td>
			<input type="text" name="Header" value="{$FC_Intercom.Header}"
			{if $Errors.Header}class="error"{/if} />
		</td>
	</tr>

	<!-- Ringing Strategy -->
	<tr>
		<td>
			Timeout
		</td>
		<td>
			<select name="Timeout">
				<option value= "30" {if $FC_Intercom.Timeout ==  '30'} selected="selected"{/if} >      30 Seconds </option>
				<option value= "60" {if $FC_Intercom.Timeout ==  '60'} selected="selected"{/if} >       1 Minute  </option>
				<option value="120" {if $FC_Intercom.Timeout == '120'} selected="selected"{/if} >       2 Minutes </option>
				<option value="180" {if $FC_Intercom.Timeout == '180'} selected="selected"{/if} >       3 Minutes </option>
				<option value="240" {if $FC_Intercom.Timeout == '240'} selected="selected"{/if} >       4 Minutes </option>
				<option value="300" {if $FC_Intercom.Timeout == '300'} selected="selected"{/if} >       5 Minutes </option>
				<option value="360" {if $FC_Intercom.Timeout == '360'} selected="selected"{/if} >       6 Minutes </option>
				<option value="420" {if $FC_Intercom.Timeout == '340'} selected="selected"{/if} >       7 Minutes </option>
				<option value="600" {if $FC_Intercom.Timeout == '600'} selected="selected"{/if} >      10 Minutes </option>
				<option value=  "0" {if $FC_Intercom.Timeout ==   '0'} selected="selected"{/if} > Never Times Out </option>
			</select>
		</td>
	</tr>

	<!-- Dialed from an IVR  -->
	<tr>
		<td>
			Intercom / Paging <br/>
		</td>
		<td>
			<input type="radio" name="TwoWay" value="1" {if $FC_Intercom.TwoWay=='1'} checked="checked"{/if}>
				2-way Intercom
			<input type="radio" name="TwoWay" value="0" {if $FC_Intercom.TwoWay=='0'} checked="checked"{/if}>
				1-way Paging
		</td>
	</tr>
	<tr>
		<td>
			Play beep/sound first
		</td>
		<td>
			<input type="radio" name="PlaySound" value="1"
			       {if $FC_Intercom.PlaySound=='1'} checked="checked"{/if}
				   onchange="SetSoundState()"/> Yes
			<input type="radio" name="PlaySound" value="0"
			       {if $FC_Intercom.PlaySound=='0'} checked="checked"{/if}
				   onchange="SetSoundState()"/> No
		</td>
	</tr>
</table>

<table class="formtable">
	<tr>
		<td>
			<p>	Choose sound to play: </p>
		</td>
	</tr>

	<tr>
		<td id="SoundRow">
			Folder:
				<select id="SoundFolders" name="SoundFolders" onchange="Enable_SoundEntries()">
				{foreach from=$SoundFolders item=Folder}
					<option value="{$Folder.PK_SoundFolder}"
					    {if $FC_Intercom.FK_Folder == $Folder.PK_SoundFolder}selected="selected"{/if}>{$Folder.Name}
					</option>
				{/foreach}
				</select>
			&nbsp;
			Language:
				<select id="SoundLanguages" name="SoundLanguages" onchange="Enable_SoundEntries()" name="Param[FK_SoundLanguage]">
				{foreach from=$SoundLanguages item=SoundLanguage}
					<option value="{$SoundLanguage.PK_SoundLanguage}"
					    {if $FC_Intercom.FK_Lang == $SoundLanguage.PK_SoundLanguage}selected="selected"{/if}>
					    {$SoundLanguage.Name}
					</option>
				{/foreach}
				</select>
			&nbsp;
			Sound:
				{foreach from=$SoundFolders item=SoundFolder}
					{foreach from=$SoundLanguages item=SoundLanguage}
						<select name="SoundEntries"
							    id="SoundEntries_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}"                                onchange="Sync_SoundEntries({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})"
								style="display: none;">
					{foreach from=$SoundEntries item=SoundEntry}
						{if $SoundEntry.FK_SoundFolder == $SoundFolder.PK_SoundFolder}
							{assign var=lang_id  value=$SoundLanguage.PK_SoundLanguage}
							{if $SoundEntry.Name.$lang_id != ""}
								<option title="{$SoundEntry.Description.$lang_id}"
								        value="{$SoundEntry.PK_SoundEntry}"
										{if $FC_Intercom.FK_Sound == $SoundEntry.PK_SoundEntry} selected="selected" {/if}>
										{$SoundEntry.PK_SoundEntry}-{$SoundEntry.Name.$lang_id}
								</option>
							{else}
								<option style="color: red" value="{$SoundEntry.PK_SoundEntry}">{$SoundEntry.Name.Default}</option>
							{/if}
						{/if}
					{/foreach}
				</select>
				{/foreach}
				{/foreach}
				<input type="hidden" name="Param[FK_SoundEntry]" id="FK_SoundEntry" value="{$FC_Intercom.FK_Sound}" />
		</td>
	</tr>
	<tr>
		<td style="font-size: x-small; padding-top: 5px;">
			Description:
			<em>
				<span id="SoundEntry_Description" name="SoundEntry_Description" style="font-size: x-small">
					{$SoundFolders.Description}
				</span>
			</em>
		</td>
	</tr>
</table>

<!-- ------------------------------------------------------------------------------------------------------------------------ -->

<table id="options" class="formtable">
	<tr>
		<td colspan="2" class="caption">
			<img src="../static/images/2.gif"/>  <b>Intercom Users and Admins</b>
		</td>
	</tr>
	<tr>
		<td>
			<table>
				<tr>
				<td>
					<select name="Admin_type" id="Admin_type" onchange="ToggleExtGr(1)">
						<option value="Extension">Extensions</option>
						<option value="Group"    >Groups    </option>
					</select>
					<strong><small> that can use the intercom</small></strong>

					<br>

					<select name="Admins[]" id="Admin_Extension" multiple="multiple" style="width: 190px; height: 200px;" class="advanced1">
						{foreach from=$Accounts item=Account}
							<option value="{$Account.PK_Extension}">
								 {$Account.Extension} "{$Account.FirstName} {$Account.LastName}"
							</option>
						{/foreach}
					</select>

					<select name="Admins[]" id="Admin_Group" multiple="multiple" style="width:190px; height:200px;" class="advanced1 hidden">
						{foreach from=$Groups item=Item}
							<option value="{$Item.PK_Group}">
								{$Item.Name}
							</option>
						{/foreach}
					</select>
				</td>
				<td>
					<strong><small>Which<select name="Member_type" id="Member_type" onchange="ToggleExtGr(2)">
						<option value="Extension">Extensions</option>
						<option value="Group"    >Groups    </option>
					</select>can they intercom</small></strong>

					<br>

					<select name="Members[]" id="Member_Extension" multiple="multiple" style="width: 190px; height: 200px;" class="advanced2">
						{foreach from=$Accounts item=Account}
							<option value="{$Account.PK_Extension}">
								{$Account.Extension} "{$Account.FirstName} {$Account.LastName}"
							</option>
						{/foreach}
					</select>
					<select name="Members[]" id="Member_Group" multiple="multiple" style="width:190px; height:200px;" class="advanced2 hidden">
						{foreach from=$Groups item=Item}
							<option value="{$Item.PK_Group}">
								{$Item.Name}
							</option>
						{/foreach}
					</select>

				</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td id="zoneedit" name="zoneedit" class="template_gen_group ">
			<button  type="button" name="submit" value="generate_groups" onClick="GenerateGroups(0)">Create Intercom Group</button>
		</td>
	</tr>
</table>

<br />

<table id="listing" class="listing fullwidth mapping">
	<tr id="template_tr" style="visibility:hidden; display:none;">
		<td class="template_td_Admin"  style="width: 40%;"> a </td>
		<td class="template_td_Member" style="width: 40%;"> b </td>
		<td class="template_td_edit"   style="width: 10%;"> c </td>
		<td class="template_td_delete" style="width: 10%;"> d </td>
	</tr>
	<tr style="display:none" id="nomapping"><td colspan="4">No mappings have been made yet.</td></tr>

	{foreach from=$Rows key=id item=variable1}
	<tr id="row{$id}"  class="{cycle values='odd,even'}">
		<td class="template_td_Admin">
		{foreach from=$variable1 key=AdminMember item=variable2}
			{if $AdminMember == "master"}
				{foreach from=$variable2 key=GrExt1 item=variable3}
					{if $GrExt1=="pk_group"}
						{assign var=idadmin value=0}
						<b>Groups</b>
						{foreach from=$variable3 item=pk_extension }
							<br>
							{foreach from=$Groups item=Group}
								{if $Group.PK_Group == $pk_extension}
									<input name ="Admin[{$id}][Group][{$idadmin}]"
									       id   ="Admin[{$id}][Group][{$idadmin}]"
										   value="{$Group.PK_Group}"
										   type ="hidden"/>
									{assign var=idadmin value=$idadmin+1}
									{$Group.Name}
								{/if}
							{/foreach}
						{/foreach}
					{else}
						{assign var=idadmin value=0}
						<b>Extensions</b>
						{foreach from=$variable3 item=pk_extension }
							<br>

							{foreach from=$Accounts item=Extension}
								{if $Extension.PK_Extension == $pk_extension}
									<input name ="Admin[{$id}][Extension][{$idadmin}]"
									       id   ="Admin[{$id}][Extension][{$idadmin}]"
										   value="{$Extension.PK_Extension}"
										   type="hidden"/>
									{assign var=idadmin value=$idadmin+1}
									{$Extension.Extension} "{$Extension.FirstName} {$Extension.LastName}"
								{/if}
							{/foreach}
						{/foreach}
					{/if}
				{/foreach}
			{else}
				</td>
				<td class="template_td_Member">
					<div id="divedit{$id}" class="editbox hidden">
						<img src="../static/images/alert.gif">Currently editing &nbsp;&nbsp;&nbsp;<a href="javascript:CancelEdit();">Cancel Edit</a>
					</div>

				{foreach from=$variable2 key=GrExt2 item=variable3}
					{if $GrExt2=="pk_group"}
						{assign var=idmember value=0}
						<b>Groups</b>
						{foreach from=$variable3 item=pk_extension }
							<br>
							{foreach from=$Groups item=Group}
								{if $Group.PK_Group == $pk_extension}
									<input name ="Member[{$id}][Group][{$idmember}]"
									       id   ="Member[{$id}][Group][{$idmember}]"
										   value="{$Group.PK_Group}"
										   type ="hidden"/>
									{assign var=idmember value=$idmember+1}
									{$Group.Name}
								{/if}
							{/foreach}
						{/foreach}
					{else}
						{assign var=idmember value=0}
						<b>Extensions</b>
						{foreach from=$variable3 item=pk_extension }
							<br>
							{foreach from=$Accounts item=Extension}
								{if $Extension.PK_Extension == $pk_extension}
									<input name="Member[{$id}][Extension][{$idmember}]"
									       id  ="Member[{$id}][Extension][{$idmember}]"
										   value="{$Extension.PK_Extension}"
										   type="hidden"/>
									{assign var=idmember value=$idmember+1}
									{$Extension.Extension} "{$Extension.FirstName} {$Extension.LastName}"
								{/if}
							{/foreach}
						{/foreach}
					{/if}
				{/foreach}
				</td>
				<td class="template_td_edit" style="width: 60px;">
					<button id="editButton{$id}" type="button" onclick = "javascript:EditRow({$id}, '{$GrExt1}', '{$GrExt2}'); " >
						Edit
					</button>
				</td>
				<td class="template_td_delete" style="width: 60px;">
					<button id="deleteButton{$id}" type="button" onclick="javascript:DeleteRow({$id},'delete'); " class="important">
						Delete
					</button>
				</td>
			{/if}
		{/foreach}
	</tr>
	{/foreach}

</table>

<br />
<p>
	{if $FC_Intercom.PK_Extension != ""}
		<button type="submit" name="submit" value="save">Modify Extension</button>
	{else}
		<button type="submit" name="submit" value="save">Create Extension</button>
	{/if}
</p>
</form>