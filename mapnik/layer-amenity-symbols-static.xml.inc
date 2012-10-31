<!-- Cut down for level_crossing only -->
<Style name="symbols">
    <Rule>
      &maxscale_zoom14;
      &minscale_zoom15;
      <Filter>[railway]='level_crossing'</Filter>
      <PointSymbolizer file="&symbols;/level_crossing.png" />
    </Rule>
    <Rule>
      &maxscale_zoom16;
      <Filter>[railway]='level_crossing'</Filter>
      <PointSymbolizer file="&symbols;/level_crossing2.png" />
    </Rule>
</Style>

<Layer name="amenity-symbols" status="on" srs="&osm2pgsql_projection;">
    <StyleName>symbols</StyleName>
    <Datasource>
      <Parameter name="table">
      (select *
      from &prefix;_point
      where railway='level_crossing'
      ) as symbols</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
<Layer name="amenity-symbols-poly" status="on" srs="&osm2pgsql_projection;">
    <StyleName>symbols</StyleName>
    <Datasource>
      <Parameter name="table">
      (select *
      from &prefix;_polygon
      where railway='level_crossing'
      ) as symbols</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
