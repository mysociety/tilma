<!-- Because we want to style labels identically on the base map and the names overlay, it makes
        sense to only define the styles once. This file is included in mapumental-map-print.xml and mapumental-names-print.xml -->
<Style name="capital">
    <Rule>
        &minscale_zoom5;
        &maxscale_zoom5;
        <TextSymbolizer size="18" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        &minscale_zoom8;
        &maxscale_zoom6;
        <TextSymbolizer size="22" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        &minscale_zoom10;
        &maxscale_zoom9;
        <TextSymbolizer size="22" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        &minscale_zoom11;
        &maxscale_zoom11;
        <TextSymbolizer size="30" fill="rgb(0,0,0)" fontset-name="bold-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        &minscale_zoom12;
        &maxscale_zoom12;
        <TextSymbolizer size="35" fill="rgb(0,0,0)" fontset-name="bold-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        &minscale_zoom14;
        &maxscale_zoom13;
        <TextSymbolizer size="40" fill="rgb(0,0,0)" fontset-name="bold-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="city">
    <Rule>
        <Filter>(([place]='city') or ([place]='metropolis'))</Filter>
        &minscale_zoom8;
        &maxscale_zoom6;
        <TextSymbolizer size="16" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(([place]='city') or ([place]='metropolis'))</Filter>
        &minscale_zoom10;
        &maxscale_zoom9;
        <TextSymbolizer size="25" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(([place]='city') or ([place]='metropolis'))</Filter>
        &minscale_zoom11;
        &maxscale_zoom11;
        <TextSymbolizer size="28" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(([place]='city') or ([place]='metropolis'))</Filter>
        &minscale_zoom12;
        &maxscale_zoom12;
        <TextSymbolizer size="33" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="3">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(([place]='city') or ([place]='metropolis'))</Filter>
        &minscale_zoom14;
        &maxscale_zoom13;
        <TextSymbolizer size="38" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="3">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="country">
    <Rule>
        <Filter>([place]='country')</Filter>
        &minscale_zoom3;
        &maxscale_zoom2;
        <TextSymbolizer size="16" fill="rgb(157,108,157)" fontset-name="book-fonts" halo-radius="2" wrap-width="20">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>([place]='country')</Filter>
        &minscale_zoom6;
        &maxscale_zoom4;
        <TextSymbolizer size="20" fill="rgb(157,108,157)" fontset-name="book-fonts" halo-radius="2" wrap-width="20">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="hamlet">
    <Rule>
        <Filter>(((([place]='hamlet') or ([place]='locality')) or ([place]='isolated_dwelling')) or ([place]='farm'))</Filter>
        &minscale_zoom15;
        &maxscale_zoom14;
        <TextSymbolizer size="16" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(((([place]='hamlet') or ([place]='locality')) or ([place]='isolated_dwelling')) or ([place]='farm'))</Filter>
        &maxscale_zoom16;
        <TextSymbolizer size="22" fill="rgb(119,119,119)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="suburb">
    <Rule>
        <Filter>([place]='suburb')</Filter>
        &minscale_zoom13;
        &maxscale_zoom12;
        <TextSymbolizer size="20" fill="rgb(40,40,40)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>([place]='suburb')</Filter>
        &maxscale_zoom14;
        <TextSymbolizer size="26" fill="rgb(40,40,40)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="town">
    <Rule>
        <Filter>(([place]='town') or ([place]='large_town'))</Filter>
        &minscale_zoom8;
        &maxscale_zoom8;
        <TextSymbolizer size="10" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="1.5" wrap-width="30" minimum-distance="10">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>((([place]='town') or ([place]='large_town')) or ([place]='small_town'))</Filter>
        &minscale_zoom10;
        &maxscale_zoom9;
        <TextSymbolizer size="20" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2" wrap-width="20">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>((([place]='town') or ([place]='large_town')) or ([place]='small_town'))</Filter>
        &minscale_zoom13;
        &maxscale_zoom11;
        <TextSymbolizer size="25" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2" wrap-width="20" minimum-distance="10">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>((([place]='town') or ([place]='large_town')) or ([place]='small_town'))</Filter>
        &maxscale_zoom14;
        <TextSymbolizer size="28" fill="rgb(119,119,119)" fontset-name="book-fonts" halo-radius="2" wrap-width="20">[name]</TextSymbolizer>
    </Rule>
</Style>
<Style name="village">
    <Rule>
        <Filter>(([place]='village') or ([place]='large_village'))</Filter>
        &minscale_zoom14;
        &maxscale_zoom11;
        <TextSymbolizer size="18" fill="rgb(0,0,0)" fontset-name="book-fonts" halo-radius="2" minimum-distance="9">[name]</TextSymbolizer>
    </Rule>
    <Rule>
        <Filter>(([place]='village') or ([place]='large_village'))</Filter>
        &maxscale_zoom15;
        <TextSymbolizer size="24" fill="rgb(119,119,119)" fontset-name="book-fonts" halo-radius="2">[name]</TextSymbolizer>
    </Rule>
</Style>

<Layer name="placenames-large" status="on" srs="&osm2pgsql_projection;">
    <StyleName>country</StyleName>
    <!-- <StyleName>state</StyleName> -->
    <Datasource>
      &datasource-settings;
        <Parameter name="table">
  (select way,place,name,ref
   from planet_osm_point
   where place in ('country','state')
  ) as placenames</Parameter>
    </Datasource>
</Layer>
<Layer name="placenames-capital" status="on" srs="&osm2pgsql_projection;">
    <StyleName>capital</StyleName>
    <Datasource>
      &datasource-settings;
        <Parameter name="table">
  (select way,place,name,ref
   from planet_osm_point
   where place in ('city','metropolis','town') and capital='yes'
  ) as placenames</Parameter>
    </Datasource>
</Layer>
<Layer name="placenames-medium" status="on" srs="&osm2pgsql_projection;">
    <StyleName>city</StyleName>
    <StyleName>town</StyleName>
    <Datasource>
      &datasource-settings;
        <Parameter name="table">
  (select way,place,name
  from planet_osm_point
  where place in ('city','metropolis','town','large_town','small_town')
    and (capital is null or capital != 'yes')
  ) as placenames</Parameter>
    </Datasource>
</Layer>
<Layer name="placenames-small" status="on" srs="&osm2pgsql_projection;">
    <StyleName>suburb</StyleName>
    <StyleName>village</StyleName>
    <StyleName>hamlet</StyleName>
    <Datasource>
      &datasource-settings;
        <Parameter name="table">
  (select way,place,name
  from planet_osm_point
  where place in ('suburb','village','large_village','hamlet','locality','isolated_dwelling','farm')
  ) as placenames</Parameter>
    </Datasource>
</Layer>
