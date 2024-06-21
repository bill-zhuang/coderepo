README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "D:/PHP_Project/coderepo/public"
   ServerName xxxxx.local  # set in your hosts file

   # This should be omitted in the production environment
   # SetEnv APPLICATION_ENV development

   <Directory "D:/PHP_Project/coderepo/public">
       AllowOverride none
       Require all granted

       RewriteEngine On

       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteRule . index.php
   </Directory>

</VirtualHost>
