<?php
/*
*       Function to get the users IP address. We "attempt"
*       to determine if the user is coming through a proxy server
*       by checking if HTTP_X_FORWARDED_FOR is set, then we
*       use a regular expression to ensure the IP address
*       found is in the proper format
*/
/*
function getIP(){
        // here we check if the user is coming through a proxy
        // NOTE: Does not always work as proxies are not required
        // to provide this information
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                //reg ex pattern
                $pattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
                // now we need to check for a valid format
                if(preg_match($pattern, $_SERVER["HTTP_X_FORWARDED_FOR"])){
                        //valid format so grab it
                        $userIP = $_SERVER["HTTP_X_FORWARDED_FOR"];
                }else{
                        //invalid (proxy provided some bogus value
                        //so just use REMOTE_ADDR and hope for the best
                        $userIP = $_SERVER["REMOTE_ADDR"];
                }               
        }
        //not coming through a proxy (or the proxy
        //didnt provide the original IP)
        else{
                //grab the IP
                $userIP = $_SERVER["REMOTE_ADDR"];
        } 
        //return the IP address
        return $userIP;
}


*/



?>		