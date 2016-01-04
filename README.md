# YunaPBX
YunaPBX is a GPLv2 fork of Starfish PBX (http://www.starfish-pbx.org/) which changed its license to Commercial in July 2015.

It's a web-based Asterisk configuration utility written in PHP, suitable for embedded systems (OpenWRT). Configuration is stored in MySQL database and Smarty templates are used to generate conf files for Asterisk.

## Features
Which Asterisk features can be managed:
* Extensions - templates, groups, etc.
* Music On Hold
* IVRs
* Sounds
* Time Frames
* Call Recording
* Voicemail
* Incoming Call Rules
* Outgoing Call Rules
* Outgoing CLID Rules

Other features:
* Asterisk Status - overview of all extensions and providers
* Hardware Status - disk space, CPU usage, etc.
* Network Settings - configure NAT, eth0 interface (only for OpenWRT at the moment)
* Time Settings - configure timezone, NTP servers  (only for OpenWRT at the moment)

Channel types supported for extensions and providers:
* SIP
* Dongle (see https://github.com/bg111/asterisk-chan-dongle)
* SCCP (see http://chan-sccp-b.sourceforge.net/) - planned

## Project Status
### Admin Interface
| Component | Status | %Complete |
| --------|---------|-------|
| Refactoring, upgrade to PHP 5.6, mysqli | work in progress | 90% |
| Support for 3G Dongles | work in progress | 80% |
| Channel Groups | design/planning | 20% |
| IPv6 Support | design/planning | 20% |
| Backups |design/planning | 20% |

### User Interface
| Component | Status | %Complete |
| --------|---------|-------|
| Refactoring, upgrade to PHP 5.6, mysqli | work in progress | 90% |
| Call Recording | design/planning | 20% |
| Blacklist | design/planning | 0% |
| Queues / Contact Center  | design/planning | 0% |
### Asterisk Integration
| Component | Status | %Complete |
| --------|---------|-------|
| Static conf files | work in progress | 90% |
| Dialplan | work in progress | 20% |
| CDR/CEL | work in progress | 80% |
