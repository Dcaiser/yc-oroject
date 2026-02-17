@extends('errors.layout')

@section('title', 'Terlalu Banyak Permintaan')
@section('code', '429')
@section('message', 'Terlalu Banyak Permintaan')
@section('icon')
    <span aria-hidden="true">⛔</span>
@endsection

@section('description')
    Maaf, Anda telah mengirim terlalu banyak permintaan ke server kami dalam waktu singkat. Silakan tunggu beberapa saat sebelum mencoba lagi.
@endsection
