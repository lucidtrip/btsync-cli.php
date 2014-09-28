btsync-cli.php
======
**btsync-cli.php** is a command line interface tool for <a href="http://www.bittorrent.com/sync">bit torrent sync</a>
to adddir, addsyncfolder, removefolder and getsyncfolders.


## Core Requirements
* PHP with phpcurl
* `bit torrent sync <http://www.bittorrent.com/sync>`_
* bit torrent sync config (important! needed webUI-listen, -login, -admin and -password)

## usage
```bash
php btsync-cli.php <btsync.conf> <action> <data>
```

## examples
adddir:
```bash
php btsync-cli.php btsync.conf adddir /home/user/folder
```

addsyncfolder:
```bash
php btsync-cli.php btsync.conf addsyncfolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder
```

removefolder:
```bash
php btsync-cli.php btsync.conf removefolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder
```

getsyncfolders:
```bash
php btsync-cli.php btsync.conf getsyncfolders
```


## Contact
* email: nibiru[at]safe-mail[dot]net
