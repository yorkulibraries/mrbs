How to setup an MRBS instance
==========================================================
git clone from git repo
make appropriate changes to web/config.inc.php
*** DO NOT commit local config changes

How to update
==============
git stash
git pull origin master
git stash pop

COPY EXISTING BOOKINGS FROM OLD MRBS pre-1.4.10 NEW MRBS
==========================================================

* Extract Data from mrbs_entry for specific area into csv file:

POSTGRES SQL> copy (select id,start_time,end_time,entry_type,repeat_id,room_id,timestamp,create_by,name,type,description from mrbs_entry where room_id in(2,9)) to '/tmp/frost_mrbs_entry.csv' delimiter ',';

* Load Extracted CSV file into new database mrbs_entry table.

MYSQL> LOAD DATA INFILE '/tmp/frost_mrbs_entry.csv' INTO TABLE mrbs_entry FIELDS TERMINATED BY ',' (id,start_time,end_time,entry_type,repeat_id,room_id,timestamp,create_by,name,type,description);

* Create the new area and rooms in the new mrbs via admin application interface.
* Set the newly created area/room id to same as the area/room id from the old MRBS (Look this up in mrbs_room table)

