@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan')
@section('code', '404')
@section('message', 'Halaman Tidak Ditemukan')
@section('icon')
    <i class="fas fa-search"></i>
@endsection

@section('description')
    Maaf, kami tidak dapat menemukan halaman yang Anda cari. Mungkin URL yang Anda masukkan salah atau halaman tersebut telah dipindahkan.
@endsection