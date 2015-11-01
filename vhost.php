<?php

class Vhost
{
    protected $domainName;
    protected $pathToIndex;

    public function __construct($domainName, $pathToIndex)
    {
        $this->domainName  = $domainName;
        $this->pathToIndex = $pathToIndex;
    }

    protected function checkRootDir()
    {
        return is_dir($this->pathToIndex);
    }

    # creating .conf file -----------------------------------------------------------

    protected function confFileContent()
    {
        return "<VirtualHost *:80>"."\n".
                "\t"."ServerName {$this->domainName}"."\n".
                "\t"."ServerAdmin webmaster@localhost"."\n".
                "\t"."DocumentRoot {$this->pathToIndex}"."\n".
                "\t"."<Directory {$this->pathToIndex}>"."\n".
                "\t"."\t"."Options Indexes FollowSymLinks"."\n".
                "\t"."\t"."AllowOverride All"."\n".
                "\t"."\t"."Require all granted"."\n".
                "\t"."</Directory>"."\n".
                "</VirtualHost>"."\n";
    }

    protected function createConfFile($path)
    {
        return file_put_contents($path.$this->domainName.'.conf', $this->confFileContent());
    }

    protected function sitesAvailable()
    {
        return $this->createConfFile('/etc/apache2/sites-available/');
    }

    protected function enableSite()
    {
        return `a2ensite {$this->domainName}`;
    }

    # hosts -------------------------------------------------------------------------

    protected function getHosts()
    {
        return file('/etc/hosts');
    }

    protected function newHost()
    {
        return  array('127.0.0.1       '.$this->domainName."\n");
    }

    protected function addNewHost()
    {
        return array_merge($this->newHost(), $this->getHosts());
    }

    protected function hosts()
    {
        return file_put_contents('/etc/hosts', $this->addNewHost());
    }

    # apache ------------------------------------------------------------------------

    protected function restartApache()
    {
        return `service apache2 restart`;
    }

    # create new vhost --------------------------------------------------------------

    public function create()
    {
        if ( $this->checkRootDir() ) {
            $this->sitesAvailable();
            $this->enableSite();
            $this->hosts();
            $this->restartApache();
        } else {
            echo "\n".$this->pathToIndex.' directory does not exists'."\n";
        }
    }
}

isset($argv[1]) ? $domainName  = $argv[1] : $domainName  = '';
isset($argv[2]) ? $pathToIndex = $argv[2] : $pathToIndex = '';

if ( ! empty($domainName) && ! empty($pathToIndex) ) {
    $vhost = new Vhost($domainName, $pathToIndex);
    $vhost->create();
} else {
    echo 'Error: domain name and path to root directory not specified' . "\n";
}

/*

TODO:

- Check if .conf file already exists in sites-available directory
- Check if host name already exists in hosts file
- Errors management
- Add possibility to configure content of .conf file, error logs etc

*/

