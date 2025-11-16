<?php
/**
 * Community Directory - SYNCHRONIZED with components/CommunityDirectory.tsx
 */

// Static data matching TSX exactly
$officials = [
    [
        'name' => 'Juan Dela Cruz',
        'position' => 'Barangay Captain',
        'phone' => '+63 912 345 6789',
        'email' => 'captain@barangay.gov.ph',
        'office' => 'Main Office, 2nd Floor'
    ],
    [
        'name' => 'Maria Santos',
        'position' => 'Barangay Secretary',
        'phone' => '+63 912 345 6788',
        'email' => 'secretary@barangay.gov.ph',
        'office' => 'Main Office, 1st Floor'
    ],
    [
        'name' => 'Pedro Reyes',
        'position' => 'Barangay Treasurer',
        'phone' => '+63 912 345 6787',
        'email' => 'treasurer@barangay.gov.ph',
        'office' => 'Finance Office, 1st Floor'
    ],
    [
        'name' => 'Ana Garcia',
        'position' => 'Kagawad - Health & Sanitation',
        'phone' => '+63 912 345 6786',
        'email' => 'health@barangay.gov.ph',
        'office' => 'Health Center'
    ],
    [
        'name' => 'Jose Martinez',
        'position' => 'Kagawad - Public Safety',
        'phone' => '+63 912 345 6785',
        'email' => 'safety@barangay.gov.ph',
        'office' => 'Tanod Station'
    ]
];

$services = [
    [
        'name' => 'Barangay Health Center',
        'phone' => '+63 912 345 6780',
        'hours' => 'Mon-Sat: 8:00 AM - 5:00 PM',
        'location' => 'Main Street, Building A'
    ],
    [
        'name' => 'Tanod Station',
        'phone' => '+63 912 345 6781',
        'hours' => '24/7',
        'location' => 'Main Street, Building B'
    ],
    [
        'name' => 'Multi-Purpose Hall',
        'phone' => '+63 912 345 6782',
        'hours' => 'Mon-Sun: 7:00 AM - 10:00 PM',
        'location' => 'Community Center'
    ]
];
?>

<div class="space-y-6">
    <!-- Header - EXACT MATCH TSX Lines 66-76 -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-blue-900">Community Directory</h2>
                <p class="text-sm text-blue-600">Contact information for barangay officials and services</p>
            </div>
        </div>
    </div>

    <!-- Barangay Officials - EXACT MATCH TSX Lines 79-111 -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">Barangay Officials</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($officials as $official): ?>
                <div class="border border-blue-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="text-blue-900 mb-1"><?php echo htmlspecialchars($official['name']); ?></h4>
                            <p class="text-sm text-blue-600"><?php echo htmlspecialchars($official['position']); ?></p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <?php echo htmlspecialchars($official['phone']); ?>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <?php echo htmlspecialchars($official['email']); ?>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?php echo htmlspecialchars($official['office']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Barangay Services - EXACT MATCH TSX Lines 115-152 -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <h3 class="text-blue-900 mb-4">Barangay Services</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach ($services as $service): ?>
                <div class="border border-blue-100 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h4 class="text-blue-900 mb-3"><?php echo htmlspecialchars($service['name']); ?></h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <?php echo htmlspecialchars($service['phone']); ?>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php echo htmlspecialchars($service['hours']); ?>
                        </div>
                        <div class="flex items-center text-blue-700">
                            <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?php echo htmlspecialchars($service['location']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>