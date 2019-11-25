nethserver-httpd
================

NethServer tries to remain compliant with the upstream configuration for ``httpd``.

In Apache 2.4 every global option is inherit by all virtual hosts,
except for the *Rewrite* directives.

In a clean installation the only defined virtual host is the one on port 443.
When nethserver-httpd-virtualhosts is installed, the package adds a new default virtual host.
This virtual host includes the ``/etc/httpd/conf.d/default-virtualhost.inc`` file
to mimic the upstream behavior.

See also "Certificate management" and "Virtualhosts" section for further information.

Templates
---------

Available templates under ``/etc/e-smith/templates/`` directory:

* ``etc/httpd/conf.d/default-virtualhost.inc``: common configuration
  included inside default virtual host on port 80

* ``etc/httpd/conf.d/nethserver.conf``: default httpd configuration,
  includes ``conf.d/default-virtualhost.inc``. 
  Normally a package shouldn't add a fragment to it.

* ``etc/httpd/conf.d/welcome.conf``: configures the "Welcome" page if
  there is no default index page for the root URL

* ``httpd/vhost``: everything inside will be included in ``default-virtualhost.inc``.
  *DO NOT USE*, it's here only for backward compatibility


Web applications
----------------

Every package which needs to change the Apache configuration:

* should add a static (or template-generated) file inside ``/etc/httpd/conf.d/`` directory
* must include all Rewrite options inside a fragment for ``default-virtualhost.inc``

Example
^^^^^^^

Package named ``nethserver-mywebapp`` which requires a rewrite rule from http to https.
The web application must be accessible under ``http://<server_address>/mywebapp``.

Static file ``/etc/httpd/conf.d/mywebapp.conf``:

::
 
 Alias /mywebapp /usr/share/mywebapp
 <Directory "/usr/share/mywebapp">
     AllowOverride None
     Options None
     Require all granted
 </Directory>

Rewrite rule fragment ``/etc/e-smith/templates/etc/httpd/conf.d/default-virtualhost.inc/30mywebapp``:

::

 #
 # 30mywebapp
 #
 
 RewriteEngine on
 RewriteRule ^/mywebapp(/.*)?$ https://%\{SERVER_NAME\}/mywebapp$1 [L,R=301]

Inside the ``createlinks`` file, the configuration should be applied during the update event:

::

  my $event = "nethserver-mywebapp-update";
  event_templates($event, 
                     '/etc/httpd/conf.d/default-virtualhost.inc',
  );

  event_services($event, 
                     'httpd' => 'reload',
  );



configuration database
----------------------

::

 httpd=service
    SSLCipherSuite=DEFAULT:!EXP:!SSLv2:!DES:!IDEA:!SEED:+3DES
    TCPPorts=80,443
    access=green,red
    status=enabled


vhost database
--------------

A new ``vhost`` database is defined by this module. It contains records of type
``vhost``, similar to: ::

    vh1=vhost
        Access=private
        Description=description_text
        ForceSslStatus=enabled
        FtpPassword=ftp_password_value
        FtpStatus=enabled
        PasswordStatus=enabled
        PasswordValue=http_password_value
        ServerNames=www.nethserver.org,www.example.com
        SslCertificate=/etc/pki/tls/certs/NSRV.crt
        status=enabled
        PhpRhVersion=default
        PhpCustomSettings=disabled
        AllowUrlfOpen=enabled
        MemoryLimit=128
        UploadMaxFilesize=4
        PostMaxSize=8
        MaxExecutionTime=0
        MaxFileUploads=20

The database contains a special ``default`` record which represents the default
virtual host: ::

  default=vhost
    Description=Default virtual host
    FtpPassword=
    FtpStatus=enabled

This virtual host is always enabled and can't be deleted. If FTP access is
enabled, the user will be chrooted inside ``/var/www/html`` directory.

rh PHP software collection
--------------------------
With the new cockpit server-manager the PHP version can be modified 
inside the virtualhost panel. It installs a pool of PHP-FPM, this new php 
version is only relevant to the current apache virtualhost. The prop 
``PhpRhVersion`` is used to set the PHP version (``default`` is the PHP 5.4,
 ``php71``, ``php72``).

The documentation page of the project are:
- https://www.softwarecollections.org/en/scls/rhscl/rh-php71/
- https://www.softwarecollections.org/en/scls/rhscl/rh-php72/

If the prop ``PhpCustomSettings`` is set to ``disabled`` the PHP setting values 
are inherited from the default values of PHP (from esmith configuration database), if 
``enabled`` each vhost gets its own PHP values from its vhost props.

Events
------

::

 signal-event nethserver-httpd-update
 signal-event nethserver-httpd-save


NethServer 6 upgrade
--------------------

Shared folders from NethServer 6 with property ``HttpStatus`` set to ``enabled`` can
be migrated to virtual hosts using the ``vhost-migrate-ibay`` event: ::

    signal-event vhost-migrate-ibay <ibay-name>

If the original ibay was availble to any virtual hosts (`HttpVirtualHost` = ``__ANY__``),
the ibay will be migrated inside the ``default`` virtual host.
Otherwise a new virtual host record will be created.

The migration process is also available from the web interface.

UI plugins
----------

The Modify action can be extended to display additional tabs, by adding a 
controller and the respective template under ``ModifyPlugin/`` directories.

See the `Samba User plugin`_ on NethServer 6.x as an example

.. _`Samba User plugin`: https://github.com/NethServer/nethserver-samba/blob/9012fbcd0cb3db60d8fb0ddfcd3db9e39a01956c/root/usr/share/nethesis/NethServer/Module/User/Plugin/Samba.php


Welcome page
------------

If there is no index page for the root URL, a default welcome page is shown accessing the HTTP and HTTPS ports of the server.
If Cockpit UI is installed, the welcome page displays a customizable app launcher; users can choose which apps to show in the launcher by accessing Cockpit Applications page and clicking the ``Add to home page`` command in the kebab menu of any app.

It is possible to display an alternative welcome page that replaces the app launcher:

- create a subdirectory inside ``/usr/share/httpd/noindex/``, e.g. ``mywebsite``
- put a custom index page (e.g. index.html) inside ``/usr/share/httpd/noindex/mywebsite/``
- create a subdirectory ``res`` inside ``/usr/share/httpd/noindex/mywebsite/``
- put all the assets used by your page (images, scripts, ...) inside ``/usr/share/httpd/noindex/mywebsite/res/``
- the assets can be accessed from the index page using the prefix ``/res_mywebsite/``, e.g. ``<link rel="stylesheet" type="text/css" href="/res_mywebsite/style.css">``
- make sure the primary page is called ``index.html``
- execute the following commands: ::

    config setprop httpd HomePage mywebsite
    signal-event nethserver-httpd-update

- your custom welcome page is now accessible the the HTTP and HTTPS ports

If you want to switch back to the default app launcher page execute:

::

  config setprop httpd HomePage nethserver
  signal-event nethserver-httpd-update
