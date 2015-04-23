<script type="text/javascript">
{literal}

/* Called to enable the 'Sound To Play' select for 'Invalid Option Settings' */
function Enable_SoundEntries_Invalid() {
	PK_Folder   = $("#SoundFolders_Invalid").val();
	PK_Language = $("#SoundLanguages_Invalid").val();
	PK_Entry    = $("#SoundEntries_Invalid_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^='SoundEntries_Invalid_']").css('display','none');
	$("#SoundEntries_Invalid_"+PK_Folder+"_"+PK_Language).css('display','inline');
}

/* Called to set the FK_SoundEntry_Invalid hidden for 'Invalid Option Settings' */
function Sync_SoundEntries_Invalid(PK_Folder, PK_Language) {
	PK_Entry  = $("#SoundEntries_Invalid_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^=SoundEntries_Invalid_"+PK_Folder+"_]").val(PK_Entry);
	$("#FK_SoundEntry_Invalid").val(PK_Entry);

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

/* Called to enable the 'Step' for 'Invalid Option Settings' */
function UpdateActions_Invalid() {
	PK_Menu = $("#FK_Menu_Invalid").val();
	$("select[id^='FK_Menu_Invalid_']").css('display','none');
	$("#FK_Menu_Invalid_"+PK_Menu+"_Actions").css('display','inline');

	PK_Action = $("#FK_Menu_Invalid_"+PK_Menu+"_Actions").val();
	$("#FK_Action_Invalid").val(PK_Action);
}




function Enable_SoundEntries_Timeout() {
	PK_Folder   = $("#SoundFolders_Timeout").val();
	PK_Language = $("#SoundLanguages_Timeout").val();
	PK_Entry    = $("#SoundEntries_Timeout_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^='SoundEntries_Timeout_']").css('display','none');
	$("#SoundEntries_Timeout_"+PK_Folder+"_"+PK_Language).css('display','inline');
}

function Sync_SoundEntries_Timeout(PK_Folder, PK_Language) {
	PK_Entry  = $("#SoundEntries_Timeout_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^=SoundEntries_Timeout_"+PK_Folder+"_]").val(PK_Entry);
	$("#FK_SoundEntry_Timeout").val(PK_Entry);

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

/* Called to enable the 'Step' for 'Timeout Option Settings' */
function UpdateActions_Timeout() {
	PK_Menu = $("#FK_Menu_Timeout").val();
	$("select[id^='FK_Menu_Timeout_']").css('display','none');
	$("#FK_Menu_Timeout_"+PK_Menu+"_Actions").css('display','inline');

	PK_Action = $("#FK_Menu_Timeout_"+PK_Menu+"_Actions").val();
	$("#FK_Action_Timeout").val(PK_Action);
}




function Enable_SoundEntries_Retry() {
	PK_Folder   = $("#SoundFolders_Retry").val();
	PK_Language = $("#SoundLanguages_Retry").val();
	PK_Entry    = $("#SoundEntries_Retry_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^='SoundEntries_Retry_']").css('display','none');
	$("#SoundEntries_Retry_"+PK_Folder+"_"+PK_Language).css('display','inline');
}

function Sync_SoundEntries_Retry(PK_Folder, PK_Language) {
	PK_Entry  = $("#SoundEntries_Retry_"+PK_Folder+"_"+PK_Language).val();

	$("select[id^=SoundEntries_Retry_"+PK_Folder+"_]").val(PK_Entry);
	$("#FK_SoundEntry_Retry").val(PK_Entry);

	desc = $("#SoundEntries_"+PK_Folder+"_"+PK_Language+" > option[value='"+PK_Entry+"']").attr('title');
	$("#SoundEntry_Description").html(desc+"");

}

/* Called to enable the 'Step' for 'Timeout Option Settings' */
function UpdateActions_Retry() {
	PK_Menu = $("#FK_Menu_Retry").val();
	$("select[id^='FK_Menu_Retry_']").css('display','none');
	$("#FK_Menu_Retry_"+PK_Menu+"_Actions").css('display','inline');

	PK_Action = $("#FK_Menu_Retry_"+PK_Menu+"_Actions").val();
	$("#FK_Action_Retry").val(PK_Action);
}

$(document).ready(function() {
	PK_SoundEntry_Invalid    = $("#FK_SoundEntry_Invalid").val();
	PK_SoundLanguage_Invalid = $("#SoundLanguages_Invalid").val();
	PK_SoundEntry_Timeout    = $("#FK_SoundEntry_Timeout").val();
	PK_SoundLanguage_Timeout = $("#SoundLanguages_Timeout").val();
	PK_SoundEntry_Retry      = $("#FK_SoundEntry_Retry").val();
	PK_SoundLanguage_Retry   = $("#SoundLanguages_Retry").val();
	
	PK_SoundFolder_Invalid=0;
	PK_SoundFolder_Timeout=0;
	PK_SoundFolder_Retry=0;
	{/literal}{foreach from=$SoundEntries item=SoundEntry}
		{if $SoundEntry.PK_SoundEntry == $IVR_Menu.FK_SoundEntry_Invalid}PK_SoundFolder_Invalid= {$SoundEntry.FK_SoundFolder};{/if}
		{if $SoundEntry.PK_SoundEntry == $IVR_Menu.FK_SoundEntry_Timeout}PK_SoundFolder_Timeout= {$SoundEntry.FK_SoundFolder};{/if}
		{if $SoundEntry.PK_SoundEntry == $IVR_Menu.FK_SoundEntry_Retry  }PK_SoundFolder_Retry  = {$SoundEntry.FK_SoundFolder};{/if}
	{/foreach}{literal}
	
	$("#SoundFolders_Invalid > option[value='"+PK_SoundFolder_Invalid+"']").attr("selected","selected");
	$("#SoundFolders_Timeout > option[value='"+PK_SoundFolder_Timeout+"']").attr("selected","selected");
	$("#SoundFolders_Retry > option[value='"+PK_SoundFolder_Retry+"']").attr("selected","selected");
	
	$("#SoundEntries_Invalid_"+PK_SoundFolder_Invalid+"_"+PK_SoundLanguage_Invalid+" > option[value='"+PK_SoundEntry_Invalid+"']").attr("selected","selected");
	$("#SoundEntries_Timeout_"+PK_SoundFolder_Timeout+"_"+PK_SoundLanguage_Timeout+" > option[value='"+PK_SoundEntry_Timeout+"']").attr("selected","selected");
	$("#SoundEntries_Retry_"+PK_SoundFolder_Retry+"_"+PK_SoundLanguage_Retry+" > option[value='"+PK_SoundEntry_Retry+"']").attr("selected","selected");

	Enable_SoundEntries_Invalid();
	Enable_SoundEntries_Timeout();
	Enable_SoundEntries_Retry();

	PK_Menu_Invalid = {/literal}{$IVR_Menu.FK_Menu_Invalid+0}{literal}; PK_Action_Invalid = 0;
	PK_Menu_Timeout = {/literal}{$IVR_Menu.FK_Menu_Timeout+0}{literal}; PK_Action_Timeout = 0;
	PK_Menu_Retry   = {/literal}{$IVR_Menu.FK_Menu_Retry+0}{literal}; PK_Action_Retry   = 0;
	{/literal}{foreach from=$Menus item=Menu}{foreach from=$Menu.Actions item=IVR_Action}
	{if $IVR_Action.PK_Action == $IVR_Menu.FK_Action_Invalid}
		PK_Menu_Invalid   = {$Menu.PK_Menu}; PK_Action_Invalid = {$IVR_Action.PK_Action};
	{/if}
	{if $IVR_Action.PK_Action == $IVR_Menu.FK_Action_Timeout}
		PK_Menu_Timeout   = {$Menu.PK_Menu}; PK_Action_Timeout = {$IVR_Action.PK_Action};
	{/if}
	{if $IVR_Action.PK_Action == $IVR_Menu.FK_Action_Retry}
		PK_Menu_Retry     = {$Menu.PK_Menu}; PK_Action_Retry = {$IVR_Action.PK_Action};
	{/if}
	{/foreach}{/foreach}{literal}
	$("#FK_Menu_Invalid").val(PK_Menu_Invalid);
	$("#FK_Menu_Timeout").val(PK_Menu_Timeout);
	$("#FK_Menu_Retry"  ).val(PK_Menu_Retry  );
	$("#FK_Menu_Invalid_"+PK_Menu_Invalid+"_Actions").val(PK_Action_Invalid);
	$("#FK_Menu_Timeout_"+PK_Menu_Timeout+"_Actions").val(PK_Action_Timeout);
	$("#FK_Menu_Retry_"  +PK_Menu_Retry  +"_Actions").val(PK_Action_Retry  );
	
	UpdateActions_Invalid();
	UpdateActions_Timeout();
	UpdateActions_Retry();
});
{/literal}
</script>

<h2>IVR Editor</h2>
{if $Message != ""}
<p class="success_message">
{if     $Message == "ADD_OPTION"    } Successfully added new option to this IVR Menu.
{elseif $Message == "MODIFY_OPTION" } Successfully modified option.
{elseif $Message == "DELETE_OPTION" } Successfully deleted option.
{/if}
</p>
{/if}

<p>
	<a href="IVR_Menus.php?PK_Menu={$History.PK_Menu}#{$History.Tree}">
		<img src="images/left-arrow.gif" />Back to IVR Editor
	</a>
</p>

<table class="listing fullwidth">
	<caption>Current Options ( 1 to {$IVR_Options|@count} ) of {$IVR_Options|@count}</caption>
	<tr>
		<th>Option</th>
		<th>Menu</th>
		<th>Action</th>
		<th style="width: 130px"></th>
	</tr>
	{foreach from=$IVR_Options item=Option}
	<tr class="{if $Hilight == $Option.PK_Option}hilight{/if} {cycle values="odd,even"}">
		<td>{$Option.Key}</td>
		<td>{$Option.Menu}</td>
		<td>
			{if $Option.Action_Order > 0}
				{$Option.Action_Order}.
				{php}
					$Option             = $this->get_template_vars('Option');
					$IVR_Action['Type'] = $Option['Action_Type'];
					$this->assign('IVR_Action', $IVR_Action);
				{/php}
				{include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Name'}
			{else}
				IVR Menu Begining
			{/if}
		</td>
		<td>
			<form method="get" action="IVR_Options_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_Option" value="{$Option.PK_Option}" />
				<button type="submit" name="submit" value="modify">Modify</button>
			</form>

			<form method="get" action="IVR_Options_Delete" style="display: inline;">
				<input type="hidden" name="PK_Menu"   value="{$PK_Menu}" />
				<input type="hidden" name="PK_Option" value="{$Option.PK_Option}" />
				<button type="submit" name="submit" value="delete" class="important">Delete</button>
			</form>
		</td>
	</tr>
	{foreachelse}
	<tr><td colspan="4" style="text-align: center">
		No options are currently defined for this IVR menu, click the "Add A New Option" button below.
	</td></tr>
	{/foreach}
</table>
<br />
<p>
	<form method="get" action="IVR_Options_Modify.php" style="display: inline;">
		<input type="hidden" name="FK_Menu" value="{$PK_Menu}" />
		<button type="submit" name="submit" value="add">Add A New Option</button>
	</form>
</p>

<form method="post" action="IVR_Options_List.php?PK_Menu={$PK_Menu}">
<br />
<br />
<hr />
<br />
<h2>Enable Extension Dialing</h2>
<p>
	Would you like to allow callers to dial extensions when they are in this IVR Menu?
</p>

<p>
	&nbsp;&nbsp;<input type="checkbox" name="ExtensionDialing" value="1" {if $IVR_Menu.ExtensionDialing}checked="checked"{/if} /> Enable extension dialing
</p>
<br />
<p>
	<button name="submit" value="ext_dialing_settings">Save Extension Dialing Settings</button>
</p>


<br />
<br />
<hr />
<br />
<h2>Invalid Option Settings</h2>
<p>
	What should happen when a caller presses a key for an option that doesn't exist?
</p>
<br />

<div >
	&nbsp;&nbsp;&nbsp;<strong>Play a sound file :</strong>
	<div style="padding-left: 20px;">
		Folder:
		<select id="SoundFolders_Invalid" onchange="Enable_SoundEntries_Invalid()">
		{foreach from=$SoundFolders item=Folder}
			<option value="{$Folder.PK_SoundFolder}">{$Folder.Name}</option>
		{/foreach}
		</select>

		Language:
		<select id="SoundLanguages_Invalid" onchange="Enable_SoundEntries_Invalid()" name="FK_SoundLanguage_Invalid">
		{foreach from=$SoundLanguages item=SoundLanguage}
			<option value="{$SoundLanguage.PK_SoundLanguage}" {if $IVR_Menu.FK_SoundLanguage_Invalid == $SoundLanguage.PK_SoundLanguage}selected="selected"{/if}>{$SoundLanguage.Name}</option>
		{/foreach}
		</select>

		Sound to Play:
		{foreach from=$SoundFolders item=SoundFolder}
		{foreach from=$SoundLanguages item=SoundLanguage}
		<select id="SoundEntries_Invalid_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}" onchange="Sync_SoundEntries_Invalid({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})" style="display: none;">
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
		<input type="hidden" name="FK_SoundEntry_Invalid" id="FK_SoundEntry_Invalid" value="{$IVR_Menu.FK_SoundEntry_Invalid}" />
		<br />
	</div>
	<br />

	&nbsp;&nbsp;&nbsp;<strong>Go To :</strong>
	<div style="padding-left: 20px;">
		IVR Menu:
		<input type="hidden" name="FK_Action_Invalid" id="FK_Action_Invalid" value="{$IVR_Menu.FK_Action_Invalid}" />
		<select name="FK_Menu_Invalid" id="FK_Menu_Invalid" onchange="UpdateActions_Invalid()">
			{foreach from=$Menus item=Menu}
			<option value="{$Menu.PK_Menu}" {if $IVR_Menu.FK_Menu_Invalid == $Menu.PK_Menu}selected="selected"{/if}>{$Menu.Name}</option>
			{/foreach}
		</select>

		Step:
		{foreach from=$Menus item=Menu}
		<select name="FK_Menu_Invalid_{$Menu.PK_Menu}_Actions" id="FK_Menu_Invalid_{$Menu.PK_Menu}_Actions" style="display:none" onchange="UpdateActions_Invalid()">
			<option value="0">IVR Menu Begining</option>
			{foreach from=$Menu.Actions item=IVR_Action}
			<option value="{$IVR_Action.PK_Action}">
				{$IVR_Action.Order}.{include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Name'} ({include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Desc'})
			</option>
			{/foreach}
		</select>
		{/foreach}
	</div>

	<br />
	<button name="submit" value="invalid_settings">Save Invalid Settings</button>
</div>

<br />
<br />
<hr />
<br />
<h2>Option Timeout Settings</h2>
<p>
	What should happen if a caller fails to select any options within 
	<select name="Timeout" id="Timeout">
		<option value="0"  {if $IVR_Menu.Timeout=='0'}selected="selected" {/if} >0</option>
		<option value="1"  {if $IVR_Menu.Timeout=='1'}selected="selected" {/if} >1</option>
		<option value="2"  {if $IVR_Menu.Timeout=='2'}selected="selected" {/if} >2</option>
		<option value="3"  {if $IVR_Menu.Timeout=='3'}selected="selected" {/if} >3</option>
		<option value="4"  {if $IVR_Menu.Timeout=='4'}selected="selected" {/if} >4</option>
		<option value="5"  {if $IVR_Menu.Timeout=='5'}selected="selected" {/if} >5</option>
		<option value="6"  {if $IVR_Menu.Timeout=='6'}selected="selected" {/if} >6</option>
		<option value="7"  {if $IVR_Menu.Timeout=='7'}selected="selected" {/if} >7</option>
		<option value="8"  {if $IVR_Menu.Timeout=='8'}selected="selected" {/if} >8</option>
		<option value="9"  {if $IVR_Menu.Timeout=='9'}selected="selected" {/if} >9</option>
		<option value="10" {if $IVR_Menu.Timeout=='10'}selected="selected"{/if} >10</option>
		<option value="11" {if $IVR_Menu.Timeout=='11'}selected="selected"{/if} >11</option>
		<option value="12" {if $IVR_Menu.Timeout=='12'}selected="selected"{/if} >12</option>
		<option value="13" {if $IVR_Menu.Timeout=='13'}selected="selected"{/if} >13</option>
		<option value="14" {if $IVR_Menu.Timeout=='14'}selected="selected"{/if} >14</option>
		<option value="15" {if $IVR_Menu.Timeout=='15'}selected="selected"{/if} >15</option>
		<option value="20" {if $IVR_Menu.Timeout=='20'}selected="selected"{/if} >20</option>
	</select>
	seconds?
</p>
<br />

<div >
	&nbsp;&nbsp;&nbsp;<strong>Play a sound file :</strong>
	<div style="padding-left: 20px;">
		Folder:
		<select id="SoundFolders_Timeout" onchange="Enable_SoundEntries_Timeout()">
		{foreach from=$SoundFolders item=Folder}
			<option value="{$Folder.PK_SoundFolder}">{$Folder.Name}</option>
		{/foreach}
		</select>

		Language:
		<select id="SoundLanguages_Timeout" onchange="Enable_SoundEntries_Timeout()" name="FK_SoundLanguage_Timeout">
		{foreach from=$SoundLanguages item=SoundLanguage}
			<option value="{$SoundLanguage.PK_SoundLanguage}">{$SoundLanguage.Name}</option>
		{/foreach}
		</select>

		Sound to Play:
		{foreach from=$SoundFolders item=SoundFolder}
		{foreach from=$SoundLanguages item=SoundLanguage}
		<select id="SoundEntries_Timeout_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}" onchange="Sync_SoundEntries_Timeout({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})" style="display: none;">
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
		<input type="hidden" name="FK_SoundEntry_Timeout" id="FK_SoundEntry_Timeout" value="{$IVR_Menu.FK_SoundEntry_Timeout}" />
		<br />
	</div>
	<br />

	&nbsp;&nbsp;&nbsp;<strong>Go To :</strong>
	<div style="padding-left: 20px;">
		IVR Menu:
		<input type="hidden" name="FK_Action_Timeout" id="FK_Action_Timeout" value="{$IVR_Menu.FK_Action_Timeout}" />
		<select name="FK_Menu_Timeout" id="FK_Menu_Timeout" onchange="UpdateActions_Timeout()">
			{foreach from=$Menus item=Menu}
			<option value="{$Menu.PK_Menu}">{$Menu.Name}</option>
			{/foreach}
		</select>

		Step:
		{foreach from=$Menus item=Menu}
		<select name="FK_Menu_Timeout_{$Menu.PK_Menu}_Actions" id="FK_Menu_Timeout_{$Menu.PK_Menu}_Actions" style="display:none" onchange="UpdateActions_Timeout()">
			<option value="0">IVR Menu Begining</option>
			{foreach from=$Menu.Actions item=IVR_Action}
			<option value="{$IVR_Action.PK_Action}">
				{$IVR_Action.Order}.{include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Name'} ({include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Desc'})
			</option>
			{/foreach}
		</select>
		{/foreach}

	</div>

	<br />
	<button name="submit" value="timeout_settings">Save Timeout Settings</button>
</div>


<br />
<br />
<hr />
<br />
<h2>Retry Settings</h2>
<p>
	What should happen when a caller chooses an invalid option or times out more than
	<select name="Retry" id="Retry">
		<option value="0" {if $IVR_Menu.Retry=='0'}selected="selected"{/if} >unlimited</option>
		<option value="1" {if $IVR_Menu.Retry=='1'}selected="selected"{/if} >1</option>
		<option value="2" {if $IVR_Menu.Retry=='2'}selected="selected"{/if} >2</option>
		<option value="3" {if $IVR_Menu.Retry=='3'}selected="selected"{/if} >3</option>
		<option value="4" {if $IVR_Menu.Retry=='4'}selected="selected"{/if} >4</option>
		<option value="5" {if $IVR_Menu.Retry=='5'}selected="selected"{/if} >5</option>
		<option value="6" {if $IVR_Menu.Retry=='6'}selected="selected"{/if} >6</option>
		<option value="7" {if $IVR_Menu.Retry=='7'}selected="selected"{/if} >7</option>
		<option value="8" {if $IVR_Menu.Retry=='8'}selected="selected"{/if} >8</option>
		<option value="9" {if $IVR_Menu.Retry=='9'}selected="selected"{/if} >9</option>
	</select>
	times?
</p>
<br />

<div >
	&nbsp;&nbsp;&nbsp;<strong>Play a sound file :</strong>
	<div style="padding-left: 20px;">
		Folder:
		<select id="SoundFolders_Retry" onchange="Enable_SoundEntries_Retry()">
		{foreach from=$SoundFolders item=Folder}
			<option value="{$Folder.PK_SoundFolder}">{$Folder.Name}</option>
		{/foreach}
		</select>

		Language:
		<select id="SoundLanguages_Retry" onchange="Enable_SoundEntries_Retry()" name="FK_SoundLanguage_Retry">
		{foreach from=$SoundLanguages item=SoundLanguage}
			<option value="{$SoundLanguage.PK_SoundLanguage}" {if $Action.Param.FK_SoundLanguage == $SoundLanguage.PK_SoundLanguage}selected="selected"{/if}>{$SoundLanguage.Name}</option>
		{/foreach}
		</select>

		Sound to Play:
		{foreach from=$SoundFolders item=SoundFolder}
		{foreach from=$SoundLanguages item=SoundLanguage}
		<select id="SoundEntries_Retry_{$SoundFolder.PK_SoundFolder}_{$SoundLanguage.PK_SoundLanguage}" onchange="Sync_SoundEntries_Retry({$SoundFolder.PK_SoundFolder},{$SoundLanguage.PK_SoundLanguage})" style="display: none;">
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
		<input type="hidden" name="FK_SoundEntry_Retry" id="FK_SoundEntry_Retry" value="{$IVR_Menu.FK_SoundEntry_Retry}" />
		<br />
	</div>
	<br />

	&nbsp;&nbsp;&nbsp;<strong>Go To :</strong>
	
	<div style="padding-left: 20px;">
		IVR Menu:
		<input type="hidden" name="FK_Action_Retry" id="FK_Action_Retry" value="{$IVR_Menu.FK_Action_Retry}" />
		<select name="FK_Menu_Retry" id="FK_Menu_Retry" onchange="UpdateActions_Retry()">
			{foreach from=$Menus item=Menu}
			<option value="{$Menu.PK_Menu}">{$Menu.Name}</option>
			{/foreach}
		</select>

		Step:
		{foreach from=$Menus item=Menu}
		<select name="FK_Menu_Retry_{$Menu.PK_Menu}_Actions" id="FK_Menu_Retry_{$Menu.PK_Menu}_Actions" style="display:none" onchange="UpdateActions_Retry()">
			<option value="0">IVR Menu Begining</option>
			{foreach from=$Menu.Actions item=IVR_Action}
			<option value="{$IVR_Action.PK_Action}">
				{$IVR_Action.Order}.{include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Name'} ({include file=IVR_Actions_Display.tpl Action=$IVR_Action Display='Desc'})
			</option>
			{/foreach}
		</select>
		{/foreach}

	</div>

	<br />
	<button name="submit" value="retry_settings">Save Retry Settings</button>
</div>
</form>