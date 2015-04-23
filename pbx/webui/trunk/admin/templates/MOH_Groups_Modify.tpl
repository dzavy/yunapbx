<link rel="stylesheet" href="newtheme/jquery-ui.css" type="text/css" />
<script type="text/javascript" src="../script/jquery-ui.js"></script>
<script type="text/javascript">
{literal}
	$(function() {
		$("#slider").slider({
			range: "min",
			min: 1,
			max: 200,
			value: {/literal}{$Group.Volume}{literal}
		});
		$('#slider').bind('slide', function(event, ui) {
			var value = $('#slider').slider('option', 'value');
			$("#Volume").val(value+'%');
		});
	});
{/literal}
</script>

<h2>Music On Hold</h2>

<!-- Error Messages -->
{if $Errors.Name.Invalid}
<p class="error_message">Your group name must be 1-15 characters in length.</p>
{/if}
{if $Errors.Name.Duplicate}
<p class="error_message">A group with that name already exists.</p>
{/if}


<!-- Form -->
<form action="MOH_Groups_Modify.php" method="post" >
<input type="hidden" name="PK_Group" value="{$Group.PK_Group}" />
<table class="formtable">
	<!-- Group Name -->
	<tr>
		<td>Group Name</td>
		<td>
			{if $Group.Protected}
				<input type="text" value="{$Group.Name}" disabled="disabled" />
				<input type="hidden" name="Name" value="{$Group.Name}" />
				<input type="hidden" name="Protected" value="1" />
			{else}
				<input type="text" name="Name" value="{$Group.Name}" {if $Errors.Name}class="error"{/if} {if $Group.Protected}disabled="disabled"{/if} />
			{/if}
		</td>
	</tr>

	<!-- Description -->
	<tr>
		<td>Description</td>
		<td>
			<textarea name="Description" {if $Errors.Description}class="error"{/if} cols="30" rows="6" {if $Group.Protected}disabled="disabled"{/if} >{$Group.Description}</textarea>
		</td>
	</tr>

	<!-- Play Volume -->
	<tr>
		<td>Play Volume</td>
		<td>
			<table class="nostyle">
				<tr>
					<td style="vertical-align: middle"><div id="slider" style="width: 150px;"></div></td>
					<td style="vertical-align: bottom">
						&nbsp;&nbsp;
						<input type="text" name="Volume" id="Volume" value="{$Group.Volume}%" {if $Errors.Volume}class="error"{/if} style=" width: 38px; margin: 0px; text-align: right;" readonly="readonly"/>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- Play Order -->
	<tr>
		<td>Play Order</td>
		<td>
			<input type="radio" name="Ordered" id="Ordered_1" value="1" {if $Group.Ordered == "1"}checked="checked"{/if} />
			<label for="Ordered_1">Chosen Order</label>
			
			<input type="radio" name="Ordered" id="Ordered_0" value="0" {if $Group.Ordered == "0"}checked="checked"{/if} />
			<label for="Ordered_0">Random</label>
		</td>
	</tr>

	<!-- Submit Button -->
	<tr>
		<td>
			{if $Group.PK_Group != "" }
				<button type="submit" name="submit" value="save">Modify Group</button>
			{else}
				<button type="submit" name="submit" value="save">Create Group</button>
			{/if}
		</td>
	</tr>
</table>
</form>