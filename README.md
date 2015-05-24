# needaride

A Ride Share finding API designed as a BowTie Backend Application. To use, connect the BowTie Project front-end to the application at *TODO:http://example.com/*.

## Architecture

### Ride

| Column | Description |
| ---    | --- |
| ride_id | record identifier |
| user_id | STR BowTie user identifier |
| start | LAT/LONG POINT |
| stop | LAT/LONG POINT |
| starts_at | INT (seconds since 00:00) |
| max_passengers_count | UINT maximum number of passengers for this ride |
| passengers_count | UINT current number of passengers for this ride |
| description | TEXT any additional information for a description |

has_many :passengers

### Passenger

| Column | Description |
| --- | --- |
| passenger_id | record identifier |
| ride_id | foreign key (rides.id) - the ride this passenger is associated with |

belongs_to :ride

## API

Each request requires a valid and authenticated user. Authentication of API calls is managed by BowTie.

### Error Responses

Status code 200 will be returned for all requests that were processed by the API. A status code of 200 does not necessarily indicate a success - the JSON response body should be checked for errors.

```
{
  status: 'error',
  message: 'Something caused the request to fail, and I'm a summary of that failure'
}
```

A 403 indicates an authorization failure, and should direct the user to create a new session or register.

### POST /rides

* param: ride[start][latitude] (required, type: double)
* param: ride[start][longitude] (required, type: double)
* param: ride[stop][latitude] (required, type: double)
* param: ride[stop][longitude] (required, type: double)
* param: ride[max_passengers_count] (required, type: unsigned int)
* param: ride[description] (optional, type: text)
* param: ride[starts_at] (optional, type: timeofday)

Creates a new `Ride` object. The current user becomes the organizer for the Ride.

### GET /rides

Provides `Ride`s that the current user organized.

```
{
  status: 'ok',
  rides: [{
    ride_id: 2234,                      # Ride identifier
    user_id: '543',                     # Ride organizer id
    start: { 
             latitude: ,                # Starting latitude of the ride
             longitude: ,               # Starting longitude of the ride
             difference:                # Difference in miles between requested start and this ride's start
    },
    stop: { 
             latitude: ,                # Stopping latitude of the ride
             longitude: ,               # Stopping longitude of the ride
             difference:                # Difference in miles between requested stop of this ride's stop
    },
    description: "...",                 # Any additional details about the ride entered by the ride organizer
    passengers_count: 1,                 # Number of folks in the vehicle
    max_passenger_count: 3,             # Maximum number of folks in the vehicle
    passengers: [...]                   # An array of BowTie User IDs for passengers on this vehicle
  }, ...]
}
```

### DELETE /rides/:ride_id.json

Removes a `Ride` that the current user organized.

### GET /rides/search.json
* param: ride[start][latitude] (required, type: double)
* param: ride[start][longitude] (required, type: double)
* param: ride[stop][latitude] (required, type: double)
* param: ride[stop][longitude] (required, type: double)
* param: ride[starts_at] (optional, type: timeofday)
  
Provides a list of size <= n, where n is some limit to the number of most relevant `Ride`s that have availability. Rides are listed in order from most relevant to least relevant. Relevance is based on a window for the start time and the location of the ride. Time of day is a time object without a date.

TODO: in the future we can filter based on profiles, but this version will simply be based on rides.
TODO: in the future we can rate a profile, but this version will not provide rating functionality (this may be something we extend the BowTie platform with, as well).
TODO: scheduling recurring rides based on a more complex schedule. We'll assume each weekday for MVP.

```
{
  status: 'ok',
  rides: [{
    ride_id: 2234,                      # Ride identifier
    user_id: '543',                     # Ride organizer id
    start: { 
             latitude: ,                # Starting latitude of the ride
             longitude: ,               # Starting longitude of the ride
             difference:                # Difference in miles between requested start and this ride's start
    },
    stop: { 
             latitude: ,                # Stopping latitude of the ride
             longitude: ,               # Stopping longitude of the ride
             difference:                # Difference in miles between requested stop of this ride's stop
    },
    description: "...",                 # Any additional details about the ride entered by the ride organizer
    passengers_count: 1,                    # Number of folks in the vehicle
    max_passengers_count: 4                 # Maximum number of folks in the vehicle
  }, ...]
}
```

### GET /rides/:ride_id.json

Provides public `Ride` details for the ride with the given `:ride_id`.

### POST /rides/:passenger_id

Adds the current user as a passenger to the ride with the given `:passenger_id`. Operation will fail when the ride is already full.