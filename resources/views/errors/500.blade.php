@extends('errors.layout')

@section('title', 'Kesalahan Server')
@section('code', '500')
@section('message', 'Terjadi Kesalahan Server')
@section('icon')
    <span aria-hidden="true">⚠️</span>
@endsection

@section('description')
    Maaf, terjadi kesalahan internal pada server kami. Tim teknis kami telah diberitahu dan sedang bekerja untuk memperbaikinya.
@endsection
