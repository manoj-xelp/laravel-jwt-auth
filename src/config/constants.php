<?php
// Define all constants here
//To access in other files use Config::get('constants.apiversion');
return [
    "UPLOAD_TYPE" => [
        "USER_PROFILE"
    ],
    "USER_PROFILE_PATH" => "/users/",
    "UNDEFINED_PATH" => "/undefined/",
    "SERVER_URL" => env('SERVER_URL'),
    "POST_PATH" => "/posts/",
    "USER_STATUS" => ["VERIFIED" => 1, "UNVERIFIED" => 0, "DELETED" => -1],
    "RESET_PASSWORD_URL" => env('SERVER_URL').'/api/v1/auth/reset_password',
];
