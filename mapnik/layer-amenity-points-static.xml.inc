<!-- Cut down to just bus_stop (starting zoom 15) and bus_station -->
<Style name="points">
    <Rule>
      &maxscale_zoom15;
      &minscale_zoom16;
      <Filter>[amenity]='bus_stop' or [highway]='bus_stop'</Filter>
      <PointSymbolizer file="&symbols;/bus_stop_small.png" />
    </Rule>
    <Rule>
      &maxscale_zoom17;
      <Filter>[amenity]='bus_stop' or [highway]='bus_stop'</Filter>
      <PointSymbolizer file="&symbols;/bus_stop.p.12.png" />
    </Rule>
    <Rule>
      &maxscale_zoom15;
      <Filter>[amenity]='bus_station'</Filter>
      <PointSymbolizer file="&symbols;/bus_station.n.16.png" allow_overlap="false"/>
    </Rule>
</Style>

<Layer name="amenity-points" status="on" srs="&osm2pgsql_projection;">
    <StyleName>points</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,amenity,highway
      from &prefix;_point
      where amenity in ('bus_stop', 'bus_station')
         or highway = 'bus_stop'
      ) as points</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
<Layer name="amenity-points-poly" status="on" srs="&osm2pgsql_projection;">
    <StyleName>points</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,amenity,highway
      from &prefix;_polygon
      where amenity in ('bus_stop', 'bus_station')
         or highway = 'bus_stop'
      ) as points</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
