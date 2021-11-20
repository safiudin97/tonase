<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing {{ $billing->billing_no }}</title>

<style type="text/css">
    * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
    }
    .gray {
        background-color: lightgray
    }
</style>

</head>
<body>

  <table width="100%">
    <tr>
        <td align="right">
            <h3>Tonase</h3>
            <pre>
                Company representative name
                Company address
                Tax ID
                phone
                fax
            </pre>
        </td>
    </tr>

  </table>

  <table width="100%">
    <tr>
        <td><strong>Billing Number: </strong>{{ $billing->billing_no }}</td>
        <td><strong>Date:</strong> {{ $billing->payment_time }}</td>
        <td><strong>Time:</strong> {{ $billing->payment_time }}</td>
    </tr>

  </table>

  <br/>

  <table width="100%">
    <thead style="background-color: lightgray;">
      <tr>
        <th>#</th>
        <th>Description</th>
        <th>Amount $</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($billing_detail as $key => $item)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->amount }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td align="right" class="gray" colspan="2">Total</td>
            <td align="right" class="gray">{{ $billing->total_amount }}</td>
        </tr>
    </tfoot>
  </table>

</body>
</html>