<script type="text/javascript" src="../static/script/jquery.highlightFade.js"></script>
<script type="text/javascript" src="../static/script/interface.js"></script>

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

function UpdateRuleOrder() {
	var Order = $.SortSerialize('Rules');
	$.post('OutgoingCalls_Ajax.php?'+Order.hash,
	{
		Action : "UpdateRuleOrder"
	}
	, UpdateRuleOrder_Callback, "json");
}

function UpdateRuleOrder_Callback(data) {
	$("#Rules div tr").removeClass('hilight');
	$("#Rules div:even tr").addClass('even');
	$("#Rules div:even tr").removeClass('odd');
	$("#Rules div:odd tr" ).addClass('odd') ;
	$("#Rules div:odd tr" ).removeClass('even');
	$("#Rules div tr td").each(
		function(intIndex) {
			$("#Rules div tr:eq("+intIndex+") td:first").html(intIndex+1);
		}
	)

	/* Fix for ie6 : handle becomes invalid after a move */
	$('#Rules').SortableDestroy();
	$('#Rules').Sortable(
		{
			accept : 		'sortableitem',
			handle :        'img',
			helperclass : 	'sorthelper',
			activeclass : 	'sortableactive',
			hoverclass : 	'sortablehover',
			opacity: 		0.8,
			fx:				200,
			axis:			'vertically',
			opacity:		0.4,
			revert:			true,
			onStop:			UpdateRuleOrder
		}
	)
}

// Delete Rule
function DeleteRule(id) {
	if (!confirm('Are you sure you want to delete this rule?')) {
		return;
	}
	$.post('OutgoingCalls_Ajax.php',
	{
		Action: "DeleteRule",
		ID    : id
	},
	DeleteRule_Callback,
	"json");
}

function DeleteRule_Callback(data) {
	$('#Rule_'+data.ID).fadeOut(600, function() { $('#Rule_'+data.ID).remove(); UpdateRuleOrder();});
}


// Delete CID Rule
function DeleteCIDRule(id) {
	if (!confirm('Are you sure you want to delete this rule?')) {
		return;
	}
	$.post('OutgoingCalls_Ajax.php',
	{
		Action: "DeleteCIDRule",
		ID    : id
	},
	DeleteCIDRule_Callback,
	"json");
}

function DeleteCIDRule_Callback(data) {
	$('#RuleCID_'+data.ID).fadeOut(600, function() { $('#RuleCID_'+data.ID).remove();});
}

function UpdateCIDRule(id) {
	ExtensionStart  = $('#RuleCID_'+id+' input[name="ExtensionStart"]').val();
	ExtensionEnd    = $('#RuleCID_'+id+' input[name="ExtensionEnd"]').val();
	FK_OutgoingRule = $('#RuleCID_'+id+' select[name="FK_OutgoingRule"]').val();
	Name            = $('#RuleCID_'+id+' input[name="Name"]').val();
	Number          = $('#RuleCID_'+id+' input[name="Number"]').val();
	Add             = $('#RuleCID_'+id+' input[name="Add"]').val();
	PrependDigits   = $('#RuleCID_'+id+' input[name="PrependDigits"]').val();
	Type            = $('#RuleCID_'+id+' input[name="Type"]').val();
	
	$.post('OutgoingCalls_Ajax.php',
	{
		Action: "UpdateCIDRule",
		ID              : id,
		ExtensionStart  : ExtensionStart,
		ExtensionEnd    : ExtensionEnd,
		FK_OutgoingRule : FK_OutgoingRule,
		Name            : Name,
		Number          : Number,
		Add             : Add,
		PrependDigits   : PrependDigits,
		Type            : Type
	},
	UpdateCIDRule_Callback,
	"json");
}

function UpdateCIDRule_Callback(data) {
	$('#RuleCID_'+data.ID+ " input").removeClass('error');
	
	if (data.Errors) {
		if (data.Errors.ExtensionStart) {
			$('#RuleCID_'+data.ID+ " input[name='ExtensionStart']").addClass('error');
			alert('Your start extension is incorrect. (3-5 digits in length)');
		}
		if (data.Errors.ExtensionEnd) {
			$('#RuleCID_'+data.ID+ " input[name='ExtensionEnd']").addClass('error');
			alert('Your end extension is incorrect. (3-5 digits in length)');
		}
	}

	$('#RuleCID_'+data.ID+ " tr ").removeClass('hilight');
	$('#RuleCID_'+data.ID+ " td ").highlightFade({color: '#c0e0f0', speed:1000, iterator:'exponential'});
}

