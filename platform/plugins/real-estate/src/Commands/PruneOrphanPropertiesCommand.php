<?php

namespace Botble\RealEstate\Commands;

use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Property;
use Illuminate\Console\Command;

/**
 * The "Add Property" wizard creates a Property draft row the moment anyone
 * opens the page (PropertySubmissionService::startDraft()), before they've
 * filled in or submitted anything. Every visitor who bounces off that page
 * without finishing leaves one of these behind, and unlike a real listing
 * it never has a member or agent attached until someone actually completes
 * the flow - so "draft, no member, no agent" is an unambiguous signal for
 * "abandoned session", not a real listing anyone still cares about.
 *
 * Deliberately scoped to submission_status = 'draft' only: a property that
 * ever made it through Submit Ad's finalize() step is never touched here,
 * no matter what its member/agent state looks like.
 *
 * Note this does a real SQL delete, not the app's usual "soft delete"
 * convention (PropertyRepository::delete() just flags is_deleted = 1) -
 * that flag only hides a property from its owner's dashboard list, it
 * doesn't reclaim the row, which defeats the point of a cleanup command.
 */
class PruneOrphanPropertiesCommand extends Command
{
    protected $signature = 'cms:properties:prune-orphans
        {--hours=24 : Only consider drafts whose last wizard activity is at least this many hours old}
        {--force : Actually delete instead of previewing what would be deleted}';

    protected $description = 'Delete abandoned "Add Property" wizard drafts that were never claimed by a member or assigned to an agent';

    public function handle(): int
    {
        $hours = max(0, (int) $this->option('hours'));

        $query = Property::query()
            ->where('submission_status', 'draft')
            ->whereNull('member_id')
            ->where(function ($query) {
                $query->whereNull('author_id')
                    ->orWhereNull('author_type')
                    ->orWhere('author_type', '!=', Account::class);
            })
            ->where('last_wizard_activity_at', '<=', now()->subHours($hours));

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('No orphan draft properties found.');

            return self::SUCCESS;
        }

        if (!$this->option('force')) {
            $this->warn(sprintf(
                'Found %d orphan draft %s (no member, no agent, inactive for %d+ hours):',
                $count,
                $count === 1 ? 'property' : 'properties',
                $hours
            ));

            $this->table(
                ['ID', 'Name', 'Wizard Step', 'Last Activity'],
                (clone $query)
                    ->orderBy('id')
                    ->limit(50)
                    ->get(['id', 'name', 'wizard_step', 'last_wizard_activity_at'])
                    ->map(fn (Property $property) => [
                        $property->id,
                        $property->name,
                        $property->wizard_step,
                        $property->last_wizard_activity_at,
                    ])
            );

            if ($count > 50) {
                $this->line('... and ' . ($count - 50) . ' more.');
            }

            $this->line('This was a preview - nothing was deleted. Re-run with --force to actually delete them.');

            return self::SUCCESS;
        }

        $deleted = 0;

        $query->chunkById(200, function ($properties) use (&$deleted) {
            foreach ($properties as $property) {
                $property->delete();
                $deleted++;
            }
        });

        $this->info(sprintf('Deleted %d orphan draft %s.', $deleted, $deleted === 1 ? 'property' : 'properties'));

        return self::SUCCESS;
    }
}
