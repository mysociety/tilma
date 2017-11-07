<!-- Altered fill colour -->
<Style name="water_areas">
    <Rule>
      <Filter>[natural] = 'glacier'</Filter>
      &maxscale_zoom6;
      &minscale_zoom7;
      <LineSymbolizer stroke="#9cf" stroke-width="1.5" stroke-dasharray="4,2"/>
      <PolygonPatternSymbolizer file="&symbols;/glacier.png" />
    </Rule>
    <Rule>
      <Filter>[natural] = 'glacier'</Filter>
      &maxscale_zoom8;
      <LineSymbolizer stroke="#9cf" stroke-width="1.5" stroke-dasharray="4,2"/>
      <PolygonPatternSymbolizer file="&symbols;/glacier2.png" />
    </Rule>
    <Rule>
      <Filter>[waterway] = 'dock' or [waterway] = 'mill_pond' or [waterway]='canal'</Filter>
      &maxscale_zoom9;
      <PolygonSymbolizer fill="#98bcda" gamma=".75"/>
    </Rule>
    <Rule>
      <Filter>[landuse] = 'basin'</Filter>
      &maxscale_zoom7;
      <PolygonSymbolizer fill="#98bcda" gamma=".75"/>
    </Rule>
    <Rule>
      <Filter>[natural] = 'lake' or [natural] = 'water' or [landuse] = 'reservoir' or [waterway] = 'riverbank' or [landuse] = 'water' or [natural]='bay'</Filter>
      &maxscale_zoom6;
      <PolygonSymbolizer fill="#98bcda" gamma=".75"/>
    </Rule>
    <Rule>
      <Filter>[natural] = 'mud'</Filter>
      &maxscale_zoom13;
      <PolygonPatternSymbolizer file="&symbols;/mud.png" />
    </Rule>
    <Rule>
      <Filter>[natural] = 'land'</Filter>
      &maxscale_zoom10;
      <PolygonSymbolizer fill="#f2efe9"/>
    </Rule>
</Style>
<Style name="water-areas-overlay">
    <Rule>
      &maxscale_zoom13;
      <Filter>[natural] = 'marsh' or [natural] = 'wetland'</Filter>
      <PolygonPatternSymbolizer file="&symbols;/marsh.png" />
    </Rule>
</Style>
<Style name="glaciers-text">
    <Rule> 
      <Filter>[way_area] &gt;= 10000000</Filter>
      &maxscale_zoom10;
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#99f" halo-radius="1" wrap-width="20"/>
    </Rule>
    <Rule>
      <Filter>[way_area] &gt;= 5000000 and [way_area] &lt; 10000000</Filter>
      &maxscale_zoom11;
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#99f" halo-radius="1" wrap-width="20"/>
    </Rule>
    <Rule>
      <Filter>[way_area] &lt; 5000000</Filter>
      &maxscale_zoom12;
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#99f" halo-radius="1" wrap-width="20"/>
    </Rule>
</Style>

<Style name="water-lines-casing">
    <Rule>
      <Filter>[waterway]='stream' or [waterway]='ditch' or [waterway]='drain'</Filter>
      &maxscale_zoom13;
      &minscale_zoom14;
      <LineSymbolizer stroke="white" stroke-width="1.5"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='stream'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="white" stroke-width="2.5"/>
    </Rule>
</Style>

<Style name="water-lines-low-zoom">
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom8;
      &minscale_zoom8;
      <LineSymbolizer stroke="#98bcda" stroke-width="0.7"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom9;
      &minscale_zoom9;
      <LineSymbolizer stroke="#98bcda" stroke-width="1.2"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom10;
      &minscale_zoom11;
      <LineSymbolizer stroke="#98bcda" stroke-width="1.6"/>
    </Rule>
</Style>

