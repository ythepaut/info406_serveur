<?php

class ClassNotFoundException extends Exception implements IException {

    protected $message = 'Class not found.';
    private   $string;
    protected $code = 1;
    protected $file;
    protected $line;
    private   $trace;


    public function __construct($message = null, $code = 1) {
        if ($message !== null) {
            $this->message = $message;
            $this->code = $code;
        }
        parent::__construct($this->message, $this->code);
    }


    public function __toString() {
        return get_class($this) . " '" . $this->message . "' in " . $this->file . "(" . $this->line . ")\n" . $this->getTraceAsString();
    }


}



?>