<?php

// Check 'externnotify' in voicemail.conf for user rights

function vm_folders($extension) {
    $vm_path = '/var/spool/asterisk/voicemail/default/' . $extension;

    // Add default dummy folder 
    $Folders[] = array('name' => 'INBOX', 'size' => '0');

    // Prevent path traversal
    if (preg_match('/\.\.\//', $extension)) {
        return $Folders;
    }

    // If the voicemail does not exist return
    if (!is_dir($vm_path)) {
        return $Folders;
    }

    $dh = opendir($vm_path) or die("Cannot open voicemail dir $vm_path");
    while (($file = readdir($dh)) !== false) {
        if (in_array($file, array('.', '..', 'tmp', 'temp'))) {
            continue;
        }

        if (is_dir("$vm_path/$file")) {
            $folder['name'] = $file;
            $out = array();
            exec("ls '$vm_path/$file/'msg*.txt | wc -l", $out);
            $folder['size'] = $out[0];

            $Folders[] = $folder;
        }
    }

    // If remove default dummy folder
    if (count($Folders) > 1) {
        $Folders = array_slice($Folders, 1);
    }

    return $Folders;
}

function vm_files($extension, $folder) {
    $vm_path = '/var/spool/asterisk/voicemail/default/' . $extension . '/' . $folder;
    $messages = array();

    // Prevent path traversal
    if (preg_match('/\.\.\//', $folder)) {
        return $messages;
    }
    if (preg_match('/\.\.\//', $extension)) {
        return $messages;
    }

    // If the voicemail does not exist return null filelist
    if (!is_dir($vm_path)) {
        return $messages;
    }

    $dh = opendir($vm_path) or die("Cannot open voicemail dir $vm_path");
    while (($file = readdir($dh)) !== false) {
        if (in_array($file, array('.', '..', 'tmp', 'temp'))) {
            continue;
        }

        if (!is_file($vm_path . '/' . $file)) {
            continue;
        }

        if (!preg_match('/^msg([0-9]*)\.txt$/', $file, $matches)) {
            continue;
        }
        $message['no'] = $matches[1];

        $msg_content = implode(file($vm_path . '/' . $file), "");
        $msg_content = explode("\n", $msg_content);
        foreach ($msg_content as $line) {
            $line = explode('=', $line);
            if (count($line) != 2) {
                continue;
            }
            $message[$line[0]] = $line[1];
        }

        $messages[] = $message;
    }

    return $messages;
}

function vm_getfile($extension, $folder, $file) {
    $vm_path = '/var/spool/asterisk/voicemail/default/' . $extension . '/' . $folder;

    // Prevent path traversal
    if (preg_match('/\.\.\//', $extension)) {
        return false;
    }
    if (preg_match('/\.\.\//', $folder)) {
        return false;
    }
    if (preg_match('/\.\.\//', $file)) {
        return false;
    }

    $file_and_path = "{$vm_path}/msg{$file}.wav";
    if (file_exists($file_and_path)) {
        header("Content-type: " . mime_content_type($file_and_path));
        //header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"msg{$file}.wav\"");
        $handle = fopen($file_and_path, "r");
        while (!feof($handle)) {
            echo fread($handle, 8192);
        }

        fclose($handle);
    } else {
        echo "File not found. $file_and_path";
    }

    die();
}

function vm_delfile($extension, $folder, $file) {
    $vm_path = '/var/spool/asterisk/voicemail/default/' . $extension . '/' . $folder;

    // Prevent path traversal
    if (preg_match('/\.\.\//', $extension)) {
        return false;
    }
    if (preg_match('/\.\.\//', $folder)) {
        return false;
    }
    if (preg_match('/\.\.\//', $file)) {
        return false;
    }

    $file_and_path = "{$vm_path}/msg{$file}.wav";
    if (file_exists($file_and_path)) {
        exec("rm -f '$vm_path/msg$file.'*", $out);
    }
}

function vm_movfile($extension, $src_folder, $dst_folder, $src_file) {

    // Prevent path traversal
    if (preg_match('/\.\.\//', $extension)) {
        return false;
    }
    if (preg_match('/\.\.\//', $src_folder)) {
        return false;
    }
    if (preg_match('/\.\.\//', $dst_folder)) {
        return false;
    }
    if (preg_match('/\.\.\//', $src_file)) {
        return false;
    }

    $DST_Extension = vm_files($extension, $dst_folder);         // Get a list with all messages
    $DST_Extension = array_pop($DST_Extension);                 // Pop the last message from the list
    $DST_Extension = $DST_Extension['no'];                      // Get the msg# of this last message
    $DST_Extension = $DST_Extension + 1;                        // Increment it to find a new msg#
    $dst_file = str_pad($DST_Extension, 4, "0", STR_PAD_LEFT);  // Pad zeros until we have 4 chars in msg#

    $vm_path_src = '/var/spool/asterisk/voicemail/default/' . $extension . '/' . $src_folder;
    $vm_path_dst = '/var/spool/asterisk/voicemail/default/' . $extension . '/' . $dst_folder;

    $valid_ext = array('wav', 'WAV', 'gsm', 'txt');
    foreach ($valid_ext as $ext) {
        exec("mv '$vm_path_src/msg$src_file.$ext' '$vm_path_dst/msg$dst_file.$ext'", $output);
    }
}
