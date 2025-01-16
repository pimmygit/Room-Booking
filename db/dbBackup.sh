#!/bin/bash
###########################################################
#
# Script to create and restore/apply backup of all
# important server configurations and databases
#
# Usage: dbBackup.sh <create/filename>
#
#       create:   Create the backup
#       filename: Restores the database using
#		  the given backup filename
#
###########################################################

OPTION=$1

if [ -z "$OPTION" ]; then
        echo
        echo " Usage: dbBackup.sh <create/filename>"
        echo
        echo "  create   - Create the backup"
        echo "  filename - Restores the database using"
        echo "             the given backup filename"
        echo
        exit 1
fi


# User settings
###########################################################
ARCHIVE="/home/stefanov/sbroomsBackup"
DB_NAME="sbrooms"
DB_USER="sbrooms_admin"
DB_PASS="vov486ryp" 


# Prompt the user for MySQL password
###########################################################
if [ -z "$DB_PASS" ]; then
	echo
	printf "Please enter the password for user [$DB_USER]: "
	read DB_PASS
fi


# System commands
###########################################################
if [ -x /usr/bin/date ]; then
    DATE="/usr/bin/date"
elif [ -x /bin/date ]; then
    DATE="/bin/date"
else
    echo
    echo "ERROR: [date] does not exist or can't be run on your system. Exiting.."
    echo
    exit 1
fi

if [ -x /usr/bin/mkdir ]; then
    MKDIR="/usr/bin/mkdir"
elif [ -x /bin/mkdir ]; then
    MKDIR="/bin/mkdir"
else
    echo
    echo "ERROR: [mkdir] does not exist or can't be run on your system. Exiting.."
    echo
    exit 1
fi

if [ -x /usr/bin/chmod ]; then
    CHMOD="/usr/bin/chmod"
elif [ -x /bin/chmod ]; then
    CHMOD="/bin/chmod"
else
    echo
    echo "ERROR: [chmod] does not exist or can't be run on your system. Exiting.."
    echo
    exit 1
fi

if [ -x /usr/local/mysql/bin/mysql ]; then
    MYSQL="/usr/local/mysql/bin/mysql -u$DB_USER -p$DB_PASS"
elif [ -x /usr/mysql/bin/mysql ]; then
    MYSQL="/usr/mysql/bin/mysql -u$DB_USER -p$DB_PASS"
elif [ -x /usr/bin/mysql ]; then
    MYSQL="/usr/bin/mysql -u$DB_USER -p$DB_PASS"
elif [ -x /opt/mysql/bin/mysql ]; then
    MYSQL="/opt/mysql/bin/mysql -u$DB_USER -p$DB_PASS"
else
    echo
    echo "ERROR: [mysql] does not exist or can't be run on your system. Exiting.."
    echo
    exit 1
fi

if [ -x /usr/local/mysql/bin/mysqldump ]; then
    MYSQLDUMP="/usr/local/mysql/bin/mysqldump -u$DB_USER -p$DB_PASS"
elif [ -x /usr/mysql/bin/mysqldump ]; then
    MYSQLDUMP="/usr/mysql/bin/mysqldump -u$DB_USER -p$DB_PASS"
elif [ -x /usr/bin/mysqldump ]; then
    MYSQLDUMP="/usr/bin/mysqldump -u$DB_USER -p$DB_PASS"
elif [ -x /opt/mysql/bin/mysqldump ]; then
    MYSQLDUMP="/opt/mysql/bin/mysqldump -u$DB_USER -p$DB_PASS"
else
    echo
    echo "ERROR: [mysqldump] does not exist or can't be run on your system. Exiting.."
    echo
    exit 1
fi


# Main routine
#######################################################
if [ "$OPTION" == "create"  ]; then
	
        # Backup database
        #######################################################
	TIMEOFBACKUP=`$DATE +%Y%m%d_%k%M%S`
	DB_BCKP_NAME=$DB_NAME"_"$TIMEOFBACKUP".sql"

	if [ ! -d $ARCHIVE ]; then
		$MKDIR -p $ARCHIVE
		if [ $? == -1 ]; then
			echo
			echo "ERROR: Failed to create directory [$ARCHIVE]. Exiting.."
			echo
			exit 1
		fi
	fi

	$MYSQLDUMP --opt --databases $DB_NAME > $ARCHIVE/$DB_BCKP_NAME

	$CHMOD 600 $ARCHIVE/$DB_BCKP_NAME
else
	# Verify that the provided backup file exists
	#######################################################
        if [ ! -f $ARCHIVE/$OPTION ]; then
                echo
                echo "ERROR: File [$ARCHIVE/$OPTION] does not exist. Exiting.."
                echo
                exit 1
        fi

        # Backup database to a temp file
        #######################################################
	TIMEOFBACKUP=`$DATE +%Y%m%d_%k%M%S`
	DB_BCKP_NAME=$DB_NAME"_TEMP_"$TIMEOFBACKUP".sql"

	if [ ! -d $ARCHIVE ]; then
		$MKDIR -p $ARCHIVE
		if [ $? == -1 ]; then
			echo
			echo "ERROR: Failed to create directory [$ARCHIVE]. Exiting.."
			echo
			exit 1
		fi
	fi

	$MYSQLDUMP --opt --databases $DB_NAME > $ARCHIVE/$DB_BCKP_NAME
	$CHMOD 600 $ARCHIVE/$DB_BCKP_NAME

	# Restore the database
	#######################################################
	$MYSQL < $ARCHIVE/$OPTION
fi
