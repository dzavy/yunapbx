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
                    <strong>Extension {$User.Extension}</strong> [ <a href="Logout.php">Logout</a> ]
                </div>
            </div>

            <div id="header">
                <h1>YunaPBX</h1>
                <h2>Asterisk Web Management Interface</h2>
            </div>
            <div id="menu">
                <ul id="nav">
                    <li><span class="menuitem">Settings</span>
                        <ul>
                            <li><a href="Account_Modify.php">Modify Account</a></li>
                            <li><a href="TimeFrames.php">Time Frames</a></li>
                            <li><a href="ConferenceSetup.php">Conference Setup</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">Voicemail</span>
                        <ul>
                            <li><a href="Voicemail.php">Mailbox</a></li>
                        </ul>
                    </li>
                    <li><span class="menuitem">Call History</span>
                        <ul>
                            <li><a href="CallLog.php">Call Log</a></li>
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
                    Copyright &copy; 2017, <strong><a href="http://yunapbx.uk/">YunaPBX</a></strong>; &copy; 2009, <strong><a href="http://www.telesoft.ro/">Telesoft SRL</a></strong>
                </p>
            </div>
        </div>
    </body>
</html>
