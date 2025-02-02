<?php



namespace Solenoid\Core\Task;



use \Solenoid\OS\Process;



class SystemTask
{
    public string  $id;
    public string  $fn;
    public array   $args;
    public string  $executor;

    public ?string  $cwd;
    public ?string  $input;



    # Returns [self]
    public function __construct (string $id, string $fn = 'run', array $args = [], string $executor = 'php bootstrap.php')
    {
        // (Getting the values)
        $this->id       = $id;
        $this->fn       = $fn;
        $this->args     = $args;
        $this->executor = $executor;



        // (Setting the values)
        $this->cwd      = null;
        $this->input    = null;
    }



    # Returns [self]
    public function set_cwd (string $cwd)
    {
        // (Getting the value)
        $this->cwd = $cwd;



        // Returning the value
        return $this;
    }

    # Returns [self]
    public function set_input (string $input)
    {
        // (Getting the value)
        $this->input = $input;



        // Returning the value
        return $this;
    }



    # Returns [Process|false]
    public function run ()
    {
        // Returning the value
        return ( new Process( $this, $this->cwd ) )->run();
    }

    # Returns [Process|false]
    public function start ()
    {
        // Returning the value
        return Process::spawn( $this, $this->cwd, $this->input );
    }



    # Returns [string]
    public function __toString ()
    {
        // (Getting the value)
        $args = implode( ' ', $this->args );



        // Returning the value
        return "$this->executor $this->id $this->fn $args";
    }
}



?>