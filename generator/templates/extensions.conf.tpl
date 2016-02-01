[general]
static=yes
writeprotect=no
clearglobalvars=no

[globals]
STARFISH_AGI_DIR={$AGI_DIR}


[default]

[internal_phones]
{foreach from=$Ext_SipPhones item=Ext_SipPhone}
exten => {$Ext_SipPhone.Extension},1,Goto(ext{$Ext_SipPhone.PK_Extension}_egress,{$Ext_SipPhone.Extension},1)
exten => msg{$Ext_SipPhone.Extension},1,MessageSend(sip:{$Ext_SipPhone.Extension},{literal}${MESSAGE(from)}{/literal})
{/foreach}

[internal]
include => internal_phones
include => internal_queues
include => internal_agents
include => parkedcalls

{foreach from=$OutgoingRules item=OutgoingRule}
[outgoing{$OutgoingRule.PK_OutgoingRule}]
{for $digits=$OutgoingRule.RestBetweenLow to $OutgoingRule.RestBetweenHigh}
exten => _{$OutgoingRule.BeginWith}{for $i=1 to $digits}X{/for},1,Goto({literal}${{/literal}TOLOWER({$OutgoingRule.ProviderType}){literal}}{/literal}{$OutgoingRule.ProviderID}_egress,{$OutgoingRule.PrependDigits}{literal}${{/literal}EXTEN:{$OutgoingRule.TrimFront}{literal}}{/literal},1)
exten => _msg{$OutgoingRule.BeginWith}{for $i=1 to $digits}X{/for},1,Goto({literal}${{/literal}TOLOWER({$OutgoingRule.ProviderType}){literal}}{/literal}{$OutgoingRule.ProviderID}_egress,msg{$OutgoingRule.PrependDigits}{literal}${{/literal}EXTEN:{$OutgoingRule.TrimFront+3}{literal}}{/literal},1)
{/for}

{/foreach}

{foreach from=$IncomingRoutes item=IncomingRoute}
[incoming{$IncomingRoute.PK_IncomingRoute}]
{if $IncomingRoute.RouteType=='single'}
exten => _{$IncomingRoute.StartNumber},1,Goto(ext{$IncomingRoute.Extension}_egress,{literal}${EXTEN}{/literal},1)
{else}
exten => _{$IncomingRoute.StartNumber}X.,1,Goto(ext{$IncomingRoute.Extension}_egress,{literal}${EXTEN}{/literal},1)
{/if}
{for $digits=$OutgoingRule.RestBetweenLow to $OutgoingRule.RestBetweenHigh}
exten => _{$OutgoingRule.BeginWith}{for $i=1 to $digits}X{/for},1,Goto({literal}${{/literal}TOLOWER({$OutgoingRule.ProviderType}){literal}}{/literal}{$OutgoingRule.ProviderID}_egress,{$OutgoingRule.PrependDigits}{literal}${{/literal}EXTEN:{$OutgoingRule.TrimFront}{literal}}{/literal},1)
{/for}

{/foreach}


{foreach from=$Ext_SipPhones item=Ext_SipPhone}
[ext{$Ext_SipPhone.PK_Extension}_ingress]
exten => _X.,1,Set(YUNA_CALLDIRECTION=OUT)
exten => _X.,n,Goto(ext{$Ext_SipPhone.PK_Extension}_routing,{literal}${EXTEN}{/literal},1)

[ext{$Ext_SipPhone.PK_Extension}_message]
exten => _X.,1,Goto(ext{$Ext_SipPhone.PK_Extension}_routing,msg{literal}${EXTEN}{/literal},1)

[ext{$Ext_SipPhone.PK_Extension}_routing]
{foreach from=$Ext_SipPhone.Rules item=Rule}
{if $Rule.ProviderType=='INTERNAL'}
include => internal
{else}
include => outgoing{$Rule.PK_OutgoingRule}
{/if}
{/foreach}
exten => i,1,Hangup(3)

[ext{$Ext_SipPhone.PK_Extension}_egress]
exten => {$Ext_SipPhone.Extension},hint,SIP/{$Ext_SipPhone.Extension}
exten => {$Ext_SipPhone.Extension},1,Noop({literal}${{/literal}DEVICE_STATE(SIP/{$Ext_SipPhone.Extension}){literal}}{/literal})
exten => {$Ext_SipPhone.Extension},n,Set(YUNA_ORIGIN={literal}${IF($["${YUNA_ORIGIN}"="OUT"]?"LOCAL":${YUNA_ORIGIN})}{/literal})
exten => {$Ext_SipPhone.Extension},n,Dial(SIP/{$Ext_SipPhone.Extension})
exten => {$Ext_SipPhone.Extension},n,Hangup

{/foreach}



