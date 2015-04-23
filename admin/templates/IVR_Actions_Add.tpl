<script type="text/javascript">
{literal}
function Next() {
	document.location = 'IVR_Actions_Modify.'+$("#Type").val()+'.php?FK_Menu={/literal}{$PK_Menu}{literal}';
}

function ShowDescription() {
	$('p span').css('display','none');
	selected = $("#Type").val();
	$("#"+selected).css('display','inline');
}
{/literal}
</script>


<h2>IVR Editor</h2>

<h2>Add New Action</h2>

<p>
	Action Type:
	<select name="Type" id="Type" onchange="ShowDescription()">
		<option value="play_sound"        >Play Sound          </option>
		<option value="record_sound"      >Record Sound        </option>
		<option value="playback_rec_sound">Play Recorded Sound </option>
		<option value="email_rec_sound"   >Email Recorded Sound</option>
		<option value="">---------------------------------     </option>
		<option value="record_digits" >Record Digits      </option>
		<option value="say_digits"    >Say Digits/Letters </option>
		<option value="say_number"    >Say A Number       </option>
		<option value="play_digits"   >Play DTMF Tones    </option>
		<option value="">---------------------------------</option>
		<option value="dial_extension">Dial Extension           </option>
		<option value="send_to_voicemail">Send to Voicemail     </option>
		<option value="goto_context"  >Goto To IVR Menu / Action</option>
		<option value="set_variable"  >Set Variable             </option>
		<option value="">---------------------------------      </option>
		<option value="gatekeeper"        >Gate Keeper       </option>
		<option value="conditional_clause">Conditional Clause</option>
		<option value="time_clause"       >Time Based Clause </option>
		<option value="alter_callerid"    >Alter Caller ID   </option>
		<option value="">---------------------------------</option>
		<option value="wait"          >Wait               </option>
		<option value="give_busy"     >Give Busy Signal   </option>
		<option value="hang_up"       >Hang Up            </option>
	</select>

	<button onclick="Next()">Next</button>
</p>

<br />
<p>

	<span id="play_sound">
		This action plays a sound back to the user then continues on to the next action.
		The sound is chosen from a list of available sounds.
		Sounds can be added to the system via the <a href="SoundEntries_List.php">Sound Manager Section</a>
		of the admin tool suite.
	</span>
	<span style="display:none" id="record_sound">
		Records a sound from the caller. To stop recording, the caller must press the pound (#) key. This sound is then saved in a chosen variable and then can be used later in other actions such as playing the recorded sound back to the caller or emailing the recorded sound to a user's email address.
	</span>
	<span style="display:none" id="playback_rec_sound">
		Play a previously recorded sound to the caller. The sound will be named by a variable from a previous "Record Sound" action in the IVR Tree.
	</span>
	<span style="display:none" id="email_rec_sound">
		Email a recorded sound to an email address. The sound will be named by a variable from a previous "Record Sound" action in the IVR Tree.
	</span>


	<span style="display:none" id="record_digits">
		Record digits from the caller. To stop recording, the caller must press the pound (#) key. The digits are then saved in a variable and can be used later in other actions such as "Say Digits/Letters" or sending the digits to a URL by using the "Send Call Values to URL".
	</span>
	<span style="display:none" id="say_digits">
		Say some digits and/or letters back to the caller. Each digit is read back to the caller as an individual number. Example: 1445 would read back "one four four five". If you want to say a number (one thousand four hundred and forty five) use the "Say Number" action. Each letter is read back to the caller as an individual letter and not as a full word. The digits and/or letters can either be manually entered or they can be ones previously set in a variable.
	</span>
	<span style="display:none" id="say_number">
		Say a number back to the caller. The number is read back to the caller as a whole. Example: 1445 would read back "one thousand four hundred and forty five". If you want to read back each individual number (one four four five) use the "Say Digits" action. The digits can either be manually entered or they can be ones previously set in a variable.
	</span>
	<span style="display:none" id="play_digits">
		Plays a group of DTMF tones on the active call. The length of the tones and the pause between the tones can be specificied.
	</span>


	<span style="display:none" id="dial_extension">
		Dials a predefined extension or an extension from previously recorded digits. The caller is then sent to that extension.
	</span>
	<span style="display:none" id="send_to_voicemail">
		Send caller to an extensions' voicemail box.
	</span>
	<span style="display:none" id="goto_context">
		Sends a caller to a chosen IVR Menu starting with a chosen action. This can also be used to jump or return to an action in the same IVR Menu.
	</span>
	<span style="display:none" id="set_variable">
		Define a new variable and set its value or set the value of an existing variable in the IVR.
	</span>


	<span style="display:none" id="gatekeeper">
		Place a gate keeper in your IVR to count how many times a caller has traveled through the gate and then perform a certain action if the count gets above a certain threshold. This is useful to keep callers from getting stuck in infinite loops or to trap callers that are trying to abuse the system.
	</span>
	<span style="display:none" id="conditional_clause">
		Creates an IF conditional statement which can be used to branch callers to different IVR Menus/actions based on IVR variables that have been previously set. For example you can Record Digits from a user into a variable then using the Conditional Clause check that variable against a predefined number and if it matches send them to a special IVR Menu.
	</span>
	<span style="display:none" id="time_clause">
		When a call reaches this action, it checks the current time and then matches that against the selected Time Frame (these can be viewed, modified, and created via the <a href="TimeFrames.php">Time Frame</a> tool in the administration suite). If the current time does match the Time Frame, then it will branch callers to to another IVR Menu/action. For example, if the current time matches the Outside Business Hours Time Frame, send the call to a IVR Menu that plays a sound announcing that it is outside business hours.
	</span>
	<span style="display:none" id="alter_callerid">
		Alters the current caller id that displays on your phone by perpending your own characters to the front of either their caller id name or the caller id number. This is useful when callers are branched in the IVR and you wan to know what branch they took to arrive at your phone.
	</span>


	<span style="display:none" id="wait">
		Wait for a given amount of time before continuing.
	</span>
	<span style="display:none" id="give_busy">
		Give the caller a busy signal for a certain amount of time.
	</span>
	<span style="display:none" id="hang_up">
		Hang up on the caller.
	</span>
</p>