<?php

namespace App\Http\Controllers;

use App\Mail\UnpaidFeeReminderMail;
use App\Models\Competition;
use App\Models\CompetitionRegistration;
use App\Models\Payment;
use App\Models\User;
use App\Services\Fortnox;
use App\SSFLicense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class AdminPaymentToolsController extends Controller
{
    public function index()
    {
        $competitions = Competition::visible()
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'date' => $c->date?->format('Y-m-d'),
            ]);

        return view('admin.payment-tools', [
            'competitions' => $competitions,
            'user' => auth()->user(),
            'view' => 'payment-tools',
        ]);
    }

    // --- Generate Membership Payments ---

    public function previewGenerateMemberships(Request $request)
    {
        $year = $request->input('year', now()->year);
        $users = $this->membersEligibleForPayment($year);

        return response()->json([
            'count' => $users->count(),
            'message' => "{$users->count()} aktiva medlemmar saknar medlemsavgift för {$year}.",
        ]);
    }

    public function executeGenerateMemberships(Request $request)
    {
        $year = $request->input('year', now()->year);
        $users = $this->membersEligibleForPayment($year);

        $count = 0;

        foreach ($users as $user) {
            $discountedByAge = ((int) $year - $user->birth_year) <= 23 || ((int) $year - $user->birth_year) >= 65;
            $discountedByStudentState = $user->is_student_over_23;

            $sekAmount = ($discountedByAge || $discountedByStudentState) ? 700 : 1500;

            $sekDiscount = 0;

            if (now()->month > 6 || (now()->month === 6 && now()->day > 15)) {
                $sekDiscount = (int) ($sekAmount / 2);
            }

            if (now()->month > 9 || (now()->month === 9 && now()->day > 15)) {
                $sekDiscount = (int) ($sekAmount / 4) * 3;
            }

            $user->payments()->create([
                'type' => 'MEMBERSHIP',
                'year' => $year,
                'sek_amount' => $sekAmount,
                'sek_discount' => $sekDiscount,
                'modifier' => $discountedByAge ? 'AGE_DISCOUNT' : ($discountedByStudentState ? 'STUDENT_DISCOUNT' : null),
            ]);

            $count++;
        }

        return response()->json([
            'message' => "{$count} medlemsavgifter skapade för {$year}.",
            'count' => $count,
        ]);
    }

    // --- Generate License Payments ---

    public function previewGenerateLicenses(Request $request)
    {
        $year = $request->input('year', now()->year);
        $users = $this->usersNeedingLicense($year);

        return response()->json([
            'count' => $users->count(),
            'message' => "{$users->count()} tävlingsregistrerade användare saknar licens för {$year}.",
        ]);
    }

    public function executeGenerateLicenses(Request $request)
    {
        $year = $request->input('year', now()->year);
        $users = $this->usersNeedingLicense($year);

        foreach ($users as $user) {
            SSFLicense::createFor($user, $year);
        }

        return response()->json([
            'message' => "{$users->count()} licensavgifter skapade för {$year}.",
            'count' => $users->count(),
        ]);
    }

    // --- Create Competition Payments ---

    public function previewCreateCompetitionPayments(Request $request)
    {
        $competitionId = $request->input('competition_id');

        $competition = Competition::findOrFail($competitionId);
        $currentYear = now()->year;
        $juniorCutoffYear = $currentYear - 23;

        $registrations = $this->activeCompetitionRegistrations($competitionId);

        $userIds = $registrations->pluck('user.id');
        $existingCount = Payment::where('type', 'COMPETITION')
            ->where('year', $currentYear)
            ->where('competition_id', $competitionId)
            ->whereIn('user_id', $userIds)
            ->count();

        $newCount = $registrations->count() - $existingCount;

        $seniors = $registrations->filter(function (CompetitionRegistration $r) use ($juniorCutoffYear) {
            /** @var User $user */
            $user = $r->user;

            return ! $user->birth_year || $user->birth_year <= $juniorCutoffYear;
        })->count();
        $juniors = $registrations->filter(function (CompetitionRegistration $r) use ($juniorCutoffYear) {
            /** @var User $user */
            $user = $r->user;

            return $user->birth_year && $user->birth_year > $juniorCutoffYear;
        })->count();

        return response()->json([
            'count' => $newCount,
            'total_registered' => $registrations->count(),
            'existing' => $existingCount,
            'seniors' => $seniors,
            'juniors' => $juniors,
            'competition_name' => $competition->name,
            'message' => "{$newCount} nya betalningar att skapa för {$competition->name} ({$seniors} seniorer, {$juniors} juniorer).",
        ]);
    }

    public function executeCreateCompetitionPayments(Request $request)
    {
        $competitionId = $request->input('competition_id');
        $seniorAmount = (int) $request->input('senior_amount');
        $juniorAmount = (int) $request->input('junior_amount');

        $currentYear = now()->year;
        $juniorCutoffYear = $currentYear - 23;

        $registrations = $this->activeCompetitionRegistrations($competitionId);

        $created = 0;
        $skipped = 0;

        foreach ($registrations as $registration) {
            /** @var User $user */
            $user = $registration->user;

            $exists = Payment::where('type', 'COMPETITION')
                ->where('year', $currentYear)
                ->where('user_id', $user->id)
                ->where('competition_id', $competitionId)
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            $isJunior = $user->birth_year && $user->birth_year > $juniorCutoffYear;
            $amount = $isJunior ? $juniorAmount : $seniorAmount;

            Payment::create([
                'user_id' => $user->id,
                'competition_id' => $competitionId,
                'type' => 'COMPETITION',
                'year' => $currentYear,
                'sek_amount' => $amount,
                'sek_discount' => 0,
                'state' => null,
                'modifier' => null,
            ]);

            $created++;
        }

        return response()->json([
            'message' => "{$created} tävlingsavgifter skapade, {$skipped} hoppade över.",
            'created' => $created,
            'skipped' => $skipped,
        ]);
    }

    // --- Sync Fortnox Customers ---

    public function previewSyncCustomers()
    {
        $count = $this->usersWithoutFortnoxId()->count();

        return response()->json([
            'count' => $count,
            'message' => "{$count} aktiva användare saknar Fortnox-kundnummer.",
        ]);
    }

    public function executeSyncCustomers(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            return response()->json(['error' => 'Fortnox-integrationen är inte aktiverad.'], 422);
        }

        $users = $this->usersWithoutFortnoxId()->get();
        $total = $users->count();

        return response()->stream(function () use ($users, $total, $fortnox) {
            foreach ($users as $index => $user) {
                if ($index > 0 && $index % 5 === 0) {
                    \sleep(2);
                }

                $todaysDate = \date('Y-m-d');
                $response = Http::withToken($fortnox->token())->post('https://api.fortnox.se/3/customers', [
                    'Customer' => [
                        'CustomerNumber' => "GKK-{$todaysDate}-{$user->id}",
                        'Name' => "{$user->first_name} {$user->last_name}",
                        'Email' => $user->email,
                        'Comments' => 'Created by GKK integration on '.\date('Y-m-d H:i:s'),
                        'Type' => 'PRIVATE',
                    ],
                ]);

                $status = 'ok';

                if ($response->failed()) {
                    $status = 'error';
                } else {
                    $user->update([
                        'fortnox_customer_id' => $response->json()['Customer']['CustomerNumber'],
                    ]);
                }

                echo \json_encode([
                    'current' => $index + 1,
                    'total' => $total,
                    'status' => $status,
                    'message' => "{$user->first_name} {$user->last_name}",
                ])."\n";
                \ob_flush();
                \flush();
            }

            echo \json_encode(['done' => true, 'processed' => $total])."\n";
            \ob_flush();
            \flush();
        }, 200, ['Content-Type' => 'text/plain', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }

    // --- Create Fortnox Invoices ---

    public function previewCreateInvoices(Request $request)
    {
        $type = $request->input('type', 'MEMBERSHIP');
        $year = $request->input('year', now()->year);

        $count = $this->uninvoicedPayments($type, $year)->count();
        $description = $this->paymentTypeDescription($type);

        return response()->json([
            'count' => $count,
            'message' => "{$count} {$description}-betalningar för {$year} saknar faktura.",
        ]);
    }

    public function executeCreateInvoices(Request $request, Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            return response()->json(['error' => 'Fortnox-integrationen är inte aktiverad.'], 422);
        }

        $type = $request->input('type', 'MEMBERSHIP');
        $year = $request->input('year', now()->year);

        $description = $this->paymentTypeDescription($type);

        // Ensure articles exist
        $this->ensureArticleExists($fortnox, "GKK-{$type}-{$year}", "{$description} {$year}");

        if ($type === 'MEMBERSHIP' || $type === 'SSFLICENSE') {
            $this->ensureArticleExists($fortnox, "GKK-{$type}-{$year}-DISCOUNT", "{$description} {$year}, rabatterad");
        }

        $payments = $this->uninvoicedPayments($type, $year)->with('competition')->get();
        $total = $payments->count();

        return response()->stream(function () use ($payments, $total, $fortnox) {
            foreach ($payments as $index => $payment) {
                if ($index > 0 && $index % 5 === 0) {
                    \sleep(2);
                }

                /** @var User $user */
                $user = $payment->user;
                $articleNumber = "GKK-{$payment->type}-{$payment->year}";

                if (\in_array($payment->modifier, ['AGE_DISCOUNT', 'STUDENT_DISCOUNT', 'YOUTH'])) {
                    $articleNumber .= '-DISCOUNT';
                }

                $invoiceRow = [
                    'ArticleNumber' => $articleNumber,
                    'Price' => $payment->sek_amount,
                    'Discount' => $payment->sek_discount,
                    'DiscountType' => 'AMOUNT',
                    'DeliveredQuantity' => 1,
                    'VAT' => 0,
                ];

                if ($payment->type === 'COMPETITION') {
                    $invoiceRow['AccountNumber'] = 3410;
                    $competition = $payment->competition;

                    /** @var ?Competition $competition */
                    if ($competition) {
                        $competitionName = $competition->name;
                        $competitionDate = $competition->date ? $competition->date->format('Y-m-d') : null;
                        $desc = $competitionName;

                        if ($competitionDate) {
                            $desc .= " ({$competitionDate})";
                        }
                        $invoiceRow['Description'] = $desc;
                    }
                }

                $data = [
                    'Invoice' => [
                        'DueDate' => now()->addDays(30)->format('Y-m-d'),
                        'Comments' => 'Created by GKK integration',
                        'CustomerNumber' => $user->fortnox_customer_id,
                        'OurReference' => 'GKK Kassör',
                        'Country' => 'Sverige',
                        'InvoiceRows' => [$invoiceRow],
                    ],
                ];

                $response = Http::withToken($fortnox->token())->post('https://api.fortnox.se/3/invoices', $data);

                $status = 'ok';
                $message = '';

                if ($response->successful()) {
                    $invoice = $response->json()['Invoice'];
                    $payment->update([
                        'fortnox_invoice_document_number' => $invoice['DocumentNumber'],
                        'fortnox_invoice_created_at' => now(),
                    ]);
                    $message = "Faktura {$invoice['DocumentNumber']} skapad för {$user->first_name} {$user->last_name}";
                } else {
                    $status = 'error';
                    $message = "Fel vid skapande av faktura för {$user->first_name} {$user->last_name}";
                }

                echo \json_encode([
                    'current' => $index + 1,
                    'total' => $total,
                    'status' => $status,
                    'message' => $message,
                ])."\n";
                \ob_flush();
                \flush();
            }

            echo \json_encode(['done' => true, 'processed' => $total])."\n";
            \ob_flush();
            \flush();
        }, 200, ['Content-Type' => 'text/plain', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }

    // --- Email Invoices ---

    public function previewEmailInvoices()
    {
        $count = $this->unemailedInvoices()->count();

        return response()->json([
            'count' => $count,
            'message' => "{$count} fakturor har inte skickats via email.",
        ]);
    }

    public function executeEmailInvoices(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            return response()->json(['error' => 'Fortnox-integrationen är inte aktiverad.'], 422);
        }

        $payments = $this->unemailedInvoices()->with('user')->get();
        $total = $payments->count();

        return response()->stream(function () use ($payments, $total, $fortnox) {
            foreach ($payments as $index => $payment) {
                if ($index > 0 && $index % 5 === 0) {
                    \sleep(2);
                }

                /** @var User $user */
                $user = $payment->user;

                Http::withToken($fortnox->token())
                    ->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}/email");

                $payment->update(['fortnox_invoice_emailed_at' => now()]);

                echo \json_encode([
                    'current' => $index + 1,
                    'total' => $total,
                    'status' => 'ok',
                    'message' => "Faktura skickad till {$user->email}",
                ])."\n";
                \ob_flush();
                \flush();
            }

            echo \json_encode(['done' => true, 'processed' => $total])."\n";
            \ob_flush();
            \flush();
        }, 200, ['Content-Type' => 'text/plain', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }

    // --- Verify Payments ---

    public function previewVerifyPayments()
    {
        $count = $this->unverifiedPayments()->count();

        return response()->json([
            'count' => $count,
            'message' => "{$count} fakturor är inte markerade som betalda.",
        ]);
    }

    public function executeVerifyPayments(Fortnox $fortnox)
    {
        if (! $fortnox->token()) {
            return response()->json(['error' => 'Fortnox-integrationen är inte aktiverad.'], 422);
        }

        $payments = $this->unverifiedPayments()->with('user')->get();
        $total = $payments->count();

        return response()->stream(function () use ($payments, $total, $fortnox) {
            $paidCount = 0;

            foreach ($payments as $index => $payment) {
                if ($index > 0 && $index % 5 === 0) {
                    \sleep(2);
                }

                /** @var User $user */
                $user = $payment->user;
                $response = Http::withToken($fortnox->token())
                    ->get("https://api.fortnox.se/3/invoices/{$payment->fortnox_invoice_document_number}");

                $paid = $response->json('Invoice.FinalPayDate') !== null;

                if ($paid) {
                    $payment->update(['state' => 'PAID']);
                    $paidCount++;
                }

                echo \json_encode([
                    'current' => $index + 1,
                    'total' => $total,
                    'status' => $paid ? 'paid' : 'unpaid',
                    'message' => "{$user->first_name} {$user->last_name} - Faktura {$payment->fortnox_invoice_document_number}: ".($paid ? 'Betald' : 'Ej betald'),
                ])."\n";
                \ob_flush();
                \flush();
            }

            echo \json_encode(['done' => true, 'processed' => $total, 'paid' => $paidCount])."\n";
            \ob_flush();
            \flush();
        }, 200, ['Content-Type' => 'text/plain', 'X-Accel-Buffering' => 'no', 'Cache-Control' => 'no-cache']);
    }

    // --- Remind Unpaid Fees ---

    public function previewRemindUnpaidFees(Request $request)
    {
        $type = $request->input('type', 'ALL');
        $payments = $this->overdueUnpaidPayments($type);

        $items = $payments->map(function (Payment $p) {
            /** @var User $user */
            $user = $p->user;

            return [
                'user' => "{$user->first_name} {$user->last_name}",
                'email' => $user->email,
                'type' => $p->type,
                'year' => $p->year,
                'amount' => $p->sek_amount - $p->sek_discount,
            ];
        });

        return response()->json([
            'count' => $payments->count(),
            'items' => $items,
            'message' => "{$payments->count()} obetalda avgifter (fakturerade för 30+ dagar sedan).",
        ]);
    }

    public function executeRemindUnpaidFees(Request $request)
    {
        $type = $request->input('type', 'ALL');
        $payments = $this->overdueUnpaidPayments($type);
        $sentCount = 0;

        foreach ($payments as $payment) {
            /** @var User $user */
            $user = $payment->user;
            Mail::to($user->email)->send(new UnpaidFeeReminderMail($user, $payment));
            $sentCount++;
        }

        return response()->json([
            'message' => "{$sentCount} påminnelser skickade.",
            'count' => $sentCount,
        ]);
    }

    // --- Shared query helpers ---

    /**
     * @return Collection<int, User>
     */
    private function membersEligibleForPayment(mixed $year): Collection
    {
        return User::whereNull('inactivated_at')
            ->where('is_honorary_member', false)
            ->whereNotExists(function ($query) use ($year) {
                $query->select('id')
                    ->from('payments')
                    ->whereColumn('payments.user_id', 'users.id')
                    ->where('payments.type', 'MEMBERSHIP')
                    ->where('payments.year', $year);
            })
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    private function usersNeedingLicense(mixed $year): Collection
    {
        $yearCompetitions = Competition::whereBetween('date', ["{$year}-01-01", "{$year}-12-31"])->get();
        $registeredUserIds = $yearCompetitions->flatMap(function ($competition) {
            return $competition->registrations->where('status', true)->pluck('user_id');
        })->unique();

        $registeredUsers = User::whereIn('id', $registeredUserIds)->get();

        return $registeredUsers->filter(
            fn ($user) => ! $user->payments()->where('type', 'SSFLICENSE')->where('year', $year)->exists(),
        );
    }

    /**
     * @return Collection<int, CompetitionRegistration>
     */
    private function activeCompetitionRegistrations(mixed $competitionId): Collection
    {
        return CompetitionRegistration::where('competition_id', $competitionId)
            ->where('status', 1)
            ->with('user')
            ->get();
    }

    /**
     * @return Builder<User>
     */
    private function usersWithoutFortnoxId(): Builder
    {
        return User::whereNull('inactivated_at')->whereNull('fortnox_customer_id');
    }

    /**
     * @return Builder<Payment>
     */
    private function uninvoicedPayments(mixed $type, mixed $year): Builder
    {
        return Payment::where('year', $year)
            ->whereNull('fortnox_invoice_document_number')
            ->whereNull('state')
            ->where('type', $type);
    }

    /**
     * @return Builder<Payment>
     */
    private function unemailedInvoices(): Builder
    {
        return Payment::whereNotNull('fortnox_invoice_document_number')
            ->whereNull('fortnox_invoice_emailed_at');
    }

    /**
     * @return Builder<Payment>
     */
    private function unverifiedPayments(): Builder
    {
        return Payment::whereNotNull('fortnox_invoice_document_number')
            ->whereNull('state');
    }

    /**
     * @return Collection<int, Payment>
     */
    private function overdueUnpaidPayments(string $type): Collection
    {
        $query = Payment::whereNull('state')
            ->where('fortnox_invoice_emailed_at', '<=', now()->subDays(30))
            ->with('user');

        if ($type !== 'ALL') {
            $query->where('type', $type);
        }

        return $query->get();
    }

    private function paymentTypeDescription(mixed $type): string
    {
        return match ($type) {
            'MEMBERSHIP' => 'Medlemsavgift',
            'SSFLICENSE' => 'Tävlingslicens',
            'COMPETITION' => 'Tävlingsavgift',
            'OTHER' => 'Övrig avgift',
            default => $type,
        };
    }

    private function ensureArticleExists(Fortnox $fortnox, string $articleNumber, string $description): void
    {
        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/articles/{$articleNumber}");

        if ($response->status() === 200) {
            return;
        }

        Http::withToken($fortnox->token())->post('https://api.fortnox.se/3/articles', [
            'Article' => [
                'ArticleNumber' => $articleNumber,
                'Description' => $description,
            ],
        ]);
    }
}
