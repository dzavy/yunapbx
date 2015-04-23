[general]
faxdetect=yes
t38pt_udptl=yes
limitonpeer=yes
context=unknown_provider
port=5060
bindaddr=0.0.0.0
srvlookup=yes
videosupport=yes
rtptimeout=900
rtpholdtimeout=3600
rtcachefriends=yes
rtupdate=yes
rtautoclear=no
rtignoreexpire=yes
useragent=Asterisk PBX (TeleSoftPBX)
callerid=unknown

tos_sip={$Settings.Network_ToS_Audio}
tos_audio={$Settings.Network_ToS_Audio}
tos_video={$Settings.Network_ToS_Video}

{if $Settings.Network_UseNAT == "1"}
externip={$Settings.Network_ExternalAddress}
;externhost={$Settings.Network_ExternalAddress}
{foreach from=$Settings.Network_Additional_LAN item=Localnet}
localnet={$Localnet}
{/foreach}
{foreach from=$Settings.Network_Interfaces_LAN item=Localnet}
localnet={$Localnet}
{/foreach}
{/if}

;########## START : Register Sip Providers ##########
{foreach from=$SipProviders item=P}
{if $P.HostType == "Provider"}
register => {$P.AccountID}:{$P.Password}:{$P.AuthUser}@sip_provider_{$P.PK_SipProvider}/{$P.AccountID}{if $P.SipExpiry != ""}~{$P.SipExpiry}{/if}
{/if}

{/foreach}
;########## END : Register Sip Providers ##########


;########## START: Define Sip Providers ############
{foreach from=$SipProviders item=Provider}
{assign var="id" value=0}
{foreach from=$Provider.Hosts item=Host}
; SIP_PROVIDER : {$Provider.Name}
{if $id == 0}[sip_provider_{$Provider.PK_SipProvider}]
{if $Provider.ProxyHost != ""}outboundproxy={$Provider.ProxyHost}
{/if}
{else}
[sip_provider_{$Provider.PK_SipProvider}_{$id}]
{/if}
{assign var="id" value=$id+1}
host={$Host}
type=friend
secret={$Provider.Password}
username={$Provider.AuthUser}

{if ! $Provider.LocalAddrFrom}
fromdomain={$Provider.Host}
{/if}

{if $Provider.CallerIDChange}
	{if $Provider.CallerIDMethod == 'Remote-Party-ID'}
sendrpid = yes
	{elseif $Provider.CallerIDMethod == 'P-Asserted-Identity'}
fromuser={$Provider.AccountID}
	{/if}
{else}
fromuser={$Provider.AccountID}
{/if}

{if     $Provider.Jitterbuffer == 'never'}
jbenable=no
{elseif $Provider.Jitterbuffer == 'yes'}
jbenable=yes
jbimpl=adaptive
{elseif $Provider.Jitterbuffer == 'always'}
jbenable=yes
jbimpl=adaptive
jbforce=yes
{elseif $Provider.Jitterbuffer == 'fixed'}
jbenable=yes
jbimpl=fixed
{elseif $Provider.Jitterbuffer == 'alwaysfixed'}
jbenable=yes
jbimpl=fixed
jbforce=yes
{/if}
context=sip_provider_{$Provider.PK_SipProvider}
dtmfmode={$Provider.DTMFMode}
port={$Provider.SipPort}

{if     $Provider.Reinvite=="no"}
canreinvite=no
{elseif $Provider.Reinvite=="yes"}
canreinvite=yes
{elseif $Provider.Reinvite=="nonat"}
canreinvite=nonat
{elseif $Provider.Reinvite=="update"}
canreinvite=update
{elseif $Provider.Reinvite=="update-nonat"}
canreinvite=update,nonat
{/if}

disallow=all
allow={$Provider.Codecs}
{if $Provider.AlwaysTrust}
insecure=invite
{/if}
{if $Provider.Qualify}
qualify=yes
{/if}
{if $Provider.UserEqPhone}
usereqphone=yes
{/if}
{if $Provider.SendEarlyMedia}
progressinband=yes
{/if}
{/foreach}

{/foreach}
;########## END: Define Sip Providers ############


;########## START : Define Sip Users ##########
{foreach from=$Extensions item=Extension}
[{$Extension.Extension}]
accountcode=S{$Extension.Extension}
type=friend
callerid={$Extension.FirstName} {$Extension.LastName} <{$Extension.Extension}>
username={$Extension.Extension}
secret={$Extension.PhonePassword}
dtmfmode={$Extension.DTMFMode}
context=internal
host=dynamic
nat={$Extension.NATType}
disallow=all
allow={$Extension.Codecs}
qualify=yes
{if 'voicemail'|in_array:$Extension.Features }
mailbox={$Extension.Extension}@default
{/if}

{/foreach}
;########## END : Define Sip Users ##########
