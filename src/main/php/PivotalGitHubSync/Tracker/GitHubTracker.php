<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL licenses.
 */

namespace PivotalGitHubSync\Tracker;

use \Github\Client;
use \PivotalGitHubSync\Issue;
use \PivotalGitHubSync\Tracker;

/**
 * Tracker implementation for GitHub
 */
class GitHubTracker implements Tracker
{
    /**
     * API wrapper used to access GitHub data.
     *
     * @var \Github\Api\Issue
     */
    private $github;

    /**
     * The project owner.
     *
     * Can be similar to username or it can be the name of a GitHub organization.
     *
     * @var string
     */
    private $owner;

    /**
     * Name of the project to sync.
     *
     * @var string
     */
    private $project;

    /**
     * Constructs a new GitHub tracker instance.
     *
     * @param string $username
     * @param string $password
     * @param string $project
     * @param string $owner Optional project owner, e.g. user or organization.
     */
    public function __construct( $username, $password, $project, $owner = null )
    {
        $github = new Client();
        $github->setHeaders(
            array( 'Authorization: Basic ' . base64_encode( "{$username}:{$password}" ) )
        );
        $github->authenticate( $username, $password, Client::AUTH_HTTP_PASSWORD );

        $this->github = $github->getIssueApi();

        $this->owner   = $owner ?: $username;
        $this->project = $project;
    }

    /**
     * Returns an array with issues found in the concrete tracker.
     *
     * @return \PivotalGitHubSync\Issue[]
     */
    public function getIssues()
    {
        return array_merge(
            $this->fetchByState( 'open' ),
            $this->fetchByState( 'closed' )
        );
    }

    /**
     * Fetches all issues for the given issue state,
     *
     * Optionally you can specify additional filters within the <b>$params</b>
     * parameter. Take a look at the GitHub V3 API documentation that can be
     * found here: http://developer.github.com/v3/issues/
     *
     * @param string $state
     * @param array $params
     * @return \PivotalGitHubSync\Issue[]
     */
    private function fetchByState( $state, array $params = array() )
    {
        $params['page'] = 1;

        $issues = array();
        do
        {
            $result = $this->github->getList( $this->owner, $this->project, $state, $params, $params );
            if ( 0 === count( $result ) )
            {
                break;
            }

            foreach ( $result as $data )
            {
                $issues[] = $this->createIssue( $data );
            }

            ++$params['page'];
        }
        while( true );

        return $issues;
    }

    /**
     * Creates an issue object from the raw data array returned by the GitHub
     * API.
     *
     * @param array $data
     * @return \PivotalGitHubSync\Issue
     */
    private function createIssue( array $data )
    {
        return new Issue(
            array(
                'id'     => trim( $data['id'] ),
                'title'  => trim( preg_replace( '(\s+)', ' ', $data['title'] ) ),
                'body'   => trim( $data['body'] ) .
                            "\n\n[Synced from GitHub: '{$data["html_url"]}']",
                'closed' => ( 'closed' === $data['state'] )
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
        echo "Sync to GitHub: ", $issue->title, PHP_EOL;
        $this->github->open(
            $this->owner,
            $this->project,
            $issue->title,
            $issue->body
        );
    }
}