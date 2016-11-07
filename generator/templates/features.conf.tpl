[general]
{if $Parking}
parkext => {$Parking.Extension}
parkpos => {$Parking.Start}-{$Parking.Stop}
context => parkedcalls
parkingtime => {$Parking.Timeout}
{/if}
