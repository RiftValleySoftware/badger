**1.0.0.2008** *(April 16, 2018)*

- Fixed one more Postgres issue (ID of last insert failing).
- Added Postgress datasets for true Postgres testing.

**1.0.0.2007** *(April 15, 2018)*

- A number of fixes to help the system work with Postgres.

**1.0.0.2006** *(April 13, 2018)*

- Made sure that getting an element's access object is bottlenecked properly.

**1.0.0.2005** *(April 12, 2018)*

- Added an "object cache" to the database class. This allows us to make sure that there is never more than one object ever instantiated for a given record.

**1.0.0.2004** *(April 10, 2018)*

- A number of minor tweaks and bug fixes.
- Reduced the "overlap" in the Haversine formula.

**1.0.0.2003** *(April 6, 2018)*

- Added accessors that check user write access for a number of basic object properties.

**1.0.0.2002** *(April 5, 2018)*

- Removed the unused key storage routines.
- Added a method to allow an instance to be queried for its access object.

**1.0.0.2001** *(April 3, 2018)*

- Added a few powerful "God Mode" auditing functions for determining who has access to what.

**1.0.0.2000** *(April 1, 2018)*

- Improved the map demo.
- Fixed a couple of pagination issues.
- Added more testing.
- Changed the names of the config items to avoid collisions with user apps.
- Beta release.

**1.0.0.1003** *(March 29, 2018)*

- Added pagination options (MySQL only).
- Added the ability to have "LIKE" string searches.

**1.0.0.1002** *(March 28, 2018)*

- Added more tests for the generic search.
- Fixed a bug in the general data class, where the "owner_id" field was not being set.
- Now make sure the map test properly cancels in-progress AJAX calls.

**1.0.0.1001** *(March 27, 2018)*

- Added some tests for multiple values of generic search terms.
- Fixed a few issues with multiple values of generic search terms.

**1.0.0.1000** *(March 27, 2018)*

- Initial Development Tag.