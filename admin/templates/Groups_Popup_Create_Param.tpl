<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}
function AddMember() {
	$("#AvailabeExtensions").copyOptions("#Extensions");
	$("#AvailabeExtensions").removeOption(/./, true);
	$("#AvailabeExtensions").sortOptions();
	$("#Extensions").sortOptions();
}

function RemoveMember() {
	$("#Extensions").copyOptions("#AvailabeExtensions");
	$("#Extensions").removeOption(/./, true);
	$("#AvailabeExtensions").sortOptions();
	$("#Extensions").sortOptions();
}

function PreSubmit() {
	$("#Extensions").selectOptions(/./, true);
}

{/literal}
</script>

{if $Errors.Name}
<p class="error_message">The group name you entered is invalid. Must be 1-32 alphanumeric characters in length.</p>
{/if}


<form action="Groups_Popup_Create_Param.php?id_select={$id_select}" method="post" onsubmit="PreSubmit()">
<p>
	<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
</p>

<table class="formtable">
	<!-- Group Name -->
	<tr>
		<td>Group Name</td>
		<td>
			<input type="text" name="Name" value="{$Group.Name}" {if $Errors.Name}class="error"{/if} />
		</td>
	</tr>

	<!-- Manage Group Members -->
	<tr>
		<td>Manage Group Members</td>

		<td>
			<small>All Available Accounts</small>
			<br />
			<select multiple="multiple" id="AvailabeExtensions" name="AvailabeExtensions[]" style="width: 190px; height: 250px;">
			{foreach from=$Extensions item=Extension}
			{if !in_array($Extension.PK_Extension, $Group.Extensions)}
				<option value="{$Extension.PK_Extension}">&nbsp;{$Extension.Extension} "{$Extension.Name}"</option>
			{/if}
			{/foreach}
			</select>
		</td>

		<td style="vertical-align: middle;">
			<a href="javascript: AddMember()">
			<img src="../static/images/right-arrow.gif" alt="<<" />
			</a>
			<br />
			<a href="javascript: RemoveMember()">
			<img src="../static/images/left-arrow.gif" alt=">>" />
			</a>
		</td>

		<td>
			<small>Group Members</small>
			<br />
			<select multiple="multiple" id="Extensions" name="Extensions[]" style="width: 190px; height: 250px;">
			{foreach from=$Extensions item=Extension}
			{if in_array($Extension.PK_Extension, $Group.Extensions)}
				<option value="{$Extension.PK_Extension}">&nbsp;{$Extension.Extension} "{$Extension.Name}"</option>
			{/if}
			{/foreach}
			</select>
		</td>
	</tr>
</table>

<!-- Submit -->
<p>
	<br />
	<button type="submit" name="submit" value="save">Create Group</button>
</p>
</form>