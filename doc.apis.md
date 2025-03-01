# APIs and Licensing

The following APIs are used by the application and require the user's own API keys

*   Google GeoCode (replacing with OpenStreetMaps API)
*   OpenStreetMaps ([Nominatim](https://nominatim.org/release-docs/latest/api/Overview/))
*   Weather (NWS)
*   Flight Data - Aviation Edge AND Flight Radar
*   Twilio / ClickSend for text messaging
*   Sparkpost for emails

In my database, in my config table, on the system node, my JSON contains the following (excerpt):

```
keys: {
  GOOGLE_API_KEY: '',
  SPARKPOST_KEY: '',
  FLIGHT_RADAR_API: '',
  CLICKSEND_USERNAME: '',
  CLICKSEND_PASSWORD: '',
  TWILIO_ACCOUNT_SID: '',
  TWILIO_AUTH_TOKEN: '',
  AVIATION_EDGE_API_KEY: '',
},
```