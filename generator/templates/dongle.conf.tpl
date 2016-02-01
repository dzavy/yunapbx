[general]
interval=15

[defaults]
context=default
group=0
rxgain=0
txgain=0
autodeletesms=yes
resetdongle=yes
u2diag=-1
usecallingpres=yes
callingpres=allowed_passed_screen
language=en
smsaspdu=yes
mindtmfgap=45
mindtmfduration=80
mindtmfinterval=200
callwaiting=auto
initstate=start
dtmf=relax

{foreach from=$Dongles item=Dongle}
[dongle{$Dongle.PK_Dongle}]
imei={$Dongle.IMEI}
imsi={$Dongle.IMSI}
context=dongle{$Dongle.PK_Dongle}_ingress
disablesms={($Dongle.EnableSMS)?"no":"yes"}
exten={$Dongle.MSISDN}

{/foreach}