$(document).ready(
	function () {
	$('#Rules').Sortable(
		{
			accept : 		'sortableitem',
			handle :        'img',
			helperclass : 	'sorthelper',
			activeclass : 	'sortableactive',
			hoverclass : 	'sortablehover',
			opacity: 		0.8,
			fx:				200,
			axis:			'vertically',
			opacity:		0.4,
			revert:			true,
			onStop:			UpdateRuleOrder
		}
	)
	}
);
{/literal}
</script>

<h2>Outgoing Calls</h2>
{if $Message == "MODIFY_RULE"}
<p class="success_message">Successfully modified the outgoing rule.</p>
{elseif $Message == "CREATE_RULE"}
<p class="success_message">Successfully created the outgoing rule.</p>
{/if}

<!-- Outging Call Rules -->
<table class="formtable">
	<tr>
		<td><img src="../static/images/1.gif" alt="1"/></td>
		<td style="font-weight: bold;">
			Outgoing Call Rules
		</td>
	</tr>
</table>
<hr />

<p>
	Create, modify and delete outgoing call rules to apply to the extensions on your phone system.
</p>
<br />

<form action="OutgoingCalls_Rule_Modify.php" method="post">
<button type="submit" name="add">Add New Outgoing Rule</button>
</form>

<!-- Outgoing Call Rules Table -->
<table class="listing fullwidth">
	<tr>
		<th style="width: 35px;">Priority</th>
		<th style="width: 25px;">Move</th>
		<th style="width: 150px;">Name</th>
		<th style="width: 200px;">Pattern to Match</th>
		<th>Outgoing Type</th>
		<th>Call Using</th>
		<th style="width: 110px"></th>
	</tr>
</table>
<div id="Rules">
{foreach from=$OutgoingRules item=Rule}
	<div id="Rule_{$Rule.PK_OutgoingRule}" class="sortableitem">
	<table class="listing fullwidth">
		<tr class="{if $Hilight == $Rule.PK_OutgoingRule}hilight{/if} {cycle values="even,odd"}">
			<td style="width: 35px;">{$Rule.RuleOrder}</td>
			<td style="width: 25px;">
				<img style="cursor: move" src="../static/images/arrow-up-down.gif" alt="move" class="sortablehandle" />
			</td>
			<td style="width: 150px;">{$Rule.Name}</td>
			<td style="width: 200px;">
				{if $Rule.BeginWith && $Rule.RestBetweenLow != $Rule.RestBetweenHigh }
					Begins with {$Rule.BeginWith} and the remainder is {$Rule.RestBetweenLow}  to {$Rule.RestBetweenHigh} digits in length.
				{elseif $Rule.BeginWith && $Rule.RestBetweenLow == 0  && $Rule.RestBetweenHigh == 0 }
					Number exactly matches {$Rule.BeginWith}
				{elseif $Rule.BeginWith && $Rule.RestBetweenLow != $Rule.RestBetweenHigh}
					Begins with {$Rule.BeginWith} and the remainder is {$Rule.RestBetweenLow} digits in length.
				{elseif !$Rule.BeginWith && $Rule.RestBetweenLow != $Rule.RestBetweenHigh }
					Any number {$Rule.RestBetweenLow} to {$Rule.RestBetweenHigh} digits in length.
				{elseif !$Rule.BeginWith && $Rule.RestBetweenLow == 0  && $Rule.RestBetweenHigh == 0 }
					This rule won't match any number.
				{elseif !$Rule.BeginWith && $Rule.RestBetweenLow == $Rule.RestBetweenHigh }
					Any number {$Rule.RestBetweenLow} digits in length.
				{/if}
			</td>
			<td>
				{if $Rule.ProviderType != "INTERNAL"}
					{$Rule.ProviderType} Provider
				{else}
					internal
				{/if}
			</td>
			<td>
				{$Rule.ProviderName}
			</td>
			<td style="text-align: right; width: 120px">
				{if !$Rule.Protected}
				<form action="OutgoingCalls_Rule_Modify.php" method="post" style="display: inline;">
					<input type="hidden" name="PK_OutgoingRule" value="{$Rule.PK_OutgoingRule}" />
					<button type="submit" name="modify">Modify</button>
				</form>

				<button type="button" class="important" name="delete" onclick="DeleteRule({$Rule.PK_OutgoingRule})">Delete</button>
				{/if}
			</td>
		</tr>
	</table>
	</div>
{/foreach}
</div>

<br /><br />

<!-- Outging Caller ID Rules -->
<table class="formtable">
	<tr>
		<td><img src="../static/images/2.gif" alt="2"/></td>
		<td style="font-weight: bold;">
			Outgoing Caller ID Rules
		</td>
	</tr>
