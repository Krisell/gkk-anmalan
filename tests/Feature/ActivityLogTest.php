<?php

use App\Models\ActivityLog;
use App\Models\User;

test('an admin can view the activity log page', function () {
    loginAdmin();

    $this->get('/admin/activity-logs')->assertOk();
});

test('a regular user cannot view the activity log page', function () {
    login();

    $this->get('/admin/activity-logs')->assertStatus(401);
});

test('a guest cannot view the activity log page', function () {
    $this->get('/admin/activity-logs')->assertStatus(302);
});

test('activity logs are passed to the view with relationships', function () {
    $admin = loginAdmin();
    $user = User::factory()->create();

    ActivityLog::create([
        'performed_by' => $admin->id,
        'action' => 'explicit-registration-approval-update',
        'user_id' => $user->id,
        'data' => 'granted',
    ]);

    $response = $this->get('/admin/activity-logs');

    $response->assertOk();
    $logs = $response->original->getData()['logs'];
    expect($logs)->toHaveCount(1);
    expect($logs[0]->performer->first_name)->toBe($admin->first_name);
    expect($logs[0]->user->first_name)->toBe($user->first_name);
    expect($logs[0]->action)->toBe('explicit-registration-approval-update');
    expect($logs[0]->data)->toBe('granted');
});

test('activity logs are ordered by most recent first', function () {
    $admin = loginAdmin();

    ActivityLog::create([
        'performed_by' => $admin->id,
        'action' => 'account-promotion',
        'user_id' => $admin->id,
        'created_at' => now()->subDays(5),
    ]);

    ActivityLog::create([
        'performed_by' => $admin->id,
        'action' => 'payment-update',
        'user_id' => $admin->id,
        'created_at' => now()->subDay(),
    ]);

    $response = $this->get('/admin/activity-logs');

    $logs = $response->original->getData()['logs'];
    expect($logs[0]->action)->toBe('payment-update');
    expect($logs[1]->action)->toBe('account-promotion');
});
