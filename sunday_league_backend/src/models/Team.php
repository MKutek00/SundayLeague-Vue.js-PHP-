<?php


class Team
{
    private $name;
    private $address;
    private $lat;
    private $lng;
    private $leagueId;

    function __construct($teamName, $teamAddress, $leagueId)
    {
        $this->name = $teamName;
        $this->address = $teamAddress;
        $this->leagueId = $leagueId;
        $this->geocode();
    }

    public function geocode()
    {

        // url encode the address
        $address = urlencode($this->getAddress());

        // google map geocode api url
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=";

        // get the json response
        $resp_json = file_get_contents($url);

        // decode the json
        $resp = json_decode($resp_json, true);
        // response status will be 'OK', if able to geocode given address
        if ($resp['status'] == 'OK') {

            // get the important data
            $lati = $resp['results'][0]['geometry']['location']['lat'] ?? "";
            $longi = $resp['results'][0]['geometry']['location']['lng'] ?? "";
            // $formatted_address = $resp['results'][0]['formatted_address'] ?? "";

            // verify if data is complete
            if ($lati && $longi) {
                $this->setLat($lati);
                $this->setLng($longi);
            } else {
                return false;
            }
        } else {
            $this->setLat(0);
            $this->setLng(0);
            return false;
        }
    }


    /**
     * Getters / Setter for $name
     */
    public function getName()
    {
        return $this->name;
    }
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getters / Setter for $address
     */
    public function getAddress()
    {
        return $this->address;
    }
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Getters / Setter for $x
     */
    public function getLat()
    {
        return $this->lat;
    }
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Getters / Setter for $y
     */
    public function getLng()
    {
        return $this->lng;
    }
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }


    public function getLeagueId()
    {
        return $this->leagueId;
    }
    public function setLeagueId($leagueId)
    {
        $this->leagueId = $leagueId;

        return $this;
    }
}
