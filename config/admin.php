<?php

/*
|--------------------------------------------------------------------------
| Initial administrator provisioning
|--------------------------------------------------------------------------
|
| These values are read only by the server-side Artisan provisioning command.
| They are not exposed to React or included in the public page payload.
| Leave all three empty to use the command's interactive prompts instead.
|
*/

return [
    'email' => env('ADMIN_EMAIL'),
    'name' => env('ADMIN_NAME'),
    'password' => env('ADMIN_PASSWORD'),
];
