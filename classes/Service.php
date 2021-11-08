<?php

/**
 * Class for representing the service data
 */
class Service {

    public string $ref = '';
    public string $centre = '';
    public string $service = '';
    public string $country = '';

    public function __construct(array $data) {

        //Sanitise the input values
        $this->ref = filter_var($data['ref'], FILTER_SANITIZE_STRING);
        $this->centre = filter_var($data['centre'], FILTER_SANITIZE_STRING);
        $this->service = filter_var($data['service'], FILTER_SANITIZE_STRING);
        $this->country = filter_var($data['country'], FILTER_SANITIZE_STRING);

    }

}