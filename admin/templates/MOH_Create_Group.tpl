<script type="text/javascript" src="../static/script/jquery.highlightFade.js"></script>

<script language="javascript">
{literal}

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
			//onStop:			UpdateRuleOrder
		}
	)	
	}
);

{/literal}

</script>

{*php}
print ("<pre>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> <br>");
print_r ($this->_tpl_vars);
print ("<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<</pre>");
{/php*}

<h2>Music On Hold</h2>

<form action="MOH.php" method="post" name="MOH">
<table class="formtable">
	
	<tr>
		<td>
			The music on hold manager lets you create groups of music, upload new songs, manage the play order, and more. <a class="help" href="#">More about Music On Hold </a>
		</td>
	</tr>
</table>
<br />
</form>


<table class="listing fullwidth">
	<caption>Music On Hold Songs 
	</caption>
	<thead>
	<tr>
		<th>
			<a href="">			#</a>
		</th>
		<th>
			<a href="?Sort=FileName">File Name</a>
			{if $Sort == "FileName"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=GroupName">Group Name</a>
			{if $Sort == "GroupName"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Name">Play order</a>
			{if $Sort == "PlayOrder"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		
		<th>
			<a href="?Sort=DateCreated">Date created</a>
			{if $Sort == "DateCreated"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>		
		<th style="width: 130px">Play</th>
	</tr>
	</thead>	
	
	{foreach from=$AudioFiles item=foo}
	<tr class=" {cycle values='odd,even'}">
		<td>    <input type="checkbox">                </td>
		<td>	{$foo.FileName}		</td>
		<td>	{$foo.GroupName}	</td>
		<td>	{$var}  	</td>
		<td>	{$foo.DateCreated} 	</td>
		<td>	<a href="{$foo.Path}"><input class="button" type='button' value="Play"></a>
	  	</td>
		
	</tr>
	{/foreach}	
</table>
	

	
	
	


