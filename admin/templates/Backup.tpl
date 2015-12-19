
{if $Message == "SUCCESSFULLY_CREATE_BACKUP"}
	<p class="success_message">	 	
		The Backup was successfully created
	</p>
{/if}

{if $Message == "BACKUP_SETTINGS_SAVED"}
	<p class="success_message">	 	
		Successfully saved your automatic backup settings. 
	</p>
{/if}

{if $Message == "DELETE_BACKUP"}
	<p class="success_message">	 	
		Successfully deleted backup. 	
	</p>
{/if}

<h2>Backups</h2>


<table class="formtable">
	<tr>
		<td>
			You may create a backup at any time by clicking on the "Create A Backup" button. If you would like to schedule automatic recurring backup creation, click the "Setup Automatic Backups" button.
		</td>
	</tr>			
	<tr>
		<td>
			<button type="button" onclick="document.location='Backup_Manual.php'">Create A Backup</button> 
			<button type="button" onclick="document.location='Backup_Setup.php'">Setup Automatic Backups</button>	
		</td>
	</tr>

	<tr>
		<td>
			If you have saved backups of your system, they will be listed in chronological order below. To restore to an older version of your system, you may click the "Restore" button" next to the corresponding backup file, or you may locate the file you would like to use on your computer and click the "Upload a Backup File" button. 
		</td>
	</tr>
</table>	
<br/>


<table class="listing fullwidth">
	{if $End < $Total}
	<caption>Backups( {$Start+1} to {$End} ) of {$Total}</caption>
	{else}
	<caption>Backups( {$Start+1} to {$Total} ) of {$Total}</caption>
	{/if}
	
	<tr>
		<th>
			#
		</th>
		<th>
			<a href="?Sort=Optionals">Optionals Contents</a>
			{if $Sort == "Optionals"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Size">Size</a>
			{if $Sort == "Size"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>
		<th>
			<a href="?Sort=Date">Date Created</a>
			{if $Sort == "Date"}
				<img src="../static/images/{$Order}.gif" alt="{$Order}" />
			{/if}
		</th>		
		<th >Delete</th>
		<th >Restore</th>
		<th >Download</th> 
	</tr>
	{assign var=id value=1}
	{foreach from=$Backups item=Backup}
	<tr class="{cycle values='odd,even'}">
		<td>{$id}</td>
		<td>{$Backup.Optionals}</td>
		<td>{$Backup.Size}</td>
		<td>{$Backup.Date}</td>
		<td>
			<form action="Backup_Delete.php" method="post" name="Backup_Delete">
				<input type="hidden" name="PK_Backup" value='{$Backup.PK_Backup}' />
				<input type="hidden" name="Date"      value='{$Backup.Date}' />
				<button type="submit" class="important" name="submit" value="delete"> Delete </button> 
			</form>			
		</td>
		<td>
			<button type="button">
				Restore
			</button>
		</td>
		<td>
			<button type="button">
				Download
			</button>
		</td>
	</tr>
	{assign var=id value=$id+1}
	{/foreach}
</table>


<p style="text-align: left">
	{if $Start > 0}
		<a class="prev" href="?Start={$Start-$PageSize}">  Previous  </a>
	{/if}
	&nbsp;	
	{if $End < $Total}
	<a class="next" href="?Start={$Start+$PageSize}"> Next </a>
	{/if}
</p>

<table>		
	<tr>
		<td>
			<button type="button">Upload a Backup File</button>
		</td>
	</tr>	
</table>
<br/>
<table>
	<tr>
		<th style="background-color:#dddddd; border:1px solid gray;" colspan=2>					
			Key of Optional Contents 
		</th>					
	</tr>
	<tr style="border:1px solid gray;">
		<td style="width:10px;">
		 &nbsp;
		</td>
		<td>
			<small> 
				VM : Voicemail and Fax Files <br/>
				VMG : Voicemail Greetings <br/>
				RC : Recorded Calls <br/>
				MOH : Music On Hold Files <br/>
				EL : PBX Error Logs <br/>
			</small>
		</td>				
	</tr>
</table>			
