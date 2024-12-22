<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email</title>
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="w-screen h-screen flex items-center justify-center">
    <section class="relative isolate overflow-hidden bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl lg:max-w-4xl">
            <x-application-logo class="w-40 fill-current my-3 mx-auto" />

            <figure class="mt-10">
                <blockquote class="text-center text-xl/8 font-semibold text-gray-900 sm:text-2xl/9">
                    @if($success)
                        <h1
                            class="head-title mb-8 text-6xl font-extrabold bg-gradient-to-r from-green-500 to-blue-400 bg-clip-text text-transparent">
                            Verifikasi Berhasil</h1>
                        <p>
                        Email Anda telah berhasil diverifikasi. Salam teropong Sejarah Tuban.
                        </p>
                    @else
                        <h1
                            class="head-title mb-8 text-6xl font-extrabold bg-gradient-to-r from-red-500 to-red-800 bg-clip-text text-transparent">
                            Verifikasi Gagal</h1>
                        <p>Maaf, email Anda gagal diverifikasi. Silahkan coba lagi.</p>
                    @endif
                </blockquote>
                <figcaption class="mt-10">
                    <div class="mt-4 flex items-center justify-center space-x-3 text-base">
                        <div class="text-gray-600">Agni Guide</div>
                    </div>
                </figcaption>
            </figure>
        </div>
    </section>

</div>
</body>
</html>
