<script type="text/javascript" src="../script/jqModal.js"></script>
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

function DisplaySource() {
	var Source = $("#Source").val();
	if (Source == 'phone') {
		$('#source_phone').show();
		$('#source_file').hide();
	} else {
		$('#source_phone').hide();
		$('#source_file').show();		
	}
}

function Record() {
	if ($("#Extension").val() == '') {
		$("#Extension").addClass('error');
		return;
	}
	$("#Extension").removeClass('error');
	
	
	$("#ring_dialog").jqmShow();
	$.post('SoundFiles_Ajax.php', 
	{
		Action       : "RecordSound",
		Extension    : $("#Extension").val()
	}, 
	Record_Callback,
	"json");
}
function Record_Callback(data) {
	$("#ring_dialog").jqmHide();
	$('#RecordedFile').val(data['TmpFile']);
	$("#wait_dialog").jqmShow();
}

function VerifyFile() {
	$.post('SoundFiles_Ajax.php', 
	{
		Action : "VerifyFile",
		File   : $("#RecordedFile").val()
	}, 
	VerifyFile_Callback,
	"json");	
}
function VerifyFile_Callback(data) {
	$('#RecordMessage').removeClass('success_message');
	$('#RecordMessage').removeClass('error_message');
	if(data.FileExists) {
		$('#RecordMessage').addClass('success_message');
		$('#RecordMessage').html('Sucessfully recorded your sound');
	} else {
		$('#RecordMessage').addClass('error_message');
		$('#RecordMessage').html("Your sound doesn't exist. Did you answer the phone?.");
	}
}

$(document).ready(
	function () {
		DisplaySource();
		$(".window").jqm({modal: true, trigger: false}).jqDrag('.window_titlebar');
	}
);
{/literal}
</script>

<div class="window" id="ring_dialog">
	<div class="window_titlebar">Answer the Phone</div>
	<strong>Your phone should be ringing.</strong>
	<br />
	<img src="images/ringing_phone.gif" alt="Ringing ...">
	<br />
	Please answer your phone and follow the said instructions.
</div>

<div class="window" id="wait_dialog">
	<div class="window_titlebar">Record Sound</div>
	<strong>After</strong> you have recorded your sound and hung up your phone press the "Done" button below.
	<br /><br />
	<button class="jqmClose" value="done" onclick="VerifyFile()">Done</button>
	<button class="important jqmClose" value="cancel">Cancel</button>
</div>

<h2>Sound Manager</h2>

{if $Errors.Name} <p class="error_message">Incorrect value for your sound name (1-30 characters)</p>{/if}
{if $Errors.File} <p class="error_message">Please upload or record a sound file.</p>{/if}

<form enctype="multipart/form-data" method="post" action="SoundFiles_Modify.php">

<input type="hidden" name="PK_SoundFile"  value="{$SoundFile.PK_SoundFile}" />
<input type="hidden" name="FK_SoundEntry" value="{$PK_SoundEntry}" />

<table class="formtable">
	<!-- Sound Name -->
	<tr>
		<td>Sound Name:</td>
		<td>
			<input type="text" name="Name" value="{$SoundFile.Name}" {if $Errors.Name }class="error"{/if} />
		</td>
	</tr>
	
	<!-- Sound Description -->
	<tr>
		<td>
			Sound Description / Script:
			<br />
			<small>What are the exact words <br />of this recording?</small>
		</td>
		<td>
			<textarea name="Description" rows="5" cols="40">{$SoundFile.Description}</textarea>
		</td>
	</tr>
	
	<!-- Folder -->
	<tr>
		<td>Folder:</td>
		<td>
		{if $PK_SoundFolder != "" }
			<input type="hidden" name="FK_SoundFolder" value="{$PK_SoundFolder}" />
			{foreach from=$SoundFolders item=Folder}
				{if $Folder.PK_SoundFolder == $PK_SoundFolder} {$Folder.Name} {/if}
			{/foreach}
		{else}
			<select name="FK_SoundFolder">
			{foreach from=$SoundFolders item=Folder}
				<option {if $Folder.PK_SoundFolder == $SoundFile.FK_SoundFolder}selected="selected"{/if} value="{$Folder.PK_SoundFolder}">{$Folder.Name}</option>
			{/foreach}
			</select>
		{/if}
		</td>
	</tr>
	
	<!-- Sound Language -->
	<tr>
		<td>Sound Language:</td>
		<td>
		{if $PK_SoundLanguage != "" }
			<input type="hidden" name="FK_SoundLanguage" value="{$PK_SoundLanguage}" />
			{foreach from=$SoundLanguages item=Language}
				{if $Language.PK_SoundLanguage == $PK_SoundLanguage} {$Language.Name} {/if}
			{/foreach}
		{else}
			<select name="FK_SoundLanguage">
			{foreach from=$SoundLanguages item=Language}
				<option {if $Language.PK_SoundLanguage == $SoundFile.FK_SoundLanguage}selected="selected"{/if} value="{$Language.PK_SoundLanguage}">{$Language.Name}</option>
			{/foreach}
			</select>
		{/if}
		</td>
	</tr>
	
	<!-- Sound Source -->
	<tr>
		<td>Sound Source:</td>
		<td>
			<select name="Source" id="Source" onchange="DisplaySource()">
				<option {if $SoundFile.Source=='phone'}selected="selected"{/if} value="phone">Record Over Phone</option>
				<option {if $SoundFile.Source=='file'}selected="selected"{/if} value="file" >Upload File</option>
			</select>
		</td>
	</tr>
	
	<!-- Extensio to Ring for Recording -->
	<tr id="source_phone">
		<td>
			Extension to Ring<br />
			for Recording:
		</td>
		<td>
			<input  type="hidden" id="RecordedFile" name="RecordedFile" value="" />
			<input  type="text"   name="Extension" id="Extension" size="3" />
			<button type="button" class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Extension','Select Extension',415,400);">&nbsp;</button>
			<button type="button" onclick="Record()">Record</button>
			<span id="RecordMessage"></span>
		</td>
	</tr>
	
	<!-- Upload File -->
	<tr id="source_file">
		<td>Upload File:</td>
		<td>
			<input type="file" name="File" />
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Save Sound</button>
</p>
</form>