<!-- Cut down for railway stations only -->
<Style name="buildings-lz">
    <Rule>
      <Filter>[railway] = 'station' or [building] = 'station'</Filter>
      &maxscale_zoom10;
     <PolygonSymbolizer fill="#d4aaaa"/>
    </Rule>
</Style>

<Layer name="buildings-lz" status="on" srs="&osm2pgsql_projection;">
    <StyleName>buildings-lz</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,building,railway,amenity from &prefix;_polygon
       where railway='station' or building='station'
       order by z_order,way_area desc) as buildings
      </Parameter>
      &datasource-settings;
    </Datasource>
</Layer>

