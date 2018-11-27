#!/usr/bin/php-cli -q
<?php
require(dirname(__FILE__) . '/../include/db_utils.inc.php');

function freadline($handle) {
    $line = "";
    $char = "";
    while ($char != "\n" && !feof($handle)) {
        $char = fread($handle, 1);
        $line .= $char;
    }

    return $line;
}

function generate_mail_body($vars) {
    $db = DB::getInstance();
    $query = "SELECT Value FROM Settings WHERE Name = 'Voicemail_EmailTemplate'";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch_row();
    $Body = $row[0];

    foreach ($vars as $name => $value) {
        $Body = str_replace("%$name%", $value, $Body);
    }

    return $Body;
}

$stdin = fopen('php://stdin', 'r');

// Read Headers ($mail['headers']
$empty_lines = 0;
while (!feof($stdin)) {
    $line = freadline($stdin);
    $line = str_replace(array("\n", "\r"), '', $line);

    if (empty($line)) {
        break;
    }

    $mail['headers'] .= $line . "\r\n";
}

// Read Body ($mail['body])
while (!feof($stdin)) {
    $line = freadline($stdin);

    // Read variables block and create the header
    if (preg_match('/^~~VARSTART~~/', $line)) {
        while (!feof($stdin)) {
            $line = freadline($stdin);
            $line = str_replace(array("\n", "\r"), '', $line);

            if (preg_match('/^~~VAREND~~/', $line)) {
                break;
            }

            $aux = split('~~', $line, 2);
            $vars[$aux[0]] = $aux[1];
        }

        $mail['body'] .= generate_mail_body($vars);

        // Append rest of the body to the email
    } else {
        $mail['body'] .= $line;
    }
}

fclose($stdin);
foreach (explode("\r\n", $mail['headers']) as $header) {
    list($name, $value) = explode(':', $header, 2);
    switch ($name) {
        case 'To':
            preg_match('/\<([a-z0-9.]*\@[a-z0-9.]*)\>[^<]*$/i', $value, $matches);
            $mail['to'] = $matches[1];
            break;

        case 'Subject':
            $mail['subject'] = $value;
            break;

        case 'From':
            preg_match('/\<([a-z0-9.@]*)\>[^<]*$/i', $value, $matches);
            if (!empty($argv[1])) {
                $mail['headers'] = str_replace("<{$matches[1]}>", "<${argv[1]}>", $mail['headers']);
            }
            break;
    }
}

mail($mail['to'], $mail['subject'], $mail['body'], $mail['headers']);
?>
