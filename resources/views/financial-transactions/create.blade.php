@extends('layouts.ems')

@section('title', 'Tambah Transaksi')
@section('page-heading', 'Tambah Transaksi Keuangan')

@section('content')
@include('financial-transactions.form', [
    'formTitle' => 'Input Transaksi Baru',
    'formAction' => route('financial-transactions.store', ['organization' => $organization->slug]),
    'method' => 'POST',
    'financialTransaction' => null
])
@endsection