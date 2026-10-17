<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Invoices are due 30 days after creation. Allow a few extra days for
     * bank payments to be registered before treating an invoice as overdue.
     */
    public const OVERDUE_INVOICE_DAYS = 35;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'edited_at' => 'datetime',
        'birth_year' => 'integer',
        'is_honorary_member' => 'boolean',
        'is_student_over_23' => 'boolean',
        'explicit_registration_approval' => 'boolean',
        'ren_vinnare_education_completed_at' => 'datetime',
        'background_check_valid_from' => 'datetime:Y-m-d',
    ];

    /** @return Attribute<string, string|null> */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null ? null : Str::lower(\trim($value)),
        );
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function competitionRegistrations(): HasMany
    {
        return $this->hasMany(CompetitionRegistration::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Statuses that block the user until the agreement is signed again.
     * 'expired' means the agreement document was updated after the user signed it,
     * 'old' means the signature is more than a year old.
     */
    public const BLOCKING_AGREEMENT_STATUSES = ['unsigned', 'expired', 'old'];

    /**
     * The agreements a member has to sign, keyed by name, with their signature column.
     */
    public const AGREEMENTS = [
        'membership' => 'membership_agreement_signed_at',
        'anti_doping' => 'anti_doping_agreement_signed_at',
    ];

    /**
     * @return array{status: string, signed_at: ?Carbon, expires_at: ?Carbon}
     */
    public function agreementStatus(string $agreement): array
    {
        $signedAt = $this->{self::AGREEMENTS[$agreement]};

        if (! $signedAt) {
            return ['status' => 'unsigned', 'signed_at' => null, 'expires_at' => null];
        }

        $signedAt = Carbon::parse($signedAt);
        $expiresAt = $signedAt->copy()->addYear();
        $lastUpdatedAt = config("agreements.{$agreement}.last_updated_at");

        $status = match (true) {
            $lastUpdatedAt && $signedAt->lt($lastUpdatedAt) => 'expired',
            $expiresAt->lte(now()) => 'old',
            $expiresAt->copy()->subMonth()->lte(now()) => 'expiring',
            default => 'signed',
        };

        return ['status' => $status, 'signed_at' => $signedAt, 'expires_at' => $expiresAt];
    }

    /**
     * @return array<string, array{status: string, signed_at: ?Carbon, expires_at: ?Carbon}>
     */
    public function agreementStatuses(): array
    {
        return collect(self::AGREEMENTS)
            ->keys()
            ->mapWithKeys(fn (string $agreement) => [$agreement => $this->agreementStatus($agreement)])
            ->all();
    }

    public function mustSignAgreements(): bool
    {
        return collect($this->agreementStatuses())
            ->contains(fn (array $status) => \in_array($status['status'], self::BLOCKING_AGREEMENT_STATUSES));
    }

    public function hasExpiringAgreements(): bool
    {
        return collect($this->agreementStatuses())->contains('status', 'expiring');
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments()
            ->whereNull('state')
            ->where('fortnox_invoice_created_at', '<=', now()->subDays(self::OVERDUE_INVOICE_DAYS))
            ->exists();
    }
}
