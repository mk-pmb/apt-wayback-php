
<!--#echo json="package.json" key="name" underline="=" -->
apt-wayback-php
===============
<!--/#echo -->

<!--#echo json="package.json" key="description" -->
Adapter for connecting HTTP-only caching proxies to HTTPS-only Debian package
repos. Offloads the actual decryption to the Wayback Machine.
<!--/#echo -->

You'll need a Webserver (e.g. Apache), PHP5 and curl.
Other PHP versions might work as well.


How it works
------------

  * :TODO: apt config
  * The PHP script checks the date of the Wayback Machine's latest copy
    of that file.
  * If file was archived less than `max-age` ago,
    the PHP script will redirect you to the archived copy.
  * If the file wasn't archived yet, or too long ago,
    the PHP script will redirect you to a link that tells the Wayback
    Machine to save the current online version and then redirect you
    to that fresh new copy.


Q&A
---

  * Stripping the SSL makes your downloads insecure!
    * Indeed, this project assumes that you can trust…
      * … the repo's GnuPG signatures for data integrity.
      * … the Wayback Machine (or whoever can impersonate it on your network)
        for currentness of data.
    * For discussion about other attack vectors, please refer to
      [this nodesource issue][nodesource-http-plz].

  * Why not use Apache's `mod_proxy`?
    * Last time I tried, I was unable to convince `mod_proxy`  to speak SSL
      with HTTPS origin servers. It did speak SSL to my browser just fine,
      and would relay files from HTTP origin servers just as expected.
      However, as soon as I changed the origin URL to `https://`, all I got
      were error messages about how it was compiled without openssl support.

  * Why not fetch the actual files with curl?
    * I'd like to not have to care about some caveats:
      * How to best handle multiple concurrent requests for the same URL.
        Obviously you wouldn't want to have multiple instances of curl
        download the same file at the same time.
      * What should happen when the request that invoked curl is aborted?
        Is that what actually does happen?







<!--#toc stop="scan" -->


  [nodesource-http-plz]: https://github.com/nodesource/distributions/issues/71


License
-------
<!--#echo json="package.json" key=".license" -->
ISC
<!--/#echo -->
