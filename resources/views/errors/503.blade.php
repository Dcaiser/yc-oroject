@extends('errors.layout')

@section('title', 'Layanan Tidak Tersedia')
@section('code', '503')
@section('message', 'Sedang Dalam Pemeliharaan')
@section('icon')
    <i class="fas fa-cog"></i>
@endsection

@section('description')
    Sistem sedang dalam pemeliharaan rutin untuk meningkatkan performa dan keamanan. Silakan coba beberapa saat lagi.
@endsection