DROP TABLE IF EXISTS co_data_nodes;
DROP SEQUENCE IF EXISTS element_id_seq;
CREATE SEQUENCE element_id_seq;
CREATE TABLE co_data_nodes (
  id BIGINT NOT NULL DEFAULT nextval('element_id_seq'),
  access_class VARCHAR(255) NOT NULL,
  last_access TIMESTAMP NOT NULL,
  read_security_id BIGINT DEFAULT NULL,
  write_security_id BIGINT DEFAULT NULL,
  object_name VARCHAR(255) DEFAULT NULL,
  access_class_context TEXT DEFAULT NULL,
  owner BIGINT DEFAULT NULL,
  longitude DOUBLE PRECISION DEFAULT NULL,
  latitude DOUBLE PRECISION DEFAULT NULL,
  tag0 VARCHAR(255) DEFAULT NULL,
  tag1 VARCHAR(255) DEFAULT NULL,
  tag2 VARCHAR(255) DEFAULT NULL,
  tag3 VARCHAR(255) DEFAULT NULL,
  tag4 VARCHAR(255) DEFAULT NULL,
  tag5 VARCHAR(255) DEFAULT NULL,
  tag6 VARCHAR(255) DEFAULT NULL,
  tag7 VARCHAR(255) DEFAULT NULL,
  tag8 VARCHAR(255) DEFAULT NULL,
  tag9 VARCHAR(255) DEFAULT NULL,
  payload BYTEA DEFAULT NULL
);

INSERT INTO co_data_nodes (access_class, last_access, read_security_id, write_security_id, object_name, access_class_context, owner, longitude, latitude, tag0, tag1, tag2, tag3, tag4, tag5, tag6, tag7, tag8, tag9, payload) VALUES
('CO_Main_DB_Record', '1970-01-01 00:00:00', -1, -1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', NULL, NULL, 'Las Vegas', NULL, NULL, 36.1699, -115.1398, 'US', 'NV', 'nevada', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 3, 3, 'Area 51', NULL, NULL, 37.2343, -115.8067, 'alien', 'spooks', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 4, 4, 'NA World Services', NULL, NULL, 34.23592, -118.563659, 'NA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 5, 5, 'Dubai', NULL, NULL, 25.2048, 55.2708, 'Middle East', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 5, 5, 'Anchorage', NULL, NULL, 61.2181, -149.9003, 'US', 'AL', 'alaska', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 6, 6, 'Adelaide', NULL, NULL, -34.9285, 138.6007, 'AU', 'SA', 'south australia', 'australia', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 0, 3, 'San Jose, Costa Rica', NULL, NULL, 9.9281, -84.0907, 'CR', 'Costa Rica', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 0, 0, 'San Jose, California', NULL, NULL, 37.3382, -121.8863, 'US', 'CA', 'california', 'northern california', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('CO_LL_Location', '1970-01-01 00:00:00', 6, 6, 'Gettysburg, Pennsylvania', NULL, NULL, 39.8309, -77.2311, 'US', 'PA', 'pennsylvania', 'Lincoln', NULL, NULL, NULL, NULL, NULL, NULL, NULL);
