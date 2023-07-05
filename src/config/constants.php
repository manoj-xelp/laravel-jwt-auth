<?php
// Define all constants here
//To access in other files use Config::get('constants.apiversion');
return [
    "USER_PROFILE_PATH" => "/users/",
    "SERVER_URL" => env('SERVER_URL'),
    "POST_PATH" => "/posts/",
    "USER_STATUS" => ["VERIFIED" => 1, "UNVERIFIED" => 0, "DELETED" => -1],
];
