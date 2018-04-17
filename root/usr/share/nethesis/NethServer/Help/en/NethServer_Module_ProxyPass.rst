Reverse proxy
=============

This page configures certain paths and virtualhosts under Apache to be served by forwarding the
original web request to another URL.


Create / Edit
-------------

Name
    The path name. It will match URLs like ``http://<hostname>/<path name>/...``.
    Matching URLs are forwarded to the *Target URL*.

Virtualhost
    The fully qualified domain name (or subdomain: ``sub.domain.com``) that you want to be redirected to the *Target URL*

Target URL
    The URL where the original request is forwarded.
    An URL has the form ``<scheme>://<hostname>:<port>/<path>``.
    If the URL has the ``https`` scheme, some restrictions on the certificate
    validity can be enforced.

Require SSL encrypted connection
    URLs under *Name* or *Virtualhost* are accessible only using ``https`` protocol.

SSL/TLS certificate
    Select a certificate that is compatible with the *Virtualhost*.

Access from CIDR networks
    Restrict the access to *Name* or *Virtualhost* from the given list of networks. Elements must
    be separated with a "," (comma).

Description
    A short note.


Delete
------

Removes the selected entry.
