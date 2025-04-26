# assemble
Read and store OAI Dublin Core metadata from the Open Journal System to create a local journal index database

# installation
1. copy files to your root directory
2. import install.sql to your database server
3. set your dbase.php file

# indexing OJS journal
1. provide input text with journal URL (with or without the "oai") and submit
2. it will show the first batch of articles DC description, and follow the link at the end of the list for the next batch, if available
3. continue to add the next batch until finished

# notes on other OAI protocol
you can add other index entries from other applications supporting the OAI protocol by providing the complete URL to access the OAI function

