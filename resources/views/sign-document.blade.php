@extends('layouts.app')

@section('head')
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="mt-24 mb-8">
    <gkk-sign-document
        :signer='@json($signer)'
        :document-name='@json($documentName)'
        :pdf-url='@json($pdfUrl)'
        :fields='@json($fields)'
        :post-url='@json($postUrl)'
    ></gkk-sign-document>
</div>
@endsection
