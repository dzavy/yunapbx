{if $Settings.Network_UseNAT == "1"}
externip = {$Settings.Network_ExternalAddress}
{foreach from=$Settings.Network_Additional_LAN item=Localnet}
localnet = {$Localnet}
{/foreach}
{/if}

{foreach from=$SipProviders item=P}
{if $P.HostType == "Provider"}
register = {$P.AccountID}:{$P.Password}:{$P.AuthUser}@{$P.ProxyHost}/{$P.AccountID}{if $P.SipExpiry != ""}~{$P.SipExpiry}{/if}
{/if}

{/foreach}

{foreach from=$SipProviders item=Provider}
; SIP_PROVIDER : {$Provider.Name}
[sip{$Provider.PK_SipProvider}]
{if $Provider.ProxyHost != ""}outboundproxy={$Provider.ProxyHost}
{/if}
host = {$Provider.ProxyHost}
type = friend
secret = {$Provider.Password}
defaultuser = {$Provider.AuthUser}
{if ! $Provider.LocalAddrFrom}
fromdomain = {$Provider.Host}
{/if}
{if $Provider.CallerIDChange}
	{if $Provider.CallerIDMethod == 'Remote-Party-ID'}
sendrpid = yes
	{elseif $Provider.CallerIDMethod == 'P-Asserted-Identity'}
fromuser = {$Provider.AccountID}
	{/if}
{else}
fromuser = {$Provider.AccountID}
{/if}
context = sip{$Provider.PK_SipProvider}_ingress
dtmfmode = {$Provider.DTMFMode}
port = {$Provider.SipPort}
{if $Provider.Reinvite=="no"}
canreinvite = no
{elseif $Provider.Reinvite=="yes"}
canreinvite = yes
{elseif $Provider.Reinvite=="nonat"}
canreinvite = nonat
{elseif $Provider.Reinvite=="update"}
canreinvite = update
{elseif $Provider.Reinvite=="update-nonat"}
canreinvite = update,nonat
{/if}
disallow = all
allow = {$Provider.Codecs}
{if $Provider.AlwaysTrust}
insecure = invite
{/if}
{if $Provider.Qualify}
qualify = yes
{/if}
{if $Provider.UserEqPhone}
usereqphone = yes
{/if}
{if $Provider.SendEarlyMedia}
progressinband = yes
{/if}

{/foreach}


{foreach from=$Extensions item=Extension}
[{$Extension.Extension}]
accountcode = S{$Extension.Extension}
type = friend
callerid = {$Extension.Name} <{$Extension.Extension}>
defaultuser = {$Extension.Extension}
secret = {$Extension.PhonePassword}
dtmfmode = {$Extension.DTMFMode}
context = ext{$Extension.PK_Extension}_ingress
;subscribecontext = hints
host = dynamic
nat = {$Extension.NATType}
disallow = all
allow = {$Extension.Codecs}
outofcall_message_context = ext{$Extension.PK_Extension}_message
qualify = yes
{if 'voicemail'|in_array:$Extension.Features }
mailbox = {$Extension.Extension}@default
{/if}

{/foreach}
