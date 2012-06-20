<?php
/**
 * This file is part of the PivotalGitHubSync component.
 *
 * @version 1.0
 * @copyright Copyright (c) 2012 Manuel Pichler
 * @license GPL licenses.
 */

namespace PivotalGitHubSync;

/**
 * Simple class to abstract the *.ini based configuration.
 */
class Configuration
{
    /**
     * Raw configuration values taken from the *.ini file.
     *
     * @var array
     */
    private $settings;

    /**
     * Constructs a new configuration instance for the given *.ini file.
     *
     * @param string $file
     */
    public function __construct( $file )
    {
        if ( false === file_exists( $file ) )
        {
            throw new \InvalidArgumentException( "Config file {$file} not exists." );
        }

        $this->settings = parse_ini_file( $file, true );
    }

    /**
     * Returns the username for Pivotaltracker.
     *
     * @return string
     */
    public function getPivotalUsername()
    {
        return $this->getSetting( 'pivotal', 'username' );
    }

    /**
     * Returns the password used for the Pivotaltracker login.
     *
     * @return string
     */
    public function getPivotalPassword()
    {
        return $this->getSetting( 'pivotal', 'password' );
    }

    /**
     * Returns the numeric identifier for the Pivotaltracker project.
     *
     * @return integer
     */
    public function getPivotalProject()
    {
        return (int) $this->getSetting( 'pivotal', 'project' );
    }

    /**
     * Returns the username used to login into GitHub.
     *
     * @return string
     */
    public function getGitHubUsername()
    {
        return $this->getSetting( 'github', 'username' );
    }

    /**
     * Returns the API token that is required to access the GitHub API.
     *
     * @return string
     */
    public function getGitHubToken()
    {
        return $this->getSetting( 'github', 'token' );
    }

    /**
     * Returns the owner of a project. This can either be the user of the API
     * or a different identifier like an organization.
     *
     * @return string
     */
    public function getGitHubOwner()
    {
        if ( $this->hasSetting( 'github', 'owner' ) )
        {
            return $this->getSetting( 'github', 'owner' );
        }
        return $this->getGitHubUsername();
    }

    /**
     * Returns the name of the github project to sync.
     *
     * @return string
     */
    public function getGitHubProject()
    {
        return $this->getSetting( 'github', 'project' );
    }

    /**
     * Tests if a setting exists.
     *
     * @param string $section
     * @param string $name
     * @return boolean
     */
    private function hasSetting( $section, $name )
    {
        return isset( $this->settings[$section][$name] );
    }

    /**
     * Returns a single configuration value for the given section and name.
     *
     * @param string $section
     * @param string $name
     * @return string
     * @throws \RuntimeException
     */
    private function getSetting( $section, $name )
    {
        if ( isset( $this->settings[$section][$name] ) )
        {
            return $this->settings[$section][$name];
        }
        if ( isset( $this->settings[$section] ) )
        {
            throw new \RuntimeException( "Missing mandatory setting {$name} in section {$section}." );
        }
        throw new \RuntimeException( "Missing mandatory section {$section}." );
    }
}