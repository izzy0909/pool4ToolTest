#### Project setup
  * Make changes in .env file, set parameter for DATABASE_URL
  * Run Composer Develop command from the console
    * `composer develop`
  * Run Symfony Web Server
    * `php bin/console server:start`
    
#### API Documentation
  * You can find all API calls here
    * `http://127.0.0.1:8000/api/docs`
  * You can run the cron jobs with 
    * `bin/console parse:xml`


#### Pool4Tool Code Test - January 2019.

# Advanced Dev Code Test

Ready for something more serious and quite a bit more creative than the standard dev hiring test? Well, this is the right place to look...

## Global Aims

Build a web application which would receive XML content via an API (REST) and store it in a stack. A separate process should parse the XML content and check if it contains
a specific segment with a specific value (there might be multiple). Every XML blob may be processed only once.

## Specific Aims

* Create an application with the following API endpoints:
    * Receive new XML content via POST and return some unique ID for that XML content
    * Query the API with the unique ID to get XML processing status: pending, processing, done, error
    * Query the API with the unique ID to get the result of processing the XML, if there is a SEGMENT called "ZZ_JD_MATLIEFNRNUM": true, false
    * Query the API with the unique ID to get the result of processing the XML, if there are multiple SEGMENTs called "ZZ_JD_MATLIEFNRNUM": true, false
    * Query the API with the unique ID to get the result of processing the XML, get the value of the segment "ZZ_JD_MATLIEFNRNUM": string
    * Query the API with the unique ID to get the result of processing the XML, get the values (when multiple) of the segment "ZZ_JD_MATLIEFNRNUM": (array) strings
    * Query the API with the unique ID to delete the XML from the stack completely
* Create a parallel PHP Cli process which will work through all the XMLs and parse them and generate necessary meta data for the API queries above

## Limitations

* XMLs may be up to 2 Gb in size
* XMLs are all UTF8
* XMLs may contain errors
* You may use any framework or write the whole code in Vanilla PHP
* You may use any database you like (if any)
* No UI is necessary, but some sort of monitoring will be a plus
* Performance matters: PHP scripts have a maximum of 512Mb RAM available, no limit on processing time