<?php



namespace Solenoid\Core\App;



use \Solenoid\Core\Routing\Router;
use \Solenoid\HTTP\Request;



class WebApp extends App
{
    public Router  $router;
    public Request $request;



    # Returns [self] | Throws [Exception]
    public function __construct (array $config, Router &$router)
    {
        if ( parent::fetch_context() !== 'http' )
        {// Match failed
            // (Setting the value)
            $message = "Cannot create the instance :: This object can be used only into HTTP contexts";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // (Calling the function)
        parent::__construct($config);

        // (Getting the value)
        $this->router = &$router;



        // (Getting the value)
        $this->request = Request::read();
    }



    # Returns [self]
    public function run ()
    {
        // (Resolving the route)
        $target = $this->router->resolve( $_GET[ parent::$route_handler ], $_SERVER['REQUEST_METHOD'] );

        if ( $target === false )
        {// (Target not found)
            // (Setting the header)
            http_response_code(404);

            // (Printing the value)
            echo 'ROUTE NOT FOUND';
        }



        // (Running the target)
        $target->run_app($this);



        // Returning the value
        return $this;
    }
}



?>