<?php

## curl_grab_page mod version 4 btsync
function grab_page( $url )
{
    ## make cookie
    $cookie = getcwd()."/cookie.txt";
    if( !is_file($cookie) )
    {
        $fp = fopen($cookie, "w+");
        fclose($fp);
    }
    ## config curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt($ch, CURLOPT_TIMEOUT, 40);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    ## proxy config
    if ( false ) {
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9150");
    }
    ## exec curl
    $exec = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    unset($ch);
    ## return
    return array($exec, $info);
}

## decodeSize function to converte byte to megabyte etc...
function decodeSize( $bytes )
{
    $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
    for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}

## btsync class 4 adddir, addsyncfolder and removefolder
class btsync
{
    ## load the config from btsync (json)
    function btsync_load_conf( $conf )
    {
        $json = implode("", file( $conf ) );
        # clear comments
        $json = preg_replace( "#\/\*.*\*\/#sU", "", $json );
        $json = preg_replace( "#\/\/.*\n#U", "", $json );
        # json2array
        $json = json_decode( $json );
        # this variabeln
        if( isset( $json->webui ) )
        {
            $this->webui = "http://{$json->webui->listen}/gui/";
            $this->user = $json->webui->login;
            $this->pass = $json->webui->password;
            $this->user_pass_url = "http://{$json->webui->login}:{$json->webui->password}@{$json->webui->listen}/gui/";
            $this->t = exec("date +%s"); //im looking for a better alternative
            //print_r($this);
        }
        else
        {
            die( "error: btsync.conf json fail!\n" );
        }
    }

    ## login to the webUi and get token
    function btsync_login()
    {
        $html = grab_page( "{$this->user_pass_url}token.html?t={$this->t}", false, true );
        if( $html[1]["http_code"] == 200 )
        {
            preg_match( "#[\w\d\_\-]{50,90}#", $html[0], $token );
            $this->token = $token[0];
        }
        else
        {
            $h = fopen("dev_token.html", "w+");
            fwrite($h, $html[0]);
            fclose($h);
            die( "error: btsync hoster down [{$html[1]["http_code"]}]" );
        }
    }
    
    ## adddir vie webUi
    function btsync_adddir( $folder )
    {
        $UEfolder = urlencode( $folder );
        $adddir = grab_page( "{$this->user_pass_url}?token={$this->token}&action=adddir&dir={$UEfolder}&t={$this->t}" );
        $json = json_decode( $adddir[0], true );
        if( isset( $json["path"] ) )
        {
            echo "{$json["path"]}\n";
        }
        else
        {
            print_r( $adddir[1] );
            $h = fopen( "dev_test.html", "w+" );
            fwrite( $h, $adddir[0] );
            fclose( $h );
        }
    }
    
    ## addsyncfolder via webUi
    function btsync_addsyncfolder( $secret, $folder )
    {
        $UEfolder = urlencode( $folder );
        $html = grab_page( "{$this->user_pass_url}?token={$this->token}&action=addsyncfolder&name={$UEfolder}&secret={$secret}&new=0&t={$this->t}" );
        $json = json_decode( $html[0], true );
        if( $json["error"] == 100 )
        {
            echo $json["message"];
            echo "\n";
        }
        if( $json["error"] == 0 )
        {
            echo "add {$secret}\n";
            //print_r( $json );
        }
    }
    
    ## removefolder via webUi
    function btsync_removefolder( $secret, $folder )
    {
        $UEfolder = urlencode( $folder );
        $html = grab_page( "{$this->user_pass_url}?token={$this->token}&action=removefolder&name={$UEfolder}&secret={$secret}&t={$this->t}" );
        if( $html[1]["http_code"] == 200 )
        {
            echo "removefolder {$folder}\n";
        }
    }
    
    ## get folders
    function btsync_getsyncfolders(  )
    {
        $html = grab_page( "{$this->user_pass_url}?token={$this->token}&action=getsyncfolders&discovery=1&t={$this->t}" );
        $json = json_decode( $html[0], true );
        if( isset( $json["folders"] ) )
        {
            foreach( $json["folders"] as $key => $value )
            {
                echo "Name:\t{$value["name"]}\n";
                echo "Secret:\t{$value["secret"]}\n";
                $p = count( $value["peers"] );
                $s = decodeSize( $value["size"] );
                echo "Peers: {$p}\tSize: {$s}\tStatus: {$value["status"]}\n\n";
            }
            echo "\n".$json["speed"]."\n";
        }
        else
        {
            echo "getsyncfolders error\n";
            die();
        }
    }
    
    ## the class main
    function main( $argv )
    {
        $this->btsync_load_conf( $argv[1] );
        $this->btsync_login();
        switch( $argv[2] )
        {
            case "addsyncfolder":
                $this->btsync_addsyncfolder( $argv[3], $argv[4] );
            break;
            
            case "adddir":
                $this->btsync_adddir( $argv[3] );
            break;
            
            case "removefolder":
                $this->btsync_removefolder( $argv[3], $argv[4] );
            break;
            
            case "getsyncfolders":
                $this->btsync_getsyncfolders();
            break;
        }
        unlink( "cookie.txt" );
    }
}

## start the class (realy main)

if( !isset($argv[2]) )
{
    ## the help
    echo "\n============================================================================\n";
    echo "\t\tbtsync-cli.php\n";
    echo "\t\t\tcode by bop\n";
    echo "============================================================================\n";
    echo "\n";
    echo "usage: \$ php ".$argv[0]." <btsync.conf> <action> <data>\n";
    echo "\n";
    echo "action:\t\tdata:\t\t\tusage:\n";
    echo "adddir\t\t<folder>\t\t./{$argv[0]} btsync.conf adddir /home/user/folder\n";
    echo "addsyncfolder\t<secret> <folder>\t./{$argv[0]} btsync.conf addsyncfolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder\n";
    echo "removefolder\t<secret> <folder>\t./{$argv[0]} btsync.conf removefolder FOOBARFOOBARFOOBARFOOBAR /home/user/folder\n";
    echo "getsyncfolders\t\t\t\t./{$argv[0]} btsync.conf getsyncfolders\n";
    echo "\n";
    die();
}
else
{
    ## start the btsync class
    $btsync = new btsync;
    $btsync->main( $argv );
}

## realy main end


?>
