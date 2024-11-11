<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 90%;
            margin: 0 auto;
            padding: 0;
        }

        .claim-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 90px;
        }

        .claim-info {
            text-align: right;
        }

        .claim-info h2 {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .claim-info p {
            font-size: 16px;
            margin: 5px 0;
            font-weight: bold;
        }

        .claim-info h3 {
            color: red;
            margin: 0;
            font-size: 20px;
        }

        .claim-details {
            margin-bottom: 30px;
            font-size: 16px;
        }

        .claim-details p {
            margin: 5px 0;
        }

        .services-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .services-table th,
        .services-table td {
            padding: 10px;
            text-align: center;
            font-size: 16px;
            border-bottom: 1px solid #ddd;
        }

        .services-table th {
            background-color: #f2f2f2;
        }

        .subtotal-row td {
            border-top: 2px solid black;
            font-weight: bold;
            font-size: 18px;
        }

        hr {
            border: none;
            height: 2px;
            background: black;
            margin-top: 50px;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px 0;
            border-top: 1px solid #ddd;
        }

        footer p {
            margin: 5px 0;
            font-size: 14px;
        }

        footer .contact-info {
            display: flex;
            justify-content: center;
            gap: 60px;
        }

        footer .thank-you {
            color: red;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="claim-header">
        <div class="logo">
            <img src="img/logo.png" alt="Logo">
        </div>
        <div class="claim-info">
            <h2>{{$title}}</h2>
            <p>For</p>
            <p style="text-decoration: underline;">{{ $name }}</p>
            <h3>No. {{ $id }}</h3>
        </div>
        <p style="position: absolute; top: 30%; right: 5%;">{{ date('F j, Y') }}</p>
    </div>

    <div class="claim-details">
        <p><strong>Company Name:</strong> {{ $name }}</p>
        <p><strong>TCN Number:</strong> {{ $id }}</p>
        <p><strong>DN Number:</strong> {{ $id }}</p>
        <p><strong>AWB | B/L:</strong> {{ $awb }}</p>
    </div>

    <table class="services-table">
        <thead>
            <tr>
                <th>QTY</th>
                <th>SERVICE</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($claims as $claim)
                <tr>
                    <td>{{ sprintf('%02d', $claim->quantity) }}</td>
                    <td>{{ $claim->note }}</td>
                    <td>EGP {{ number_format($claim->price, 2) }}</td>
                </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="2" style="text-align: right;"><strong>SUBTOTAL:</strong></td>
                <td style="color: red;">EGP {{ number_format(collect($claims)->sum('price'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p style="text-align: right; font-weight: bold;">7R Logistics</p>

    <div style="margin-bottom: 30px;">
        <p><strong>Please make payment to:</strong></p>
        <p><strong>Hossam Nassef</strong></p>
        <p><strong>1 Saad Zaghloul Street, Zagazig Minya Al Qamh Road, Sharqia, Egypt</strong></p>
    </div>

    <hr>

    <footer>
        <div class="contact-info">
            <p>Phone: +200 110 11677170</p>
            <p>Email: hossam.nassef@7RLogistics.net</p>
        </div>
        <div class="thank-you">
            <p>Thank You!</p>
        </div>
    </footer>
</body>

</html>
