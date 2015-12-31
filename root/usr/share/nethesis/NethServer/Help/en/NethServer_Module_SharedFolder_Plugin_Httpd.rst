.. --initial-header-level=3 

Web access 
^^^^^^^^^^

Enables access to the shared folder from the web.

Virtual Host 
    Allows you to choose which host name is available on the shared
    folder. The list comes from the card "Server Alias" in the
    module "DNS and DHCP."

Web address (URL)
    Defines the web address on which the resource is available. 

Allow access only from trusted networks
    If only enabled, restricts access to the resource only to trusted
    networks.

Require a password 
    The access to the resource from the web requires no
    authentication. Enable this option to require a password: specify
    it in the field below.

Require a user or group
    The access to the resource from the web requires no
    authentication. Enable this option to require a User or Group 
    granted by the ACL Tab, at least with read permissions.

Enable WebDav
    WebDAV allows users/groups with write permissions to create, change 
    and move documents on an Ibay (a read-only access is possible). You 
    must use a webdav client to connect to the share.

Require SSL encrypted connection
    Web clients will be forced to connect to the shared folder using
    the HTTPS protocol.

Allow .htaccess and write permissions overrides
    A file named ``.htaccess`` on the shared folder root is considered a web
    server configuration file. See `Apache .htaccess howto`_.
    A file named ``.htwritable`` on the shared folder root enables write access
    on specific subdirectories. The expected contents of that file are a list
    of subdirectories (one fore each line) where the web server is granted
    write permission.

.. _Apache .htaccess howto: http://httpd.apache.org/docs/2.2/howto/htaccess.html
