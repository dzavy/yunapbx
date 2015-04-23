#!/bin/bash
. SQL.sh

SQL_Connect "TeleSoftPBX" "root" "lwnbts."

SQL_Run "DELETE FROM SoundEntries"
SQL_Run "DELETE FROM SoundFiles"

find /usr/share/asterisk/sounds/ -name '*.gsm' |
while read file ;do
	query="
		INSERT INTO 
		SoundEntries(Type, FK_SoundFolder)
		VALUES('System',1);
		SELECT LAST_INSERT_ID();
	"
	PK_SoundEntry=$(SQL_Run "$query")
	PK_SoundEntry=$(SQL_Field "1" $PK_SoundEntry)

	query="
	INSERT INTO
		SoundFiles
	SET
		FK_SoundEntry    = $PK_SoundEntry,
		Name             = '$(basename $file| sed 's/.gsm$//')',
		Description      = '',
		Filename         = '$file',
		FK_SoundLanguage = 1
	"
	SQL_Run "$query"
done 