<Style name="water_lines">
    <Rule>
      <Filter>[waterway]='weir'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="#aaa" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='wadi'</Filter>
      &maxscale_zoom13;
      &minscale_zoom15;
      <LineSymbolizer stroke="#98bcda" stroke-width="1" stroke-linejoin="round" stroke-linecap="round" stroke-dasharray="4,4"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='wadi'</Filter>
      &maxscale_zoom16;
      <LineSymbolizer stroke="#98bcda" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke-dasharray="4,4"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom12;
      &minscale_zoom12;
      <LineSymbolizer stroke="#98bcda" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom13;
      &minscale_zoom13;
      <LineSymbolizer stroke="#98bcda" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" placement="line" spacing="400" />
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom14;
      &minscale_zoom14;
      <LineSymbolizer stroke="#98bcda" stroke-width="5" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" spacing="400" />
    </Rule><Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom15;
      &minscale_zoom16;
      <LineSymbolizer stroke="#98bcda" stroke-width="6" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" spacing="400" />
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom17;
      &minscale_zoom17;
      <LineSymbolizer stroke="#98bcda" stroke-width="10" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" spacing="400" />
    </Rule>
    <Rule>
      <Filter>[waterway]='river'</Filter>
      &maxscale_zoom18;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="12" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" spacing="400" />
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and not [disused]='yes'</Filter>
      &maxscale_zoom12;
      &minscale_zoom12;
      <LineSymbolizer stroke="#98bcda" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and not [disused]='yes'</Filter>
      &maxscale_zoom13;
      &minscale_zoom13;
      <LineSymbolizer stroke="#98bcda" stroke-width="4" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="8" fill="#6699cc" halo-radius="1" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='stream' or [waterway]='ditch' or [waterway]='drain'</Filter>
      &maxscale_zoom13;
      &minscale_zoom14;
      <LineSymbolizer stroke="#98bcda" stroke-width="1"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom14;
      &minscale_zoom14;
      <LineSymbolizer stroke="#98bcda" stroke-width="6" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="4"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" minimum-distance="200"/>
    </Rule><Rule>
      <Filter>[waterway]='river' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom15;
      &minscale_zoom16;
      <LineSymbolizer stroke="#98bcda" stroke-width="7" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="4"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" minimum-distance="200"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom17;
      &minscale_zoom17;
      <LineSymbolizer stroke="#98bcda" stroke-width="11" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="7"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" minimum-distance="200"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='river' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="13" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="9"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#6699cc" halo-radius="1" placement="line" minimum-distance="200"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom14;
      &minscale_zoom16;
       <LineSymbolizer stroke="#98bcda" stroke-width="7" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="3"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and not ([tunnel] = 'yes' or [disused]='yes')</Filter>
      &maxscale_zoom14;
      &minscale_zoom16;
      <LineSymbolizer stroke="#98bcda" stroke-width="7" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and [tunnel] = 'yes'</Filter>
      &maxscale_zoom17;
      &minscale_zoom18;
       <LineSymbolizer stroke="#98bcda" stroke-width="11" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="white" stroke-width="7"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and not ([tunnel] = 'yes' or [disused]='yes' or [lock]='yes')</Filter>
      &maxscale_zoom17;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="11" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='canal' and not ([tunnel] = 'yes' or [disused]='yes') and [lock]='yes'</Filter>
      &maxscale_zoom17;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="7" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#6699cc" halo-radius="1" wrap-width="20"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='derelict_canal' or ([waterway]='canal' and [disused]='yes')</Filter>
      &maxscale_zoom12;
      &minscale_zoom12;
      <LineSymbolizer stroke="#b5e4d0" stroke-width="1.5" stroke-dasharray="4,4" stroke-opacity="0.5" stroke-linejoin="round" stroke-linecap="round"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='derelict_canal' or ([waterway]='canal' and [disused]='yes')</Filter>
      &maxscale_zoom13;
      &minscale_zoom13;
      <LineSymbolizer stroke="#b5e4d0" stroke-width="2.5" stroke-dasharray="4,6" stroke-opacity="0.5" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="9" fill="#80d1ae" halo-radius="1" spacing="600" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='derelict_canal' or ([waterway]='canal' and [disused]='yes')</Filter>
      &maxscale_zoom14;
      &minscale_zoom18;
      <LineSymbolizer stroke="#b5e4d0" stroke-width="4.5" stroke-dasharray="4,8" stroke-opacity="0.5" stroke-linejoin="round" stroke-linecap="round"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="10" fill="#80d1ae" halo-radius="1" spacing="600" placement="line"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='stream' and [tunnel]='yes'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="2.4" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="#f3f7f7" stroke-width="1.2"/>
    </Rule>
    <Rule>
      <Filter>[waterway]='stream' and not [tunnel]='yes'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="2"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="8" fill="#6699cc" halo-radius="1" spacing="600" placement="line"/>
    </Rule>
    <Rule>
      <Filter>([waterway]='drain' or [waterway]='ditch') and [tunnel]='yes'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="2" stroke-dasharray="4,2"/>
      <LineSymbolizer stroke="#f3f7f7" stroke-width="1"/>
    </Rule>
    <Rule>
      <Filter>([waterway]='drain' or [waterway]='ditch') and not [tunnel]='yes'</Filter>
      &maxscale_zoom15;
      &minscale_zoom18;
      <LineSymbolizer stroke="#98bcda" stroke-width="1"/>
      <TextSymbolizer name="[name]" fontset-name="book-fonts" size="8" fill="#6699cc" halo-radius="1" spacing="600" placement="line"/>
    </Rule>
