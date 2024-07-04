<!DOCTYPE html>
<html>
<head>
	<title>Taskforce Report</title>
    <style>
        /* Your PDF styling here */
        .main {
            margin: 1em
        },
        table {
           border-collapse: collapse; /* Ensures borders between cells */
        }
        th, td {
            border: 1px solid #ddd; /* Define 1px solid gray border for all cells */
            padding: 5px; /* Add padding for readability */
        }
        .table-header {
            background-color: #D3D3D3
        }
        .table {
            border-color: #D3D3D3
            border-radius: 5px
            border-width: 1px
        }
        .row {
            margin-bottom: 10px
            border-width: 1px
        }
        .amount {
            text-align: center,
            width: '15%'
        }
        .date_time {
            text-align: center,
            width: '20%'
        }
        .car_number {
            text-align: center
        }
        .agent {
            text-align: center;
            padding: 10px
        }

    </style>
</head>
<body>
    <div class="main">
        <h3 style="text-align: center">{{$title}}</h3>

        <?php
            $totalAmount = 0;
            $totalNumberOfTaskforceTickets = 0;
        ?>
        <table >
        <thead class="table-header">
          <tr>
            <th>Date Time Issued</th>
            <th>Car Number</th>
            <th class="amount">Amount</th>
            <th class="agent">Agent</th>
          </tr>
        </thead>
        <tbody>
          @foreach($data as $taskforceInfo)
            <?php
                $totalAmount += floatval($taskforceInfo->amount);
                $totalNumberOfTaskforceTickets++;
            ?>
            <tr>
              <td class="date_time">{{ $taskforceInfo->issued_date_time }}</td>
              <td class="car_number">{{ $taskforceInfo->car_number }}</td>
              <td class="amount">{{ $taskforceInfo->amount }}</td>
              <td class="agent">{{ $taskforceInfo->fname ." ". $taskforceInfo->lname}}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total Amount</td>
                <td>{{ $totalAmount }}</td>
                <td>Total Number of Taskforce Tickets</td>
                <td>{{ $totalNumberOfTaskforceTickets }}</td>
            </tr>
        </tfoot>
      </table>


    </div>
    </body>
</html>
