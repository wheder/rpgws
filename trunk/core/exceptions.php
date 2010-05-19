<?php
class Exceptions extends Exception {
    
    private   $info;                             // info message for user
    private   $info_h;                           // info message for user - header
    public function __construct($message = null, $info_h = null, $info_message = null, $code = 0)
    {
        parent::__construct($message, $code);
        $this->info = $info_message;
        $this->info_h = $info_h;
    }
    
    public function __toString()
    {
        if (RPGWS_ENVINRONMENT === 'debug') return $this->info_h ." - ".$this->info;
        return get_class($this) . ' "'.$this->message.'" in '.$this->file.' ('.$this->line.')'."\n". $this->getTraceAsString()."\n\n".$this->info_h."\n".$this->info;
    }
    
    public function get_header() {
        return $this->info_h;
    }

    public function get_info() {
        return $this->info;
    }

}

?>