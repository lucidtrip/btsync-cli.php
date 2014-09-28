btsync-cli.php
======
**btsync-cli.php** is a tool to adddir, addsyncfolder, removefolder and getsyncfolders via terminal.

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
