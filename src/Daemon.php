<?php



namespace Solenoid\Core;



interface Daemon
{
    public static function init ();



    public function startup ();

    public function tick ();

    public function stop (int $signal);
}



?>