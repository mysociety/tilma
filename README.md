tilma
=====

The code for the mySociety tileserver, serving maps and asset layer for sites
such as FixMyStreet.

Adding a new layer
------------------

1. Put the geographic data under layers/ generally organised by organisation.
1. Add a new layer to the relevant .map file in the fixmystreet.com repository
(which is deployed alongside tilma).

Adding a new raster tileset
---------------------------

1. Add to the RewriteRule in `conf/httpd.conf` to pass requests through to
MapCache.
1. In `conf/mapcache.xml`, add a new layer, using the existing entries for
guidance. They are all currently WMS (passing through to MapServer) layers.

## mySociety deployment

A server running the tilma.mysociety.org vhost should be in the tilma Puppet
module. The module makes sure MapCache and MapServer are installed.

The tilma.mysociety.org vhost provides a web front end to MapCache/MapServer,
and a place to store the cache/ world boundaries/ etc. All tile requests are
made using Google/OSM terminology, ie. .../Z/X/Y.png.

MapCache points at OS OpenData tiles (Open Map Local) which is what FixMyStreet
is using. This works by mapcache passing the requests through to MapServer
which looks up the right bit of the TIFF file and returns it. These are
generated on demand.
