# Usage Translator Service Coding Challenge - Net Nation


## Steps to start the project
  The test was made using Lumen - a microframework of laravel
  It comes with its server setting using Homestead
  - To start the server , and mysql run -
      #####docker-compose up
## For running the test
The input csv file, Sample_Report.csv is passed through an api call. please note, i havnt used any host alias, at this point it will run on localhost. Configuration defined in .env file 
- the api will be a POST call to localhost:8080/upload-file.

- Default Output with be insert queries for Chargeable and Domains tables.

## Possible Configurations

For the purpose of this test, the possible configurations are added to the .env file

Some of the possible configurations are listed below

- RETURN_PREPARED_QUERY - This decides if the output query is a raw insert query or a prepared query which is a default format for lumen query builder. Defaults to TRUE (lumen query builder format)

- EXECUTE_QUERY - If you wish to run the query and see the entries in the database, set this to TRUE, Defaults to FALSE.

- INSERT_BATCH_SIZE - this decides the batch size of an insert query. defaults to 10.


## Logging
For the purpose of this test, the expected logs are added to storage/logs folder