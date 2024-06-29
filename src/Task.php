<?php



namespace Solenoid\Core;



use \Solenoid\Core\App\App;



class Task
{
    public App $app;



    # Returns [self]
    public function __construct (App &$app)
    {
        // (Getting the value)
        $this->app = &$app;
    }
}



?>