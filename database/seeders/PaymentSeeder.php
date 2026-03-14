<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'unpaid-membership@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Membership',
        ]);

        $user->payments()->create([
            'sek_amount' => 1500,
            'type' => 'MEMBERSHIP',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-discounted-membership@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Discounted Membership',
        ]);

        $user->payments()->create([
            'sek_amount' => 700,
            'sek_discount' => 0, // This is a separate article, discount is used for half year memberships
            'modifier' => 'AGE_DISCOUNT',
            'type' => 'MEMBERSHIP',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-ssf-license@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'SSF License',
        ]);

        $user->payments()->create([
            'sek_amount' => 900,
            'type' => 'SSFLICENSE',
            'year' => 2025,
        ]);

        $user = User::factory()->create([
            'email' => 'unpaid-competition-fee@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Competition Fee',
        ]);

        $user->payments()->create([
            'sek_amount' => 500,
            'type' => 'COMPETITION',
            'year' => 2025,
        ]);

        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            $admin->payments()->create([
                'sek_amount' => 1500,
                'type' => 'MEMBERSHIP',
                'year' => 2025,
                'state' => 'PAID',
                'fortnox_invoice_document_number' => 'SEED-001',
            ]);

            $this->generateDummyInvoicePdf();
        }

        $user = User::factory()->create([
            'email' => 'unpaid-other-fee@example.com',
            'first_name' => 'Unpaid',
            'last_name' => 'Other Fee',
        ]);

        $user->payments()->create([
            'sek_amount' => 300,
            'type' => 'OTHER',
            'year' => 2025,
        ]);
    }

    private function generateDummyInvoicePdf(): void
    {
        $pdf = <<<'PDF'
%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 595 842]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>endobj
4 0 obj
<</Length 244>>
stream
BT
/F1 28 Tf
50 780 Td
(Faktura) Tj
/F1 12 Tf
0 -40 Td
(Fakturanummer: SEED-001) Tj
0 -20 Td
(Datum: 2025-01-15) Tj
0 -20 Td
(Forfallodag: 2025-02-15) Tj
0 -30 Td
(Medlemsavgift 2025) Tj
0 -20 Td
(Belopp: 1 500,00 SEK) Tj
0 -20 Td
(Status: Betald) Tj
ET
endstream
endobj
5 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
xref
0 6
0000000000 65535 f
0000000009 00000 n
0000000058 00000 n
0000000115 00000 n
0000000266 00000 n
0000000562 00000 n
trailer<</Size 6/Root 1 0 R>>
startxref
634
%%EOF
PDF;

        \file_put_contents(storage_path('app/dummy-invoice.pdf'), $pdf);
    }
}
