<?php namespace Robust\Boilerplate\HTTP\API;

use Robust\Boilerplate\HTTP\RCODES;

// Plantilla de respuesta
class JSONResponse {
    private $data;
    private string $details;
    private RCODES $rcode;

    public function __construct() {
    }

    public function setData($d) {
        $this->data = $d;
    }
    
    public function setCode(RCODES $c, $details = '') {
        $this->rcode = $c;

        if ($details !== '') $this->details = $details;
        else $this->details = $c->name;
    }
    
    public function getData() {
        if ( $this->rcode === RCODES::OK || $this->rcode === RCODES::Created) {
            return json_encode( $this->data );
        
        } else {
            return json_encode($this->details);
        }
    }

    public function getCode() {
        return $this->rcode->value;
    }
}
