VAGRANTFILE_API_VERSION = "2"

Vagrant.require_version ">= 1.8.0"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    # fix for "stdin: is not a tty" msg which appears during provisioning
    # see https://github.com/mitchellh/vagrant/issues/1673
    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
    config.ssh.username = "vagrant"
    config.ssh.password = "vagrant"

    # enable vagrant-cachier plugin if plugin is installed
    if Vagrant.has_plugin?("vagrant-cachier")
       config.cache.scope = :box
       config.cache.enable :apt
       config.cache.enable :apt_lists
    end

    # https://stefanwrobel.com/how-to-make-vagrant-performance-not-suck
    config.vm.provider "virtualbox" do |v|
      host = RbConfig::CONFIG['host_os']

      # Give VM 1/4 system memory & access to all cpu cores on the host
      if host =~ /darwin/
        cpus = `sysctl -n hw.ncpu`.to_i
        # sysctl returns Bytes and we need to convert to MB
        mem = `sysctl -n hw.memsize`.to_i / 1024 / 1024 / 4
      elsif host =~ /linux/
        cpus = `nproc`.to_i
        # meminfo shows KB and we need to convert to MB
        mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i / 1024 / 4
      else
        # sorry Windows folks, I can't help you
        cpus = 2
        mem = 1024
      end

      v.customize ["modifyvm", :id, "--memory", mem]
      v.customize ["modifyvm", :id, "--cpus", cpus]

      # disable USB since it prevents the machine to start 
      # when not having installed the non-commercial USB extension
      v.customize ["modifyvm", :id, "--usb", "off"]
      v.customize ["modifyvm", :id, "--usbehci", "off"]

      # enable linked clone support for faster provisioning 
      # (needs Vagrant >= 1.8.0)
      v.linked_clone = true
    end

    config.vm.define "trusty" do |trusty|
      trusty.vm.box = 'ubuntu/trusty64'

      # Expose PostgreSQL port
      trusty.vm.network :forwarded_port, guest: 5432, host: 8432

      # Expose Webserver port
      trusty.vm.network :forwarded_port, guest: 8080, host: 8080

      trusty.vm.provision "shell", inline: <<-SHELL
          # install PostgreSQL
          sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
          sudo apt-get install wget ca-certificates git
          wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
          sudo apt-get update && sudo apt-get install -yq --no-install-recommends postgresql-9.5 postgresql-client-9.5 postgresql-contrib-9.5 postgresql-server-dev-9.5 
          # install Redis
          sudo apt-get install -yq --no-install-recommends redis-server libhiredis-dev
          # install PHP
          sudo apt-get install -yq --no-install-recommends php5-cli php5-pgsql php5-json php5-redis
          # install npm & bower
          curl -sL https://deb.nodesource.com/setup_7.x | sudo -E bash -
          sudo apt-get install -yq --no-install-recommends git nodejs
          sudo npm install -g bower
          # install composer
          cd /usr/local/bin
          php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
          php -r "if (hash_file('SHA384', 'composer-setup.php') === 'aa96f26c2b67226a324c27919f1eb05f21c248b987e6195cad9690d5c1ff713d53020a02ac8c217dbf90a7eacc9d141d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
          php composer-setup.php;
          php -r "unlink('composer-setup.php');"

          # let PostgreSQL listen on all devices
          sed -i "s|.*listen_addresses.*|listen_addresses = '*'|g" /etc/postgresql/9.5/main/postgresql.conf

          # allow access also from local machine
          echo "host    all          all         10.0.2.2/24      md5" >> /etc/postgresql/9.5/main/pg_hba.conf

          # restart PostgreSQL service
          service postgresql restart
      SHELL
    end
end
