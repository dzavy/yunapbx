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

function Sync_SoundEntries(PK_Folder, PK_Language) {
	PK_Entry  = $("#SoundEntries_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^=SoundEntries_"+PK_Folder+"_]").val(PK_Entry);
	$("#FK_SoundEntry").val(PK_Entry);

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

$(document).ready(function() {
	PK_Entry    = $("#FK_SoundEntry").val();
	PK_Language = $("#SoundLanguages").val();
	{/literal}
	{assign var=SetDefaulSelection value=1}
	{foreach from=$SoundEntries item=SoundEntry}
		{if $SoundEntry.PK_SoundEntry == $Action.Param.FK_SoundEntry}
			{assign var=SetDefaulSelection value=0}
			PK_Folder = {$SoundEntry.FK_SoundFolder};
		{/if}
	{/foreach}

	{if $SetDefaulSelection == 1}
		PK_Folder = {$SoundFolders.0.PK_SoundFolder};
	{/if}

	{literal}

	$("#SoundFolders > option[value='"+PK_Folder+"']").attr("selected","selected");
	$("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr("selected","selected");

	Enable_SoundEntries();
	Sync_SoundEntries(PK_Folder, PK_Language);
});

{/literal}
</script>

<h2>IVR Editor</h2>

{if $Action.PK_Action == "" }
	<h3>New Action: Play Sound</h3>
{else}
	<h3>Modify Action: Play Sound</h3>
{/if}

<form method="post" action="IVR_Actions_Modify.play_sound.php" />

<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
<input type="hidden" name="FK_Menu" value="{$Action.FK_Menu}" />

Folder:
<select id="SoundFolders" onchange="Enable_SoundEntries()">
{foreach from=$SoundFolders item=Folder}
	<option value="{$Folder.PK_SoundFolder}">{$Folder.Name}</option>
{/foreach}
</select>

Language:
<select id="SoundLanguages" onchange="Enable_SoundEntries()" name="Param[FK_SoundLanguage]">
{foreach from=$SoundLanguages item=SoundLanguage}
	<option value="{$SoundLanguage.PK_SoundLanguage}" {if $Action.Param.FK_SoundLanguage == $SoundLanguage.PK_SoundLanguage}selected="selected"{/if}>{$SoundLanguage.Name}</option>
{/foreach}
</select>

Sound to Play:
{foreach from=$SoundFolders item=SoundFolder}
{foreach from=$SoundLanguages item=SoundLanguage}
<select id="SoundEntries_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}" onchange="Sync_SoundEntries({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})" style="display: none;">
	{foreach from=$SoundEntries item=SoundEntry}
		{if $SoundEntry.FK_SoundFolder == $SoundFolder.PK_SoundFolder}
			{assign var=lang_id  value=$SoundLanguage.PK_SoundLanguage}
			{if $SoundEntry.Name.$lang_id != ""}
				<option title="{$SoundEntry.Description.$lang_id}" value="{$SoundEntry.PK_SoundEntry}">{$SoundEntry.Name.$lang_id}</option>
			{else}
				<option style="color: red" value="{$SoundEntry.PK_SoundEntry}">{$SoundEntry.Name.Default}</option>
			{/if}
		{/if}
	{/foreach}
</select>
{/foreach}
{/foreach}
<input type="hidden" name="Param[FK_SoundEntry]" id="FK_SoundEntry" value="{$Action.Param.FK_SoundEntry}" />

<br /><br >
<p>
	<strong>Sound Description:</strong>
	<span id="SoundEntry_Description"></span>
</p>

<br />
<p>
	&nbsp;<input type="checkbox" id="Intrerruptible" name="Param[Intrerruptible]" {if $Action.Param.Intrerruptible}checked="checked"{/if} value="1"/>
	<label for="Intrerruptible">This sound is interruptible</label>
</p>

<br />
<p>
	<button type="submit" name="submit" value="save">Save Settings</button>
</p>
</form>