</table>
<hr />

<p>
	Change the caller ID for extensions making outgoing calls on your phone system.
</p>

<br />
<a id="CIDRulesHead"></a>
<form action="OutgoingCalls.php#CIDRulesHead" method="post" >
	<button type="submit" name="submit" value="add_cid_rule">Add new Caller ID Rule</button>
	<button type="submit" name="submit" value="add_cids_rule">Add new Ranged Caller ID Rule</button>
</form>


{literal}
<style>
input, select {
	border: 0 !important; 
	border: 1px solid #aaa !important;
	font-size: 11px !important;
}
</style>
{/literal}

{if $OutgoingCIDRules|@count }
<table class="listing fullwidth">
	<tr>
		<th>#</th>
		<th>Outgoing Caller ID Rules</th>
		<th style="width: 110px"></th>
	</tr>
	{foreach from=$OutgoingCIDRules item=Rule}
	{if $Rule.Type=='Single'}
	<tr id="RuleCID_{$Rule.PK_OutgoingCIDRule}" class="{cycle values="even,odd"}">
		<td>{counter}</td>
		<td>
			<input type="hidden" name="Type" value="Single" />
			When extension
			<input type="text" name="ExtensionStart" id="ExtensionStart_{$Rule.PK_OutgoingCIDRule}" value="{$Rule.ExtensionStart}" size="3" />
			<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=ExtensionStart_{$Rule.PK_OutgoingCIDRule}','Select Extension',415,400);">&nbsp;</button>

			is using rule
			<select name="FK_OutgoingRule">
				<option value="0" {if $ORule.PK_OutgoingRule == 0}selected="selected"{/if}>Any Outgoing Rule</option>
			{foreach from=$OutgoingRules item=ORule}
				<option value="{$ORule.PK_OutgoingRule}" {if $ORule.PK_OutgoingRule == $Rule.FK_OutgoingRule}selected="selected"{/if}>{$ORule.Name}</option>
			{/foreach}
			</select>

			change their caller id name to
			<input type="text" name="Name" value="{$Rule.Name}" />

			and their caller id number to
			<input type="text" name="Number" value="{$Rule.Number}" />
		</td>
		<td style="width: 110px">
			<button type="button" onclick="UpdateCIDRule({$Rule.PK_OutgoingCIDRule})">Save</button>
			<button type="button" class="important" onclick="DeleteCIDRule({$Rule.PK_OutgoingCIDRule})">Delete</button>
		</td>
	</tr>
	{else}
	<tr id="RuleCID_{$Rule.PK_OutgoingCIDRule}" class="{cycle values="even,odd"}">
		<td>{counter}</td>
		<td>
			<input type="hidden" name="Type" value="Multiple" />
			When extensions
			<input  type="text" name="ExtensionStart" id="ExtensionStart_{$Rule.PK_OutgoingCIDRule}" value="{$Rule.ExtensionStart}" size="3" />
			<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=ExtensionStart_{$Rule.PK_OutgoingCIDRule}','Select Extension',415,400);">&nbsp;</button>

			to
			<input type="text" name="ExtensionEnd" id="ExtensionEnd_{$Rule.PK_OutgoingCIDRule}" value="{$Rule.ExtensionEnd}" size="3" />
			<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=ExtensionEnd_{$Rule.PK_OutgoingCIDRule}','Select Extension',415,400);">&nbsp;</button>

			are using rule
			<select name="FK_OutgoingRule">
				<option value="0" {if $ORule.PK_OutgoingRule == 0}selected="selected"{/if}>Any Outgoing Rule</option>
			{foreach from=$OutgoingRules item=ORule}
				<option value="{$ORule.PK_OutgoingRule}" {if $ORule.PK_OutgoingRule == $Rule.FK_OutgoingRule}selected="selected"{/if}>{$ORule.Name}</option>
			{/foreach}
			</select>

			change their caller id to a number derived by adding
			<input type="text" name="Add" value="{$Rule.Add}" size="2"/>

			to their extension and prepending
			<input type="text" name="PrependDigits" value="{$Rule.PrependDigits}" />
		</td>
		<td style="width: 110px">
			<button type="button" onclick="UpdateCIDRule({$Rule.PK_OutgoingCIDRule})">Save</button>
			<button type="button" class="important" onclick="DeleteCIDRule({$Rule.PK_OutgoingCIDRule})">Delete</button>
		</td>
	</tr>
	{/if}
	{/foreach}
</table>
{else}
<p class="warning_message">
	There are no outgoing caller id rules defined on this system. All extensions will use their default caller id.
</p>
{/if}
