@extends('layout.base')

@section('title-head')
    <title>Dashboard Equipment AFC</title>
    <style>
        .svg-container svg {
            width: 100%;
            height: auto;
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 stretch-card grid-margin">
                <div class="svg-container">
                    @include('layout.svg.' . ($station_code ?? 'default'))
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipments = @json($results);

            equipments.forEach(eq => {
                const svgElement = document.getElementById(eq.id);
                if (svgElement) {
                    // Hapus class 'online' dan 'offline' dulu
                    svgElement.classList.remove('online', 'offline');

                    // Tambah class sesuai status
                    svgElement.classList.add(eq.status);
                }
            });
        });
    </script>
@endsection
