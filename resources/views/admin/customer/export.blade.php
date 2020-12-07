<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customers as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->mobile }}</td>
            <td>{{ $user->status==0?'Inactive':($user->status==1?'Active':'Blocked') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
