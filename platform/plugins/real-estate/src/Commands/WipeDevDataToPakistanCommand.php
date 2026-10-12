<?php

namespace Botble\RealEstate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * One-shot reset to Pakistan-only data: wipes every member/agent/property/
 * project/lead/consult/comment/document/transaction row and trims the
 * countries/states/cities/city-areas and agent spoken-language lookup down
 * to Pakistan only. Runs against whichever database connection the app is
 * currently configured with - dev or production - gated solely by the
 * password prompt below. There is no environment check: this is
 * deliberately allowed to run on production too, for the v1.0.0 launch
 * reset. Double-check the target database/connection before entering the
 * password.
 *
 * Deliberately NOT wrapped in one big DB::transaction(): every table/phase
 * is handled independently (see wipeTable()) so progress is visible as it
 * happens, a problem in one table can't roll back or block the rest, and
 * running the command again on an already-wiped DB is always safe - each
 * step finds nothing left to do and just says so instead of erroring.
 */
class WipeDevDataToPakistanCommand extends Command
{
    protected $signature = 'cms:dev:wipe-to-pakistan';

    protected $description = 'DESTRUCTIVE: wipe member/agent/property/transaction data and trim location + language lookups to Pakistan only (runs on dev or production - password gated only)';

    private const PASSWORD = 'gemlisting-dev';

    private const KEEP_LANGUAGES = [
        'urdu', 'english', 'punjabi', 'saraiki', 'sindhi',
        'pashto', 'balochi', 'brahvi', 'persian', 'shena',
    ];

    private int $warnings = 0;

    public function handle(): int
    {
        $this->warn('This will PERMANENTLY delete members/agents/properties/transactions and trim locations to Pakistan-only on the "' . config('database.default') . '" connection (database: ' . config('database.connections.' . config('database.default') . '.database') . ').');

        $password = $this->secret('Enter password to continue');

        if ($password !== self::PASSWORD) {
            $this->error('Incorrect password.');

            return self::FAILURE;
        }

        $this->wipeTransactionalData();
        $this->trimLocationsToPakistan();
        $this->trimSpokenLanguages();

        // Every delete above went through the raw query builder, not the
        // app's Repository classes - so Botble's repository cache decorator
        // (see CacheAbstractDecorator::flushCacheAndUpdateData()) was never
        // told anything changed. If "Enable Cache" is on, cached query
        // results (property/project/member/etc. listings) would otherwise
        // keep serving the pre-wipe data for up to their TTL. Clear it now
        // so the public site reflects the wipe immediately.
        $this->call('cache:clear');

        if ($this->warnings > 0) {
            $this->warn("Pakistan-only reset complete with {$this->warnings} warning(s) - see above.");
        } else {
            $this->info('Pakistan-only reset complete.');
        }

        return self::SUCCESS;
    }

    private function wipeTransactionalData(): void
    {
        $this->line('--- Wiping transactional data ---');

        // Order matters only where a real FK constraint exists (re_properties
        // and re_accounts cascade their dependents automatically on delete);
        // everything else is a plain logical reference with no DB-level FK,
        // so deleting them in this order is just for tidy, readable output.
        $this->wipeTable('re_properties', 'Properties');
        $this->wipeTable('re_property_features', 'Property features (pivot)');
        $this->wipeTable('favourite_properties', 'Favourite properties');
        $this->wipeTable('buyer', 'Buyer deals');
        $this->wipeTable('ratings', 'Ratings');
        $this->wipeTable('re_projects', 'Projects');
        $this->wipeTable('re_project_features', 'Project features (pivot)');
        $this->wipeTable('comments', 'Comments / chat');
        $this->wipeTable('wanted', 'Wanted / Leads');
        $this->wipeTable('re_consults', 'Consults');
        $this->wipeTable('contact_replies', 'Contact replies');
        $this->wipeTable('contacts', 'Contact inquiries');
        $this->wipeTable('transactions', 'Payment transactions');
        $this->wipeTable('payments', 'Payments');
        $this->wipeTable('re_accounts', 'Agents');
        $this->wipeTable('members', 'Members');
        $this->wipeTable('table_properties_check_lists', 'Property documents');
        $this->wipeTable('category_documents', 'Category documents (pivot)');
    }

