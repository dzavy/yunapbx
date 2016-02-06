<script type="text/javascript" src="../static/script/calendar.js"></script>
<script type="text/javascript" src="../static/script/calendar-setup.js"></script>
<script type="text/javascript" src="../static/script/calendar-en.js"></script>

<script type="text/javascript">
{literal}
function DisplaySelect() {
	var Type = $("#Type").val();

	$('div[id$=_Select]').hide();
	$('div[id$=_Select] select').attr('disabled', 'disabled');

	$('#'+Type+'_Select').show();
	$('#'+Type+'_Select select').removeAttr('disabled');
}

function KeepType_onChange() {
	if( $("#KeepType_1").is(':checked')) {
		$("#KeepType_1_Container input[type=text]").removeAttr('disabled');
		$("#KeepType_2_Container input[type=text]").attr('disabled', 'disasbled');
		$("#KeepType_3_Container input[type=text]").attr('disabled', 'disasbled');

		$("#KeepType_1_Container select").removeAttr('disabled');
		$("#KeepType_2_Container select").attr('disabled', 'disasbled');
		$("#KeepType_3_Container select").attr('disabled', 'disasbled');

		$("#KeepType_1_Container label").removeClass('disabled');
		$("#KeepType_2_Container label").addClass('disabled');
		$("#KeepType_3_Container label").addClass('disabled');

		$("#KeepCount").val(0);
		$("#KeepSize" ).val(0);
		$("#Backup"   ).val(0);


	} else if( $("#KeepType_2").is(':checked')) {
		$("#KeepType_2_Container input[type=text]").removeAttr('disabled');
		$("#KeepType_1_Container input[type=text]").attr('disabled', 'disasbled');
		$("#KeepType_3_Container input[type=text]").attr('disabled', 'disasbled');

		$("#KeepType_2_Container select").removeAttr('disabled');
		$("#KeepType_1_Container select").attr('disabled', 'disasbled');
		$("#KeepType_3_Container select").attr('disabled', 'disasbled');

		$("#KeepType_2_Container label").removeClass('disabled');
		$("#KeepType_1_Container label").addClass('disabled');
		$("#KeepType_3_Container label").addClass('disabled');

		value  = $("#KeepValue_2").val();
		option = $("#KeepOption_2").val();
		if (option == 'count') {
			$("#KeepCount").val(value);
			$("#KeepSize" ).val(0);
		} else {
			$("#KeepCount").val(0);
			$("#KeepSize" ).val(value);
		}
		$("#Backup"   ).val('0');


	} else if( $("#KeepType_3").is(':checked')) {
		$("#KeepType_3_Container input[type=text]").removeAttr('disabled');
		$("#KeepType_2_Container input[type=text]").attr('disabled', 'disasbled');
		$("#KeepType_1_Container input[type=text]").attr('disabled', 'disasbled');

		$("#KeepType_3_Container select").removeAttr('disabled');
		$("#KeepType_2_Container select").attr('disabled', 'disasbled');
		$("#KeepType_1_Container select").attr('disabled', 'disasbled');

		$("#KeepType_3_Container label").removeClass('disabled');
		$("#KeepType_2_Container label").addClass('disabled');
		$("#KeepType_1_Container label").addClass('disabled');

		value  = $("#KeepValue_3").val();
		option = $("#KeepOption_3").val();
		if (option == 'count') {
			$("#KeepCount").val(value);
			$("#KeepSize" ).val(0);
		} else {
			$("#KeepCount").val(0);
			$("#KeepSize" ).val(value);
		}
		$("#Backup"   ).val('1');
	}
}
function MinLength_onChange() {
	if ($("#MinLength_Delete").is(":checked")) {
		$("#MinLength_Container input[type=text]").removeAttr('disabled');
	} else {
		$("#MinLength_Container input[type=text]").attr('disabled', 'disasbled');
	}
}

$(document).ready(
	function () {
		KeepType_onChange();
		MinLength_onChange();
		DisplaySelect();
	}
);
{/literal}
</script>

<h2>Call Recording</h2>

