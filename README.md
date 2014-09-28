
============================================================================
		btsync-cli.php
			code by bop
============================================================================

usage: $ php btsync-cli.php <btsync.conf> <action> <data>

action:		data:			usage:
adddir		<folder>		./btsync-cli.php btsync.conf adddir /home/user/folder
addsyncfolder	<secret> <folder>	./btsync-cli.php btsync.conf addsyncfolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder
removefolder	<secret> <folder>	./btsync-cli.php btsync.conf removefolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder
getsyncfolders				./btsync-cli.php btsync.conf getsyncfolders

