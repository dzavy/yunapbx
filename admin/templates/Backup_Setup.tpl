<script type="text/javascript" src="../static/script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}

function popUp(url)
	{		
		FtpHostname = $('#FtpHostname').val();				
		FtpUsername = $('#FtpUsername').val();				
		FtpPassword = $('#FtpPassword').val();				
		FtpPath     = $('#FtpPath').val();	
		
		url += "?FtpHostname=" + FtpHostname + 
		       "&FtpUsername=" + FtpUsername + 
			   "&FtpPassword=" + FtpPassword + 
			   "&FtpPath="     + FtpPath ;
		newwindow=window.open(url,null,'height=700,width=890');
		if (window.focus) {newwindow.focus();}
		return false;		
	}	
	
function RequestedSizes() {
	$.post('Backup_Setup_Ajax.php',
	{
		Action: "RequestedSizes"
	},
	RequestedSizes_CallBack,	
	"json");
}

function RequestedSizes_CallBack(data) {		
	
	$("#PBX_Conf").html(convert_bytes(data['PBX_Conf']));	
	$("#Hardware_Conf").html(convert_bytes(data['Hardware_Conf']));
	$("#DB_Conf").html(convert_bytes(data['DB_Conf']));
	$("#Sounds").html(convert_bytes(data['Sounds']));
	$("#IAX_RSA_Keys").html(convert_bytes(data['IAX_RSA_Keys']));
	$("#Audio_Codecs").html(convert_bytes(data['Audio_Codecs']));
	$("#Voicemail_Fax_Files").html(convert_bytes(data['VM']));
	$("#Voicemail_Greetings").html(convert_bytes(data['VMG']));	
	$("#Recorded_Calls").html(convert_bytes(data['RC']));
	$("#MOH_Files").html(convert_bytes(data['MOH']));
	$("#PBX_Error_Logs").html(convert_bytes(data['EL']));	
	
	total  = data['PBX_Conf'] + data['Hardware_Conf'] + data['DB_Conf'] + data['Sounds'] + data['IAX_RSA_Keys'];
	total += data['Audio_Codecs'] + data['VM'] + data['VMG'] + data['RC'] + data['MOH'] + data['EL'];	
	
	$("#total").val(convert_bytes(total));	
}
//converteste bytii in kb, mb , gb pentru afisare
function convert_bytes(data){
	if (data > 1073741824) {
		return (Math.round(data/1073741824) + "GB");
	}
	else if ((data < 1073741824) && (data > 1048576)){
		return (Math.round(data/1048576) + "MB");
	}
	else if ((data < 1048576) && (data > 1024)){
		return (Math.round(data/1024) + "KB");
	}
	else if ((data < 1024) && (data > 0)){
		return (data + "B");
	}
	else return ("-1");
}

$(document).ready(function(){
	RequestedSizes();
});	
{/literal}

</script>

{if $Errors.NrBackups}
<p class="error_message">
	ERROR: Invalid number of backups (1-99)
</p>
{/if}

{if $Errors.FTP}
<p class="error_message">
	ERROR: No connection could be established with the ftp server.
Please make sure you typed in host correctly, its running a valid ftp server, and your PBX machine has a route to your ftp host (ie no firewall restrictions).
</p>
{/if}


<h2>Backups</h2>

