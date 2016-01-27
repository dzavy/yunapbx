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

function UpdateRouteOrder() {
	$("#RoutesTable tr:even").addClass('even');
	$("#RoutesTable tr:even").removeClass('odd');
	$("#RoutesTable tr:odd" ).addClass('odd') ;
	$("#RoutesTable tr:odd" ).removeClass('even');
	$("#RoutesTable tr").each(
		function(intIndex) {
			$("#RoutesTable tr:eq("+intIndex+") td:first").html(intIndex);
		}
	)
}


// Delete Route
function DeleteRoute(id) {
	if (!confirm('Are you sure you want to delete this route?')) {
		return;
	}

	$.post('IncomingCalls_Ajax.php',
	{
		Action: "DeleteRoute",
		ID    : id
	},
	DeleteRoute_Callback,
	"json");
}
function DeleteRoute_Callback(data) {
	$('#Route_'+data.ID).fadeOut(600, function() { $('#Route_'+data.ID).remove(); UpdateRouteOrder();});
}


// Save Route
function SaveRoute(id) {
	$.post('IncomingCalls_Ajax.php',
	{
		Action       : "SaveRoute",
		ID           : id,
		RouteType    : $("#Route_"+id+" input[name='RouteType']").val(),
		Provider     : $("#Route_"+id+" select[name='Provider']").val(),
		StartNumber  : $("#Route_"+id+" input[name='StartNumber']").val(),
		EndNumber    : $("#Route_"+id+" input[name='EndNumber']").val(),
		Extension    : $("#Route_"+id+" input[name='Extension']").val(),
		TrimFront    : $("#Route_"+id+" input[name='TrimFront']").val(),
		Add          : $("#Route_"+id+" input[name='Add']").val()
	},
	SaveRoute_Callback,
	"json");
}

function SaveRoute_Callback(data) {
	$('#Route_'+data.ID+ " input").removeClass('error');

	if (data.Errors) {
		if (data.Errors.StartNumber) {
			$('#Route_'+data.ID+ " input[name='StartNumber']").addClass('error');
			alert('Start number required and must only contain digits,*,or # and be 1-32 characters in length.');
		}
		if (data.Errors.EndNumber) {
			$('#Route_'+data.ID+ " input[name='EndNumber']").addClass('error');
			alert('End number required and must only contain digits,*,or # and be 1-32 characters in length.');
		}
		if (data.Errors.TrimFront) {
			$('#Route_'+data.ID+ " input[name='TrimFront']").addClass('error');
			alert('The trim value must be numeric.');
		}
		if (data.Errors.Add) {
			$('#Route_'+data.ID+ " input[name='Add']").addClass('error');
			alert('The add value must be numeric.');
		}
	}

	$('#Route_'+data.ID+ " tr").removeClass('hilight');
	$('#Route_'+data.ID+ " td").highlightFade({color: '#c0e0f0', speed:1000, iterator:'exponential'});
}


function UpdateRuleOrder() {
	var Order = $.SortSerialize('Rules');
	$.post('IncomingCalls_Ajax.php?'+Order.hash,
	{
		Action : "UpdateRuleOrder"
	}
	, UpdateRuleOrder_Callback, "json");
}

