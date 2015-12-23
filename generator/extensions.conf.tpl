[general]
static=yes
writeprotect=no
clearglobalvars=no

[globals]
STARFISH_AGI_DIR={$AGI_DIR}

[hints]
exten = 1234,hint,SIP/1234
exten = 1235,hint,SIP/1235
exten = 1236,hint,SIP/1236

[messages]{literal}
exten => _XXXX,1,MessageSend(sip:${EXTEN}, ${MESSAGE(from)})
exten => _XXXX,2,NoOp(Send status is ${MESSAGE_SEND_STATUS})
exten => _XXXX,3,Hangup()
exten => _XXXXXXXXXXX,1,DongleSendSMS(dongle0,${EXTEN},${MESSAGE(body)})
exten => _XXXXXXXXXXX,2,Hangup()
{/literal}
    
[internal]{literal}
exten => _XXXX,1,Dial(SIP/${EXTEN})
{/literal}


[default]

;; --------- Context to route calls received from the PBX -----------
[internal]{literal}
exten => _XX,1,agi(${STARFISH_AGI_DIR}/Route_Outgoing.php)
exten => _XX,n,Hangup()
exten => _XXX,1,agi(${STARFISH_AGI_DIR}/Route_Outgoing.php)
exten => _XXX,n,Hangup()
exten => _XXXX,1,agi(${STARFISH_AGI_DIR}/Route_Outgoing.php)
exten => _XXXX,n,Hangup()
exten => _XXXXX,1,agi(${STARFISH_AGI_DIR}/Route_Outgoing.php)
exten => _XXXXX,n,Hangup()
exten => _XXXXX.,1,agi(${STARFISH_AGI_DIR}/Route_Outgoing.php)
exten => _XXXXX.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
exten => _XXXXXXXXXXX,1,Dial(Dongle/dongle3/${EXTEN})
{/literal}


;; ---------- Context for different type of local extensions --------
[Extension_SipPhone]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_SipPhone.php)
exten => _X.,n,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_AgentLogin]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_AgentLogin.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_IVR]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_IVR.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_Voicemail]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_Voicemail.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_SimpleConf]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_SimpleConf.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_DialTone]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_DialTone.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_Queue]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_Queue.php)
exten => _X.,2,Hangup()
{/literal}

[Extension_Intercom]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_Intercom.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_ConfCenter]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_ConfCenter.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

;; --------- Context used to ring queues and queue agents ----------
[Extension_Queue_RingQueue]{literal}
exten => 0,1,Goto(internal,${QUEUE_OPER_EXTEN},1)
exten => 0,2,Hangup()
exten => _X.,1,Answer()
exten => _X.,n,Queue(${QUEUE_ARGS})
exten => _X.,n,GotoIf($[${QUEUESTATUS} == FULL]?50)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == JOINEMPTY]?20)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == JOINUNAVAIL]?20)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == LEAVEEMPTY]?20)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == LEAVEUNAVAIL]?20)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == TIMEOUT]?60)
exten => _X.,n,GotoIf($[${QUEUESTATUS} == NUMCYCLES]?40)
exten => _X.,n,Hangup()
exten => _X.,20,Goto(internal,${QUEUE_NOAGENTS_EXTEN},1)
exten => _X.,21,Hangup()
exten => _X.,40,Goto(internal,${QUEUE_MAXCYCLES_EXTEN},1)
exten => _X.,41,Hangup()
exten => _X.,50,Goto(internal,${QUEUE_MAXLEN_EXTEN},1)
exten => _X.,51,Hangup()
exten => _X.,60,Goto(internal,${QUEUE_TIMEOUT_EXTEN},1)
exten => _X.,61,Hangup()
exten => _X.,99,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}

[Extension_Queue_RingAgent]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_Queue_RingAgent.php)
exten => _X.,2,Hangup()
{/literal}

[Extension_ParkingLot]{literal}
exten => _X.,1,agi(${STARFISH_AGI_DIR}/Extension_ParkingLot.php)
exten => _X.,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}


;; --------- Contexts for Parking --------------
[park_call]{literal}
;exten => 0,1,Park(${PARK_TIMEOUT}|${PARK_RETURN}|1|park_ringback|${PARK_OPTIONS}|${PARK_START_STOP})
exten => 0,1,Park()
exten => 0,2,Hangup
exten => _X.,1,ParkedCall(${EXTEN})
exten => _X.,2,Hangup
{/literal}

[park_ringback]{literal}
exten => _X.,1,Goto(internal,${EXTEN},1)
{/literal}

;; --------- Context used mainly by web interface to record / play sounds ------
[record_web_sound]{literal}
exten => s,1,Answer
exten => s,n,Set(TIMEOUT(digit)=5)
exten => s,n,Set(TIMEOUT(response)=10)
exten => s,n,Playback(${MESSAGE})
exten => s,n,Wait(0)
exten => s,n,Record(/tmp/${FILE}:wav)
exten => s,n,Playback(auth-thankyou)
exten => s,n,NoCDR()
exten => s,n,Hangup
exten => h,1,System(chmod 777 /tmp/${FILE}.wav)
{/literal}

[play_sound]{literal}
exten => s,1,Answer
exten => s,2,Wait(2)
exten => s,3,Playback(${FILE})
exten => s,4,Wait(1)
exten => s,5,NoCDR()
exten => s,6.Hangup
{/literal}



;; --------- Context for routing call from sip providers --------------
{foreach from=$SipProviders item=Provider}
[sip_provider_{$Provider.PK_SipProvider}]
{literal}
exten => _[a-zA-Z0-9_+][a-zA-Z0-9_].,1,agi(${STARFISH_AGI_DIR}/Route_Incoming.php)
exten => _[a-zA-Z0-9_+][a-zA-Z0-9_].,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}
{/foreach}

;; --------- Context for routing call from iax providers --------------
{foreach from=$IaxProviders item=Provider}
[iax_provider_{$Provider.PK_IaxProvider}]
{literal}
exten => _[a-zA-Z0-9_+][a-zA-Z0-9_].,1,agi(${STARFISH_AGI_DIR}/Route_Incoming.php)
exten => _[a-zA-Z0-9_+][a-zA-Z0-9_].,2,Hangup()
exten => h,1,agi(${STARFISH_AGI_DIR}/Hangup.php)
{/literal}
{/foreach}
