<!-- resources/views/rajaongkir/cities.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kota</title>
</head>
<body>
    <h1>Daftar Kota</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID Kota</th>
                <th>Nama Kota</th>
                <th>Provinsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cities as $city)
                <tr>
                    <td>{{ $city['city_id'] }}</td>
                    <td>{{ $city['city_name'] }}</td>
                    <td>{{ $city['province'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