function UpdateRuleOrder_Callback(data) {
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

	$.post('IncomingCalls_Ajax.php',
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

// Save Rule
function SaveRule(id) {
	$.post('IncomingCalls_Ajax.php',
	{
		Action       : "SaveRule",
		ID           : id,
		Subject      : $("#Rule_"+id+" select[name='Subject']").val(),
		Digits       : $("#Rule_"+id+" input[name='Digits']").val(),
		Extension    : $("#Rule_"+id+" input[name='Extension']").val(),
		BlockType    : $("#Rule_"+id+" select[name='BlockType']").val(),
		FK_Timeframe : $("#Rule_"+id+" select[name='FK_Timeframe']").val()
	},
	SaveRule_Callback,
	"json");
}
function SaveRule_Callback(data) {
	$('#Rule_'+data.ID+ " input").removeClass('error');

	if (data.Errors) {
		if (data.Errors.Digits) {
			$('#Rule_'+data.ID+ " input[name='Digits']").addClass('error');
			alert('Your number is incorrect only 1-20 digits are allowed.');
		}
		if (data.Errors.Extension) {
			$('#Rule_'+data.ID+ " input[name='Extension']").addClass('error');
			alert('Your extension is incorrect. (3-5 digits in length)');
		}
	}

	$('#Rule_'+data.ID+ " tr ").removeClass('hilight');
	$('#Rule_'+data.ID+ " td ").highlightFade({color: '#c0e0f0', speed:1000, iterator:'exponential'});
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
{literal}
<style>
input, select {
	border: 0 !important;
	border: 1px solid #aaa !important;
	font-size: 11px !important;
}
</style>
{/literal}

<h2>Incoming Calls</h2>

<a id="RulesHead"></a>
<!-- Incoming Call Rules -->
<table class="formtable">
	<tr>
		<td><img src="../static/images/1.gif" alt="1"/></td>
		<td style="font-weight: bold;">
			Incoming Call Rules
		</td>
	</tr>
</table>
<hr />

<p>
	Create, modify, prioritize and delete incoming call rules to apply to the extensions on your PBX system.
</p>
<br />

<!-- Create a New Incoming Call Rule -->
<form action="IncomingCalls.php#RulesHead" method="post" >
	<strong>Create a New Incoming Call Rule:</strong>
	<select name="Type">
		<option value="block">Block Number</option>
		<option value="transfer">Transfer Call</option>
	</select>
	<button type="submit" name="add_rule">Add Rule</button>
</form>

<!-- Incoming Call Rule Table -->
<table class="listing fullwidth">
	<tr>
		<th style="width: 15px;">#</th>
		<th style="width: 25px;">Move</th>
		<th>Incoming Call Rules</th>
		<th style="width: 110px"></th>
	</tr>
</table>

<div id="Rules">
{foreach from=$IncomingRules item=Rule}
	{if $Rule.RuleType == 'transfer'}
		<!-- Transfer Rule -->
		<div id="Rule_{$Rule.PK_IncomingRule}" class="sortableitem">
			<table class="listing fullwidth">
				<tr class="{cycle values="even,odd"} {if $HilightRule == $Rule.PK_IncomingRule}hilight{/if}">
					<td style="width: 15px;">{$Rule.RuleOrder}</td>
					<td style="width: 25px;">
						<img style="cursor: move" src="../static/images/arrow-up-down.gif" alt="move" class="sortablehandle"/>
					</td>
					<td style="padding: 10px 0px;">
						<strong>Transfer</strong>
						<!-- Subject -->
						<select name="Subject">
							<option {if $Rule.Subject=='phone' }selected="selected"{/if} value="phone">Phone Number</option>
							<option {if $Rule.Subject=='prefix'}selected="prefix"  {/if} value="prefix">Prefix</option>
						</select>
						<!-- Digits -->
						<input type="text" size="9" name="Digits"  value="{$Rule.Digits}"/>
						to extension
						<!-- Extension -->
						<input type="text" size="4" name="Extension" value="{$Rule.Extension}" id="Extension_Rule_{$Rule.PK_IncomingRule}"/>
						<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Extension_Rule_{$Rule.PK_IncomingRule}','Select Extension',415,400);">&nbsp;</button>
						if time is
						<!-- FK_Timeframe -->
						<select name="FK_Timeframe">
							<option value="0" {if $Rule.FK_Timeframe == 0}selected="selected"{/if}>Any Time</option>
							{foreach from=$Timeframes item=Timeframe}
								<option value="{$Timeframe.PK_Timeframe}" {if $Timeframe.PK_Timeframe == $Rule.FK_Timeframe}selected="selected"{/if}>{$Timeframe.Name}</option>
							{/foreach}
						</select>
					</td>
					<td style="text-align: right; width: 110px">
						<button type="submit" name="save" onclick="SaveRule({$Rule.PK_IncomingRule})">Save</button>
						<button class="important" name="delete" onclick="DeleteRule({$Rule.PK_IncomingRule})">Delete</button>
					</td>
				</tr>
			</table>
		</div>
	{else}
		<!-- Block Rule -->
		<div id="Rule_{$Rule.PK_IncomingRule}" class="sortableitem">
			<table class="listing fullwidth">
				<tr class="{cycle values="even,odd"} {if $HilightRule == $Rule.PK_IncomingRule}hilight{/if}">
					<td style="width: 15px;">{$Rule.RuleOrder}</td>
					<td style="width: 25px;">
						<img style="cursor: move" src="../static/images/arrow-up-down.gif" alt="move" class="sortablehandle"/>
					</td>
					<td style="padding: 10px 0px;">
						<strong>Block</strong>
						<!-- Subject -->
						<select name="Subject">
							<option {if $Rule.Subject=='phone' }selected="selected"{/if} value="phone">Phone Number</option>
							<option {if $Rule.Subject=='prefix'}selected="prefix"  {/if} value="prefix">Prefix</option>
						</select>
						<!-- Digits -->
						<input type="text" size="9" name="Digits"  value="{$Rule.Digits}"/>
						then
						<!-- BlockType -->
						<select name="BlockType">
							<option {if $Rule.BlockType == 'busy'      }selected="selected"{/if} value="busy">Play Busy Signal</option>
							<option {if $Rule.BlockType == 'congestion'}selected="selected"{/if} value="congestion">Play Congestion</option>
							<option {if $Rule.BlockType == 'hangup'    }selected="selected"{/if} value="hangup">Hang Up</option>
						</select>
						if time is
						<!-- FK_Timeframe -->
						<select name="FK_Timeframe">
							<option value="0" {if $Rule.FK_Timeframe == 0}selected="selected"{/if}>Any Time</option>
							{foreach from=$Timeframes item=Timeframe}
								<option value="{$Timeframe.PK_Timeframe}" {if $Timeframe.PK_Timeframe == $Rule.FK_Timeframe}selected="selected"{/if}>{$Timeframe.Name}</option>
							{/foreach}
						</select>
						&nbsp;
					</td>
					<td style="text-align: right; width: 110px">
						<button type="submit" name="save" onclick="SaveRule({$Rule.PK_IncomingRule})">Save</button>
						<button class="important" name="delete" onclick="DeleteRule({$Rule.PK_IncomingRule})">Delete</button>
					</td>
				</tr>
			</table>
		</div>
	{/if}
{/foreach}
</div>

<br /><br />

<!-- Incoming Call Routes -->
<a id="Routes"></a>
<table class="formtable">
	<tr>
		<td><img src="../static/images/2.gif" alt="2"/></td>
		<td style="font-weight: bold;">
			Incoming Call Routes
		</td>
	</tr>
</table>
<hr />
<p>
	Route incoming numbers to different internal extensions.
</p>
<br />

<!-- Create a New Incoming Call Rule -->
<form action="IncomingCalls.php#Routes" method="post" >
	<strong>Create a New Incoming Call Route:</strong>
	<select name="Type">
		<option value="single">Single DID</option>
		<option value="multiple">Multiple DID</option>
	</select>
	<button type="submit" name="add_route">Add Route</button>
</form>

<!-- Incoming Call Rule Table -->
<table class="listing fullwidth" id="RoutesTable">
	<tr>
		<th>#</th>
		<th>Incoming Call Routes</th>
		<th style="width: 120px"></th>
	</tr>
	{foreach from=$IncomingRoutes item=IncomingRoute}
	<tr id="Route_{$IncomingRoute.PK_IncomingRoute}"  class="{cycle values="even,odd"}  {if $HilightRoute == $IncomingRoute.PK_IncomingRoute}hilight{/if}">
		<td>{counter}</td>
		<td style="padding: 10px 0px;">
				<input type="hidden" name="RouteType" value="{$IncomingRoute.RouteType}" />
			{if $IncomingRoute.RouteType == 'single'}
				<strong>Route number</strong>
				<input type="text" name="StartNumber" value="{$IncomingRoute.StartNumber}"/>

				from
				<select name="Provider">
					<option value="0">Any Provider</option>
					<optgroup label="VoIP:">
						{foreach from=$SipProviders item=SipProvider}
						<option value="SIP~{$SipProvider.PK_SipProvider}" {if $IncomingRoute.ProviderType=='SIP' && $IncomingRoute.ProviderID==$SipProvider.PK_SipProvider}selected="selected"{/if}>
							{$SipProvider.Name}
						</option>
						{/foreach}
					</optgroup>
					<optgroup label="3G Dongle:">
						{foreach from=$Dongles item=Dongle}
						<option value="DONGLE~{$Dongle.PK_Dongle}" {if $IncomingRoute.ProviderType=='DONGLE' && $IncomingRoute.ProviderID==$Dongle.PK_Dongle}selected="selected"{/if}>
							{$Dongle.Name}
						</option>
						{/foreach}
					</optgroup>
				</select>

				to extension
				<input type="text"  size="4" name="Extension" value="{$IncomingRoute.Extension}" id="Extension_Route_{$IncomingRoute.PK_IncomingRoute}"/>
				<button class="users" onclick="javacript:popUp('Extensions_Popup.php?FillID=Extension_Route_{$IncomingRoute.PK_IncomingRoute}','Select Extension',415,400);">&nbsp;</button>
			{else}
				<strong>Route numbers</strong> from
				<select name="Provider">
					<option value="0">Any Provider</option>
					<optgroup label="VoIP:">
						{foreach from=$SipProviders item=SipProvider}
						<option value="SIP~{$SipProvider.PK_SipProvider}" {if $IncomingRoute.ProviderType=='SIP' && $IncomingRoute.ProviderID==$SipProvider.PK_SipProvider}selected="selected"{/if}>
							{$SipProvider.Name}
						</option>
						{/foreach}
					</optgroup>
					<optgroup label="3G Dongle:">
						{foreach from=$Dongles item=Dongle}
						<option value="DONGLE~{$Dongle.PK_Dongle}" {if $IncomingRoute.ProviderType=='DONGLE' && $IncomingRoute.ProviderID==$Dongle.PK_Dongle}selected="selected"{/if}>
							{$Dongle.Name}
						</option>
						{/foreach}
					</optgroup>
				</select>

				raging from
				<input type="text" size="10" name="StartNumber" value="{$IncomingRoute.StartNumber}" />
				to
				<input type="text" size="10" name="EndNumber" value="{$IncomingRoute.EndNumber}"/>

				to an extension derived by trimming
				<input type="text" size="2" name="TrimFront" value="{$IncomingRoute.TrimFront}" />
				digits from the front and adding
				<input type="text" size="3" name="Add" value="{$IncomingRoute.Add}" />
				to the result.
			{/if}
		</td>
		<td style="text-align: right;">
			<button onclick="SaveRoute({$IncomingRoute.PK_IncomingRoute})">Save</button>
			<button class="important" onclick="DeleteRoute({$IncomingRoute.PK_IncomingRoute})">Delete</button>
		</td>
	</tr>
	{/foreach}
	{foreach from=$SipProviders item=SipProvider}
	<tr class="{cycle values="even,odd"}">
		<td>{counter}</td>
		<td style="padding: 10px 0px;">
			<strong>Route All</strong> unmatched numbers from VoIP Provider "{$SipProvider.Name}" to extension {$SipProvider.CallbackExtension}
			<br />
			Click
				<a href="VoipProviders_Modify.php?PK_SipProvider={$SipProvider.PK_SipProvider}">here</a>
			to change this VoIP Providers's default extension.
		</td>
		<td>&nbsp;</td>
	</tr>
	{/foreach}
	{foreach from=$Dongles item=Dongle}
	<tr class="{cycle values="even,odd"}">
		<td>{counter}</td>
		<td style="padding: 10px 0px;">
			<strong>Route All</strong> unmatched numbers from 3G Dongle "{$Dongle.Name}" to extension {$Dongle.CallbackExtension}
			<br />
			Click
				<a href="Dongles_Modify.php?PK_Dongle={$Dongle.PK_Dongle}">here</a>
			to change this Dongle's default extension.
		</td>
		<td>&nbsp;</td>
	</tr>
	{/foreach}
	<tr>
		<td>{counter}</td>
		<td style="padding: 10px 0px;">
			<strong>Route all VOIP calls</strong> from any <strong>unknown host</strong> to
			<select>
				<option value="busy"      >Busy Signal</option>
				<option value="congestion">Congestion</option>
				<option value="hangup"    >Hang Up</option>
				<option value="extension" >Extension</option>
			</select>
		</td>
		<td style="text-align: right;">
			<button>Save</button>
		</td>
	</tr>
</table>
