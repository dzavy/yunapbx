<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

        <link rel="stylesheet" href="../static/css/calendar.css" type="text/css" />
        <link rel="stylesheet" href="../static/css/reset.css" type="text/css" />
        <link rel="stylesheet" href="../static/css/main.css" type="text/css" />
        <link rel="stylesheet" href="../static/css/suckerfish.css" type="text/css" />

        <script type="text/javascript" src="../static/script/jquery.js"></script>
        <script type="text/javascript" src="../static/script/jquery.suckerfish.js"></script>

        <title>YunaPBX</title>
    </head>

    <body>
        <div id="wrapper">
            <div id="top">
                <div class="padding">
                    <a href="Logout.php">Logout</a>
                </div>
            </div>

            <div id="header">
                <h1>YunaPBX</h1>
                <h2>Asterisk Web Management Interface</h2>
            </div>
            <div id="menu">
                <ul id="nav">
                    <li><span class="menuitem">Extensions</span>
                        <ul>
                            <li><a href="Extensions_List.php">Manage Extensions</a></li>
                            <li><a href="Templates_List.php">Extension Templates</a></li>
                            <!--<li><a href="#">Extension Settings</a></li>-->
                            <li><a href="Groups_List.php">Extension Groups</a></li>
                            <!--<li><a href="#">Extension Permissions</a></li>-->
                        </ul>
                    </li>
                    <li><span class="menuitem">PBX Features</span>
                        <ul>
                            <li><a href="MOH_Files_List.php">Music On Hold</a></li>
                            <li><a href="IVR_Menus.php">IVR Editor</a></li>
                            <li><a href="SoundEntries_List.php">Sound Manager</a></li>
                            <li><a href="TimeFrames.php">Time Frames</a></li>						
                            <li><a href="Recordings_List.php">Call Recording</a></li>
                            <li><a href="VoicemailSettings.php">Voicemail Settings</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">Connectivity</span>
                        <ul>
                            <li><a href="VoipProviders_List.php">VoIP Providers</a></li>
                            <li><a href="Dongles_List.php">3G Dongles</a></li>
                            <li><a href="OutgoingCalls.php">Outgoing Calls</a></li>
                            <li><a href="IncomingCalls.php">Incoming Calls</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">Diagnostics</span>
                        <ul>
                            <li><a href="SystemStatus.php">System Status</a></li>
                            <li><a href="HardwareMonitor.php">Hardware Monitor</a></li>
                            <li><a href="CallLog.php">Call Log</a></li>

                        </ul>
                    </li>
                    <li><span class="menuitem">System Setup</span>
                        <ul>
                            <li><a href="NetworkSettings.php">Network Settings</a></li>
                            <li><a href="SystemClockSettings.php">Set System Clock</a></li>
                            <li><a href="Backup.php">Backups</a></li>
                            <li><a href="SystemReload.php">System Reload</a></li>
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
                    Copyright &copy; 2015, <strong><a href="http://yunapbx.uk/">YunaPBX</a></strong>; &copy; 2009, <strong><a href="http://www.telesoft.ro/">Telesoft SRL</a></strong>
                </p>
            </div>
        </div>
    </body>
</html>
