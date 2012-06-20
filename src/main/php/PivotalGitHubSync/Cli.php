<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL licenses.
 */

namespace PivotalGitHubSync;

use \PivotalGitHubSync\Synchronizer\FullSynchronizer;
use \PivotalGitHubSync\Tracker\GitHubTracker;
use \PivotalGitHubSync\Tracker\PivotalTracker;

/**
 * Simple cli interface for the sync tool.
 */
class Cli
{
    /**
     * @var array
     */
    private $args;

    /**
     * Should we print debug output on errors?
     *
     * @var boolean
     */
    private $debug = false;

    /**
     * Should we show the help text?
     *
     * @var boolean
     */
    private $help = false;

    /**
     * The project configuration file.
     *
     * @var string
     */
    private $config;

    /**
     * Constructs a new cli instance for the given arguments.
     *
     * @param array $args
     */
    public function __construct( array $args )
    {
        $this->args = $args;
    }

    /**
     * Runs the main workflow for this tool.
     *
     * @return integer
     */
    public function run()
    {
        try
        {
            $this->parseArguments();

            if ( $this->help )
            {
                return $this->showHelp();
            }

            $this->sync();
        }
        catch( \Exception $e )
        {
            fwrite( STDERR, $e->getMessage() . PHP_EOL );
            if ( $this->debug )
            {
                fwrite( STDERR, $e->getTraceAsString() . PHP_EOL );
            }
            return 2;
        }
        return 0;
    }

    /**
     * Parses the command line arguments into object properties.
     *
     * @return void
     * @throws \RuntimeException
     */
    private function parseArguments()
    {
        for ( $i = 0, $c = count( $this->args ); $i < $c; ++$i )
        {
            switch ( $this->args[$i] )
            {
                case '-d':
                case '--debug':
                    $this->debug = true;
                    break;

                case '-h':
                case '--help':
                    $this->help = true;

                default:
                    if ( is_string( $this->config ) )
                    {
                        throw new \RuntimeException( "Invalid cli option '{$this->args[$i]}'." );
                    }
                    $this->config = $this->args[$i];
                    break;
            }
        }

        if ( null === $this->config )
        {
            $this->config = getcwd() . '/sync.ini';
        }
    }

    /**
     * Synchronizes data between two issue trackers.
     *
     * @return void
     */
    private function sync()
    {
        $config = new Configuration( $this->config );

        $github = new GitHubTracker(
            $config->getGitHubUsername(),
            $config->getGitHubToken(),
            $config->getGitHubProject(),
            $config->getGitHubOwner()
        );

        $pivotal = new PivotalTracker(
            $config->getPivotalUsername(),
            $config->getPivotalPassword(),
            $config->getPivotalProject()
        );

        $sync = new FullSynchronizer();
        $sync->synchronizeBidirectional( $github, $pivotal );
    }

    /**
     * Displays the help text for this tool.
     *
     * @return integer
     */
    private function showHelp()
    {
        echo 'Usage:', PHP_EOL,
             '  sync [--debug] [--help] [sync.ini]', PHP_EOL,
             PHP_EOL,
             '  sync.ini     Optional configuration file with the project', PHP_EOL,
             '               settings. If no such file exists sync looks', PHP_EOL,
             '               for a file named sync.ini in the working dir. ', PHP_EOL,
             PHP_EOL,
             '  -d  --debug  Prints full exception stack traces.', PHP_EOL,
             '  -h  --help   Displays this help text.', PHP_EOL;

        return 0;
    }

    /**
     * Main method that starts the sync process. The return value of this method
     * can be used as cli exit code.
     *
     * @return integer
     */
    public static function main()
    {
        $args = $GLOBALS['argv'];
        array_shift( $args );

        set_error_handler( array( __CLASS__, 'errorHandler' ) );

        $cli = new Cli( $args );
        return $cli->run();
    }

    /**
     * Simple error handler that maps PHP errors into exceptions.
     *
     * @param integer $code
     * @param string $message
     * @return void
     * @throws \ErrorException
     */
    public static function errorHandler( $code, $message )
    {
        throw new \ErrorException( $message, $code );
    }
}