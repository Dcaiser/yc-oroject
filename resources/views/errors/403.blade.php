@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('message', 'Akses Ditolak')
@section('icon')
    <i class="fas fa-lock"></i>
@endsection

@section('description')
    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Pastikan Anda login dengan akun yang memiliki hak akses yang sesuai.
@endsection