    /**
     * Deletes every row from $table, in chunks of 500 with a live progress
     * bar. Safe to call repeatedly: an empty or missing table is reported
     * and skipped rather than treated as an error, and any unexpected
     * exception is caught so it can't take the rest of the command down.
     *
     * Some pivot tables (re_property_features, re_project_features) have no
     * surrogate `id` column at all - just a composite key - so chunkById()
     * can't be used there. Those are deleted in one shot instead; they're
     * small pivot tables, so a progress bar isn't needed.
     */
    private function wipeTable(string $table, string $label): void
    {
        try {
            if (! Schema::hasTable($table)) {
                $this->warn("⚠ {$label} ({$table}): table not found, skipping.");
                $this->warnings++;

                return;
            }

            $total = DB::table($table)->count();

            if ($total === 0) {
                $this->line("{$label}: already empty, nothing to do.");

                return;
            }

            if (! Schema::hasColumn($table, 'id')) {
                DB::table($table)->delete();
                $this->info("{$label}: deleted {$total} rows.");

                return;
            }

            $bar = $this->output->createProgressBar($total);
            $bar->setFormat(" {$label}: %current%/%max% [%bar%]");
            $bar->start();

            DB::table($table)
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($table, $bar) {
                    DB::table($table)->whereIn('id', $rows->pluck('id'))->delete();
                    $bar->advance($rows->count());
                });

            $bar->finish();
            $this->newLine();
            $this->info("{$label}: deleted {$total} rows.");
        } catch (Throwable $exception) {
            $this->newLine();
            $this->warn("⚠ {$label} ({$table}): {$exception->getMessage()} - skipped.");
            $this->warnings++;
        }
    }

    /**
     * Keeps only Pakistan in countries/states/cities/city_area. Uses the
     * raw query builder throughout, never the Country/State Eloquent models
     * - Country::boot() has a known bug where its deleting event cascades
     * to ALL states globally, not just the deleted country's.
     */
    private function trimLocationsToPakistan(): void
    {
        $this->line('--- Trimming locations to Pakistan ---');

        try {
            $pakistanId = DB::table('countries')->where('name', 'Pakistan')->value('id');

            if (! $pakistanId) {
                $this->warn('⚠ Locations: no country named "Pakistan" found, skipping location trim.');
                $this->warnings++;

                return;
            }

            $keptStateIds = DB::table('states')->where('country_id', $pakistanId)->pluck('id');
            $keptCityIds = DB::table('cities')->whereIn('state_id', $keptStateIds)->pluck('id');

            if (Schema::hasTable('city_area')) {
                $deleted = DB::table('city_area')->whereNotIn('city_id', $keptCityIds)->delete();
                $this->info("City areas: deleted {$deleted} rows outside Pakistan.");
            } else {
                $this->warn('⚠ Locations: city_area table not found, skipping.');
                $this->warnings++;
            }

            $deleted = DB::table('cities')->whereNotIn('id', $keptCityIds)->delete();
            $this->info("Cities: deleted {$deleted} rows outside Pakistan.");

            $deleted = DB::table('states')->whereNotIn('id', $keptStateIds)->delete();
            $this->info("States: deleted {$deleted} rows outside Pakistan.");

            $deleted = DB::table('countries')->where('id', '!=', $pakistanId)->delete();
            $this->info("Countries: deleted {$deleted} rows, kept Pakistan only.");
        } catch (Throwable $exception) {
            $this->warn("⚠ Locations: {$exception->getMessage()} - skipped.");
            $this->warnings++;
        }
    }

    private function trimSpokenLanguages(): void
    {
        $this->line('--- Trimming spoken languages ---');

        try {
            if (! Schema::hasTable('re_spoken_languages')) {
                $this->warn('⚠ Spoken languages: re_spoken_languages table not found, skipping.');
                $this->warnings++;

                return;
            }

            $deleted = DB::table('re_spoken_languages')
                ->whereRaw('LOWER(name) NOT IN (' . implode(',', array_fill(0, count(self::KEEP_LANGUAGES), '?')) . ')', self::KEEP_LANGUAGES)
                ->delete();

            $this->info("Spoken languages: deleted {$deleted} rows, kept Pakistani languages only.");
        } catch (Throwable $exception) {
            $this->warn("⚠ Spoken languages: {$exception->getMessage()} - skipped.");
            $this->warnings++;
        }
    }
}
