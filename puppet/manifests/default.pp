node default {

  include 'monit'
  include 'cm::services'
  include 'php5::extension::ssh2'

  class {'cm::application':
    development => true,
  }

  cm::vhost {'www.admin-panel.dev':
    path => '/home/vagrant/admin-panel',
    debug => true,
    aliases => ['admin-panel.dev'],
    cdn_origin => 'origin-www.admin-panel.dev',
  }

  environment::variable {'PHP_IDE_CONFIG':
    value => 'serverName=www.admin-panel.dev',
  }

  class {'cayley::server':
    database => 'mongo',
    database_path => 'localhost:27017',
    database_options => {
      database_name => 'cayley'
    },
    require => Class['mongodb::role::standalone'],
  }

  feedify::server-dev {'api':
    source => '/home/vagrant/api',
    go_script => "/home/vagrant/api/api.go",
    install_script => "/home/vagrant/api/install.sh",
    port => 10111,
  }

  feedify::server-dev {'scenario':
    source => '/home/vagrant/scenario-engine',
    go_script => "/home/vagrant/scenario-engine/scenario.go",
    install_script => "/home/vagrant/scenario-engine/install.sh",
    port => 10222,
  }

  class {'neo4j':
  }

}
