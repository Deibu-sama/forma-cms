<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('formacms:about', function () {
    $this->info('Bookkeeper Pro Portfolio — Laravel, React, Tailwind CSS, and Firestore.');
})->purpose('Display project information');

/*
 * Provision an administrator from a trusted server terminal only.
 * No public registration endpoint or seeded default password is provided.
 */
Artisan::command('bookkeeper:make-admin {email}', function () {
    $email = strtolower(trim((string) $this->argument('email')));

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Please provide a valid email address.');

        return 1;
    }

    if (User::query()->where('email', $email)->exists()) {
        $this->error('A user with this email already exists. This command will not promote or overwrite an existing account.');

        return 1;
    }

    $name = trim((string) $this->ask('Administrator name'));

    if ($name === '') {
        $this->error('A name is required.');

        return 1;
    }

    $password = (string) $this->secret('Choose a password (minimum 12 characters)');
    $confirmation = (string) $this->secret('Confirm password');

    if (strlen($password) < 12) {
        $this->error('Password must contain at least 12 characters.');

        return 1;
    }

    if (! hash_equals($password, $confirmation)) {
        $this->error('Passwords do not match.');

        return 1;
    }

    $user = new User();
    $user->name = $name;
    $user->email = $email;
    $user->password = $password; // User model's hashed cast handles password hashing.
    $user->is_admin = true;
    $user->email_verified_at = now();
    $user->save();

    $this->info('Administrator created successfully.');
    $this->line('Sign in at /admin/login. Keep the password private.');

    return 0;
})->purpose('Create the first Bookkeeper Pro administrator securely from the server terminal');
