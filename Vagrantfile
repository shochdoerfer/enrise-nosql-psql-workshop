VAGRANTFILE_API_VERSION = "2"

Vagrant.require_version ">= 1.7.1"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    # Fix for "stdin: is not a tty" msg which appears during provisioning
    # see https://github.com/mitchellh/vagrant/issues/1673
    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

    # Enable vagrant-cachier plugin if plugin is installed
    if Vagrant.has_plugin?("vagrant-cachier")
       config.cache.scope = :box
       config.cache.enable :apt
       config.cache.enable :apt_lists
       config.cache.enable :composer
    end

    # Optimize performance
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
    end

    # Configure the virtual machine
    config.vm.define "jessie" do |jessie|
        jessie.vm.box      = 'psql-nosql-workshop-jessie32'
        jessie.vm.hostname = "psql-workshop"

        # Share an additional folder to the guest VM. The first argument is
        # an identifier, the second is the path on the guest to mount the
        # folder, and the third is the path on the host to the actual folder.
        jessie.vm.synced_folder ".", "/vagrant", owner: "vagrant", group: "vagrant"

        # Forward PostgreSQL port from guest to host
        jessie.vm.network :forwarded_port, guest: 5432, host: 5432
        # Forward web server port from guest to host
        jessie.vm.network :forwarded_port, guest: 8080, host: 8080
    end
end
