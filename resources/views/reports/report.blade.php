<!DOCTYPE html>
<html>
<head>
    <title>Hotel Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Hotel Report</h1>

    <h2>Income Report</h2>
    <table>
        <tr>
            <th>Floor</th>
            <th>Room Type</th>
            <th>Month</th>
            <th>Income</th>
        </tr>
        <tr>
            <td>{{ request('floor') }}</td>
            <td>{{ request('type') }}</td>
            <td>{{ request('month') }}</td>
            <td>Rp {{ number_format($income, 2) }}</td>
        </tr>
    </table>

    <h3>Detailed Transactions</h3>
    <table>
        <tr>
            <th>Room</th>
            <th>Check-In Date</th>
            <th>Check-Out Date</th>
            <th>Payment</th>
        </tr>
        @foreach ($incomeData as $transaction)
        <tr>
            <td>{{ $transaction->room->name_room }}</td>
            <td>{{ $transaction->checkIn_date }}</td>
            <td>{{ $transaction->checkOut_date }}</td>
            <td>Rp {{ number_format($transaction->payment, 2) }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Payroll Report</h2>
    <table>
        <tr>
            <th>Job Name</th>
            <th>Payroll</th>
        </tr>
        <tr>
            <td>{{ request('job_name') }}</td>
            <td>Rp {{ number_format($payroll, 2) }}</td>
        </tr>
    </table>

    <h3>Detailed Payroll</h3>
    <table>
        <tr>
            <th>Worker</th>
            <th>Job</th>
        </tr>
        @foreach ($payrollData as $worker)
        <tr>
            <td>{{ $worker->name_worker }}</td>
            <td>{{ $worker->job->name_job }}</td>
        </tr>
        @endforeach
    </table>

    <h2>Profit</h2>
    <table>
        <tr>
            <th>Profit</th>
        </tr>
        <tr>
            <td>Rp {{ number_format($profit, 2) }}</td>
        </tr>
    </table>
</body>
</html>
