<h2>Extension Templates</h2>
<p>
	Create, modify and delete extension templates to streamline the administration of extensions on your PBX.
</p>

<h2>Create A New Extension Template</h2>
{if $Message == "ERR_TEMPLATE_NAME"}
<p class="error_message">Must provide a valid template name (1-32 alphanumeric characters)</p>
{/if}
<form action="Templates_List.php" method="post" >
<p>
	<label for="Name">Template Name:</label>
	<input type="text" name="Name" id="Name" {if $Message == "ERR_TEMPLATE_NAME"}class="error"{/if} />
	<button type="submit" name="submit" value="create">Create New</button>
</p>
</form>
<br />

{if $Message == "DELETE_TEMPLATE"}
<p class="success_message">Successfully deleted extension template.</p>
{/if}
{if $Message == "MODIFY_TEMPLATE"}
<p class="success_message">Successfully updated extension template.</p>
{/if}

<table class="listing fullwidth">
	<caption>Modify Existing Templates</caption>
	<tr>
		<th>
			<a href="?Sort=Name">Template Name</a>
			{if $Sort == "Name"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th style="width: 130px"></th>
	</tr>
	{foreach from=$Templates item=Template}
	<tr class="{if $Hilight == $Template._PK_}hilight{/if} {cycle values="odd,even"}">
		<td>{$Template.Name}</td>
		<td>
			<form method="get" action="Templates_Modify.php" style="display: inline;">
				<input type="hidden" name="PK_Template" value="{$Template._PK_}" />
				<button type="submit" name="submit" value="modify">Modify</button>
			</form>

			<form method="get" action="Templates_Delete.php" style="display: inline;">
				<input type="hidden" name="PK_Template" value="{$Template._PK_}" />
				{if $Template.Protected}
				<button type="button" class="important_disabled">Delete</button>
				{else}
				<button type="submit" name="submit" value="delete" class="important">Delete</button>
				{/if}
			</form>
		</td>
	</tr>
	{/foreach}
</table>
