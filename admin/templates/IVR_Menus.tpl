<h2>IVR Editor</h2>

{if $Message != ""}
<p class="success_message">
{if     $Message == "ADD_MENU"    } Successfully created new IVR Menu.
{elseif $Message == "MODIFY_MENU" } Successfully modified IVR Menu.
{/if}
</p>
{/if}

<script type="text/javascript" src="../static/script/jquery.treeview.js" ></script>
<script>
{literal}

function SelectTreeNode(type, id) {
	$('#IVR_Tree a').removeClass('selected');
	$("#"+type+"_"+id).addClass('selected');
	$("#IVR_Details").html('Loading ...');
	$.get('IVR_Menus_Ajax.php', {type:type, id:id} , function(data) {
		$("#IVR_Details").html(data);
	}
	);
}

function ChangeMenu() {
	if ($("#PK_Menu").val() > 0) {
		document.location = 'IVR_Menus.php?PK_Menu='+$("#PK_Menu").val();
	} else {
		document.location = 'IVR_Menus_Modify.php';
	}

}

$(document).ready(function(){
	$('#treeview').treeview({collapsed: true, persist: "location"});
	my_location = document.location.toString();
	if(my_location.match("#")) {
		selected_node = my_location.split('#')[1];
		node_type = selected_node.split('_')[0];
		node_id   = selected_node.split('_')[1];
		SelectTreeNode(node_type, node_id);
	} else {
		if ($("#PK_Menu").val() > 0) {
			SelectTreeNode('menu',{/literal}{$PK_Menu}{literal}+0);
		}
	}
});
{/literal}
</script>

<table class="formtable">
	<tr>
		<td>
			Select Starting IVR Menu <br />
			<small>IVR Menu Name (Extension)</small>
		</td>
		<td>
			<select name="PK_Menu" id="PK_Menu" onchange="ChangeMenu()">
				<option value="0">Create New IVR Menu</option>
				<option value="0" disabled="disabled">---------------------------------------------</option>

				{foreach from=$IVR_Menus item=Menu}
				{if $Menu.Extensions != ""}
				<option value="{$Menu.PK_Menu}" {if $PK_Menu==$Menu.PK_Menu}selected="selected"{/if}>{$Menu.Name} ( {$Menu.Extensions} )</option>
				{/if}
				{/foreach}

				<option value="0" disabled="disabled">---------------------------------------------</option>

				{foreach from=$IVR_Menus item=Menu}
				{if $Menu.Extensions == ""}
				<option value="{$Menu.PK_Menu}" {if $PK_Menu==$Menu.PK_Menu}selected="selected"{/if}>{$Menu.Name}</option>
				{/if}
				{/foreach}

			</select>
			<button type="button" onclick="ChangeMenu()">Go</button>
		</td>
	</tr>
</table>
<br />
<table width="100%">
	<tr>
		<!-- IVR Tree -->
		<td style="vertical-align: top; padding-right: 10px;">
			<table class="ivrtable">
				<tr><th>IVR Tree</th></tr>
				<td id="IVR_Tree">
					{include file="IVR_Menus_treeview.tpl" IVR_Tree=$IVR_Tree}
				</td>
			</table>
		</td>

		<!-- IVR Menu Details -->
		<td style="vertical-align: top">
			<table class="ivrtable">
				<tr><th>IVR Menu Details</th></tr>
				<td id="IVR_Details">
				</td>
			</table>
		</td>
	</tr>
</table>