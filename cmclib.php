<?php
include("Mail.php");

class Transport {
    
    private $factory = null;
    private $isSuccess = 0;
    function __construct($method, $smtpinfo)
    {
        $this->factory =& Mail::factory($method, $smtpinfo);
    }

    function send($recipients, $headers, $mailmsg)
    {
        $iplist = $this->resolveDNS($this->factory->host);
        if (count($iplist)==0)
            throw new Exception('Cannot find ipaddr: ' . $headers["host"]);
        foreach ($iplist as &$ipaddr)
        {
            try
            {
                $this->factory->host = $ipaddr;
                $mail = $this->factory->send($recipients, $headers, $mailmsg);
                if (PEAR::isError($mail)) {
                    echo('<p>' . $mail->getMessage() . '</p>');
                    $this->factory["host"] = '';
                    continue;
                } else {
                    echo('<p>Message successfully sent!</p>');
                    $isSuccess=1;
                    break;
                }
            } catch (Exception $e) {
                $errmsg = $errmsg . $ipaddr . ' failed: '.get_class($e).':'.$e->getMessage();
                file_put_contents('php://stderr',$errmsg);
                $this->factory["host"] = '';
                continue;
            }    
        }
        if ($isSuccess == 0)
            file_put_contents('php://stderr','ip address is not available');
    }

    function resolveDNS($hostname)
    {
        $iplist = [];
        try {
            $ipaddrlist = gethostbynamel($hostname);
            if (count($ipaddrlist) > 0)
                $iplist = $ipaddrlist;
            else
                throw new Exception('no IP address assign to the hostname');
        } catch (Exception $e) {
            if($this->debuglevel>0)
                file_put_contents('php://stderr', 'gethostbyname_ex failed: ' . $e->getMessage() . 'hostname=' . $hostname);
        }
        print_r($iplist);
        if (count($iplist)>1)
            shuffle($iplist);
        return $iplist;
    }
}

?>
