**1.0.0.2034** *(TBD)*

- Improved documentation.
- Added method to clear the API Key.

**1.0.0.2033** *(June 8, 2018)*

- Tweaked the config trait to have ANDISOL-specific aspects.
- Added support for the API keys (for REST access).

**1.0.0.2032** *(June 3, 2018)*

- Removed redundant files from spec
- Added support for Doxygen.
- Added support for a collection "visibility vetting."
- Moved the config trait into BADGER, where it is more fitting.
- Improved the documentation of some of the code.

**1.0.0.2031** *(June 2, 2018)*

- There was an issue in the user_can_read() routine, where the write permission was not being taken into account.
- Improved the README and documetation.

**1.0.0.2030** *(May 31, 2018)*

- There were instances when errors were not being returned properly. That has been fixed.
- Improved the comments and documentation for the main access class.
- Added a bit of code to clear the ID of deleted items, so subsequent saves will actually create new items.

**1.0.0.2029** *(May 26, 2018)*

- Fixed the display string in the location base class, so it won't have an empty Long/Lat if there is no long/lat.

**1.0.0.2028** *(May 22, 2018)*

- Fixed one of the tests. It was looking in the wrong place for the state.
- Fixed a bug in the generic search, where tags were not actually being checked for blank.

**1.0.0.2027** *(May 20, 2018)*

- Fixed the generic search, so that it is possible to specify empty tags in the tag search. There were also a few other issues with tha search, which have been addressed.

**1.0.0.2026** *(May 19, 2018)*

- Added the ability to "crawl the stack" in a PDO instance, so we could add auditing logging in the future.
- Added a bit to the login class to clear the CHAMELEON user from the login, if we delete the login alone.

**1.0.0.2025** *(May 18, 2018)*

- Fixed an issue where IDs were not being correctly loaded for security DB items.

**1.0.0.2024** *(May 17, 2018)*

- Sequestered the configuration God Mode password.
- D'OH! I never created a set_password() method! That's been addressed.

**1.0.0.2023** *(May 16, 2018)*

- Added the "1" token, which means only logged-in users, but ALL logged-in users can see.
- Tweaked read, so that having write access to a record also confers read access.

**1.0.0.2022** *(May 15, 2018)*

- Added an extra test to the query, so there are multiple levels of vetting.
- Added a force-read-check to the security token class.
- Added a better way of dealing with hashing the password.

**1.0.0.2021** *(May 13, 2018)*

- Fixed an issue where a warning was emitted when there was no response to a search for IDs.
- Removed the token label stuff. You know what? It's too complicated for this level, and is better left to the higher-level implementation.
- Changed deleted security logins (which become token placeholders) to have a read ID of the token's ID. The write remains -1 (God-only).

**1.0.0.2020** *(May 12, 2018)*

- Added some tweaks to make sure the lang is determined properly.

**1.0.0.2019** *(May 11, 2018)*

- Added extra vetting for security IDs.
- Made sure that only login managers can see logins other than themselves, even when they have the token.
- Added a language assignment to logins.
- Added the basics for a set of strings that can be associated with token IDs.
- When a user logs in, the last_access column of the login record is touched.

**1.0.0.2019** *(May 10, 2018)*

- Added a bit more functionality to help COBRA in its auditing functions.

**1.0.0.2018** *(May 9, 2018)*

- This now supports the special "decomissioning" of login IDs. They never die, they just become security IDs.

**1.0.0.2017** *(May 8, 2018)*

- Added support for vetting the logins and users for COBRA.
- Added support for determining the existence of items in the Data DB for collection cleanup.

**1.0.0.2016** *(May 2, 2018)*

- Did some extra tweaking for COBRA. Tightened security for "God" IDs.

**1.0.0.2015** *(April 30, 2018)*

- Added a check for being able to edit IDs for logins. Users can no longer edit their own ID lists.

**1.0.0.2014** *(April 27, 2018)*

- Improved support for "fuzzy locations."

**1.0.0.2013** *(April 26, 2018)*

- Added support for "fuzzy locations" at a very low level.

**1.0.0.2012** *(April 22, 2018)*

- Added the ability to have multiple extension directories.
- Improved error reporting and handling.

**1.0.0.2011** *(April 21, 2018)*

- The IDs Only generic search was inaccurate if we did a location search, as the Vincenty filter was skipped. That has been fixed.

**1.0.0.2010** *(April 20, 2018)*

- Added support for the "IDs only" search, which is necesary for owners.

**1.0.0.2009** *(April 18, 2018)*

- The payload is now stored as Base64 text. In Postgres, this is a TEXT type.
- Did some work to explicitly sort the items in the generic search by ID. There was no explicit sort before.
- Updated the "primer" SQL.

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