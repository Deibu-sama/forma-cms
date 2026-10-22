<?php

return [
    'firestore' => [
        'project_id' => env('FIRESTORE_PROJECT_ID'),
        // Base64-encoded service-account JSON. Keep this value in the host's
        // secret/environment settings; never commit the actual credentials.
        'credentials_base64' => env('FIREBASE_CREDENTIALS_BASE64'),
    ],
];
