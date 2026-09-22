<?php

use App\Models\Competition;
use App\Models\CompetitionAdminTask;

test('an admin can complete a competition follow-up task and it is logged', function () {
    $admin = loginAdmin();
    $task = CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
    ]);

    $this->patchJson("/admin/competition-tasks/{$task->id}", ['status' => 'done'])
        ->assertOk()
        ->assertJsonPath('status', 'done');

    $this->assertDatabaseHas('competition_admin_tasks', [
        'id' => $task->id,
        'status' => 'done',
        'completed_by' => $admin->id,
    ]);
    $this->assertDatabaseHas('activity_logs', [
        'performed_by' => $admin->id,
        'action' => 'competition-admin-task-updated',
        'data' => \json_encode([
            'competition_id' => $task->competition_id,
            'task_id' => $task->id,
            'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
            'status' => 'done',
            'competition_name' => $task->competition->name,
        ]),
    ]);
});

test('a non-admin cannot complete a competition follow-up task', function () {
    login();
    $task = CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
    ]);

    $this->patchJson("/admin/competition-tasks/{$task->id}", ['status' => 'done'])->assertUnauthorized();
});

test('an already handled task cannot be updated again', function () {
    loginAdmin();
    $task = CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
        'status' => 'done',
    ]);

    $this->patchJson("/admin/competition-tasks/{$task->id}", ['status' => 'not_applicable'])->assertConflict();

    expect($task->fresh()->status)->toBe('done');
});

test('admins see pending tasks on the inside page', function () {
    loginAdmin();
    $pending = CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
    ]);
    CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
        'status' => 'done',
    ]);

    $tasks = $this->get('/insidan')->assertSuccessful()->viewData('adminTasks');

    expect($tasks)->toHaveCount(1);
    expect($tasks->first()->id)->toBe($pending->id);
});

test('non-admins do not see tasks on the inside page', function () {
    login();
    CompetitionAdminTask::create([
        'competition_id' => Competition::factory()->create()->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
    ]);

    $this->get('/insidan')->assertSuccessful()->assertViewHas('adminTasks', []);
});

test('tasks for deleted competitions are hidden from the inside page', function () {
    loginAdmin();
    $competition = Competition::factory()->create();
    CompetitionAdminTask::create([
        'competition_id' => $competition->id,
        'type' => CompetitionAdminTask::SUBMIT_REGISTRATION,
    ]);
    $competition->delete();

    expect($this->get('/insidan')->assertSuccessful()->viewData('adminTasks'))->toHaveCount(0);
});
