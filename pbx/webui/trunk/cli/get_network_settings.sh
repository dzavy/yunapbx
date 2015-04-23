#!/bin/bash

for iface in `/sbin/ifconfig -a | grep 'Link encap:Ethernet' | awk '{print $1}'` ;do
	ip=`/sbin/ifconfig $iface | grep 'inet addr:' | sed 's/.*inet addr:\([0-9.]*\).*$/\1/g'`
	netmask=`/sbin/ifconfig $iface | grep 'inet addr:' | sed 's/.*Mask:\([0-9.]*\).*$/\1/g'`
	if [[ -f "/var/run/dhclient-$iface.pid" ]] ;then
		protocol='dhcp'
	else
		protocol='static'
	fi

	echo "$iface,$ip,$netmask,$protocol"
done
