{foreach from=$Dongles item=Dongle}
[dongle{$Dongle.PK_Dongle}]
imei={$Dongle.IMEI}
imsi={$Dongle.IMSI}
context=dongle{$Dongle.PK_Dongle}_ingress
disablesms={($Dongle.EnableSMS)?"no":"yes"}
exten={$Dongle.MSISDN}

{/foreach}
