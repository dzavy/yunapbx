<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />

	<link rel="stylesheet" type="text/css" media="all" href="css/calendar.css" title="win2k-cold-1" />
	<link rel="stylesheet" href="newtheme/reset.css"   type="text/css" />
	<link rel="stylesheet" href="newtheme/telesoft.css"   type="text/css" />
	<link rel="stylesheet" href="newtheme/suckerfish.css"   type="text/css" />

	<script type="text/javascript" src="../script/jquery.js"></script>
	<script type="text/javascript" src="../script/jquery.suckerfish.js"></script>

	<title>TeleSoft PBX</title>
</head>

<body>
	<div id="wrapper">
		<div id="top">
			<div class="padding">
				<strong>Extension {$User.Extension}</strong> [ <a href="Logout.php" style="color: #fff; font-size: 10px;">Logout</a> ]
			</div>
		</div>

		<div id="header">
			<h1>TeleSoftPBX</h1>
			<h2>Asterisk Web Management Interface</h2>
		</div>
		<div id="menu">
			<ul id="nav">
				<li><span class="menuitem">{t}Settings{/t}</span>
					<ul>
						<li><a href="Account_Modify.php">{t}Modify Account{/t}</a></li>
						<li><a href="#">{t}Call Rules{/t}</a></li>
						<li><a href="TimeFrames.php">{t}Time Frames{/t}</a></li>
						<li><a href="#">{t}Phonebook{/t}</a></li>
						<li><a href="ConferenceSetup.php">{t}Conference Setup{/t}</a></li>
					</ul>
				</li>
				<li><span class="menuitem">{t}Voicemail{/t}</span>
					<ul>
						<li><a href="Voicemail.php">{t}Mailbox{/t}</a></li>
						<li><a href="#">{t}Voicemail Options{/t}</a></li>
					</ul>
				</li>
				<li><span class="menuitem">{t}Call History{/t}</span>
					<ul>
						<li><a href="CallLog.php">{t}Call Log{/t}</a></li>
						<li><a href="#">{t}Call Reporting{/t}</a></li>
					</ul>
				</li>
			</ul>
			<div style="clear:both"></div>
		</div>

		<div id="content">
			{$Output}
		</div>

		<div id="footer">
			<p style="padding: 12px;">
			Copyright &copy; 2008, <strong><a href="http://www.telesoft.ro/">Telesoft SRL</a></strong>
			</p>
		</div>
	</div>
</body>
</html>
