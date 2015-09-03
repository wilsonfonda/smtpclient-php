<?php
include("Mail.php");

# SMTP transport class using Mail.php that handle redundancy of IP addresses 

class Transport {
    
    private $method ='';
    private $smtpinfo = [];
    private $isSuccess = 0;
    function __construct($method, $smtpinfo)
    {
        $this->method = $method;
        $this->smtpinfo = $smtpinfo;
    }

    function send($recipients, $headers, $mailmsg)
    {
        $iplist = $this->resolveDNS($this->smtpinfo['host']);
        if (!is_array($iplist) || count($iplist)<=1)
            file_put_contents('php://stderr','Error: Cannot find ipaddr: ' . $this->smtpinfo["host"]."\n");
        else
        {
            array_unshift($iplist,"111.222.333.444");
            foreach ($iplist as &$ipaddr)
            {
                $this->smtpinfo['host'] = $ipaddr;
                $factory =& Mail::factory($this->method, $this->smtpinfo);
                $mail = $factory->send($recipients, $headers, $mailmsg);
                if (PEAR::isError($mail)) {
                    file_put_contents('php://stderr',$mail->getMessage()."\n");
                    $this->smtpinfo['host'] = '';
                } else {
                    print("Message successfully sent!\n");
                    $isSuccess=1;
                    break;
                }
            }
        }
        if ($isSuccess == 0)
            file_put_contents('php://stderr',"ip address is not available\n");
    }

    function resolveDNS($hostname)
    {
        $iplist = [];
        try {
            $ipaddrlist = gethostbynamel($hostname);
            if (count($ipaddrlist) > 0)
                $iplist = $ipaddrlist;
            else
                file_put_contents('php://stderr',"no IP address assign to the hostname\n");
        } catch (Exception $e) {
            if($this->debuglevel>0)
                file_put_contents('php://stderr', 'gethostbyname_ex failed: ' . $e->getMessage() . 'hostname=' . $hostname."\n");
        }
        if (count($iplist)>1)
            shuffle($iplist);
        return $iplist;
    }
}

?>
