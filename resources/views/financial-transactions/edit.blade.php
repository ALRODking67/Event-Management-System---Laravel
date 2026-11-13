@extends('layouts.ems')

@section('title', 'Edit Transaksi')
@section('page-heading', 'Edit Transaksi Keuangan')

@section('content')
@include('financial-transactions.form', [
    'formTitle' => 'Perbarui Data Transaksi',
    'formAction' => route('financial-transactions.update', [
        'organization' => $organization->slug,
        'transaction' => $financialTransaction->id
    ]),
    'method' => 'PUT',
    'financialTransaction' => $financialTransaction
])
@endsection