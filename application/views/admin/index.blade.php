<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Pass Types</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 p-6">

<div class="max-w-7xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">📋 Pass Type Management</h1>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($error))
        <div class="mb-4 p-4 rounded-lg bg-red-100 border border-red-400 text-red-700">
            {{ $error }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow-md p-6">
        <table class="min-w-full divide-y divide-gray-200 px-4 py-6">
            <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TCODE</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TNAME</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">EMPTYPE</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TNAME_HINDI</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">TCATEGORY</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">AC_UPDATE</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">FIXED_DATES</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ALLOWED</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            @foreach ($pass_types as $row)
                <tr class="hover:bg-gray-50 transition">
                    <form method="POST" action="{{ site_url('admin/pass_type/update/'.$row['TCODE']) }}">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $row['TCODE'] }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <input type="text" name="tname" value="{{ $row['TNAME'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <input type="text" name="emptype" value="{{ $row['EMPTYPE'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <input type="text" name="tname_hindi" value="{{ $row['TNAME_HINDI'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            <input type="text" name="tcategory" value="{{ $row['TCATEGORY'] }}" class="border border-gray-300 rounded px-2 py-1 text-sm w-full">
                        </td>
                        <td class="px-4 py-2">
                            <select name="ac_update" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="Y" @if($row['AC_UPDATE'] == 'Y') selected @endif>Y</option>
                                <option value="N" @if($row['AC_UPDATE'] == 'N') selected @endif>N</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <select name="fixed_dates" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="Y" @if($row['FIXED_DATES'] == 'Y') selected @endif>Y</option>
                                <option value="N" @if($row['FIXED_DATES'] == 'N') selected @endif>N</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <select name="allowed" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="Y" @if($row['ALLOWED'] == 'Y') selected @endif>Y</option>
                                <option value="N" @if($row['ALLOWED'] == 'N') selected @endif>N</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded shadow">Update</button>
                        </td>
                    </form>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @if (empty($pass_types))
        <div class="text-center text-gray-500 mt-6">No pass types found.</div>
    @endif
</div>

</body>
</html>