<?php
/*
 * Shared class that implements functions common to all official Pushup Social plugins.
 * File: Pushup-Common.php
 * Version: 1.0
 * Author: Pushup Social
 */



//And now for the actual class
class PushupSocial {

    //This checks if the Community ID it is passed is valid (optionally for a given domain)
    public function validateCommunityID($community_id) {
        $url = PUSHUP_OPTIONS_API_URL . "/network/communities/?network_id={$community_id}";
        $result = $this->callAPI("GET", $url);

        $response = json_decode($result, true);
        return (isset($response["network_id"]));
    }


    //This checks if the Site ID it is passed is valid (optionally for a given domain)
    public function getSiteCommunityID($site_id) {
        $url = PUSHUP_OPTIONS_API_URL . "/network/communities/?third_party_name=wordpress&third_party_id={$site_id}";
        $result = $this->callAPI("GET", $url);

        $response = json_decode($result, true);
        return isset($response["network_id"]) ? $response['network_id'] : null;
    }
    /*
     * Call Pushup API
     */
    private function callAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // Optional Authentication:
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
}
