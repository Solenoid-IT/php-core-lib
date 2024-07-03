<?php



namespace Solenoid\Core\Task;



use \Solenoid\System\Process;



class SystemTask
{
    public string  $id;
    public string  $fn;
    public array   $args;
    public string  $executor;



    # Returns [self]
    public function __construct (string $id, string $fn = 'run', array $args = [], string $executor = 'php bootstrap.php')
    {
        // (Getting the values)
        $this->id       = $id;
        $this->fn       = $fn;
        $this->args     = $args;
        $this->executor = $executor;
    }



    # Returns [string]
    public function run ()
    {
        // Returning the value
        return trim( shell_exec($this) );
    }

    # Returns [Process|false]
    public function start ()
    {
        // Returning the value
        return Process::start($this);
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