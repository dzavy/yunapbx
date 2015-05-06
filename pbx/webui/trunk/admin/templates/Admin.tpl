<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <!--
                <link rel="stylesheet" href="css/admin.css"   type="text/css" />
                <link rel="stylesheet" href="css/jqModal.css" type="text/css" />
                <link rel="stylesheet" href="css/tooltip.css" type="text/css" />
                <link rel="stylesheet" type="text/css" media="all" href="css/calendar.css" title="win2k-cold-1" />-->

        <link rel="stylesheet" type="text/css" media="all" href="css/calendar.css" />
        <link rel="stylesheet" href="newtheme/reset.css" type="text/css" />
        <link rel="stylesheet" href="newtheme/telesoft.css" type="text/css" />
        <link rel="stylesheet" href="newtheme/suckerfish.css" type="text/css" />
        <link rel="stylesheet" href="newtheme/jquery.tooltip.css" type="text/css" />

        <script type="text/javascript" src="../script/jquery.js"></script>
        <script type="text/javascript" src="../script/jquery.suckerfish.js"></script>

        <title>Starfish PBX</title>
    </head>

    <body>
        <div id="wrapper">
            <div id="top">
                <div class="padding">
                    <a href="Logout.php">Logout</a>
                </div>
            </div>

            <div id="header">
                <h1>DzavyNetPBX</h1>
                <h2>Asterisk Web Management Interface</h2>
            </div>
            <div id="menu">
                <ul id="nav">
                    <li><span class="menuitem">{t}Extensions{/t}</span>
                        <ul>
                            <li><a href="Extensions_List.php">{t}Manage Extensions{/t}</a></li>
                            <li><a href="Templates_List.php">{t}Extension Templates{/t}</a></li>
                            <!--<li><a href="#">{t}Extension Settings{/t}</a></li>-->
                            <li><a href="Groups_List.php">{t}Extension Groups{/t}</a></li>
                            <!--<li><a href="#">{t}Extension Permissions{/t}</a></li>-->
                        </ul>
                    </li>
                    <li><span class="menuitem">{t}PBX Features{/t}</span>
                        <ul>
                            <li><a href="MOH_Files_List.php">{t}Music On Hold{/t}</a></li>
                            <li><a href="IVR_Menus.php">{t}IVR Editor{/t}</a></li>
                            <li><a href="SoundEntries_List.php">{t}Sound Manager{/t}</a></li>
                            <li><a href="TimeFrames.php">{t}Time Frames{/t}</a></li>						
                            <li><a href="Recordings_List.php">{t}Call Recording{/t}</a></li>
                            <li><a href="VoicemailSettings.php">{t}Voicemail Settings{/t}</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">{t}Connectivity{/t}</span>
                        <ul>
                            <li><a href="VoipProviders_List.php">{t}VOIP Providers{/t}</a></li>
                            <li><a href="GSMModems_List.php">{t}GSM Modems{/t}</a></li>
                            <li><a href="OutgoingCalls.php">{t}Outgoing Calls{/t}</a></li>
                            <li><a href="IncomingCalls.php">{t}Incoming Calls{/t}</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">{t}Diagnostics{/t}</span>
                        <ul>
                            <li><a href="SystemStatus.php">{t}System Status{/t}</a></li>
                            <li><a href="HardwareMonitor.php">{t}Hardware Monitor{/t}</a></li>
                            <li><a href="CallLog.php">{t}Call Log{/t}</a></li>

                        </ul>
                    </li>
                    <li><span class="menuitem">{t}System Setup{/t}</span>
                        <ul>
                            <li><a href="NetworkSettings.php">{t}Network Settings{/t}</a></li>
                            <li><a href="SystemClockSettings.php">{t}Set System Clock{/t}</a></li>
                            <li><a href="Backup.php">{t}Backups{/t}</a></li>
                            <li><a href="SystemReload.php">{t}System Reload{/t}</a></li>
                        </ul>

                    </li>
                </ul>
                <div style="clear:both"></div>
            </div>

            <div id="content">
                {$Output}
            </div>

            <div id="footer">
                <p>
                    Copyright &copy; 2009, <strong><a href="http://www.telesoft.ro/">Telesoft SRL</a></strong>
                </p>
            </div>
        </div>
    </body>
</html>
