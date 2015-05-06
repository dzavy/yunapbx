<?php

class AsteriskManagerMessage {

    protected $Keys;
    protected $Variables;

    function __construct($response = "") {
        $this->Keys = array();
        $this->Variables = array();

        if ($response != "") {
            $lines = explode("\r\n", $response);
            foreach ($lines as $line) {
                if (substr_count($line, ":") > 0) {
                    $elements = explode(": ", $line, 2);
                    $this->SetKey($elements[0], $elements[1]);
                }
            }
        }
    }

    function GetKeys() {
        return $this->Keys;
    }

    function SetKey($Key, $Value) {
        $this->Keys["$Key"] = $Value;
    }

    function GetKey($Key) {
        return $this->Keys["$Key"];
    }

    function SetVar($Var, $Value) {
        $this->Variables["$Var"] = $Value;
    }

    public function ToString() {
        $message = "";

        foreach ($this->Keys as $key => $value) {
            $message .= "$key: $value\r\n";
        }

        foreach ($this->Variables as $var => $value) {
            $message .= "Variable: $var=$value\r\n";
        }

        $message .= "\r\n";

        return $message;
    }

}

?>