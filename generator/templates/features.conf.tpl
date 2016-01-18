[general]
{if $Parking}
parkext => {$Parking.Extension}
parkpos => {$Parking.Start}-{$Parking.Stop}
context => parkedcalls
parkingtime => {$Parking.Timeout}
{/if}


[featuremap]
blindxfer => #                ; Blind transfer, default is #
disconnect => *0               ; Disconnect (for attended transfer)
atxfer => *2                   ; Attended transfer