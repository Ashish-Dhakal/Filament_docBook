<table class="table-auto w-full">
    <thead>
        <tr>
            <!-- Loop through the columns to display table headers -->
            @foreach($columns as $column)
                <th class="px-4 py-2 text-left">{{ ucfirst($column) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Loop through the rows to display each schedule's data -->
        @foreach($rows as $row)
            <tr>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($row['date'])->format('l, F j, Y') }}</td> <!-- Format the date -->
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($row['start_time'])->format('h:i A') }} - {{ \Carbon\Carbon::parse($row['end_time'])->format('h:i A') }}</td> <!-- Format start and end time -->
                <td class="px-4 py-2">
                    <!-- Display status in a more readable form (e.g., Active/Inactive, or colors) -->
                    <span class="inline-block px-2 py-1 text-white 
                    @if($row['status'] === 'active') bg-green-500 @elseif($row['status'] === 'inactive') bg-red-500 @else bg-gray-500 @endif">
                        {{ ucfirst($row['status']) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
