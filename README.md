## support-tool-api

This api is the wrapper for the database. Requests are being made to this endpoint
in order to prevent direct requests to the database.

Requirements:

 * PHP
 * MySQL database with demo data.
 * Optional: Apache 2


### Examples

These examples might help you requesting the api with your demo data.

#### Requesting users

Users are all users. This means customers, support employees, and administrators.

You will receive the following attributes when requesting a user:

 * email: String
 * firstname: String
 * lastname: String
 * role: Int

##### Request user by authkey

URL: [here](http://localhost/src/api/Endpoints/get/user.php?authkey=3c7ebb-b25806-b3c21f-352432-804562)

Returns:

```json
[
    {
        "email": "mueller@mail.xyz",
        "firstname": "Thomas",
        "lastname": "Müller",
        "role": "1"
    }
]
```

##### Request user by id

URL: [here](http://localhost/src/api/Endpoints/get/user.php?authkey=3c7ebb-b25806-b3c21f-352432-804562&userid=2)

Returns:

```json
[
    {
        "email": "philipp.schwaighofer@sbg.at",
        "firstname": "Philipp",
        "lastname": "Schwaighofer",
        "role": "2"
    }
]
```

##### Request other users without access

URL: [here](http://localhost/src/api/Endpoints/get/user.php?authkey=3c7ebb-b25806-b3c21f-352432-804562&userid=3)

Returns:

```json
{
    "error": "You do not have permission to request that data!"
}
```

##### Request without a valid authkey

URL: [here](http://localhost/src/api/Endpoints/get/user.php?authkey=3c7ebb-b25806-b3c21f-352432-80456)

Returns:

```json
{
    "error": "Invalid or unknown authkey!"
}
```

#### Request roles

##### Request all roles

URL: [here](http://localhost/src/api/Endpoints/get/roles.php?authkey=31166d-85d82e-4ea258-3bfa60-c903f5)

Returns:

```json
[
    {
        "id": "1",
        "name": "Kunde"
    },
    {
        "id": "2",
        "name": "Supportmitarbeiter"
    },
    {
        "id": "3",
        "name": "Administrator"
    }
]
```

#### 