<?php



namespace Solenoid\Core\MVC;



use \Solenoid\Core\App\App;



class Gate
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