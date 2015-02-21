.. --initial-header-level=3 

Web settings 
^^^^^^^^^^^^

Modify the Shared Folder web settings.

Force the SSL access 
    Allows you to force the SSL encrypted connection. The Shared Folder
    will be reachable only by https with your web browser.

Allow the .htaccess policy
    Allows specific settings by a .htaccess files directly in the web folder.
    The .htaccess files can override directives from the parent httpd.conf.

Allow PHP access to remote files
    When the allow_url_fopen directive is enabled, you can write php scripts
    that open remote files as if they are local files.

Php memory limit
    This sets the maximum amount of memory in bytes that a script is allowed 
    to allocate, memory_limit also affects file uploading. Generally speaking,
    memory_limit should be larger than post_max_size.

Maximum upload file size
    The maximum size of an uploaded file.

Maximum size of post data allowed
    Sets max size of post data allowed. This setting also affects file upload.
    To upload large files, this value must be larger than upload_max_filesize.
    If memory limit is enabled by your configure script, memory_limit also 
    affects file uploading. Generally speaking, memory_limit should be larger 
    than post_max_size.

Maximum execution time of PHP scripts
    This sets the maximum time in seconds a script is allowed to run before 
    it is terminated by the parser. This helps prevent poorly written scripts 
    from tying up the server.

Maximum number of uploaded files
    The maximum number of files allowed to be uploaded simultaneously.
