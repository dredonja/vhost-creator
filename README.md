# vhost creator

Small php script for making vhosts in Apache 2.4

### Usage:

```sh
$ php vhost.php [your.domain.name] [/path/to/root/directory]
```

### What does it do?

* Creates new `.conf` file in `/etc/apache2/sites-available`
* Creates `symlink` of `.conf` file in `/etc/apache2/sites-enabled`
* Creates new host in `/etc/hosts`
* Restarts Apache server
