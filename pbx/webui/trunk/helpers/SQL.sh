#!/bin/bash
#Example:
#  QUERY="SELECT field1, field2 FROM table"
#  RESULT=$(SQL_Run "$QUERY")
#  for ROW in $RESULT; do
#	  FIELD1=$(SQL_Field 1 "$ROW")
#	  FIELD2=$(SQL_Field 2 "$ROW")
#	  echo "Field1: $FIELD1; Field2: $FIELD2"
#  done

if [[ -n "$HEADER_SQL_Ops" ]]; then
	return 0
fi
HEADER_SQL_Ops=included

SQL_DB=""
SQL_HOST="localhost"
SQL_USER="root"
SQL_PASS=""


SQL_Connect() {
	SQL_DB="$1"

	if [[ "$2" != "" ]] ;then
		SQL_USER="$2"
	fi

	if [[ "$3" != "" ]] ;then
		SQL_PASS="-p$3"
	fi

	if [[ "$4" != "" ]] ;then
		SQL_HOST="$4"
	fi
}

SQL_Run()
{
	local Q Pass
	Q="$*"
	Pass=
	if [[ -n "$MySqlPassword" ]]; then
		Pass="-p$MySqlPassword"
	fi
	if [[ -n "$Q" ]]; then
		echo "$Q;" | mysql -A -N "$SQL_DB" -h "$SQL_HOST" -u "$SQL_USER" $SQL_PASS | SQL_Translate
	fi
}

SQL_Field()
{
	local Row FieldNumber
	FieldNumber="$1"; shift
	Row="$*"
	echo "$Row" | cut -d$'\x01' -f"$FieldNumber" | tr $'\x02' ' '
}


SQL_Translate()
{
	tr '\n\t ' $'\x20'$'\x01'$'\x02' | sed 's/ *$//'
}

