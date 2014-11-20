Vagrant.configure('2') do |config|
  config.ssh.forward_agent = true
  config.vm.box = 'cargomedia/debian-7-amd64-cm'

  config.vm.hostname = 'www.admin-panel.dev'
  if Vagrant.has_plugin? 'landrush'
    config.landrush.enable
    config.landrush.tld = 'dev'
    config.landrush.host 'admin-panel.dev'
  end

  if Vagrant.has_plugin? 'vagrant-phpstorm-tunnel'
    config.phpstorm_tunnel.project_home = '/home/vagrant/admin-panel'
  end

  synced_folder_type = ENV.fetch('SYNC_TYPE', 'nfs')
  synced_folder_type = nil if 'vboxsf' == synced_folder_type

  config.vm.network :private_network, ip: '10.10.10.30'
  config.vm.network :public_network, :bridge => 'en0: Wi-Fi (AirPort)'
  config.vm.synced_folder '.', '/home/vagrant/admin-panel', :type => synced_folder_type, :rsync__args => ["--verbose", "--archive", "--delete", "-z"]
  config.vm.synced_folder '../sdk-php', '/home/vagrant/sdk-php', :type => synced_folder_type if Dir.exists? '../sdk-php'

  config.librarian_puppet.puppetfile_dir = 'puppet'
  config.librarian_puppet.placeholder_filename = '.gitkeep'
  config.librarian_puppet.resolve_options = {:force => true}
  config.vm.provision :puppet do |puppet|
    puppet.module_path = 'puppet/modules'
    puppet.manifests_path = 'puppet/manifests'
  end

  config.vm.provision 'shell', run: 'always', inline: [
    'cd /home/vagrant/admin-panel',
    'composer --no-interaction install --dev',
  ].join(' && ')

  if ENV['LINK']
    config.vm.provision 'shell', run: 'always', inline: [
      'cd /home/vagrant/admin-panel',
      'rm -rf vendor/feedlabs/sdk-php',
      'ln -s ../../../sdk-php vendor/feedlabs/sdk-php',
    ].join(' && ')
  end

  config.vm.provision 'shell', run: 'always', inline: [
    'cd /home/vagrant/admin-panel',
    'cp resources/config/local.dev.php resources/config/local.php',
    'bin/cm app set-deploy-version',
    'bin/cm app setup',
    'bin/cm db run-updates',
    'sudo foreman-debian stop --app admin-panel',
    'sudo foreman-debian install --app admin-panel --user root',
    'sudo foreman-debian start --app admin-panel',
  ].join(' && ')
end
