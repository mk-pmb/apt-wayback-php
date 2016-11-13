
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

  * Install on some webserver, e.g. http://server.local/apt-wayback/
  * Make your own `local-config.php` based on `example.local-config.php`
  * In your `/etc/apt/sources.list.d/whatever.list`, change your
    `deb` and/or `deb-src` line URLs to your `r.php` URL + slash + repo name,
    e.g.<br />`deb http://whatever.net/ubuntu trusty main` -><br />
    `deb http://server.local/apt-wayback/r.php/whatever trusty main`
  * Let your package manager scan for updated package lists.
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

  * Does this approach waste U.S. tax money?
    * afaik, no.
      * Memory for new files: covered by archive.org mission.
      * Duplicates due to saving files that haven't changed on the web:
        Won't cost memory. They're professionals, they know deduplication.
        I trust that their file cluster always stores exactly as many copies
        of each file as they want to store.
      * Memory for meta data about when a file changed: CIA needs this info
        anyway, so it's just about which public agency uses the tax money
        to save the meta data.

  * Does this approach violate the Wayback Machine ToS?
    * Yes, so expect to maybe get banned if they catch you.
      Last time I checked, they preferred you use their official API
      but I'm too lazy to implement that right now. PR welcome.





<!--#toc stop="scan" -->


  [nodesource-http-plz]: https://github.com/nodesource/distributions/issues/71


License
-------
<!--#echo json="package.json" key=".license" -->
ISC
<!--/#echo -->
