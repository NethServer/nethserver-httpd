Reverse proxy
=============

This page configures certain paths under Apache to be served by forwarding the
original web request to another URL.


Create / Edit
-------------

Name
    The path name. It will match URLs like ``http://<hostname>/<path name>/...``.
    Matching URLs are forwarded to the *Target URL*.

Target URL
    The URL where the original request is forwarded.
    An URL has the form ``<scheme>://<hostname>:<port>/<path>``.
    If the URL has the ``https`` scheme, some restrictions on the certificate
    validity can be enforced.

Require SSL encrypted connection
    URLs under *Name* are accessible only using ``https`` protocol.

Access from CIDR networks
    Restrict the access to *Name* from the given list of networks. Elements must
    be separated with a "," (comma).

Description
    A short note.


Delete
------

Removes the selected entry.