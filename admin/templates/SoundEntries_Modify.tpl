<script type="text/javascript" src="../static/script/jquery.jqModal.js"></script>
<script type="text/javascript">
{literal}
function PlayPhone() {
	$("#extension_dialog").jqmShow();
}

function PlayPhone_Ring() {
	if ($("#Extension").val() == '') {
		$("#Extension").addClass('error');
		return;
	}	
	$("#Extension").removeClass('error');
	
	$("#extension_dialog").jqmHide();
	$.post('SoundFiles_Ajax.php', 
	{
		Action         : "PlayFile",
		PK_SoundFile   : $("#PK_SoundFile").val(),
		Extension      : $("#Extension").val()
	}, 
	PlayPhone_Callback, "json");	
	$("#ring_dialog").jqmShow();
}

function PlayPhone_Callback(data) {
	$("#ring_dialog").jqmHide();
}

function Download() {
	document.location='SoundFiles_Download.php?PK_SoundFile='+$("#PK_SoundFile").val();
}

function PerformAction(PK_SoundEntry, PK_SoundLanguage, PK_SoundFile) {
	var Action = $('#Action_'+PK_SoundLanguage).val();
	
	if (Action == 'Create') {
		document.location = 'SoundFiles_Modify.php?PK_SoundEntry='+PK_SoundEntry+'&PK_SoundLanguage='+PK_SoundLanguage+'';
		
	} else if (Action == 'Modify') {
		document.location = 'SoundFiles_Modify.php?PK_SoundFile='+PK_SoundFile;
		
	} else if (Action == 'Phone') {
		$("#PK_SoundFile").val(PK_SoundFile);
		PlayPhone();
		
	} else if (Action == 'Download') {
		$("#PK_SoundFile").val(PK_SoundFile);
		Download();
	} else if (Action == 'Delete') {
		document.location = 'SoundFiles_Delete.php?PK_SoundFile='+PK_SoundFile;
	}
}

$(document).ready(
	function () {
		$(".window").jqm({modal: true, trigger: false}).jqDrag('.window_titlebar');
	}
);

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

<!-- Answer the Phone Window -->
<div class="window" id="ring_dialog">
	<div class="window_titlebar">Answer the Phone</div>
	<strong>Ringing Phone.</strong>
	<br />
	<img src="../static/images/ringing_phone.gif" alt="Ringing ...">
	<br />
	Your phone should now be ringing.<br />
	Please answer your phone to hear the sound.
	<br /><br />
	<button type="button" class="jqmClose">Close</button>
</div>

<!-- Play to Phone Window -->
<div class="window" id="extension_dialog">
	<div class="window_titlebar">Play to Phone</div>
	<br />
	Extension to Call :
	<input type="text" name="Extension" id="Extension" size="3" />
	<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Extension','Select Extension',415,400);">&nbsp;</button>
	<button type="button" onclick="PlayPhone_Ring()">Go</button>
	<button type="button" class="important jqmClose">Close</button>
	<br />
	&nbsp;
</div>

<input type="hidden" name="_PK_SoundFile" id="PK_SoundFile" value="" />

<h2>Sound Manager</h2>
<br />
<strong>Update a Sound</strong>
<table class="listing fullwidth">
	<tr>
		<th>Status</th>
		<th>Language</th>
		<th>Sound Information</th>
		<th>Actions</th>
	</tr>
	{foreach from=$SoundFiles item=File}
	<tr class="{cycle values="even,odd"}">
		<td>
			{if $File.PK_SoundFile}
				<img src="../static/images/success.gif" />
				Active
			{else}
				<img src="../static/images/alert.gif" />
				Empty
			{/if}
		</td>
		<td>{$File.Language}</td>
		<td>
			{if $File.PK_SoundFile}
				<strong>Name:</strong> {$File.Name}
				<br />
				<strong>Description:</strong> {$File.Description}
			{else}
				There isn't a sound for this language. 
			{/if}
		</td>
		<td>
			<select id="Action_{$File.FK_SoundLanguage}" style="width: 120px;">
				{if $File.PK_SoundFile}
				<option value="Phone"   >Play To Phone</option>
				<option value="Download">Download</option>
				<option value="Modify"  >Modify</option>
				<option value="Delete"  >Delete</option>
				{else}
				<option value="Create">Create</option>
				{/if}
			</select>
			<button onclick="PerformAction('{$File.FK_SoundEntry}', '{$File.FK_SoundLanguage}', '{$File.PK_SoundFile}')">Go</button>
		</td>
	</tr>
	{/foreach}
</table>