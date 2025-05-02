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
            // Dapatkan semua elemen rect dengan id dari 4 sampai 17
            for (let i = 4; i <= 17; i++) {
                const rect = document.getElementById(i.toString());
                if (rect) {
                    // Tambahkan event listener untuk setiap rect
                    rect.addEventListener('click', function() {
                        // Toggle antara class online dan offline
                        if (this.classList.contains('online')) {
                            this.classList.remove('online');
                            this.classList.add('offline');
                        } else {
                            this.classList.remove('offline');
                            this.classList.add('online');
                        }
                    });
                }
            }
        });
    </script>
@endsection