<form action="Backup_Setup.php" method="post" name="Backup_Setup">
<table class="formtable">
	<tr>
		<td>
			Automatic backups will occur every 
				<select name="Weekday">
					<option value=0 {if $Settings.Weekday == 0} selected="selected" {/if}>Day       </option>
					<option value=1 {if $Settings.Weekday == 1} selected="selected" {/if}>Sunday    </option>
					<option value=2 {if $Settings.Weekday == 2} selected="selected" {/if}>Monday    </option>
					<option value=3 {if $Settings.Weekday == 3} selected="selected" {/if}>Tuesday   </option>
					<option value=4 {if $Settings.Weekday == 4} selected="selected" {/if}>Wednesday </option>
					<option value=5 {if $Settings.Weekday == 5} selected="selected" {/if}>Thursday  </option>
					<option value=6 {if $Settings.Weekday == 6} selected="selected" {/if}>Friday    </option>
					<option value=7 {if $Settings.Weekday == 7} selected="selected" {/if}>Saturday  </option>
				</select>
			at 
				<select name="Hour">
					{assign var=ztime value=0}
					{section name=foo loop=24}
						<option value="{$ztime}" {if $Settings.Hour == $ztime} selected="selected" {/if}>{$ztime}:00</option>	
						{assign var=ztime value=$ztime+1}			
					{/section}			
				</select>			
			<br>
		</td>
	</tr>		
	<tr>
		<td>
			Number of backup copies to keep around 
				<input name="NrBackups" type="text" size="2" value="{$Settings.NrBackups}" {if $Errors.NrBackups} class="error"{/if}/>
				<br>
			<small>When the above threshold is reached the oldest backup will be deleted from the ftp host. </small>		
		</td>
	</tr>
	<tr>
		<td>
			Backups should be automatically transfered to: <br>
			<table class="nostyle">
				<tr>
					<td> FTP Hostname: </td> 
					<td>
						<input name="FtpHostname" id="FtpHostname" type="text" size="20" value="{$Settings.FtpHostname}" 
						{if $Errors.FTP} class="error"{/if}/>
					</td>
				</tr>
				<tr>
					<td> FTP Username: </td> 
					<td> 
						<input name="FtpUsername" id="FtpUsername" type="text" size="20" value="{$Settings.FtpUsername}"
						{if $Errors.FTP} class="error"{/if}/>
					</td>
				</tr>
				<tr>
					<td> FTP Password: </td> 
					<td> 
						<input name="FtpPassword" id="FtpPassword" type="password" size="20" value="{$Settings.FtpPassword}"
						{if $Errors.FTP} class="error"{/if}/>
					</td>
				</tr>
				<tr>
					<td> FTP Path: </td> 
					<td> 
						<input name="FtpPath" id="FtpPath" type="text" size="20" value="{$Settings.FtpPath}"
						{if $Errors.FTP} class="error"{/if}/>
						<br/>
					 	<small>
							Path of '/' denotes root login directory.
						</small>
					</td>
				</tr>			
			</table>
		</td>
	</tr>	
	<tr>
		<td>
			<button type="button" onclick="javacript:popUp('Backup_FTP_Test.php');">Test FTP Settings</button> 	
		</td>	
	</tr>
</table>


<table class="listing fullwidth">
	<tr>
		<th> <b>Name    </b></th>
		<th> <b>Size    </b></th>
		<th> <b>Include </b></th>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> PBX Configuration </td>
		<td> 
			<p  id="PBX_Conf">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required </td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Hardware Configuration </td>
		<td> 
			<p id="Hardware_Conf">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Database Configuration </td>
		<td> 
			<p id="DB_Conf">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Sounds / Sound Packs </td>
		<td> 
			<p id="Sounds">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> IAX RSA Keys </td>
		<td> 
			<p id="IAX_RSA_Keys">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Audio Codecs </td>
		<td> 
			<p id="Audio_Codecs">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required </td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Voicemail and Fax Files </td>
		<td>
			<p id="Voicemail_Fax_Files">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td>  
			<input type="checkbox" name="VM" id="VM"
					{if $Settings.VM == 1} checked='checked' {/if}/> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Voicemail Greetings </td>
		<td> 
			<p id="Voicemail_Greetings">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td>
			<input type="checkbox" name="VMG" id="VMG" 
				{if $Settings.VMG == 1} checked='checked' {/if}/> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Recorded Calls </td>
		<td> 
			<p id="Recorded_Calls">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> 
			<input type="checkbox" name="RC" id="RC" 
				{if $Settings.RC == 1} checked='checked'{/if}/> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Music On Hold Files </td>
		<td> 
			<p id="MOH_Files">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> 
			<input type="checkbox" name="MOH" id="MOH"
				{if $Settings.MOH == 1} checked='checked'  {/if}/> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> PBX Error Logs  </td>
		<td> 
			<p id="PBX_Error_Logs">
				<img src="../static/images/ajax_activity.gif">
				Determining Size
			</p>
			
		</td>
		<td> 
			<input type="checkbox" name="EL" id="EL" 
				{if $Settings.EL == 1} checked='checked'  {/if}/> 
			Optional
		</td>		
	</tr>
	<tr>
		<td>
			<br/>
			Total Size of Backup*: <br/>
			<small>*Approximate size before compression</small>
		</td>
		<td>
			<br/>
			<input id="total" type="text" readonly />
		</td>
		<td>
			<br/>
			<button type="submit" name="submit" value="submit_setup"> Save Settings </button>			
		</td>
	</tr>	
</table>
</form>
