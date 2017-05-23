# google packages meta info parser

## db table

Parses app category and download counts based on input List of google play id.
Stores data in small CSV files (16k rows each)
Once done, you can concat all files in single CSV.


## db table
    create table google-packages    (
        package mediumtext null,
        id int auto_increment
    );




### IMPORT DATA

    LOAD DATA LOCAL INFILE 'C:\\google-packages\\files\\Packagenames.csv'
    INTO TABLE `google-packages`
    FIELDS TERMINATED BY ','
    ENCLOSED BY '"'
    LINES TERMINATED BY '\n'


### RUN ALL TASKS
    /usr/bin/php /home/username/google-packages/runner.php

### RUN SINGLE TASK WIH PARAMS
    /usr/bin/php /home/bridgedev/google-packages/parseparams.php 6000 7000 >data_6000_7000.log &



### see processes
    pgrep -l -u username

### kill all php if needed
    pkill -9 php

### count files
    ls -l | grep ^- | wc -l

### concat files
    cat /home/username/google-packages/logs/*csv > /home/username/google-packages/google-packages1.csv
