@extends('errors.layout')

@section('title', 'Layanan Tidak Tersedia')
@section('code', '503')
@section('message', 'Sedang Dalam Pemeliharaan')
@section('icon')
    <span aria-hidden="true">🛠️</span>
@endsection

@section('description')
    Sistem sedang dalam pemeliharaan rutin untuk meningkatkan performa dan keamanan. Silakan coba beberapa saat lagi.
@endsection
