<?php

    function isAddressWhitelisted($address) {
        global $api_whitelisted_addresses;

        for ($ip=0; $ip<sizeof($api_whitelisted_addresses); $ip++) {
            if ($address == $api_whitelisted_addresses[$ip]) {
                return true;
            }
        }

        return false;
    }

    function julianToEpoch($julianDay) {
        $unixTimeStamp = ($julianDay - 2440587.5) * 86400;
        return $unixTimeStamp;
    }

?>
