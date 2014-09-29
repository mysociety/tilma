tilma
=====

The code for the mySociety tileserver, serving maps for sites such as
FixMyStreet and Mapumental.

Adding a new tileset
--------------------

1. Add to the RewriteRule in `conf/httpd.conf` to pass requests through to
TileCache.
1. In puppet, edit `modules/tilecache/templates/etc/tilecache.cfg.erb` to add a
new layer, using the existing entries for guidance. They are all currently
either Mapnik or WMS (passing through to MapServer) layers.
1. If you're adding a WMS layer, edit `web/os.map` to add the new layer.
1. If it's a Mapnik layer, put the XML style in the appropriate place (and
potentially hook up to the scripts in `mapnik`).

## mySociety deployment

A server running the tilma.mysociety.org vhost should be in the tilecache
Puppet module. The module makes sure TileCache and MapServer are installed, and
stores TileCache's configuration.

The tilma.mysociety.org vhost provides a web front end to TileCache/MapServer,
and a place to store the cache/ world boundaries/ etc. All tile requests are
made using Google/OSM terminology, ie. .../Z/X/Y.png, and tilecache saves them
in that format too, which makes it easier to use/move elsewhere if need be.

TileCache points at OS OpenData tiles (both StreetView and (unused) Vector Map
District) which is what FixMyStreet is using. This works by tilecache passing
the requests through to MapServer which looks up the right bit of the TIFF file
and returns it. These are generated on demand.

TileCache calls Mapnik to return maps generated from OpenStreetMap data, styled
using our own styles (as used by e.g. the Mapumental maps). The Mapnik XML
includes how to fetch the data (so database host/username) and how to display
it (so all the different styles).

Tiles are generated on demand, with metatiling. Alternatively, you can use
Mapnik's `generate_tiles.py` script to pre-generate, or whatever method you want.

### Generating Mapnik styles

The `mapnik` directory contains the files and scripts needed to generate our
current OSM style XML files using Mapnik.

### Updating map style

If you change a map style and you want it used everywhere, you will need to:

* restart Apache (so TileCache FastCGI picks up new style);
* remove all cached tiles (from
  /data/vhost/tilma.mysociety.org/tilecache/LAYERNAME);
* optionally restart Varnish (otherwise old tiles will be cached for an hour).
