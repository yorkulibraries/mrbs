COPY EXISTING BOOKINGS FROM OLD MRBS pre-1.4.10 NEW MRBS
==========================================================

* Extract Data from mrbs_entry for specific area into csv file:

POSTGRES SQL> copy (select id,start_time,end_time,entry_type,repeat_id,room_id,timestamp,create_by,name,type,description from mrbs_entry where room_id in(2,9)) to '/tmp/frost_mrbs_entry.csv' delimiter ',';

* Load Extracted CSV file into new database mrbs_entry table.

MYSQL> LOAD DATA INFILE '/tmp/frost_mrbs_entry.csv' INTO TABLE mrbs_entry FIELDS TERMINATED BY ',' (id,start_time,end_time,entry_type,repeat_id,room_id,timestamp,create_by,name,type,description);

* Create the new area and rooms in the new mrbs via admin application interface.

* Update the old room_id with the new room_id. (Look this up in mrbs_room table)
MYSQL> update mrbs_entry set room_id=2 where room_id=9;

* Repeat previous step for all rooms

