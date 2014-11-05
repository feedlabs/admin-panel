#### /application
- method:  GET -> List of all Applications
 - id (*string*)
 - name (*string*)
 - description (*string*) (is optional)
 - createStamp (*int*)

- method:  POST -> Create new Application
 - name (*string*)
 - description (*string*) (is optional)

- method:  PUT -> *not supported*

- method:  DELETE -> *not supported*


#### /application/*applicationId*
- method:  GET -> Content of a specific Application by id
 - id (*string*)
 - name (*string*)
 - description (*string*) (is optional)
 - createStamp (*int*)

- method:  POST -> *not supported*

- method:  PUT -> update the specific Application by id
 - name (*string*)
 - description (*string*) (is optional)

- method:  DELETE -> delete the specific Application by id


#### /application/*applicationId*/feed
- method:  GET -> List of all Feeds per Application
 - id (*string*)
 - name (*string*)
 - description (*string*) (is optional)
 - tag (*array*)
 - channelId (*string*)
 - createStamp (*int*)

- method:  POST -> Create new Feed in specific Application
 - name (*string*)
 - description (*string*) (is optional)
 - tag (*array*)

- method:  PUT -> *not supported*

- method:  DELETE -> *not supported*


#### /application/*applicationId*/feed/*feedId*
- method:  GET -> Content of a specific Feed by id
 - id (*string*)
 - name (*string*)
 - description (*string*) (is optional)
 - tag (*array*)
 - channelId (*string*)
 - createStamp (*int*)

- method:  POST -> *not supported*

- method:  PUT -> update the specific Feed by id
 - name (*string*)
 - description (*string*) (is optional)
 - tag (*array*)

- method:  DELETE -> delete the specific Feed by id


#### /application/*applicationId*/feed/*feedId*/entry
- method:  GET -> List of all Entries per Feed
 - id (*string*)
 - data (*string*)
 - tag (*array*)
 - createStamp (*int*)

- method:  POST -> Create new Entry in specific Feed
 - data (*string*)
 - tag (*array*)

- method:  PUT -> *not supported*

- method:  DELETE -> *not supported*

#### /application/*applicationId*/feed/*feedId*/entry/*entryId*
- method:  GET -> Content of a specific Entry by id
 - id (*string*)
 - data (*string*)
 - tag (*array*)
 - createStamp (*int*)

- method:  POST -> *not supported*

- method:  PUT -> update the specific Entry by id
 - data (*string*)
 - tag (*array*)

- method:  DELETE -> delete the specific Entry by id


#### /token
- method:  GET -> List of all Tokens
 - token (*string*)
 - name (*string*)
 - createStamp (*int*)

- method:  POST -> Create new Token
 - name (*string*)

- method:  PUT -> *not supported*

- method:  DELETE -> *not supported*


#### /token/*tokenName*
- method:  GET -> Content of a specific Token by token
 - token (*string*)
 - name (*string*)
 - createStamp (*int*)

- method:  POST -> *not supported*

- method:  PUT -> *not supported*

- method:  DELETE -> delete the specific Token by token
