<?php

namespace Botble\ACL\Commands;

use Botble\ACL\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * Resets an admin panel user's password by email, for account-recovery
 * situations (e.g. a lost/forgotten admin password before launch) where
 * going through the normal "forgot password" email flow isn't an option.
 *
 * The new password is always entered at the prompt, never passed as a
 * command argument or hardcoded in source - so it never ends up sitting in
 * plaintext in shell history or git history.
 */
class ResetUserPasswordCommand extends Command
{
    protected $signature = 'cms:user:reset-password {email=admin@gemlisting.co : Email address of the user to reset}';

    protected $description = 'Reset an admin panel user\'s password by email';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email \"{$email}\".");

            return self::FAILURE;
        }

        $this->info("Resetting password for {$user->email} ({$user->first_name} {$user->last_name}).");

        $password = $this->secret('Enter new password');
        $confirmation = $this->secret('Confirm new password');

        $validator = Validator::make(compact('password'), ['password' => 'required|min:8']);

        if ($validator->fails()) {
            $this->error($validator->errors()->first('password'));

            return self::FAILURE;
        }

        if ($password !== $confirmation) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password updated for {$user->email}.");

        return self::SUCCESS;
    }
}
