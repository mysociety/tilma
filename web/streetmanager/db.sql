CREATE TABLE streetmanager (
   created timestamp not null default current_timestamp,
   modified timestamp not null default current_timestamp,
   permit_reference_number TEXT PRIMARY KEY,
   promoter_organisation TEXT,
   location geometry,
   works_location_type TEXT,
   proposed_start_date timestamp with time zone,
   proposed_end_date timestamp with time zone,
   work_category TEXT,
   work_status TEXT,
   traffic_management_type TEXT,
   permit_status TEXT,
   close_footway TEXT
);

CREATE INDEX streetmanager_location_idx ON streetmanager USING gist(location);

CREATE OR REPLACE FUNCTION update_modified_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.modified = now();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_streetmanager_modified
BEFORE UPDATE ON streetmanager
FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
