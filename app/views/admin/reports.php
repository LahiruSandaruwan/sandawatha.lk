<?php require dirname(__DIR__) . '/shared/header.php'; ?>

<div class="bg-white shadow rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
    </div>

    <!-- Reports Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Report ID
                    </th>
                    <!-- Other table headers will go here -->
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Report rows will go here -->
            </tbody>
        </table>
    </div>
</div>

<?php require dirname(__DIR__) . '/shared/footer.php'; ?>