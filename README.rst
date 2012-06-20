===================
Pivotal GitHub Sync
===================

This tool can be used to synchronize issues/stories between the GitHub issue
tracker and the PivotalTracker.

By default this tool works bidirectional, so that issues created on GitHub will
appear in your PivotalTracker project and new tickets on PivotalTracker will be
synced into the project's GitHub issue tracker.

Install
=======

Clone the entire repository. ::

  ~ $ git clone git://github.com/manuelpichler/pivotal-github-sync.git
  ~ $ cd pivotal-github-sync

Install composer to retrieve the project's dependencies. ::

  ~ $ curl -s http://getcomposer.org/installer | php

Install the project dependencies. ::

  ~ $ ./composer.phar install

That's it. To test that everything works as expected type the following: ::

  ~ $ ./src/bin/pivotal-github-sync -h

Now you can use the sync tool.

Synchronizing
=============

To synchronize two issue tracker you must first create a simple configuration
file that contains the credentials and project settings for both issue trackers.

::

  [pivotal]
  username = ptuser
  password = $sEcReTe
  project  = 12345

  [github]
  username = ghuser
  password = $SeCrEtE
  project  = build-commons

  ; Optional project owner. Use this when the username differs from project owner.
  ; This may happen when the project is in an organization.
  ; owner    = OrganizationName

To use this configuration you must specify the config file location as argument
when you call the sync tool. ::

  ~ $ ./src/bin/pivotal-github-sync ../build-commons.ini

Now Pivotal-GitHub-Sync will synchronize all open issues between your GitHub
issue tracker and the corresponding project in PivotalTracker.