</Style>

<Layer name="water-lines-casing" status="on" srs="&osm2pgsql_projection;">
    <StyleName>water-lines-casing</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,waterway
      from &prefix;_line
      where waterway in ('stream','drain','ditch')
        and (tunnel is null or tunnel != 'yes')
      ) as water_lines</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
<Layer name="water_areas" status="on" srs="&osm2pgsql_projection;">
    <StyleName>water_areas</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,"natural",waterway,landuse,name
      from &prefix;_polygon
      where (waterway in ('dock','mill_pond','riverbank','canal')
         or landuse in ('reservoir','water','basin')
         or "natural" in ('lake','water','land','glacier','mud','bay'))
         and building is null
      order by z_order,way_area desc
      ) as water_areas</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
<!-- Make sure overlay styles are always rendered on top of solid areas. -->
<Layer name="water-areas-overlay" status="on" srs="&osm2pgsql_projection;">
    <StyleName>water-areas-overlay</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,"natural"
      from &prefix;_polygon
      where "natural" in ('marsh','wetland') and building is null
      order by z_order,way_area desc
      ) as water_areas</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
<Layer name="glaciers-text" status="on" srs="&osm2pgsql_projection;">
    <StyleName>glaciers-text</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,name,way_area
      from &prefix;_polygon
      where "natural"='glacier' and building is null
      order by way_area desc
      ) as glaciers</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>

<Layer name="water-lines-low-zoom" status="on" srs="&osm2pgsql_projection;">
    <StyleName>water-lines-low-zoom</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,waterway
      from &prefix;_line
      where waterway='river'
      ) as water_lines</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>

<Layer name="water_lines" status="on" srs="&osm2pgsql_projection;">
    <StyleName>water_lines</StyleName>
    <Datasource>
      <Parameter name="table">
      (select way,waterway,disused,lock,name,
      case when tunnel in ('yes','true','1') then 'yes'::text else tunnel end as tunnel
      from &prefix;_line
      where waterway in ('weir','river','canal','derelict_canal','stream','drain','ditch','wadi')
        and (bridge is null or bridge not in ('yes','true','1','aqueduct'))
      order by z_order
      ) as water_lines</Parameter>
      &datasource-settings;
    </Datasource>
</Layer>
