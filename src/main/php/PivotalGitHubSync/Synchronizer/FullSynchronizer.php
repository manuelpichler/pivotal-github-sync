<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL v3 license
 */

namespace PivotalGitHubSync\Synchronizer;

use \PivotalGitHubSync\Synchronizer;
use \PivotalGitHubSync\Tracker;

/**
 * Synchronizer implementation that synchronizes all issues.
 */
class FullSynchronizer extends Synchronizer
{
    /**
     * Synchronizes all issues from <b>$source</b> into <b>$target</b> that are
     * not present in <b>$target</b>.
     *
     * @param \PivotalGitHubSync\Tracker $source
     * @param \PivotalGitHubSync\Tracker $target
     * @return integer
     */
    public function synchronize( Tracker $source, Tracker $target )
    {
        return $this->doSynchronize(
            $source->getIssues(),
            $target->getIssues(),
            $target
        );
    }

    /**
     * Synchronizes all issues between <b>$source</b> and <b>$target</b> that
     * are not present in the other tracker.
     *
     * @param \PivotalGitHubSync\Tracker $source
     * @param \PivotalGitHubSync\Tracker $target
     * @return integer
     */
    public function synchronizeBidirectional( Tracker $source, Tracker $target )
    {
        $sourceIssues = $source->getIssues();
        $targetIssues = $target->getIssues();

        return (
            $this->doSynchronize( $sourceIssues, $targetIssues, $target ) +
            $this->doSynchronize( $targetIssues, $sourceIssues, $source )
        );
    }
}