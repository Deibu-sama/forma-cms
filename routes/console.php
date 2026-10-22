<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('formacms:about', function () {
    $this->info('Bookkeeper Pro Portfolio — Laravel, React, Tailwind CSS, and Firestore.');
})->purpose('Display project information');

/*
 * Provision an administrator from a trusted server terminal only.
 * ADMIN_EMAIL, ADMIN_NAME, and ADMIN_PASSWORD may be supplied by the host
 * environment. There is intentionally no public administrator registration.
 */
Artisan::command('bookkeeper:make-admin {email?}', function () {
    $envEmail = config('admin.email');
    $envName = config('admin.name');
    $envPassword = config('admin.password');

    $hasAnyEnvironmentValue = collect([$envEmail, $envName, $envPassword])
        ->contains(fn ($value) => is_string($value) && trim($value) !== '');

    $usingEnvironment = $hasAnyEnvironmentValue;

    if ($usingEnvironment) {
        $allEnvironmentValuesPresent = is_string($envEmail) && trim($envEmail) !== ''
            && is_string($envName) && trim($envName) !== ''
            && is_string($envPassword) && $envPassword !== '';

        if (! $allEnvironmentValuesPresent) {
            $this->error('Set all three variables ADMIN_EMAIL, ADMIN_NAME, and ADMIN_PASSWORD, or clear all three to use interactive prompts.');

            return 1;
        }

        $email = strtolower(trim($envEmail));
        $name = trim($envName);
        $password = $envPassword;
    } else {
        $email = strtolower(trim((string) ($this->argument('email') ?? '')));

        if ($email === '') {
            $email = strtolower(trim((string) $this->ask('Administrator email')));
        }

        $name = '';
        $password = '';
    }

    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Please provide a valid administrator email address.');

        return 1;
    }

    if (User::query()->where('email', $email)->exists()) {
        $this->error('A user with this email already exists. This command will not promote or overwrite an existing account.');

        return 1;
    }

    if (! $usingEnvironment) {
        $name = trim((string) $this->ask('Administrator name'));

        if ($name === '') {
            $this->error('A name is required.');

            return 1;
        }

        $password = (string) $this->secret('Choose a password (minimum 12 characters)');
        $confirmation = (string) $this->secret('Confirm password');

        if (! hash_equals($password, $confirmation)) {
            $this->error('Passwords do not match.');

            return 1;
        }
    }

    if ($name === '') {
        $this->error('A name is required.');

        return 1;
    }

    if (strlen($password) < 12) {
        $this->error('The administrator password must contain at least 12 characters. Choose a strong, unique password.');

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
    $this->line('Sign in at /admin/login. The password was not displayed.');

    return 0;
})->purpose('Create the first administrator from server environment variables or secure interactive prompts');
