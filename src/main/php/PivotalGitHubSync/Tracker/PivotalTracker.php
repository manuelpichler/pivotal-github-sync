<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL licenses.
 */

namespace PivotalGitHubSync\Tracker;

use \pivotal;
use \PivotalGitHubSync\Issue;
use \PivotalGitHubSync\Tracker;

/**
 * Tracker implementation for PivotalTracker
 */
class PivotalTracker implements Tracker
{
    /**
     * API wrapper used to access Pivotaltracker data.
     *
     * @var \pivotal
     */
    private $pivotal;

    /**
     * Pivotaltracker issue states, that represent an open issue.
     *
     * @var array
     */
    private $openStates = array(
        'accepted',
        'unscheduled',
        'unstarted',
        'started'
    );

    /**
     * Constructs a new Tracker instance for Pivotaltracker.
     *
     * @param string $username
     * @param string $password
     * @param integer $project
     */
    public function __construct( $username, $password, $project )
    {
        $this->pivotal = new \PivotalGitHubSync\Glue\PivotalClient( $project );
        $this->pivotal->authenticate( $username, $password );
    }

    /**
     * Returns an array with issues found in the concrete tracker.
     *
     * @return \PivotalGitHubSync\Issue[]
     */
    public function getIssues()
    {
        $issues = array();
        foreach ( $this->pivotal->getStories() as $data )
        {
            $issues[] = $this->createIssue( $data );
        }
        return $issues;
    }

    /**
     * Creates an issue instance from the raw xml data returned by the api
     * wrapper.
     *
     * @param \SimpleXMLElement $data
     * @return \PivotalGitHubSync\Issue
     */
    private function createIssue( \SimpleXMLElement $data )
    {
        return new Issue(
            array(
                'id'     => trim( (string) $data->id ),
                'title'  => trim( preg_replace( '(\s+)', ' ', (string) $data->name ) ),
                'body'   => trim( (string) $data->description ) .
                            "\n\n[Synced from PivotalTracker: '{$data->url}']",
                'closed' => ( false === in_array( $data->current_state, $this->openStates ) )
            )
        );
    }

    /**
     * Adds the given issue to the concrete tracker implementation.
     *
     * @param Issue $issue
     * @return void
     */
    public function addIssue( Issue $issue )
    {
        echo "Sync to PivotalTracker: ", $issue->title, PHP_EOL;
        $this->pivotal->addStory( 'bug', $issue->title,  $issue->body );
    }
}