<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
<script type="text/javascript">
{literal}
	
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
//converteste bytii in kb, mb, gb pentru afisare
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

<h2>Backups</h2>
<p>
	Backup creation may take several minutes and should not affect any current calls in your system. After you click the "Create Backup" button, you will be directed to a wait screen while the backup file is created.
</p>


<form action="Backup_Manual.php" method="post" name="Backup_Manual">

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
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required </td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Hardware Configuration </td>
		<td> 
			<p id="Hardware_Conf">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Database Configuration </td>
		<td> 
			<p id="DB_Conf">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Sounds / Sound Packs </td>
		<td> 
			<p id="Sounds">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> IAX RSA Keys </td>
		<td> 
			<p id="IAX_RSA_Keys">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Audio Codecs </td>
		<td> 
			<p id="Audio_Codecs">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> Required </td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Voicemail and Fax Files </td>
		<td>
			<p id="Voicemail_Fax_Files">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td>  
			<input type="checkbox" name="VM" id="VM" /> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Voicemail Greetings </td>
		<td> 
			<p id="Voicemail_Greetings">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td>
			<input type="checkbox" name="VMG" id="VMG" /> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Recorded Calls </td>
		<td> 
			<p id="Recorded_Calls">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> 
			<input type="checkbox" name="RC" id="RC" /> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> Music On Hold Files </td>
		<td> 
			<p id="MOH_Files">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p> 
		</td>
		<td> 
			<input type="checkbox" name="MOH" id="MOH" /> 
			Optional
		</td>
	</tr>
	<tr class="{cycle values='odd,even'}">
		<td> PBX Error Logs  </td>
		<td> 
			<p id="PBX_Error_Logs">
				<img src="./images/ajax_activity.gif">
				Determining Size
			</p>
			
		</td>
		<td> 
			<input type="checkbox" name="EL" id="EL" /> 
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
			<input id="total" name="total" type="text" readonly  />
		</td>
		<td>
			<br/>
			<button type="submit" name="submit" value="submit_manual">Create Backup</button>
		</td>
	</tr>
	
</table>
</form>