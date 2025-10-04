<?php
/**
 * Information Hub Component - EXACT MATCH to InformationHub.tsx
 * Displays barangay news, events, and service information
 */

function InformationHub() {
    $announcements = [
        [
            'title' => 'Free Medical Mission',
            'date' => 'October 5, 2025',
            'content' => 'The barangay will conduct a free medical mission at the health center. Services include general checkup, dental services, and free medicines.',
            'category' => 'Health'
        ],
        [
            'title' => 'Basketball League Season 5',
            'date' => 'October 10-30, 2025',
            'content' => 'Registration for the annual barangay basketball league is now open. Visit the barangay hall to register your team.',
            'category' => 'Sports'
        ],
        [
            'title' => 'Livelihood Training Program',
            'date' => 'October 15, 2025',
            'content' => 'Free training on food processing and entrepreneurship. Limited slots available. Register at the barangay office.',
            'category' => 'Livelihood'
        ]
    ];
    
    $events = [
        [
            'date' => 'Oct 5',
            'title' => 'Community Assembly',
            'time' => '6:00 PM',
            'location' => 'Multi-Purpose Hall'
        ],
        [
            'date' => 'Oct 8',
            'title' => 'Senior Citizens Day',
            'time' => '9:00 AM',
            'location' => 'Community Center'
        ],
        [
            'date' => 'Oct 12',
            'title' => 'Youth Summit',
            'time' => '2:00 PM',
            'location' => 'Multi-Purpose Hall'
        ]
    ];
    
    $documents = [
        [
            'title' => 'Barangay Clearance',
            'fee' => '₱50',
            'requirements' => 'Valid ID, Proof of Residency',
            'processingTime' => '1-2 days'
        ],
        [
            'title' => 'Certificate of Indigency',
            'fee' => 'Free',
            'requirements' => 'Valid ID, Proof of Residency',
            'processingTime' => '1 day'
        ],
        [
            'title' => 'Business Permit',
            'fee' => '₱200',
            'requirements' => 'Business Registration, Valid ID',
            'processingTime' => '3-5 days'
        ]
    ];
    
    ob_start();
?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h2 class="text-blue-900">Information Hub</h2>
                <p class="text-sm text-blue-600">Stay updated with barangay news, events, and services</p>
            </div>
        </div>
    </div>

    <!-- Latest Announcements -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
            </svg>
            <h3 class="text-blue-900">Latest Announcements</h3>
        </div>
        <div class="space-y-4">
            <?php foreach ($announcements as $announcement): ?>
            <div class="border border-blue-100 rounded-lg p-4">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="text-blue-900"><?php echo htmlspecialchars($announcement['title']); ?></h4>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                        <?php echo htmlspecialchars($announcement['category']); ?>
                    </span>
                </div>
                <p class="text-sm text-blue-600 mb-2">📅 <?php echo htmlspecialchars($announcement['date']); ?></p>
                <p class="text-blue-700"><?php echo htmlspecialchars($announcement['content']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-blue-900">Upcoming Events</h3>
        </div>
        <div class="space-y-3">
            <?php foreach ($events as $event): ?>
            <div class="flex items-center space-x-4 border border-blue-100 rounded-lg p-4">
                <div class="bg-blue-100 rounded-lg p-3 text-center flex-shrink-0">
                    <p class="text-blue-900"><?php echo htmlspecialchars($event['date']); ?></p>
                </div>
                <div class="flex-1">
                    <h4 class="text-blue-900 mb-1"><?php echo htmlspecialchars($event['title']); ?></h4>
                    <p class="text-sm text-blue-600">⏰ <?php echo htmlspecialchars($event['time']); ?></p>
                    <p class="text-sm text-blue-600">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Document Services -->
    <div class="bg-white rounded-lg p-6 border border-blue-100">
        <div class="flex items-center space-x-2 mb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-blue-900">Document Services</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <?php foreach ($documents as $doc): ?>
            <div class="border border-blue-100 rounded-lg p-4">
                <h4 class="text-blue-900 mb-3"><?php echo htmlspecialchars($doc['title']); ?></h4>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-blue-600">Fee:</p>
                        <p class="text-blue-900"><?php echo htmlspecialchars($doc['fee']); ?></p>
                    </div>
                    <div>
                        <p class="text-blue-600">Requirements:</p>
                        <p class="text-blue-700"><?php echo htmlspecialchars($doc['requirements']); ?></p>
                    </div>
                    <div>
                        <p class="text-blue-600">Processing:</p>
                        <p class="text-blue-700"><?php echo htmlspecialchars($doc['processingTime']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Office Hours -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-blue-900 mb-4">Barangay Office Hours</h3>
        <div class="grid md:grid-cols-2 gap-4 text-blue-700">
            <div>
                <p class="text-blue-900 mb-2">Weekdays:</p>
                <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
            </div>
            <div>
                <p class="text-blue-900 mb-2">Weekends:</p>
                <p>Saturday: 8:00 AM - 12:00 NN</p>
                <p>Sunday: Closed</p>
            </div>
        </div>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>