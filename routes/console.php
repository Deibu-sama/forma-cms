<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

Artisan::command('formacms:about', function () {
    $this->info('Bookkeeper Pro Portfolio — Laravel, React, Tailwind CSS, and Firestore.');
})->purpose('Display project information');

/*
 * Provision an administrator from trusted server environment variables or
 * secure interactive prompts. There is intentionally no public registration.
 * When environment credentials are configured, this command can be rerun to
 * synchronize the configured account with the hosting environment.
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

    if (! $usingEnvironment) {
        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser) {
            $this->error('A user with this email already exists. This command will not promote or overwrite an existing account.');

            return 1;
        }

        $name = trim((string) $this->ask('Administrator name'));

        if ($name === '') {
            $this->error('A name is required.');

            return 1;
        }

        $password = (string) $this->secret('Choose a password');
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

    if ($password === '') {
        $this->error('The administrator password cannot be empty.');

        return 1;
    }

    $user = User::query()->firstOrNew(['email' => $email]);
    $wasExisting = $user->exists;
    $user->name = $name;
    $user->password = $password; // User model's hashed cast hashes the password.
    $user->is_admin = true;
    $user->email_verified_at ??= now();
    $user->save();

    $this->info($wasExisting ? 'Administrator credentials synchronized successfully.' : 'Administrator created successfully.');
    $this->line('Sign in at /admin/login. The password was not displayed.');

    return 0;
})->purpose('Create or synchronize the administrator from server environment variables, or create one with secure prompts');
