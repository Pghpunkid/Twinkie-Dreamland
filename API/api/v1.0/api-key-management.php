<?php
    function generate_api_token($accountID) {
        global $api_key_salt;
        /*$timeBlock = (ceil(time()/100)*100);
        return sha1(md5($timeBlock.$api_key_salt));*/

        $time = time() - 946684800;
        return encrypt(rand(100,999)."-".($time)."-".rand(100,999)."-".($time+60)."-".rand(100,999)."-twdlkeyv1.0");
    }

    function check_api_token($token) {
        /*$correctToken = generate_api_token();
        if ($token != $correctToken) {
            return false;
        }
        return true;*/

        $string = decrypt($token);
        $parts = explode("-",$string);
        $start = $parts[1];
        $end   = $parts[3];
        $time  = time() - 946684800;

        if ($time >= $start && $time <= $end) {
            return true;
        }
        else if ($time > $end || $time < $start) {
            return false;
        }
    }

    function check_token_validity($token, $newToken) {
        $stringA = decrypt($token);
        $partsA = explode("-",$stringA);
        $startA = $partsA[1];
        $endA   = $partsA[3];
        $identA = $partsA[5];
        $timeA  = time() - 946684800;

        $stringB = decrypt($newToken);
        $partsB = explode("-",$stringB);
        $startB = $partsB[1];
        $endB   = $partsB[3];
        $identB = $partsB[5];
        $timeB  = time() - 946684800;

        if ($identA == $identB) {
            return true;
        }
        return false;
    }

    function check_api_token_status($key) {
        $now = time();
        $timeBlock = (ceil(time()/50)*50);

        $difference = $timeBlock - $now;

        if ($difference >= 50) {
            return "fresh";
        }
        else if ($difference < 50 && $difference >= 25) {
            return "stale";
        }
        else if ($difference < 25 && $difference >= 10) {
            return "old";
        }
        else if ($difference < 10) {
            return "rotten";
        }
    }

    function encrypt($string) {
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        $encryption_iv = '1234567891011121';
        $encryption_key = "spongeyyellowbastards";
        $encryption = openssl_encrypt($string, $ciphering, $encryption_key, $options, $encryption_iv);
        return base64_encode($encryption);
    }

    function decrypt($string) {
        $string = base64_decode($string);
        $ciphering = "AES-128-CTR";
        $iv_length = openssl_cipher_iv_length($ciphering);
        $options = 0;

        $decryption_iv = '1234567891011121';
        $decryption_key = "spongeyyellowbastards";
        $decryption = openssl_decrypt ($string, $ciphering, $decryption_key, $options, $decryption_iv);
        return $decryption;
    }

?>
