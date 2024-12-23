<?php

namespace App\Http\Controllers;

use App\Models\IntegrationToken;
use App\Services\Fortnox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FortnoxIntegrationController extends Controller
{
    public function activation(Request $request)
    {
        $baseURL = 'https://apps.fortnox.se/oauth-v1';
        $config = config('services.fortnox');

        $authorizationUrl = $baseURL.'/auth?'.\http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => config('app.url').'/fn/activation',
            'scope' => 'invoice customer article',
            'access_type' => 'offline',
            'response_type' => 'code',
            // Following OAuth security best practices, the state parameter should be a random string,
            // set in the session, and checked when the user is redirected back to the application.
            'state' => Str::random(40),
        ]);

        if ($request->code === null) {
            return redirect($authorizationUrl);
        }

        $response = Http::asForm()->withBasicAuth($config['client_id'], $config['client_secret'])->post($baseURL.'/token', [
            'code' => $request->code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('app.url').'/fn/activation',
        ]);

        $accessToken = $response->json()['access_token'];
        $refreshToken = $response->json()['refresh_token'];
        $expiresIn = $response->json()['expires_in'];
        $scope = $response->json()['scope'];

        IntegrationToken::create([
            'type' => 'fortnox',
            'scope' => $scope,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'access_token_expires_at' => now()->addSeconds($expiresIn),
        ]);

        return redirect()->route('fortnox.index');
    }

    public function index(Fortnox $fortnox)
    {
        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        return Http::withToken($fortnox->token())->get('https://api.fortnox.se/3/customers/1')->json();
    }

    public function pdfInvoice(Fortnox $fortnox, $invoice)
    {
        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$invoice}/preview");

        return response($response->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice.pdf"',
        ]);
    }

    public function emailInvoice(Fortnox $fortnox, $invoice)
    {
        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        $response = Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$invoice}/email");

        return $response->json();
    }

    public function invoices(Fortnox $fortnox, $invoice = null)
    {
        $invoices = [];

        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        if ($invoice) {
            return Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/invoices/{$invoice}")->json();
        }

        $page = 1;
        while (true) {
            $response = Http::withToken($fortnox->token())->get('https://api.fortnox.se/3/invoices', ['page' => $page]);
            $invoices = \array_merge($invoices, $response->json()['Invoices']);

            if ($response->json()['MetaInformation']['@TotalPages'] === $response->json()['MetaInformation']['@CurrentPage']) {
                break;
            }

            $page += 1;
        }

        return $invoices;
    }

    public function customers(Fortnox $fortnox, $customer = null)
    {
        $customers = [];

        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        if ($customer) {
            return Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/customers/{$customer}")->json();
        }

        $page = 1;
        while (true) {
            $response = Http::withToken($fortnox->token())->get('https://api.fortnox.se/3/customers', ['page' => $page]);
            $customers = \array_merge($customers, $response->json()['Customers']);

            if ($response->json()['MetaInformation']['@TotalPages'] === $response->json()['MetaInformation']['@CurrentPage']) {
                break;
            }

            $page += 1;
        }

        return $customers;
    }

    public function articles(Fortnox $fortnox, $article = null)
    {
        $articles = [];

        $token = $fortnox->token();

        if (! $token) {
            return redirect()->route('fortnox.activation');
        }

        if ($article) {
            return Http::withToken($fortnox->token())->get("https://api.fortnox.se/3/articles/{$article}")->json();
        }

        $page = 1;
        while (true) {
            $response = Http::withToken($fortnox->token())->get('https://api.fortnox.se/3/articles', ['page' => $page]);
            $articles = \array_merge($articles, $response->json()['Articles']);

            if ($response->json()['MetaInformation']['@TotalPages'] === $response->json()['MetaInformation']['@CurrentPage']) {
                break;
            }

            $page += 1;
        }

        return $articles;
    }

    public function disconnect()
    {
        IntegrationToken::where('type', 'fortnox')->delete();

        return redirect()->route('fortnox.index');
    }
}
