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
 * Base class for a synchronizer that triggers the synchronization between
 * two trackers.
 */
abstract class Synchronizer
{
    /**
     * Synchronizes all issues from <b>$source</b> into <b>$target</b> that are
     * not present in <b>$target</b>.
     *
     * @param \PivotalGitHubSync\Tracker $source
     * @param \PivotalGitHubSync\Tracker $target
     * @return integer
     */
    public abstract function synchronize( Tracker $source, Tracker $target );

    /**
     * Synchronizes all issues between <b>$source</b> and <b>$target</b> that
     * are not present in the other tracker.
     *
     * @param \PivotalGitHubSync\Tracker $source
     * @param \PivotalGitHubSync\Tracker $target
     * @return integer
     */
    public abstract function synchronizeBidirectional( Tracker $source, Tracker $target );

    /**
     * Synchronizes all issues from <b>$sourceIssues</b> into <b>$target</b> that
     * are not present in <b>$targetIssues</b>.
     *
     * @param \PivotalGitHubSync\Issue[] $sourceIssues
     * @param \PivotalGitHubSync\Issue[] $targetIssues
     * @param \PivotalGitHubSync\Tracker $target
     * @return integer
     */
    protected function doSynchronize( array $sourceIssues, array $targetIssues, Tracker $target )
    {
        $synced = 0;

        foreach ( $sourceIssues as $sourceIssue )
        {
            foreach ( $targetIssues as $targetIssue )
            {
                if ( $targetIssue->equals( $sourceIssue ) )
                {
                    continue 2;
                }
            }

            if ( false === $sourceIssue->closed )
            {
                $target->addIssue( $sourceIssue );
            }

            ++$synced;
        }

        return $synced;
    }
}