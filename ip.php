
<?php

echo $_SERVER['REMOTE_ADDR'];
echo "<br>";

//echo gethostbyaddr($_SERVER['REMOTE_ADDR']) ;

     function returnMacAddress() {
       // This code is under the GNU Public Licence
       // Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
       // Tested only on linux, please report bugs

       // WARNING: the commands 'which' and 'arp' should be executable
      // by the apache user; on most linux boxes the default configuration
      // should work fine

       // Get the arp executable path
        $location = `which arp`;
       // Execute the arp command and store the output in $arpTable
       $arpTable = `arp -a`;
       // Split the output so every line is an entry of the $arpSplitted array
       $arpSplitted = explode("\\n",$arpTable);
       // Get the remote ip address (the ip address of the client, the browser)
       $remoteIp = getenv('REMOTE_ADDR');
       // Cicle the array to find the match with the remote ip address
       foreach ($arpSplitted as $value) {
         // Split every arp line, this is done in case the format of the arp
         // command output is a bit different than expected
          $valueSplitted = explode(" ",$value);
          foreach ($valueSplitted as $spLine) {
           if (preg_match("/$remoteIp/",$spLine)) {
                $ipFound = true;
          }
        // The ip address has been found, now rescan all the string
        // to get the mac address
        if (isset($ipFound)) {
               // Rescan all the string, in case the mac address, in the string
               // returned by arp, comes before the ip address
               // (you know, Murphy's laws)
           reset($valueSplitted);
           foreach ($valueSplitted as $spLine) {
                 if (preg_match("/[0-9a-f][0-9a-f][:-]".
                     "[0-9a-f][0-9a-f][:-]".
                     "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                    "[0-9a-f][0-9a-f][:-]".
                  "[0-9a-f][0-9a-f]/i",$spLine)) {
                     return $spLine;
                  }
              }
         }
        $ipFound = false;
       }
       }
      return false;
     }
echo 'Mac de la PC: ' . returnMacAddress(); 


?>
<script type="text/javascript" src="//ra.revolvermaps.com/0/0/8.js?i=0te72cltgp3&amp;m=0&amp;s=220&amp;c=ff0000&amp;cr1=ffffff&amp;f=arial&amp;l=33" async="async"></script>