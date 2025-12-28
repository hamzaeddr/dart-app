<?php

namespace Tests\Feature;

use App\Models\Contribution;
use App\Models\Daret;
use App\Models\DaretMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DaretFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_daret_and_becomes_member(): void
    {
        $this->seed();

        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->actingAs($user)->post('/darets', [
            'name' => 'Test Daret',
            'contribution_amount' => 100,
            'period' => 'monthly',
            'total_members' => 2,
            'start_date' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        $daret = Daret::where('name', 'Test Daret')->first();

        $this->assertNotNull($daret);
        $this->assertEquals($user->id, $daret->owner_id);
        $this->assertTrue($daret->members()->where('user_id', $user->id)->exists());
    }

    public function test_member_can_upload_receipt_and_admin_can_confirm(): void
    {
        $this->seed();

        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $member = User::where('email', 'user@example.com')->firstOrFail();

        Storage::fake('public');

        $daret = Daret::create([
            'owner_id' => $admin->id,
            'name' => 'Flow Daret',
            'contribution_amount' => 150,
            'period' => 'monthly',
            'total_members' => 2,
            'start_date' => now()->toDateString(),
            'schedule' => null,
            'status' => 'active',
        ]);

        DaretMember::create([
            'daret_id' => $daret->id,
            'user_id' => $admin->id,
            'position_in_cycle' => 1,
            'joined_at' => now(),
        ]);

        DaretMember::create([
            'daret_id' => $daret->id,
            'user_id' => $member->id,
            'position_in_cycle' => 2,
            'joined_at' => now(),
        ]);

        $daret->refresh();
        $daret->generateCycles();

        $cycle = $daret->cycles()->orderBy('cycle_number')->first();

        $file = UploadedFile::fake()->create('receipt.pdf', 10, 'application/pdf');

        $uploadResponse = $this->actingAs($member)->post(route('contributions.upload', [$daret, $cycle]), [
            'receipt' => $file,
        ]);

        $uploadResponse->assertRedirect();

        $contribution = Contribution::where('daret_id', $daret->id)
            ->where('user_id', $member->id)
            ->first();

        $this->assertNotNull($contribution);
        $this->assertEquals('pending', $contribution->status);

        $confirmResponse = $this->actingAs($admin)->post(route('contributions.confirm', $contribution));

        $confirmResponse->assertRedirect();

        $contribution->refresh();

        $this->assertEquals('confirmed', $contribution->status);
        $this->assertNotNull($contribution->confirmed_at);
    }
}