{foreach from=$SipProviders item=Provider}
[sip{$Provider.PK_SipProvider}_ingress]
exten => _+X.,1,Macro(fix-international-code)
exten => _+X.,n,Goto(sip{$Provider.PK_SipProvider}_routing,{literal}00${EXTEN:1}{/literal},1)
exten => _X.,1,Macro(fix-international-code)
exten => _X.,n,Goto(sip{$Provider.PK_SipProvider}_routing,{literal}${EXTEN}{/literal},1)

{if $Provider.ApplyIncomingRules}
[sip{$Provider.PK_SipProvider}_default]
exten => _X.,1,Goto(ext{$Provider.PK_SipProvider}_egress,,{literal}${EXTEN}{/literal},1)
{/if}

[sip{$Provider.PK_SipProvider}_routing]
{if $Provider.ApplyIncomingRules}
{foreach from=$Provider.IncomingRules item=IncomingRule}
include => outgoing{$IncomingRule.PK_IncomingRule}
{/foreach} 
include => sip{$Provider.PK_SipProvider}_default
{else}
{foreach from=$Provider.OutgoingRules item=OutgoingRule}
{if $OutgoingRule.ProviderType=='INTERNAL'}
include => internal
{else}
include => outgoing{$OutgoingRule.PK_OutgoingRule}
{/if}
{/foreach} 
{/if}

[sip{$Provider.PK_SipProvider}_egress]
exten => _X.,1,Dial(SIP/sip{$Provider.PK_SipProvider}/{literal}${EXTEN}{/literal})
exten => _X.,n,Hangup
exten => _msgX.,1,Dial(SIP/sip{$Provider.PK_SipProvider}/{literal}${EXTEN:3}{/literal})
exten => _msgX.,n,Hangup

{/foreach}

{foreach from=$Dongles item=Dongle}
[dongle{$Dongle.PK_Dongle}_ingress]
exten => sms,1,Macro(fix-international-code)
exten => sms,n,Set(MESSAGE(body)={literal}${BASE64_DECODE(${SMS_BASE64})}{/literal})
exten => sms,n,MessageSend(sip:1235,{literal}${CALLERID(num)}{/literal})
exten => sms,n,Hangup()
exten => ussd,1,Hangup()
exten => _+X.,1,Macro(fix-international-code)
exten => _+X.,n,Goto(dongle{$Dongle.PK_Dongle}_routing,{literal}00${EXTEN:1}{/literal},1)
exten => _X.,1,Macro(fix-international-code)
exten => _X.,n,Goto(dongle{$Dongle.PK_Dongle}_routing,{literal}${EXTEN}{/literal},1)
exten => i,1,Macro(fix-international-code)
exten => i,n,Goto(dongle{$Dongle.PK_Dongle}_routing,{$Dongle.MSISDN},1)
exten => s,1,Macro(fix-international-code)
exten => s,n,Goto(dongle{$Dongle.PK_Dongle}_routing,{$Dongle.MSISDN},1)

{if $Dongle.ApplyIncomingRules}
[dongle{$Dongle.PK_Dongle}_default]
exten => _X.,1,Goto(ext{$Dongle.PK_Dongle}_egress,,{literal}${EXTEN}{/literal},1)
{/if}

[dongle{$Dongle.PK_Dongle}_routing]
{if $Dongle.ApplyIncomingRules}
{foreach from=$Dongle.IncomingRules item=IncomingRule}
include => outgoing{$IncomingRule.PK_IncomingRule}
{/foreach} 
include => dongle{$Dongle.PK_Dongle}_default
{else}
{foreach from=$Dongle.OutgoingRules item=OutgoingRule}
{if $OutgoingRule.ProviderType=='INTERNAL'}
include => internal
{else}
include => outgoing{$OutgoingRule.PK_OutgoingRule}
{/if}
{/foreach} 
{/if}

[dongle{$Dongle.PK_Dongle}_egress]
exten => _X.,1,Dial(Dongle/dongle{$Dongle.PK_Dongle}/{literal}${EXTEN}{/literal})
exten => _X.,n,Hangup
exten => _msgX.,1,DongleSendSMS(dongle{$Dongle.PK_Dongle},{literal}${EXTEN:3}{/literal},{literal}${MESSAGE(body)}{/literal})
exten => _msgX.,2,Hangup()

{/foreach}


[macro-fix-international-code]{literal}
exten => s,1,Set(CALLERID(num)=${IF($["${CALLERID(num):0:1}"="+"]?00${CALLERID(num):1}:${CALLERID(num)})})
exten => s,2,Set(CALLERID(num)=${IF($[$["${CALLERID(num):0:2}"!="00"] & $[${LEN(${CALLERID(num)})}>11]]?00${CALLERID(num)}:${CALLERID(num)})})
exten => s,3,Set(CALLERID(num)=${IF($["${CALLERID(num):0:4}"="0044"]?0${CALLERID(num):4}:${CALLERID(num)})})
exten => s,4,Set(CALLERID(ANI-num)=${CALLERID(num)}){/literal}