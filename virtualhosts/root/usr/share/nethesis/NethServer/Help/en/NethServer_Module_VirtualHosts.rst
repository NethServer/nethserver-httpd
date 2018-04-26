Virtual hosts
=============

This page configures web sites as Apache named virtual hosts. It configures also
the FTP access to the each web site root folder.

Edit
----

Name
    A simple short name that identifies the virtual host configuration.

Description
    Optional field for a brief description of the web site.

Virtual host names (FQDN)
    The list of Fully Qualified Domain Names that are associated to the virtual
    host. Values must be separated with a "," (comma).

Allow access from trusted networks only
    Only clients from trusted networks can see the web site.

Require HTTP authentication
    The web site is visible only to those who provide the given credentials.

Require SSL encrypted connection
    The web site is accessible only using ``https`` protocol.

SSL/TLS certificate
    Select a certificate that is compatible with the host names
    in the list :guilabel:`Virtual host names (FQDN)`.

Enable FTP access
    Allow uploading the web site contents via FTP, by providing the given
    credentials.

Root directory file listings
    This permits the server to generate a directory listing 
    for a web site if no index.* is specified

Create new
----------

The :guilabel:`Create new` action is similar to :guilabel:`Edit`.  The only
difference is the :guilabel:`Additional actions` section at the bottom of the
form.

Enable/Disable
--------------

When a virtual host is *disabled* it cannot be accessed in any way. By default
a newly created virtual host is in *enabled* state.

Default virtual host
--------------------

This virtual host is always enabled and can't be deleted.
If FTP access is enabled, the user will be chrooted inside ``/var/www/html`` directory.
It may contain data migrated from the old-format shared folder.

.. raw:: html

   {{{INCLUDE NethServer_Module_VirtualHosts_ModifyPlugin_*.html}}}
