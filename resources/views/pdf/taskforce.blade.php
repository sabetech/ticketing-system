<!DOCTYPE html>
<html>
<head>
	<title>Taskforce Report</title>
</head>
<body>
	<h1>{{ $title }}</h1>
    <?php
        $totalAmount = 0;
        $totalNumberOfTaskforceTickets = 0;
    ?>
    <table>
        <thead>
          <tr>
            <th>Date Time Issued</th>
            <th>Car Number</th>
            <th>Amount</th>
            <th>Agent</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sales as $taskforceInfo)
            <?php
                $totalAmount += floatval($taskforceInfo->amount);
                $totalNumberOfTaskforceTickets++;
            ?>
            <tr>
              <td>{{ $taskforceInfo->issued_date_time }}</td>
              <td>{{ $taskforceInfo->car_number }}</td>
              <td>{{ $taskforceInfo->amount }}</td>
              <td>{{ $taskforceInfo->fname ." ". $taskforceInfo->lname}}</td>
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


</body>
</html>
