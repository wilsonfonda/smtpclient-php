<?php
require_once 'lib/swift_required.php';

# SMTP transport class using swiftMailer that handle redundancy of IP addresses 

class CMCMailer {
    
    private $transport = null;
    private $isSuccess = 0;
    function __construct($transport)
    {
        $this->transport = $transport;
    }

    function send($message, &$failedRecipients)
    {
        $iplist = $this->resolveDNS($this->transport->getHost());
        if (!is_array($iplist) || count($iplist)<=1)
            file_put_contents('php://stderr','Error: Cannot find ipaddr: ' . $this->transport->getHost()."\n");
        else
        {
            foreach ($iplist as &$ipaddr)
            {
                try
                {
                    $this->transport->setHost($ipaddr);
                    $mailer = Swift_Mailer::newInstance($this->transport);
                    $mailer->send($message, $failedRecipients);
                    $isSuccess=1;
                    print("\nMessage sent successfully!\n");
                    break;
                }catch(Swift_TransportException $e)
                {
                    
                    $failedRecipients = "\n Error in accessing IP: ".$ipaddr."\n".$e."\n".$failedRecipients;
                    continue;   
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
