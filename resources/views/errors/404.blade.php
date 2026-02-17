@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('message', 'Halaman Tidak Ditemukan')
@section('icon')
    <span aria-hidden="true">🔍</span>
@endsection

@section('description')
    Maaf, kami tidak dapat menemukan halaman yang Anda cari. Mungkin URL yang Anda masukkan salah atau halaman tersebut telah dipindahkan.
@endsection
