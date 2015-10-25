<script type="text/javascript" src="../script/jquery.highlightFade.js"></script>
<script type="text/javascript" src="../script/interface.js"></script>

<script type="text/javascript">
{literal}

function UpdateActionOrder() {
	var Order = $.SortSerialize('Actions');

	$.post('IVR_Actions_Ajax.php?'+Order.hash,
	{
		Action : "UpdateActionOrder"
	}
	, UpdateActionOrder_Callback, "json");
}

function UpdateActionOrder_Callback(data) {
	$("#Actions div tr").removeClass('hilight');
	$("#Actions div:even tr").addClass('even');
	$("#Actions div:even tr").removeClass('odd');
	$("#Actions div:odd tr" ).addClass('odd') ;
	$("#Actions div:odd tr" ).removeClass('even');
	$("#Actions div tr td").each(
		function(intIndex) {
			$("#Actions div tr:eq("+intIndex+") td:eq(1)").html(intIndex+1);
		}
	)

	/* Fix for ie6 : handle becomes invalid after a move */
	$('#Actions').SortableDestroy();
	$('#Actions').Sortable(
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
			onStop:			UpdateActionOrder
		}
	)
}

// Delete Rule
function DeleteAction(id) {
	if (!confirm('Are you sure you want to delete this action?')) {
		return;
	}
	$.post('IVR_Actions_Ajax.php',
	{
		Action: "DeleteAction",
		ID    : id
	},
	DeleteAction_Callback,
	"json");
}
function DeleteAction_Callback(data) {
	$('#Action_'+data.ID).fadeOut(600, function() { $('#Action_'+data.ID).remove(); UpdateActionOrder();});
}

$(document).ready(
	function () {
	$('#Actions').Sortable(
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
			onStop:			UpdateActionOrder
		}
	)
	}
);
{/literal}
</script>

<h2>IVR Editor</h2>

<p>
	<a href="IVR_Menus.php?PK_Menu={$History.PK_Menu}#{$History.Tree}">
		<img src="images/left-arrow.gif" />Back to IVR Editor
	</a>
</p>

<hr />
<br />

<table class="listing fullwidth">
	<caption>Current Actions ( 1 to {$Total} ) of {$Total}</caption>
	<tr>
		<th style="width: 25px;">Pic</th>
		<th style="width: 35px;">Order</th>
		<th style="width: 35px;">Move</th>
		<th style="width: 230px;">Type</th>
		<th>Arguments</th>
		<th style="width: 120px"></th>
	</tr>
</table>
<div id="Actions">
{foreach from=$IVR_Actions item=Action}
	<div id="Action_{$Action.PK_Action}" class="sortableitem">
		<table class="listing fullwidth">
			<tr class="{if $Hilight == $Action.PK_Action}hilight{/if} {cycle values="even,odd"}">
				<td style="width: 25px;">
					<img src='images/tree_icons/{$Action.Type}.gif' alt="{$Action.Type}"/>
				</td>
				<td style="width: 35px;">
					{$Action.Order}
				</td>
				<td style="width: 35px;">
					<img style="cursor: move" src="images/arrow-up-down.gif" alt="move" class="sortablehandle" />
				</td>
				<td style="width: 230px;">
					{include file="IVR_Actions_Display.tpl" Display="Name" Action=$Action}
				</td>
				<td>
					{include file="IVR_Actions_Display.tpl" Display="Desc" Action=$Action}
				</td>

				<td style="text-align: right; width: 120px">
					<form action="IVR_Actions_Modify.{$Action.Type}.php" method="post" style="display: inline;">
						<input type="hidden" name="PK_Action" value="{$Action.PK_Action}" />
						<button type="submit" name="modify">Modify</button>
					</form>
					<button class="important" name="delete" onclick="DeleteAction({$Action.PK_Action})">Delete</button>
				</td>
			</tr>
		</table>
	</div>
{/foreach}
</div>
	<div>
		<table class="listing fullwidth">
			<td style="width: 25px;"><img src='images/tree_icons/final.gif' alt="final"/></td>
			<td style="width: 35px;">{$Action.Order+1}</td>
			<td style="width: 35px;">&nbsp;</td>
			<td style="width: 230px;">Listen for Options</td>
			<td>Final rule</td>
			<td style="width: 120px">&nbsp;</td>
		</table>
	</div>
<br />
<p>
	<form action="IVR_Actions_Add.php" method="post" style="display: inline;">
		<input type="hidden" name="PK_Menu" value="{$PK_Menu}" />
		<button type="submit">Add New Action</button>
	</form>
</p>