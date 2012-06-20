<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL v3 license
 */

namespace PivotalGitHubSync;

/**
 * Simple domain class representing an issue.
 */
class Issue
{
    /**
     * This unique issue identifier.
     *
     * @var mixed
     */
    public $id;

    /**
     * The issue title.
     *
     * @var string
     */
    public $title;

    /**
     * The detailed issue description.
     *
     * @var string
     */
    public $body;

    /**
     * Is this issue already closed?
     *
     * @var boolean
     */
    public $closed = false;

    /**
     * Constructs a new issue instance and initializes it's properties with the
     * values from the given <b>$values</b> parameter.
     *
     * @param array $values
     */
    public function __construct( array $values = array() )
    {
        foreach ( $values as $name => $value )
        {
            $this->$name = $value;
        }
    }

    /**
     * Tests if this this issue equals the given <b>$issue</b> object.
     *
     * @param \PivotalGitHubSync\Issue $issue
     * @return boolean
     */
    public function equals( Issue $issue )
    {
        return ( $this->title === $issue->title );
    }

    /**
     * Avoid magic properties.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     * @throws \OutOfBoundsException
     */
    public function __set( $name, $value )
    {
        throw new \OutOfBoundsException( "Property \${$name} not exists." );
    }


    /**
     * Avoid magic properties.
     *
     * @param string $name
     * @return void
     * @throws \OutOfBoundsException
     */
    public function __get( $name )
    {
        throw new \OutOfBoundsException( "Property \${$name} not exists." );
    }
}