<!-- Error Messages -->
{if $Errors.Label.Invalid}
<p class="error_message">A recording tag is required and must be 1-30 characters in length. May only contain alphanumeric characters, spaces, and dashes.</p>
{/if}
{if $Errors.Call_Type.None}
<p class="error_message">You must select at least 1 call type.</p>
{/if}
{if $Errors.Calls.Empty}
<p class="error_message">You must select at least 1 extension to record</p>
{/if}
{if $Errors.MinLength.Invalid}
<p class="error_message">The minimum recording length must be a number between 1 and 999.</p>
{/if}
{if $Errors.KeepValue.Invalid}
<p class="error_message">Invalid keep count. Must be a digit (1-999).</p>
{/if}
<!-- Form -->
<form action="Recordings_ModifyRule.php" method="post" >
<input type="hidden" name="PK_Rule" value="{$Rule.PK_Rule}" />
<table class="formtable fullwidth">
	<tr><td colspan="2"><b>What do you want to label this rule?</b></td></tr>
	<tr><td>&nbsp;</td>
		<td>
			<input type="text" name="Label" value="{$Rule.Label}" {if $Errors.Label}class="error"{/if} />
		</td>
	</tr>

	<tr><td colspan="2"><b>Whose calls would you like to record?</b></td></tr>
	<tr><td>&nbsp;</td>
		<td>
			<select name="Type" id="Type" onchange="DisplaySelect()">
				<option value="Phone" {if $Rule.Type=='Phone'}selected="selected"{/if}>Phone Extensions</option>
				<option value="Queue" {if $Rule.Type=='Queue'}selected="selected"{/if}>Call Queues</option>
				<option value="Group" {if $Rule.Type=='Group'}selected="selected"{/if}>Extension Groups</option>
			</select>
			<br />

			<div id="Phone_Select" {if $Rule.Type!='Phone'}style="display:none"{/if} >
				<small>Phone Accounts</small>
				<br />
				<select multiple="multiple" style="width: 190px; height: 170px;" name='Extensions[]' {if $Errors.Calls}class="error"{/if} >
					{foreach from=$Phones item=Phone}
					<option value="{$Phone.PK_Extension}" {if in_array($Phone.PK_Extension, $Rule.Extensions)}selected="selected"{/if}>{$Phone.Extension} "{$Phone.Name}"</option>
					{/foreach}
				</select>
			</div>

			<div id="Group_Select" {if $Rule.Type!='Group'}style="display:none"{/if}>
				<small>Group Name</small>
				<br />
				<select multiple="multiple" style="width: 190px; height: 170px;" name='Groups[]' {if $Errors.Calls}class="error"{/if}>
					{foreach from=$Groups item=Group}
					<option value="{$Group.PK_Group}" {if in_array($Group.PK_Group, $Rule.Groups)}selected="selected"{/if}>{$Group.Name}</option>
					{/foreach}
				</select>
			</div>

			<div id="Queue_Select" {if $Rule.Type!='Queue'}style="display:none"{/if}>
				<small>Call Queues</small>
				<br />
				<select multiple="multiple" style="width: 190px; height: 170px;" name='Extensions[]' {if $Errors.Calls}class="error"{/if}>
					{foreach from=$Queues item=Queue}
					<option value="{$Queue.PK_Extension}" {if in_array($Queue.PK_Extension, $Rule.Extensions)}selected="selected"{/if}>{$Queue.Extension} "{$Queue.Name}"</option>
					{/foreach}
				</select>
			</div>
		</td>
	</tr>

	<tr><td colspan="2"><b>What type of calls would you like to record?</b></td></tr>
	<tr><td>&nbsp;</td>
		<td>
			<div {if $Errors.Call_Type}class="error"{/if} style="width: 300px;">
			<input type="checkbox" name="Call_Outgoing" {if $Rule.Call_Outgoing==1}checked="checked"{/if} id="Call_Outgoing" value="1"/>
			<label for="Call_Outgoing">All Outgoing Calls</label><br />

			<input type="checkbox" name="Call_Incoming" {if $Rule.Call_Incoming==1}checked="checked"{/if} id="Call_Incoming" value="1"/>
			<label for="Call_Incoming">Incoming Direct Calls</label><br />

			<input type="checkbox" name="Call_Queue"    {if $Rule.Call_Queue==1   }checked="checked"{/if} id="Call_Queue" value="1" />
			<label for="Call_Queue">Incoming Queue Calls</label><br />
			</div>
		</td>
	</tr>

	<tr><td colspan="2">
		<b>How long do you want to keep the recordings?</b>
	</td></tr>
	<tr><td>&nbsp;</td>
		<td>
			<div id="KeepType_1_Container">
				<input name="KeepType" value="1" type="radio" id="KeepType_1" {if $Rule.KeepCount=='0' && $Rule.KeepSize =='0'}checked="checked"{/if} onchange="KeepType_onChange()" />
				<label for="KeepType_1">Keep Forever</label>
			</div>

			<div id="KeepType_2_Container">
				<input  id="KeepType_2" name="KeepType" value="2" type="radio" {if ($Rule.KeepCount > 0  || $Rule.KeepSize > 0 || $Rule.KeepType == 2) && $Rule.Backup==0}checked="checked"{/if} onchange="KeepType_onChange()"/>
				<label for="KeepType_2">Delete after</label>


				<input  id="KeepValue_2" name="KeepValue" type="text" size="3" value="{if $Rule.KeepSize > 0}{$Rule.KeepSize}{else}{$Rule.KeepCount}{/if}"  onchange="KeepType_onChange()" {if $Errors.KeepValue}class="error"{/if}/>
				<select id="KeepOption_2" onchange="KeepType_onChange()">
					<option {if $Rule.KeepCount > 0 }selected="selected"{/if} value="count">Calls</option>
					<option {if $Rule.KeepSize  > 0 }selected="selected"{/if} value="size" >Megabytes</option>
				</select>
			</div>

			<div id="KeepType_3_Container">
				<input  id="KeepType_3" name="KeepType" value="3" type="radio" {if ($Rule.KeepCount > 0 || $Rule.KeepSize > 0 || $Rule.KeepType == 3) && $Rule.Backup==1}checked="checked"{/if} onchange="KeepType_onChange()"/>
				<label for="KeepType_3">Backup then delete calls after</label>

				<input  id="KeepValue_3" name="KeepValue" type="text" size="3" value="{if $Rule.KeepSize > 0}{$Rule.KeepSize}{else}{$Rule.KeepCount}{/if}" onchange="KeepType_onChange()" {if $Errors.KeepValue}class="error"{/if} />
				<select id="KeepOption_3" onchange="KeepType_onChange()">
					<option {if $Rule.KeepCount > 0 }selected="selected"{/if} value="count">Calls</option>
					<option {if $Rule.KeepSize  > 0 }selected="selected"{/if} value="size" >Megabytes</option>
				</select>
			</div>
		{if $Rule.PK_Rule}
		<p class="warning_message">Changing this option may result in deletion of previously recorded calls</p>
		{/if}

			<br />
			<input type="hidden" name="KeepCount" id="KeepCount" value="{$Rule.KeepCount}" />
			<input type="hidden" name="KeepSize"  id="KeepSize"  value="{$Rule.KeepSize}"  />
			<input type="hidden" name="Backup"    id="Backup"    value="{$Rule.Backup}"    />
		</td>
	</tr>

	<tr><td colspan="2"><b>What is the minimum recording length?</b></td></tr>
	<tr><td>&nbsp;</td>
		<td>
			<div id="MinLength_Container">
			<input type="checkbox" id="MinLength_Delete" name="MinLength_Delete" value="1" onchange="MinLength_onChange()" {if $Rule.MinLength > 0 || $Rule.MinLength_Delete }checked="checked"{/if}>
			<label for="MinLength_Delete">Delete recording shorter than</label>
			<input type="text" size="5" name="MinLength" id="MinLength" value="{$Rule.MinLength}" {if $Errors.MinLength}class="error"{/if}/>
			<label for="MinLength_Delete">seconds.</label>
			</div>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			{if $Rule.PK_Rule != "" }
				<button type="submit" name="submit" value="save">Modify Call Recording Rule</button>
			{else}
				<button type="submit" name="submit" value="save">Create Call Recording Rule</button>
			{/if}
		</td>
	</tr>
</table>
</form>