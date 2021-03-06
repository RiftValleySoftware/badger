**1.1.2.3000** *(May 27, 2021)*

- Added support for a "perpetual timeout."

**1.1.0.3000** *(March 1, 2021)*

- Added support for personal tokens.
- Improved some basic security issues.

**1.0.7.3000** *(December 15, 2020)*

- Added a fast lookup for visible logins.

**1.0.6.3000** *(December 12, 2020)*

- Fixed a bug in the new fast user query.

**1.0.5.3000** *(December 9, 2020)*

- Added support for a fast query for visible users.

**1.0.4.3000** *(November 5, 2020)*

- Added support for getting user IDs for access to tokens.

**1.0.3.3000** *(October 29, 2020)*

- Added Support for counting token access.

**1.0.1.3000** *(September 12, 2020)*

- Fixed a possible security issue with the God Mode login (bad touch).

**1.0.0.3001** *(October 31, 2018)*

- Switched the license to MIT.

**1.0.0.3000** *(September 3, 2018)*

- Improved the password hashing algorithm.
- Simplified the test for being able to see "fuzzed" long/lat.
- Improved the access check to immediately accept 0 or 1 access tokens.
- Fixed a bug in the token test.
- Tweaked the PDO handler to properly encapsulate transactions, and rollback, if an error is encountered.
- Made sure that the accurate (raw) locations for "fuzzed" records were used in radius searches, if the logged-in user has permission to see them.
- Added the $enable_bulk_upload variable to the sample config.
- Added the DB backup fetch routine to the base DB class.
- Fixed an issue with the "can see through the fuzz" setter, where God Mode wasn't being given the respect it deserved.
- Removed all punctuation from the API key. It probably won't make any difference, but it doesn't hurt.
- Simplified and solidified the check for ability to edit security tokens.
- Reduced the size of the API keys by 50% (they made the GET requests too long).
- Fixed a couple of possible divide-by-0 bugs.
- Added an abstraction for the serialization routines.
- Fixed a bug that prevented new logins from being created.

**1.0.0.2036** *(July 22, 2018)*

- Added a new way to access security tokens safely.
- Updated the README.
- Added a quick "manager check" to login objects.
- Added a quick "God check" to login objects.
- Added the ability to get every single token, when in "God Mode."
- Made sure that the login_id field for Postgres Security is "unique."
- Added a function to test tokens.
- Added a method to check the access class of rows quickly.
- Added methods to validate visibility of rows.
- Made sure to force the owner_id data member to be integer, as Postgres and MySQL seemed to have different opinions on what it should be.
- Changed the "Delete All Children" method to make sure that any children we don't know about are preserved.
- Fixed an issue with the generic search, where we couldn't ask for "just anything" in a field. Now, use '%' for a non-empty field.
- Added a quick shortcut to the accurate distance calculator to return 0 if the points are the same.
- Added support for testing the uniqueness of a key.

**1.0.0.2035** *(July 6, 2018)*

- Added very low-level logging facility.
- Added a "blocker" to prevent the "God" login from being deleted.
- Added the user's IP address as a component of the saved API key (can be enabled/disabled in the config).
- Added the new login validator to the example config file.
- Added a facility to prevent logins while another login is still active.
- Now make sure that cached record "last_access" field is updated when changes are made.
- Added a "Server Secret" to make the API a bit more secure.
- Added "locking" to the records, to allow for blocked access while updating.
- Added a "batch mode," which greatly speeds up PUT operations.
- Added support for the address lookup in the sample config file.
- Added access to the "can_see_clearly" ID for "fuzzed" locations.
- Changed the way the SSL requirement is done.
- Changed the payload loader to a chunk-based loader, just to be sure.
- God Mode gets a longer (80 characters) API Key.
- Making sure that new records get NULL fields set as 'NULL'.
- Made a fix to the generic search setup, where we really shouldn't have been sending in "open wildcard" values for string searches.

**1.0.0.2034** *(June 11, 2018)*

- Improved documentation.
- Added method to clear the API Key.
- Added a method to access the API Key age.
- Added the new logging function to the sample config file.
- Updated the sample database SQL to be relevant to the current structure of things.

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
