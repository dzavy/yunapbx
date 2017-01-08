{foreach from=$SipProviders item=Provider}
; SIP_PROVIDER : {$Provider.Name}
[sip{$Provider.PK_SipProvider}]
type = endpoint
dtmf_mode = {$Provider.DTMFMode}
{if $Provider.ProxyHost != ""}outboundproxy={$Provider.ProxyHost}
{/if}
{if ! $Provider.LocalAddrFrom}
from_domain = {$Provider.Host}
{/if}
{if $Provider.CallerIDChange}
	{if $Provider.CallerIDMethod == 'Remote-Party-ID'}
send_rpid = yes
	{elseif $Provider.CallerIDMethod == 'P-Asserted-Identity'}
from_user = {$Provider.AccountID}
	{/if}
{else}
from_user = {$Provider.AccountID}
{/if}
context = sip{$Provider.PK_SipProvider}_ingress
disallow = all
allow = {$Provider.Codecs}
{if $Provider.UserEqPhone}
user_eq_phone = yes
{/if}
{if $Provider.SendEarlyMedia}
inband_progress = yes
{/if}
outbound_auth = sip{$Provider.PK_SipProvider}
aors = sip{$Provider.PK_SipProvider}

[sip{$Provider.PK_SipProvider}]
type = auth
auth_type = userpass
password = {$Provider.Password}
username = {$Provider.AuthUser}

[sip{$Provider.PK_SipProvider}]
type = aor
contact = sip:{$Provider.ProxyHost}:{$Provider.SipPort}
qualify_frequency = 60

[sip{$Provider.PK_SipProvider}]
type = identify
endpoint = sip{$Provider.PK_SipProvider}
match = {$Provider.ProxyHost}

[sip{$Provider.PK_SipProvider}]
type = registration
outbound_auth = sip{$Provider.PK_SipProvider}
server_uri = sip:{$Provider.ProxyHost}
retry_interval = 120

{/foreach}


{foreach from=$Extensions item=Extension}
[{$Extension.Extension}]
type = endpoint
accountcode = S{$Extension.Extension}
callerid = {$Extension.Name} <{$Extension.Extension}>
dtmf_mode = {$Extension.DTMFMode}
context = ext{$Extension.PK_Extension}_ingress
message_context = ext{$Extension.PK_Extension}_message
disallow = all
allow = {$Extension.Codecs}
{if 'voicemail'|in_array:$Extension.Features }
mailboxes = {$Extension.Extension}@default
{/if}
aors = {$Extension.Extension}
auth = {$Extension.Extension}

[{$Extension.Extension}]
type = aor
max_contacts = 1
qualify_frequency = 60
maximum_expiration = 120

[{$Extension.Extension}]
type = auth
auth_type = userpass
password = {$Extension.PhonePassword}
username = {$Extension.Extension}

{/foreach}
