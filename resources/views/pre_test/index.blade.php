<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pre Test</title>
    <style>
        .container{
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><center>PRE-TEST PROGRAMMER</center></h1>
        <h2>A. Logic Test</h2>
        <p>
            Sebuah kapal memiliki bagian lambung Kanan, Kiri, dan Tengah. Setiap kontainer yang akan
            dimuat ke atas kapal memiliki nomer kontainer dengan 7 (tujuh) numeric. Petugas menaruh
            posisi kontainer di atas kapal dengan kriteria tertentu, sebagai berikut:
        </p>
        <table border="1">
            <tr>
                <td>Posisi</td>
                <td>Ketentuan</td>
            </tr>
            <tr>
                <td>Tengah</td>
                <td>
                    (a) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
                    3 digit awal dihapus, maka tetap menjadi bilangan prima;
                </td>
            </tr>
            <tr>
                <td>Kanan</td>
                <td>
                    (a) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
                    3 (tiga) digit awal dihapus, 3 digit paling akhir merupakan
                    bilangan yang sama
                </td>
            </tr>
            <tr>
                <td>Kiri</td>
                <td>
                    (b) Id bilangan prima; (b) tidak mengandung angka 0; (c) apabila
                    3 (tiga) digit awal dihapus, 2 digit terakhir menjadi bilangan
                    prima yang berurutan angkanya;
                </td>
            </tr>
            <tr>
                <td>Reject</td>
                <td>
                    (a) Selain bilangan prima; (b) mengandung angka 0;
                </td>
            </tr>
        </table>
        <h2>Answer : </h2>
        <form action="{{ route('check') }}" method="POST">
            @csrf
            <label for="question">Silahkan Nomor Kontainer : </label>
            <br>
            <input type="number" name="nomor_kontainer" id="question">
            @if($errors->has("nomor_kontainer"))
                <br>
                <span style="color:red;font-size:10px;">{{ $errors->first('nomor_kontainer') }}</span>
            @endif
            <br><br>
            <input type="submit" value="Kirim">
        </form>

        @if(Session::has('nomor_kontainer'))
            <h3>Nomor Kontainer : {{ Session::get('nomor_kontainer') }}</h3>
            <h2>Posisi : {{ Session::get('status') }}</h2>
        @endif
    </div>
</body>
</html>