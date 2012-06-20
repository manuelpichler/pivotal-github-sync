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
 * Base interface representing an issue tracker.
 */
interface Tracker
{
    /**
     * Returns an array with issues found in the concrete tracker implementation.
     *
     * @return \PivotalGitHubSync\Issue[]
     */
    public function getIssues();

    /**
     * Adds the given issue to the concrete tracker implementation.
     *
     * @param Issue $issue
     * @return void
     */
    public function addIssue( Issue $issue );
}