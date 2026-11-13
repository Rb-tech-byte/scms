<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BackfillUserBranchIdSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasColumn('users', 'branch_id')) {
            if ($this->command) {
                $this->command->warn("users.branch_id column not found. Run migrations first.");
            }
            return;
        }

        $updatedUsers = 0;
        $syncedTutors = 0;

        Model::withoutEvents(function () use (&$updatedUsers, &$syncedTutors) {
            User::query()
                ->where('user_type', 'staff')
                ->whereNull('branch_id')
                ->select(['id'])
                ->chunkById(200, function ($users) use (&$updatedUsers) {
                    foreach ($users as $user) {
                        $branchId = Tutor::where('user_id', $user->id)->value('branch_id');

                        if (!$branchId) {
                            $branchId = Applicant::where('user_id', $user->id)->value('branch_id');
                        }

                        if ($branchId) {
                            User::where('id', $user->id)->update(['branch_id' => $branchId]);
                            $updatedUsers++;
                        }
                    }
                });

            Tutor::query()
                ->with(['user:id,branch_id'])
                ->whereHas('user', function ($q) {
                    $q->whereNotNull('branch_id');
                })
                ->select(['id', 'user_id', 'branch_id'])
                ->chunkById(200, function ($tutors) use (&$syncedTutors) {
                    foreach ($tutors as $tutor) {
                        $userBranchId = $tutor->user?->branch_id;
                        if ($userBranchId && (int) $tutor->branch_id !== (int) $userBranchId) {
                            Tutor::where('id', $tutor->id)->update(['branch_id' => $userBranchId]);
                            $syncedTutors++;
                        }
                    }
                });
        });

        $remaining = User::where('user_type', 'staff')->whereNull('branch_id')->count();

        if ($this->command) {
            $this->command->info("Backfill complete. users.branch_id updated: {$updatedUsers}. tutors.branch_id synced: {$syncedTutors}. Remaining staff without branch_id: {$remaining}.");
        }
    }
}
