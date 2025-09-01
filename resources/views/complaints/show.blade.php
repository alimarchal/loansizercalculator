<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Complaint Details
                </h2>
                <span class="px-3 py-1 rounded-full text-sm font-medium shadow-sm
                @switch($complaint->status)
                    @case('Open') bg-yellow-100 text-yellow-800 border border-yellow-200 @break
                    @case('In Progress') bg-blue-100 text-blue-800 border border-blue-200 @break
                    @case('Pending') bg-orange-100 text-orange-800 border border-orange-200 @break
                    @case('Resolved') bg-green-100 text-green-800 border border-green-200 @break
                    @case('Closed') bg-gray-100 text-gray-800 border border-gray-200 @break
                    @case('Reopened') bg-red-100 text-red-800 border border-red-200 @break
                    @default bg-gray-100 text-gray-800 border border-gray-200
                @endswitch">
                    {{ $complaint->status }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-medium shadow-sm
                @switch($complaint->priority)
                    @case('Low') bg-green-100 text-green-800 border border-green-200 @break
                    @case('Medium') bg-yellow-100 text-yellow-800 border border-yellow-200 @break
                    @case('High') bg-orange-100 text-orange-800 border border-orange-200 @break
                    @case('Critical') bg-red-100 text-red-800 border border-red-200 @break
                    @default bg-gray-100 text-gray-800 border border-gray-200
                @endswitch">
                    {{ $complaint->priority }} Priority
                </span>
                @if($complaint->sla_breached)
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                    <svg class="w-4 h-4 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5C3.312 16.333 4.275 18 5.814 18z" />
                    </svg>
                    SLA Breached
                </span>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('complaints.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-950 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to List
                </a>
                <button id="structured-pdf-btn"
                    class="inline-flex items-center px-4 py-2 bg-indigo-700 hover:bg-indigo-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm"
                    title="Generate printable PDF (excludes binary attachments)">
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v12m0 0l-3.5-3.5M12 16l3.5-3.5M6 20h12" />
                    </svg>
                    Download Structured PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-status-message />

            <!-- Main Complaint Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">{{ $complaint->title }}</h3>
                                <p class="text-blue-100">{{ $complaint->complaint_number }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-white text-sm opacity-90">Created</div>
                            <div class="text-white text-lg font-bold">{{ $complaint->created_at->format('M d, Y') }}
                            </div>
                            <div class="text-blue-100 text-sm">{{ $complaint->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Main Information -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Description -->
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Description</h4>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                    <p
                                        class="text-gray-700 leading-relaxed whitespace-pre-wrap break-all break-words overflow-hidden">
                                        {{
                                        $complaint->description }}</p>
                                </div>
                            </div>

                            @if(strcasecmp($complaint->category,'Harassment')===0)
                            <!-- Harassment Details -->
                            <div
                                class="bg-gradient-to-br from-red-50 to-rose-50 rounded-xl p-6 border border-rose-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-rose-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.1 0-2 .9-2 2m2-2a2 2 0 110 4m0-4v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Harassment Details</h4>
                                    @if($complaint->harassment_confidential)
                                    <span
                                        class="ml-3 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 font-medium">Confidential</span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($complaint->harassment_sub_category)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Sub Category</label>
                                        <p class="text-gray-900 font-semibold">{{ $complaint->harassment_sub_category }}
                                        </p>
                                    </div>
                                    @endif
                                    @if($complaint->harassment_incident_date)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Incident Date &
                                            Time</label>
                                        <p class="text-gray-900 font-semibold">{{
                                            $complaint->harassment_incident_date?->format('M d, Y H:i') }}</p>
                                    </div>
                                    @endif
                                    @if($complaint->harassment_location)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 md:col-span-2">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Location</label>
                                        <p class="text-gray-900">{{ $complaint->harassment_location }}</p>
                                    </div>
                                    @endif
                                    @if($complaint->harassment_employee_number || $complaint->harassment_employee_phone)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Victim
                                            Employee</label>
                                        <p class="text-gray-900 text-sm">Number: <span class="font-semibold">{{
                                                $complaint->harassment_employee_number ?? 'N/A' }}</span></p>
                                        @if($complaint->harassment_employee_phone)
                                        <p class="text-gray-900 text-sm">Phone: <span class="font-semibold">{{
                                                $complaint->harassment_employee_phone }}</span></p>
                                        @endif
                                    </div>
                                    @endif
                                    @if($complaint->harassment_abuser_name ||
                                    $complaint->harassment_abuser_employee_number)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Alleged
                                            Abuser</label>
                                        @if($complaint->harassment_abuser_name)
                                        <p class="text-gray-900 font-semibold">{{ $complaint->harassment_abuser_name }}
                                        </p>
                                        @endif
                                        <p class="text-gray-900 text-sm">Emp #: <span class="font-semibold">{{
                                                $complaint->harassment_abuser_employee_number ?? 'N/A' }}</span></p>
                                        @if($complaint->harassment_abuser_relationship)
                                        <p class="text-gray-700 text-xs mt-1">Relationship: {{
                                            $complaint->harassment_abuser_relationship }}</p>
                                        @endif
                                        @if($complaint->harassment_abuser_email)
                                        <p class="text-gray-700 text-xs">Email: {{ $complaint->harassment_abuser_email
                                            }}</p>
                                        @endif
                                        @if($complaint->harassment_abuser_phone)
                                        <p class="text-gray-700 text-xs">Phone: {{ $complaint->harassment_abuser_phone
                                            }}</p>
                                        @endif
                                    </div>
                                    @endif
                                    @if($complaint->harassment_details)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100 md:col-span-2">
                                        <label class="text-xs font-medium text-gray-500 block mb-1">Incident
                                            Details</label>
                                        <p class="text-gray-900 whitespace-pre-wrap text-sm leading-relaxed">{{
                                            $complaint->harassment_details }}</p>
                                    </div>
                                    @endif
                                </div>
                                @php $witnesses = $complaint->witnesses; @endphp
                                @if($witnesses->count())
                                <div class="mt-6">
                                    <h5 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 text-rose-500 mr-1" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14v7m-5-3h10" />
                                        </svg>
                                        Witnesses ({{ $witnesses->count() }})
                                    </h5>
                                    <div class="space-y-3">
                                        @foreach($witnesses as $w)
                                        <div class="bg-white rounded-lg p-3 border border-gray-100 shadow-sm">
                                            <div class="flex flex-wrap justify-between text-sm">
                                                <div class="font-medium text-gray-800">{{ $w->name }}</div>
                                                @if($w->employee_number)
                                                <div class="text-xs text-gray-500">Emp #: {{ $w->employee_number }}
                                                </div>
                                                @endif
                                            </div>
                                            <div
                                                class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-gray-600">
                                                @if($w->email)<div>Email: {{ $w->email }}</div>@endif
                                                @if($w->phone)<div>Phone: {{ $w->phone }}</div>@endif
                                            </div>
                                            @if($w->statement)
                                            <div class="mt-2 text-xs text-gray-700 whitespace-pre-wrap">{{ $w->statement
                                                }}</div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif


                            @if(strcasecmp($complaint->category, 'Grievance') === 0)
                            <!-- Grievance Details Section -->
                            <div
                                class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-100 shadow-sm">

                                <!-- Section Header -->
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
                                    </svg>
                                    <h4 class="text-lg font-bold text-black">GRIEVANCE DETAILS</h4>
                                    @if($complaint->grievance_acknowledgment)
                                    <span
                                        class="ml-3 px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800 font-bold">
                                        ACKNOWLEDGED
                                    </span>
                                    @endif
                                </div>

                                <!-- Grievance Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    <!-- Employee ID -->
                                    @if($complaint->grievance_employee_id)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Employee
                                            ID</label>
                                        <p class="text-black font-bold text-sm">{{ $complaint->grievance_employee_id }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Employment Start Date with Service Calculation -->
                                    @if($complaint->grievance_employment_start_date)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Employment
                                            Start Date</label>
                                        <p class="text-black font-bold text-sm">
                                            {{ $complaint->grievance_employment_start_date->format('M d, Y') }}
                                        </p>

                                        @php
                                        $startDate = $complaint->grievance_employment_start_date;
                                        $currentDate = now();
                                        $service = $startDate->diff($currentDate);

                                        $serviceText = '';
                                        if ($service->y > 0) {
                                        $serviceText .= $service->y . ' year' . ($service->y > 1 ? 's' : '');
                                        }
                                        if ($service->m > 0) {
                                        if ($serviceText) $serviceText .= ', ';
                                        $serviceText .= $service->m . ' month' . ($service->m > 1 ? 's' : '');
                                        }
                                        if ($service->d > 0) {
                                        if ($serviceText) $serviceText .= ', ';
                                        $serviceText .= $service->d . ' day' . ($service->d > 1 ? 's' : '');
                                        }

                                        if (!$serviceText) {
                                        $serviceText = 'Less than 1 day';
                                        }
                                        @endphp
                                        <p class="text-xs text-black mt-1">
                                            <span class="font-bold">Service:</span> {{ $serviceText }}
                                            <span class="text-gray-800">({{ $service->days }} total days)</span>
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Department / Position -->
                                    @if($complaint->grievance_department_position)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Department
                                            / Position</label>
                                        <p class="text-black font-semibold">{{ $complaint->grievance_department_position
                                            }}</p>
                                    </div>
                                    @endif

                                    <!-- Supervisor Name -->
                                    @if($complaint->grievance_supervisor_name)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Immediate
                                            Supervisor Name</label>
                                        <p class="text-black font-semibold">{{ $complaint->grievance_supervisor_name }}
                                        </p>
                                    </div>
                                    @endif

                                    <!-- Grievance Type -->
                                    @if($complaint->grievance_type)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Grievance
                                            Type</label>
                                        <p class="text-black font-bold">{{ $complaint->grievance_type }}</p>
                                    </div>
                                    @endif

                                    <!-- Policy Violated -->
                                    @if($complaint->grievance_policy_violated)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Policy
                                            Violated</label>
                                        <p class="text-black font-bold">{{ $complaint->grievance_policy_violated }}</p>
                                    </div>
                                    @endif

                                    <!-- Previous Attempts -->
                                    @if(!is_null($complaint->grievance_previous_attempts))
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Previous
                                            Attempts to Resolve</label>
                                        <p class="text-black font-bold">{{ $complaint->grievance_previous_attempts ?
                                            'YES' : 'NO' }}</p>
                                    </div>
                                    @endif

                                    <!-- Previous Attempts Details -->
                                    @if($complaint->grievance_previous_attempts_details)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Previous
                                            Attempts Details</label>
                                        <p class="text-black leading-relaxed text-sm">{{
                                            $complaint->grievance_previous_attempts_details }}</p>
                                    </div>
                                    @endif

                                    <!-- Desired Outcome -->
                                    @if($complaint->grievance_desired_outcome)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Desired
                                            Outcome / Remedy</label>
                                        <p class="text-black leading-relaxed text-sm">{{
                                            $complaint->grievance_desired_outcome }}</p>
                                    </div>
                                    @endif

                                    <!-- Subject / Respondent -->
                                    @if($complaint->grievance_subject_name || $complaint->grievance_subject_position)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Subject
                                            / Respondent</label>
                                        <p class="text-black text-sm">Name: <span class="font-bold">{{
                                                $complaint->grievance_subject_name ?? 'N/A' }}</span></p>
                                        @if($complaint->grievance_subject_position)
                                        <p class="text-black text-sm">Position: <span class="font-bold">{{
                                                $complaint->grievance_subject_position }}</span></p>
                                        @endif
                                        @if($complaint->grievance_subject_relationship)
                                        <p class="text-black text-xs mt-1">Relationship: <span class="font-semibold">{{
                                                $complaint->grievance_subject_relationship }}</span></p>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Process Flags -->
                                    @if(!is_null($complaint->grievance_union_representation) ||
                                    !is_null($complaint->grievance_anonymous) ||
                                    !is_null($complaint->grievance_acknowledgment))
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-2 uppercase tracking-wide">Process
                                            Flags</label>
                                        <div class="flex flex-wrap gap-2">
                                            @if(!is_null($complaint->grievance_union_representation))
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold {{ $complaint->grievance_union_representation ? 'bg-amber-600 text-white' : 'bg-gray-200 text-black' }}">
                                                UNION REPRESENTATION: {{ $complaint->grievance_union_representation ?
                                                'YES' : 'NO' }}
                                            </span>
                                            @endif
                                            @if(!is_null($complaint->grievance_anonymous))
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold {{ $complaint->grievance_anonymous ? 'bg-amber-600 text-white' : 'bg-gray-200 text-black' }}">
                                                ANONYMOUS: {{ $complaint->grievance_anonymous ? 'YES' : 'NO' }}
                                            </span>
                                            @endif
                                            @if(!is_null($complaint->grievance_acknowledgment))
                                            <span
                                                class="px-3 py-1 text-xs rounded-full font-bold {{ $complaint->grievance_acknowledgment ? 'bg-amber-600 text-white' : 'bg-gray-200 text-black' }}">
                                                ACKNOWLEDGMENT: {{ $complaint->grievance_acknowledgment ? 'YES' : 'NO'
                                                }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Timeline / Pattern -->
                                    @if($complaint->grievance_first_occurred_date ||
                                    $complaint->grievance_most_recent_date || $complaint->grievance_pattern_frequency)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Timeline
                                            / Pattern</label>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                            @if($complaint->grievance_first_occurred_date)
                                            <div>
                                                <p class="text-black text-xs font-bold">FIRST OCCURRED</p>
                                                <p class="text-black font-bold">{{
                                                    optional($complaint->grievance_first_occurred_date)->format('M d,
                                                    Y') }}</p>
                                            </div>
                                            @endif
                                            @if($complaint->grievance_most_recent_date)
                                            <div>
                                                <p class="text-black text-xs font-bold">MOST RECENT</p>
                                                <p class="text-black font-bold">{{
                                                    optional($complaint->grievance_most_recent_date)->format('M d, Y')
                                                    }}</p>
                                            </div>
                                            @endif
                                            @if($complaint->grievance_pattern_frequency)
                                            <div>
                                                <p class="text-black text-xs font-bold">PATTERN/FREQUENCY</p>
                                                <p class="text-black font-bold">{{
                                                    $complaint->grievance_pattern_frequency }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Performance Impact -->
                                    @if($complaint->grievance_performance_effect)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black block mb-1 uppercase tracking-wide">Impact
                                            on Performance / Well-being</label>
                                        <p class="text-black leading-relaxed text-sm">{{
                                            $complaint->grievance_performance_effect }}</p>
                                    </div>
                                    @endif

                                    <!-- Witnesses -->
                                    @php $grievanceWitnesses = $complaint->witnesses; @endphp
                                    @if($grievanceWitnesses->count())
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 md:col-span-2">
                                        <label
                                            class="text-xs font-bold text-black mb-3 flex items-center uppercase tracking-wide">
                                            WITNESSES ({{ $grievanceWitnesses->count() }})
                                        </label>
                                        <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                                            @foreach($grievanceWitnesses as $w)
                                            <div class="p-3 border border-gray-300 rounded-lg bg-gray-50">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <p class="text-black text-sm font-bold flex items-center">
                                                            <span
                                                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-600 text-white text-xs mr-2 font-bold">
                                                                {{ $loop->iteration }}
                                                            </span>
                                                            {{ $w->name }}
                                                        </p>
                                                        <div class="grid grid-cols-2 gap-2 text-xs text-black mt-1">
                                                            @if($w->employee_number)
                                                            <p><span class="font-bold">EMP #:</span> {{
                                                                $w->employee_number }}</p>
                                                            @endif
                                                            @if($w->phone)
                                                            <p><span class="font-bold">PHONE:</span> {{ $w->phone }}</p>
                                                            @endif
                                                            @if($w->email)
                                                            <p class="col-span-2"><span class="font-bold">EMAIL:</span>
                                                                {{ $w->email }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($w->statement)
                                                <p class="mt-2 text-xs text-black">
                                                    <span class="font-bold">STATEMENT:</span> {{ $w->statement }}
                                                </p>
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Complainant Information -->
                            @if($complaint->complainant_name || $complaint->complainant_email ||
                            $complaint->complainant_phone)
                            <div
                                class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Complainant Information</h4>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($complaint->complainant_name)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Name</label>
                                        <p class="text-gray-900 font-semibold">{{ $complaint->complainant_name }}</p>
                                    </div>
                                    @endif
                                    @if($complaint->complainant_email)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Email</label>
                                        <p class="text-gray-900 font-semibold">
                                            <a href="mailto:{{ $complaint->complainant_email }}"
                                                class="text-blue-600 hover:underline">
                                                {{ $complaint->complainant_email }}
                                            </a>
                                        </p>
                                    </div>
                                    @endif
                                    @if($complaint->complainant_phone)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Phone</label>
                                        <p class="text-gray-900 font-semibold">
                                            <a href="tel:{{ $complaint->complainant_phone }}"
                                                class="text-blue-600 hover:underline">
                                                {{ $complaint->complainant_phone }}
                                            </a>
                                        </p>
                                    </div>
                                    @endif
                                    @if($complaint->complainant_account_number)
                                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <label class="text-sm font-medium text-gray-500 block mb-1">Account
                                            Number</label>
                                        <p class="text-gray-900 font-mono font-semibold bg-gray-50 px-2 py-1 rounded">
                                            {{ $complaint->complainant_account_number }}
                                        </p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Resolution -->
                            @if($complaint->resolution)
                            <div
                                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Resolution</h4>
                                </div>
                                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{
                                        $complaint->resolution }}</p>
                                    @if($complaint->resolved_by && $complaint->resolved_at)
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                                        <div class="text-sm text-gray-600">
                                            Resolved by: <span class="font-medium">{{ $complaint->resolvedBy->name
                                                }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            {{ $complaint->resolved_at->format('M d, Y \a\t H:i') }}
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Sidebar Information -->
                        <div class="space-y-6">
                            <!-- Quick Info -->
                            <div
                                class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border border-gray-200 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Quick Info</h4>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Source</span>
                                        <span class="text-sm text-gray-800 bg-gray-100 px-2 py-1 rounded">{{
                                            $complaint->source }}</span>
                                    </div>
                                    @if($complaint->category)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Category</span>
                                        <span class="text-sm text-gray-800">{{ $complaint->category }}</span>
                                    </div>
                                    @endif
                                    @if($complaint->branch)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Branch</span>
                                        <span class="text-sm text-gray-800">{{ $complaint->branch->name }}</span>
                                    </div>
                                    @endif
                                    @if($complaint->region)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Region</span>
                                        <span class="text-sm text-gray-800">{{ $complaint->region->name }}</span>
                                    </div>
                                    @endif
                                    @if($complaint->division)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Division</span>
                                        <span class="text-sm text-gray-800">{{ $complaint->division->short_name ??
                                            $complaint->division->name }}</span>
                                    </div>
                                    @endif
                                    <div class="grid grid-cols-3 gap-2 pt-2">
                                        <div
                                            class="bg-white rounded-lg p-2 text-center shadow-sm border border-gray-100">
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wide">Same Branch
                                            </div>
                                            <div class="text-sm font-semibold text-indigo-600">
                                                {{ \App\Models\Complaint::where('branch_id',
                                                $complaint->branch_id)->count() }}
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white rounded-lg p-2 text-center shadow-sm border border-gray-100">
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wide">Same Region
                                            </div>
                                            <div class="text-sm font-semibold text-indigo-600">
                                                {{ $complaint->region_id ? \App\Models\Complaint::where('region_id',
                                                $complaint->region_id)->count() : 0 }}
                                            </div>
                                        </div>
                                        <div
                                            class="bg-white rounded-lg p-2 text-center shadow-sm border border-gray-100">
                                            <div class="text-[10px] text-gray-500 uppercase tracking-wide">Same Division
                                            </div>
                                            <div class="text-sm font-semibold text-indigo-600">
                                                {{ $complaint->division_id ? \App\Models\Complaint::where('division_id',
                                                $complaint->division_id)->count() : 0 }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($complaint->expected_resolution_date)
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm">
                                        <span class="text-sm font-medium text-gray-600">Expected Resolution</span>
                                        <span
                                            class="text-sm {{ $complaint->expected_resolution_date->isPast() && !$complaint->isResolved() ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                            {{ $complaint->expected_resolution_date->format('M d, Y') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Assignment Info -->
                            <div
                                class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-6 border border-blue-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Assignment</h4>
                                </div>
                                <div class="space-y-3">
                                    @if($complaint->assignedTo)
                                    <div class="p-3 bg-white rounded-lg shadow-sm border-l-4 border-blue-400">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-sm font-medium text-gray-600">Assigned To</span>
                                                <p class="text-lg font-bold text-blue-600">{{
                                                    $complaint->assignedTo->name }}</p>
                                            </div>
                                            <div class="p-2 bg-blue-100 rounded-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @if($complaint->assigned_at)
                                        <div class="mt-2 text-xs text-gray-500">
                                            Assigned {{ $complaint->assigned_at->diffForHumans() }}
                                        </div>
                                        @endif
                                    </div>
                                    @else
                                    <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mr-2"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5C3.312 16.333 4.275 18 5.814 18z" />
                                            </svg>
                                            <span class="text-sm font-medium text-yellow-800">Unassigned</span>
                                        </div>
                                    </div>
                                    @endif

                                    @if($complaint->assignedBy)
                                    <div class="p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium text-gray-600">Assigned By</span>
                                            <span class="text-sm text-gray-800">{{ $complaint->assignedBy->name
                                                }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Metrics -->
                            @if($complaint->metrics)
                            <div
                                class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Metrics</h4>
                                </div>
                                <div class="space-y-3">
                                    @if($complaint->metrics->time_to_first_response)
                                    <div
                                        class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm border-l-4 border-green-400">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">First Response</span>
                                            <p class="text-lg font-bold text-green-600">{{
                                                $complaint->metrics->formatted_response_time }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @php
                                    $computedResolutionMinutes = null;
                                    if(in_array($complaint->status, ['Resolved','Closed']) && $complaint->resolved_at) {
                                    $computedResolutionMinutes =
                                    $complaint->created_at->diffInMinutes($complaint->resolved_at);
                                    }
                                    @endphp
                                    @if($complaint->metrics->time_to_resolution || $computedResolutionMinutes)
                                    <div
                                        class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm border-l-4 border-blue-400">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Resolution Time</span>
                                            <p class="text-lg font-bold text-blue-600">{{
                                                $complaint->metrics->formatted_resolution_time ??
                                                \Carbon\CarbonInterval::minutes($computedResolutionMinutes)->cascade()->forHumans(short:true)
                                                }}</p>
                                        </div>

                                    </div>
                                    @endif
                                    @if($complaint->metrics->time_to_first_response &&
                                    $complaint->metrics->time_to_resolution)
                                    <div
                                        class="flex justify-between items-center p-3 bg-white rounded-lg shadow-sm border-l-4 border-indigo-400">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600">Handling Duration (Post
                                                First Response)</span>
                                            <p class="text-lg font-bold text-indigo-600">{{
                                                $complaint->metrics->formatted_handling_duration }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">
                                            <div class="text-lg font-bold text-purple-600">{{
                                                $complaint->metrics->escalation_count }}</div>
                                            <div class="text-xs text-gray-600">Escalations</div>
                                        </div>
                                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">
                                            <div class="text-lg font-bold text-orange-600">{{
                                                $complaint->metrics->assignment_count }}</div>
                                            <div class="text-xs text-gray-600">Assignments</div>
                                        </div>
                                        <div class="p-3 bg-white rounded-lg shadow-sm text-center">
                                            <div class="text-lg font-bold text-red-600">{{
                                                $complaint->metrics->reopened_count }}</div>
                                            <div class="text-xs text-gray-600">Reopened</div>
                                        </div>
                                    </div>
                                    @if($complaint->metrics->customer_satisfaction_score)
                                    <div class="p-3 bg-white rounded-lg shadow-sm border-l-4 border-yellow-400">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-600">Customer Satisfaction</span>
                                            <div class="flex items-center">
                                                <span class="text-lg font-bold text-yellow-600">{{
                                                    $complaint->metrics->customer_satisfaction_score }}/5</span>
                                                <div class="ml-2 flex">
                                                    @for($i = 1; $i <= 5; $i++) <svg
                                                        class="w-4 h-4 {{ $i <= $complaint->metrics->customer_satisfaction_score ? 'text-yellow-400' : 'text-gray-300' }}"
                                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                        </svg>
                                                        @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- Watchers -->
                            @if($complaint->watchers->count() > 0)
                            <div
                                class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100 shadow-sm">
                                <div class="flex items-center mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <h4 class="text-lg font-semibold text-gray-800">Watchers</h4>
                                </div>
                                <div class="space-y-2">
                                    @foreach($complaint->watchers as $watcher)
                                    <div class="flex items-center p-2 bg-white rounded-lg shadow-sm">
                                        <div
                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-semibold text-sm">{{
                                                substr($watcher->user->name, 0, 1) }}</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ $watcher->user->name
                                            }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            class="tab-button border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600"
                            data-tab="history">
                            History & Timeline ({{ $complaint->histories->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="comments">
                            Comments ({{ $complaint->comments->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="attachments">
                            Attachments ({{ $complaint->attachments->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="escalations">
                            Escalations ({{ $complaint->escalations->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="assignments">
                            Assignments ({{ $complaint->assignments->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="watchers">
                            Watchers ({{ $complaint->watchers->count() }})
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="satisfaction">
                            Satisfaction
                        </button>
                        <button
                            class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            data-tab="operations">
                            Operations
                        </button>
                    </nav>
                </div>

                <!-- History Tab -->
                <div id="history-tab" class="tab-content p-6">
                    <div class="relative">
                        <div
                            class="absolute left-4 top-0 bottom-0 w-px bg-gradient-to-b from-indigo-300 via-gray-200 to-transparent pointer-events-none">
                        </div>
                        <ul class="space-y-6">
                            @forelse($complaint->histories as $history)
                            <li class="relative pl-12 group">
                                <span class="absolute left-0 flex items-center justify-center w-8 h-8 rounded-full ring-4 ring-white dark:ring-gray-800 shadow-sm
                                    @switch($history->action_type)
                                        @case('Created') bg-blue-600 text-white @break
                                        @case('Assigned') @case('Reassigned') bg-green-600 text-white @break
                                        @case('Status Changed') bg-yellow-500 text-white @break
                                        @case('Resolved') bg-emerald-600 text-white @break
                                        @case('Escalated') bg-red-600 text-white @break
                                        @case('Priority Changed') bg-orange-500 text-white @break
                                        @case('Branch Transfer') bg-teal-600 text-white @break
                                        @case('Region Transfer') bg-indigo-600 text-white @break
                                        @case('Division Transfer') bg-pink-600 text-white @break
                                        @default bg-gray-500 text-white
                                    @endswitch">
                                    @switch($history->action_type)
                                    @case('Created')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v12m6-6H6" />
                                    </svg>
                                    @break
                                    @case('Assigned')
                                    @case('Reassigned')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    @break
                                    @case('Resolved')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    @break
                                    @case('Branch Transfer')
                                    @case('Region Transfer')
                                    @case('Division Transfer')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h10M7 12h10M7 17h10" />
                                    </svg>
                                    @break
                                    @case('Escalated')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    @break
                                    @default
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @endswitch
                                </span>
                                <div
                                    class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm hover:shadow-md transition">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{
                                            $history->action_type }}</h4>
                                        <time class="text-xs text-gray-500">{{ $history->performed_at->format('M d, Y
                                            H:i') }}</time>
                                    </div>
                                    @php
                                    // Map any lingering numeric user IDs in historical records (pre-change) to names
                                    // for reassignment histories
                                    $oldDisplay = $history->old_value;
                                    $newDisplay = $history->new_value;
                                    if ($history->action_type === 'Reassigned') {
                                    $userMap = $users->keyBy('id');
                                    if (is_numeric($oldDisplay)) {
                                    $oldDisplay = ($userMap[$oldDisplay]->name ?? ('User #'.$oldDisplay));
                                    }
                                    if (is_numeric($newDisplay)) {
                                    $newDisplay = ($userMap[$newDisplay]->name ?? ('User #'.$newDisplay));
                                    }
                                    }
                                    @endphp
                                    @if($oldDisplay || $newDisplay)
                                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                        @if($oldDisplay && $newDisplay)
                                        <span class="font-medium text-gray-700 dark:text-gray-200">{{
                                            $oldDisplay }}</span>
                                        <span class="mx-1 text-gray-400">→</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $newDisplay
                                            }}</span>
                                        @elseif($newDisplay)
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $newDisplay
                                            }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    @if($history->comments)
                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 leading-snug">{{
                                        $history->comments }}</p>
                                    @endif
                                    <div
                                        class="mt-3 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                                        <span>by {{ $history->performedBy->name }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">#{{
                                            $history->id }}</span>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="text-center py-10">
                                <div class="inline-flex flex-col items-center text-gray-500">
                                    <svg class="h-12 w-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="mt-3 text-sm">No history records found</p>
                                </div>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Comments Tab -->
                <div id="comments-tab" class="tab-content p-6" style="display: none;">
                    <!-- Add Comment Form -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Add Comment</h4>
                        <form method="POST" action="{{ route('complaints.add-comment', $complaint) }}">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <textarea name="comment_text" rows="3" required
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                                        placeholder="Enter your comment..."></textarea>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <select name="comment_type" required
                                            class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                            <option value="Internal">Internal</option>
                                            <option value="Customer">Customer</option>
                                            <option value="System">System</option>
                                        </select>
                                        <label class="flex items-center">
                                            <input type="checkbox" name="is_private" value="1"
                                                class="rounded border-gray-300 text-indigo-600">
                                            <span class="ml-2 text-sm text-gray-700">Private</span>
                                        </label>
                                    </div>
                                    <button type="submit"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                        Add Comment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        @forelse($complaint->comments as $comment)
                        <div
                            class="p-4 border border-gray-200 rounded-lg {{ $comment->is_private ? 'bg-yellow-50 border-yellow-200' : 'bg-white' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                        <span class="text-gray-600 font-semibold text-sm">{{
                                            substr($comment->creator->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-gray-900">{{ $comment->creator->name }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                        @switch($comment->comment_type)
                                                            @case('Internal') bg-blue-100 text-blue-800 @break
                                                            @case('Customer') bg-green-100 text-green-800 @break
                                                            @case('System') bg-gray-100 text-gray-800 @break
                                                        @endswitch">
                                                {{ $comment->comment_type }}
                                            </span>
                                            @if($comment->is_private)
                                            <span
                                                class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Private</span>
                                            @endif
                                        </div>
                                        <time class="text-xs text-gray-500">{{ $comment->created_at->format('M d, Y
                                            H:i') }}</time>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $comment->comment_text }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="mt-2">No comments yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Attachments Tab -->
                <div id="attachments-tab" class="tab-content p-6" style="display: none;">
                    <div class="space-y-6">
                        <!-- Upload New Attachments -->
                        <div class="p-4 border border-indigo-200 rounded-lg bg-indigo-50/60">
                            <h4 class="text-sm font-semibold text-gray-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Add / Upload Attachments
                            </h4>
                            <p class="text-xs text-gray-600 mb-3">Attach additional supporting files (screenshots,
                                documents, logs). Files are stored securely and appear in the list below after upload.
                                You can select multiple files at once.</p>
                            <form method="POST" action="{{ route('complaints.add-attachments', $complaint) }}"
                                enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Choose Files</label>
                                    <input type="file" name="attachments[]" multiple
                                        class="w-full text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-200" />
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] text-gray-500">Accepted any type. Large files may take
                                        longer to process.</span>
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded shadow-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- Existing Attachments List -->
                        @forelse($complaint->attachments as $attachment)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $attachment->file_name }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $attachment->formatted_file_size }} •
                                        Uploaded {{ $attachment->created_at->format('M d, Y') }} by {{
                                        $attachment->creator->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('complaints.download-attachment', $attachment) }}"
                                    class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Download
                                </a>
                                <form method="POST" action="{{ route('complaints.delete-attachment', $attachment) }}"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this attachment?')"
                                        class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 00-5.656-5.656l-6.586 6.586a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <p class="mt-2">No attachments</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Escalations Tab -->
                <div id="escalations-tab" class="tab-content p-6" style="display: none;">
                    <div class="space-y-4">
                        @forelse($complaint->escalations as $escalation)
                        <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <span class="text-red-600 font-bold text-sm">L{{ $escalation->escalation_level
                                            }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            Escalated from {{ $escalation->escalatedFrom->name }} to {{
                                            $escalation->escalatedTo->name }}
                                        </div>
                                        <time class="text-sm text-gray-500">{{ $escalation->escalated_at->format('M d, Y
                                            H:i') }}</time>
                                    </div>
                                </div>
                                @if($escalation->resolved_at)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Resolved</span>
                                @else
                                <span
                                    class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                                @endif
                            </div>
                            <div class="mt-3">
                                <p class="text-sm text-gray-700">{{ $escalation->escalation_reason }}</p>
                                @if($escalation->resolved_at)
                                <div class="mt-2 text-xs text-gray-500">
                                    Resolved on {{ $escalation->resolved_at->format('M d, Y H:i') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <p class="mt-2">No escalations</p>
                        </div>
                        @endforelse
                        <!-- Inline Escalation Form -->
                        <div class="mt-6 p-4 border border-red-200 rounded-lg bg-white">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">New Escalation</h4>
                            <form method="POST" action="{{ route('complaints.escalate', $complaint) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Escalate To</label>
                                        <select name="escalated_to" class="w-full border-gray-300 rounded-md" required>
                                            <option value="">Select user</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Level</label>
                                        <select name="escalation_level" class="w-full border-gray-300 rounded-md"
                                            required>
                                            @for($i=1;$i<=5;$i++) <option value="{{ $i }}">Level {{ $i }}</option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Reason</label>
                                        <textarea name="escalation_reason" rows="2"
                                            class="w-full border-gray-300 rounded-md" required></textarea>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-3">
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-600 text-white rounded text-sm">Escalate</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Assignments Tab -->
                <div id="assignments-tab" class="tab-content p-6" style="display: none;">
                    <div class="space-y-4">
                        @forelse($complaint->assignments as $assignment)
                        <div
                            class="p-4 border border-gray-200 rounded-lg {{ $assignment->is_active ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 {{ $assignment->is_active ? 'bg-green-100' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 {{ $assignment->is_active ? 'text-green-600' : 'text-gray-600' }}"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            {{ $assignment->assignedTo->name }}
                                            <span class="px-2 py-1 text-xs rounded-full
                                                        @switch($assignment->assignment_type)
                                                            @case('Primary') bg-blue-100 text-blue-800 @break
                                                            @case('Secondary') bg-gray-100 text-gray-800 @break
                                                            @case('Observer') bg-purple-100 text-purple-800 @break
                                                        @endswitch">
                                                {{ $assignment->assignment_type }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Assigned by {{ $assignment->assignedBy->name }} on {{
                                            $assignment->assigned_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                                @if($assignment->is_active)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                                @else
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                @endif
                            </div>
                            @if($assignment->reason)
                            <div class="mt-3">
                                <p class="text-sm text-gray-700">{{ $assignment->reason }}</p>
                            </div>
                            @endif
                            @if($assignment->unassigned_at)
                            <div class="mt-2 text-xs text-gray-500">
                                Unassigned on {{ $assignment->unassigned_at->format('M d, Y H:i') }}
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-2">No assignments</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Operations Tab -->
                <div id="operations-tab" class="tab-content p-6" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Update -->
                        <div class="bg-white rounded-lg p-4 border border-gray-200">
                            <h4 class="text-md font-medium mb-3">Update Status</h4>
                            <form method="POST" action="{{ route('complaints.update-status', $complaint) }}">
                                @csrf
                                @method('PATCH')
                                <div class="space-y-3">
                                    <select name="status" required class="w-full border-gray-300 rounded-md">
                                        <option value="">Select status</option>
                                        <option value="Open">Open</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Reopened">Reopened</option>
                                    </select>
                                    <input type="text" name="status_change_reason" placeholder="Reason (optional)"
                                        class="w-full border-gray-300 rounded-md">
                                    <div class="flex justify-end">
                                        <button type="submit"
                                            class="px-3 py-1 bg-yellow-600 text-white rounded">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Assignment / Priority / Branch -->
                        <div class="space-y-4">
                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="text-md font-medium mb-3">Assign to User</h4>
                                <form method="POST" action="{{ route('complaints.update', $complaint) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-3">
                                        <select name="assigned_to" required class="w-full border-gray-300 rounded-md">
                                            <option value="">Select user</option>
                                            @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $complaint->assigned_to == $user->id ?
                                                'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="assignment_reason" placeholder="Reason (optional)"
                                            class="w-full border-gray-300 rounded-md">
                                        <div class="flex justify-end">
                                            <button type="submit"
                                                class="px-3 py-1 bg-blue-600 text-white rounded">Assign</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="bg-white rounded-lg p-4 border border-gray-200">
                                <h4 class="text-md font-medium mb-3">Priority / Location Transfer</h4>
                                <form method="POST" action="{{ route('complaints.update', $complaint) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-3">
                                        <select name="priority" class="w-full border-gray-300 rounded-md">
                                            <option value="">Select priority</option>
                                            <option value="Low">Low</option>
                                            <option value="Medium">Medium</option>
                                            <option value="High">High</option>
                                            <option value="Critical">Critical</option>
                                        </select>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <div>
                                                <select name="branch_id"
                                                    class="w-full border-gray-300 rounded-md text-sm">
                                                    <option value="">Branch: Not Applicable</option>
                                                    @foreach($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ $complaint->branch_id ==
                                                        $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <select name="region_id"
                                                    class="w-full border-gray-300 rounded-md text-sm">
                                                    <option value="">Region: Not Applicable</option>
                                                    @foreach($regions as $region)
                                                    <option value="{{ $region->id }}" {{ $complaint->region_id ==
                                                        $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <select name="division_id"
                                                    class="w-full border-gray-300 rounded-md text-sm">
                                                    <option value="">Division: Not Applicable</option>
                                                    @foreach($divisions as $division)
                                                    <option value="{{ $division->id }}" {{ $complaint->division_id ==
                                                        $division->id ? 'selected' : '' }}>{{ $division->short_name ??
                                                        $division->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <input type="text" name="priority_change_reason"
                                            placeholder="Priority change reason (required if raising to Critical)"
                                            class="w-full border-gray-300 rounded-md" />
                                        <div class="flex flex-wrap gap-2 justify-end text-xs">
                                            <button type="submit" name="_transfer_scope" value="priority"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded">Update
                                                Priority</button>
                                            <button type="submit" name="_transfer_scope" value="branch"
                                                class="px-3 py-1 bg-blue-600 text-white rounded">Transfer
                                                Branch</button>
                                            <button type="submit" name="_transfer_scope" value="region"
                                                class="px-3 py-1 bg-purple-600 text-white rounded">Transfer
                                                Region</button>
                                            <button type="submit" name="_transfer_scope" value="division"
                                                class="px-3 py-1 bg-pink-600 text-white rounded">Transfer
                                                Division</button>
                                            <button type="submit" name="_transfer_scope" value="all"
                                                class="px-3 py-1 bg-green-600 text-white rounded">Save All</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Watchers Tab -->
                <div id="watchers-tab" class="tab-content p-6" style="display:none;">
                    <div
                        class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg text-xs text-gray-700 leading-relaxed">
                        <strong class="text-purple-700">What is a Watcher?</strong> A watcher is a user who is
                        subscribed to this complaint for visibility and updates. Watchers are NOT responsible for
                        resolving the issue (unlike the assignee) but they receive updates, can monitor progress, and
                        provide input when necessary (e.g. managers, stakeholders, subject-matter experts). Use the list
                        below to add or remove watchers without affecting assignment or workflow status.
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Current Watchers</h4>
                            <div class="space-y-2">
                                @forelse($complaint->watchers as $watcher)
                                <div
                                    class="p-2 bg-white border border-gray-200 rounded flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $watcher->user->name }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-gray-500">No watchers.</p>
                                @endforelse
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Update Watchers</h4>
                            <form method="POST" action="{{ route('complaints.update-watchers', $complaint) }}">
                                @csrf
                                <select name="watchers[]" multiple size="8" class="w-full border-gray-300 rounded-md">
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $complaint->
                                        watchers->pluck('user_id')->contains($user->id) ?
                                        'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <div class="flex justify-end mt-3">
                                    <button type="submit"
                                        class="px-3 py-1 bg-indigo-600 text-white rounded text-sm">Save
                                        Watchers</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Satisfaction Tab -->
                <div id="satisfaction-tab" class="tab-content p-6" style="display:none;">
                    <div class="max-w-lg space-y-6">
                        <div class="bg-white p-4 border border-gray-200 rounded">
                            <h4 class="text-sm font-semibold text-gray-800 mb-3">Customer Satisfaction Score</h4>
                            <p class="text-xs text-gray-500 mb-3">Set a score from 1 (lowest) to 5 (highest). Only for
                                resolved/closed complaints.</p>
                            <form method="POST" action="{{ route('complaints.update-satisfaction', $complaint) }}">
                                @csrf
                                <select name="customer_satisfaction_score"
                                    class="w-full border-gray-300 rounded-md mb-3" required>
                                    <option value="">Select score</option>
                                    @for($i=1;$i<=5;$i++) <option value="{{ $i }}" {{ optional($complaint->
                                        metrics)->customer_satisfaction_score == $i ? 'selected' : '' }}>{{ $i }}
                                        </option>
                                        @endfor
                                </select>
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="px-3 py-1 bg-yellow-600 text-white rounded text-sm">Update
                                        Score</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- Escalation Modal -->
    <div id="escalation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Escalate Complaint</h3>
                <form method="POST" action="{{ route('complaints.escalate', $complaint) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="escalated_to" class="block text-sm font-medium text-gray-700">Escalate
                                To</label>
                            <select name="escalated_to" id="escalated_to" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="escalation_level" class="block text-sm font-medium text-gray-700">Escalation
                                Level</label>
                            <select name="escalation_level" id="escalation_level" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                                @for($i = 1; $i <= 5; $i++) <option value="{{ $i }}">Level {{ $i }}</option>
                                    @endfor
                            </select>
                        </div>
                        <div>
                            <label for="escalation_reason" class="block text-sm font-medium text-gray-700">Escalation
                                Reason</label>
                            <textarea name="escalation_reason" id="escalation_reason" rows="3" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" id="cancel-escalation"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Escalate Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Clean tab initialization (legacy snapshot PDF code removed)
        (function(){
            function initTabs(){
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');
                if(!tabButtons.length) return;
                tabContents.forEach(c=>c.style.display='none');
                const historyTab = document.getElementById('history-tab');
                if(historyTab) historyTab.style.display='block';
                tabButtons.forEach(btn=>{
                    if(btn.dataset.tabInit) return; btn.dataset.tabInit='1';
                    btn.addEventListener('click', e=>{
                        e.preventDefault();
                        const tabName = btn.getAttribute('data-tab');
                        tabButtons.forEach(b=>{ b.classList.remove('border-indigo-500','text-indigo-600'); b.classList.add('border-transparent','text-gray-500'); });
                        tabContents.forEach(tc=>tc.style.display='none');
                        btn.classList.add('border-indigo-500','text-indigo-600');
                        btn.classList.remove('border-transparent','text-gray-500');
                        const tgt = document.getElementById(tabName+'-tab');
                        if(tgt) tgt.style.display='block';
                    });
                    btn.addEventListener('keydown', e=>{ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); btn.click(); }});
                });
            }
            if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', initTabs); else initTabs();
        })();
    </script>

    <!-- Structured PDF generator (black & white, bank branding) -->
    <script>
        (function(){
            function loadScriptOnce(id, src){
                return new Promise((res, rej)=>{
                    if (document.getElementById(id)) return res(true);
                    const s=document.createElement('script'); s.id=id; s.src=src; s.onload=()=>res(true); s.onerror=()=>rej(new Error('Failed '+src)); document.head.appendChild(s);
                });
            }

            async function ensureLibs(){
                // Load jsPDF (with fallback) & autotable if missing
                async function attemptLoad(id, primary, fallback){
                    try { await loadScriptOnce(id, primary);} catch(e){ if(fallback){ await loadScriptOnce(id+'-fallback', fallback);} else throw e; }
                }
                if(!(window.jspdf && window.jspdf.jsPDF)){
                    await attemptLoad('jspdf-core','https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js','https://unpkg.com/jspdf@2.5.1/dist/jspdf.umd.min.js');
                }
                if(!(window.jspdf && window.jspdf.jsPDF)) throw new Error('jsPDF not loaded');
                if(!window.jspdf.jsPDF.API.autoTable){
                    await attemptLoad('jspdf-autotable','https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js','https://unpkg.com/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js');
                }
            }

            function fmtDate(dt){
                if(!dt) return '-';
                try { return new Date(dt).toLocaleString('en-GB',{day:'2-digit',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}); } catch(e){ return dt; }
            }

            function textWrap(str){ return (str||'').toString(); }

            function addSectionTitle(doc, title, y){
                doc.setFontSize(12); doc.setTextColor(30); doc.setFont('helvetica','bold'); doc.text(title.toUpperCase(), 40, y); doc.setFont('helvetica','normal'); return y+8; }

            function footer(doc, complaint){
                const pageCount = doc.getNumberOfPages();
                for (let i=1;i<=pageCount;i++){
                    doc.setPage(i);
                    const txt = `Complaint # ${complaint.complaint_number || '-'} | ID ${complaint.id || '-'} | Page ${i} of ${pageCount}`;
                    doc.setFontSize(8); doc.setTextColor(90);
                    const pageWidth = doc.internal.pageSize.getWidth();
                    const textWidth = doc.getTextWidth(txt);
                    doc.text(txt, (pageWidth/2)-(textWidth/2), doc.internal.pageSize.getHeight()-12);
                }
            }

            async function buildStructuredPdf(){
                const btn = document.getElementById('structured-pdf-btn');
                if(!btn) return;
                if(btn.dataset.init) return; btn.dataset.init='1';
                btn.addEventListener('click', async ()=>{
                    try {
                        btn.disabled=true; btn.classList.add('opacity-50'); btn.textContent='Building...';
                        await ensureLibs();
                        const { jsPDF } = window.jspdf;
                        const url = @json(route('complaints.full',$complaint));
                        const res = await fetch(url,{headers:{'Accept':'application/json'}});
                        if(!res.ok) throw new Error('Fetch failed');
                        const data = await res.json();
                        const c = data.complaint || {};
                        const doc = new jsPDF('p','pt');

                        // Header
                        // Header / Branding
                        doc.setFont('helvetica','bold');
                        doc.setFontSize(16); doc.setTextColor(20); doc.text('THE BANK OF AZAD JAMMU AND KASHMIR', 40, 40);
                        doc.setFontSize(13); doc.text('COMPLAINT REPORT', 40, 60);
                        doc.setFont('helvetica','normal');
                        doc.setFontSize(9); doc.setTextColor(70);
                        doc.text('Generated: '+fmtDate(data.exported_at), 40, 74);
                        doc.text('Complaint #: '+(c.complaint_number||'-'), 300, 74);
                        doc.text('Status: '+(c.status||'-'), 480, 74);

                        // Divider line
                        doc.setDrawColor(0); doc.setLineWidth(0.5); doc.line(40, 78, 555, 78);

                        let y = 95;
                        // Summary table
                        const summaryRows = [
                            ['Number', c.complaint_number || '-','Status', c.status || '-'],
                            ['Title', c.title || '-','Priority', c.priority || '-'],
                            ['Category', c.category || '-','Source', c.source || '-'],
                            ['Created', fmtDate(c.created_at),'Expected Due', fmtDate(c.expected_resolution_date)],
                            ['Assigned To', c.assigned_to?.name || '-','Assigned At', fmtDate(c.assigned_at)],
                            ['Branch', c.branch?.name || '-','Region', c.region?.name || '-'],
                            ['Division', c.division?.short_name || c.division?.name || '-','SLA Breached', c.sla_breached? 'Yes':'No']
                        ];
                        // Summary table (slightly reduced total width to avoid fractional overflow warnings)
                        doc.autoTable({
                            startY:y,
                            head:[['FIELD','VALUE','FIELD','VALUE']],
                            body:summaryRows,
                            styles:{fontSize:8,cellPadding:3, lineColor:[0,0,0], lineWidth:0.1, overflow:'linebreak'},
                            headStyles:{fillColor:[0,0,0], textColor:[255,255,255], fontStyle:'bold', halign:'left'},
                            columnStyles:{0:{cellWidth:94},1:{cellWidth:172},2:{cellWidth:94},3:{cellWidth:146}},
                            tableWidth:510,
                            theme:'grid'
                        });
                        y = doc.lastAutoTable.finalY + 15;

                        // Complainant
                        y = addSectionTitle(doc,'Complainant', y);
                        doc.autoTable({ startY:y, body:[
                            ['Name', c.complainant_name || '-'],
                            ['Email', c.complainant_email || '-'],
                            ['Phone', c.complainant_phone || '-'],
                            ['Account #', c.complainant_account_number || '-']
                        ], styles:{fontSize:8, cellPadding:3, lineColor:[0,0,0], lineWidth:0.1}, theme:'grid', headStyles:{fillColor:[0,0,0]}, head:[] });
                        y = doc.lastAutoTable.finalY + 15;

                        // Description
                        y = addSectionTitle(doc,'Description', y);
                        const desc = textWrap(c.description); const split = doc.splitTextToSize(desc || '-', 515); doc.setFontSize(9); doc.text(split, 40, y); y += (split.length*11)+10;

                        // Harassment details (if category is Harassment)
                        if((c.category||'').toLowerCase()==='harassment'){
                            y = addSectionTitle(doc,'Harassment Details', y);
                            const hRows = [
                                ['Incident Date', fmtDate(c.harassment_incident_date), 'Location', c.harassment_location || '-'],
                                ['Sub Category', c.harassment_sub_category || '-', 'Confidential', c.harassment_confidential? 'Yes':'No'],
                                ['Victim Emp #', c.harassment_employee_number || '-', 'Victim Phone', c.harassment_employee_phone || '-'],
                                ['Abuser Name', c.harassment_abuser_name || '-', 'Abuser Emp #', c.harassment_abuser_employee_number || '-'],
                                ['Abuser Phone', c.harassment_abuser_phone || '-', 'Abuser Email', c.harassment_abuser_email || '-'],
                                ['Relationship', c.harassment_abuser_relationship || '-', '', '']
                            ];
                            doc.autoTable({
                                startY:y,
                                head:[['FIELD','VALUE','FIELD','VALUE']],
                                body:hRows,
                                styles:{fontSize:8, cellPadding:3, lineColor:[0,0,0], lineWidth:0.1, overflow:'linebreak'},
                                headStyles:{fillColor:[0,0,0], textColor:[255,255,255]},
                                columnStyles:{0:{cellWidth:94},1:{cellWidth:172},2:{cellWidth:94},3:{cellWidth:146}},
                                tableWidth:510,
                                theme:'grid'
                            });
                            y = doc.lastAutoTable.finalY + 15;
                            if(c.harassment_details){
                                doc.setFontSize(9); doc.setTextColor(60); const hs = doc.splitTextToSize('Details: '+c.harassment_details, 515); doc.text(hs,40,y); y += hs.length*11 + 10;
                            }
                        }

                        // Grievance details (if category is Grievance)
                        if((c.category||'').toLowerCase()==='grievance'){
                            y = addSectionTitle(doc,'Grievance Details', y);
                            const gRows = [
                                ['Employee ID', c.grievance_employee_id || '-', 'Type', c.grievance_type || '-'],
                                ['Employment Start', fmtDate(c.grievance_employment_start_date), 'Policy Violated', c.grievance_policy_violated || '-'],
                                ['Prev Attempts', c.grievance_previous_attempts==null?'-':(c.grievance_previous_attempts? 'Yes':'No'), 'Subject Name', c.grievance_subject_name || '-'],
                                ['Subject Position', c.grievance_subject_position || '-', 'Relationship', c.grievance_subject_relationship || '-'],
                                ['Union Representation', c.grievance_union_representation==null?'-':(c.grievance_union_representation?'Yes':'No'), 'Anonymous', c.grievance_anonymous==null?'-':(c.grievance_anonymous?'Yes':'No')],
                                ['Acknowledgment', c.grievance_acknowledgment==null?'-':(c.grievance_acknowledgment?'Yes':'No'), 'First Occurred', fmtDate(c.grievance_first_occurred_date)],
                                ['Most Recent', fmtDate(c.grievance_most_recent_date), 'Pattern/Frequency', c.grievance_pattern_frequency || '-']
                            ];
                            doc.autoTable({
                                startY:y,
                                head:[['FIELD','VALUE','FIELD','VALUE']],
                                body:gRows,
                                styles:{fontSize:8, cellPadding:3, lineColor:[0,0,0], lineWidth:0.1, overflow:'linebreak'},
                                headStyles:{fillColor:[0,0,0], textColor:[255,255,255]},
                                columnStyles:{0:{cellWidth:94},1:{cellWidth:172},2:{cellWidth:94},3:{cellWidth:146}},
                                tableWidth:510,
                                theme:'grid'
                            });
                            y = doc.lastAutoTable.finalY + 15;
                            if(c.grievance_previous_attempts_details){
                                const pa = doc.splitTextToSize('Previous Attempts Details: '+c.grievance_previous_attempts_details, 515); doc.setFontSize(9); doc.text(pa,40,y); y += pa.length*11 + 8;
                            }
                            if(c.grievance_desired_outcome){
                                const dox = doc.splitTextToSize('Desired Outcome: '+c.grievance_desired_outcome, 515); doc.setFontSize(9); doc.text(dox,40,y); y += dox.length*11 + 8;
                            }
                            if(c.grievance_performance_effect){
                                const pe = doc.splitTextToSize('Performance Impact: '+c.grievance_performance_effect, 515); doc.setFontSize(9); doc.text(pe,40,y); y += pe.length*11 + 10;
                            }
                        }

                        // Metrics
                        if(c.metrics){
                            y = addSectionTitle(doc,'Performance Metrics', y);
                            const m = c.metrics;
                            const metricRows = [
                                ['Time to First Response (min)', m.time_to_first_response ?? '-'],
                                ['Time to Resolution (min)', m.time_to_resolution ?? '-']
                            ];
                            if(m.handling_duration !== undefined && m.handling_duration !== null){
                                metricRows.push(['Handling Duration (min)', m.handling_duration]);
                            }
                            metricRows.push(['Reopened Count', m.reopened_count ?? 0]);
                            metricRows.push(['Escalation Count', m.escalation_count ?? 0]);
                            metricRows.push(['Assignment Count', m.assignment_count ?? 0]);
                            metricRows.push(['Customer Satisfaction', m.customer_satisfaction_score ?? '-']);
                            doc.autoTable({ startY:y, body:metricRows, styles:{fontSize:8, cellPadding:3, lineColor:[0,0,0], lineWidth:0.1}, theme:'grid' });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Histories
                        if(Array.isArray(c.histories) && c.histories.length){
                            y = addSectionTitle(doc,'History', y);
                            const historyRows = c.histories.slice(0,150).map(h=>[
                                fmtDate(h.performed_at), h.action_type, h.performed_by?.name || '-', h.old_value || '-', h.new_value || '-', (h.comments||'').substring(0,80)
                            ]);
                            doc.autoTable({ startY:y, head:[['WHEN','ACTION','BY','OLD','NEW','COMMENTS']], body:historyRows, styles:{fontSize:7,cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Comments
                        if(Array.isArray(c.comments) && c.comments.length){
                            y = addSectionTitle(doc,'Comments', y);
                            const commentRows = c.comments.slice(0,100).map(cm=>[
                                fmtDate(cm.created_at), cm.creator?.name || '-', (cm.comment_type||'-'), (cm.comment_text||'').substring(0,120)
                            ]);
                            doc.autoTable({ startY:y, head:[['WHEN','BY','TYPE','COMMENT']], body:commentRows, styles:{fontSize:7,cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Assignments
                        if(Array.isArray(c.assignments) && c.assignments.length){
                            y = addSectionTitle(doc,'Assignments', y);
                            const assignRows = c.assignments.map(a=>[
                                fmtDate(a.assigned_at), a.assigned_to?.name || '-', a.assigned_by?.name || '-', a.assignment_type, a.is_active? 'Active':'Inactive'
                            ]);
                            doc.autoTable({ startY:y, head:[['WHEN','TO','BY','TYPE','ACTIVE']], body:assignRows, styles:{fontSize:7, cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Escalations
                        if(Array.isArray(c.escalations) && c.escalations.length){
                            y = addSectionTitle(doc,'Escalations', y);
                            const escRows = c.escalations.map(e=>[
                                e.escalation_level, fmtDate(e.escalated_at), e.escalated_from?.name || '-', e.escalated_to?.name || '-', (e.escalation_reason||'').substring(0,60)
                            ]);
                            doc.autoTable({ startY:y, head:[['LEVEL','WHEN','FROM','TO','REASON']], body:escRows, styles:{fontSize:7, cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Watchers
                        if(Array.isArray(c.watchers) && c.watchers.length){
                            y = addSectionTitle(doc,'Watchers', y);
                            const wRows = c.watchers.map(w=>[ w.user?.name || '-', w.user?.email || '-' ]);
                            doc.autoTable({ startY:y, head:[['NAME','EMAIL']], body:wRows, styles:{fontSize:8, cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Witnesses
                        if(Array.isArray(c.witnesses) && c.witnesses.length){
                            y = addSectionTitle(doc,'Witnesses', y);
                            const witRows = c.witnesses.map(w=>[ w.name, w.employee_number||'-', w.phone||'-', w.email||'-', (w.statement||'').substring(0,50) ]);
                            doc.autoTable({ startY:y, head:[['NAME','EMP #','PHONE','EMAIL','STATEMENT']], body:witRows, styles:{fontSize:7, cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Category Reference
                        if(Array.isArray(data.categories) && data.categories.length){
                            y = addSectionTitle(doc,'Category Reference', y);
                            const catRows = data.categories.map(cat=>[cat.category_name, cat.default_priority, cat.sla_hours, cat.is_active?'Yes':'No']);
                            doc.autoTable({ startY:y, head:[['NAME','DEFAULT PRIORITY','SLA HOURS','ACTIVE']], body:catRows.slice(0,40), styles:{fontSize:7, cellPadding:2, lineColor:[0,0,0], lineWidth:0.1}, headStyles:{fillColor:[0,0,0], textColor:[255,255,255]} });
                            y = doc.lastAutoTable.finalY + 15;
                        }

                        // Footer page numbers
                        footer(doc, c);

                        doc.save('complaint-'+(c.complaint_number||c.id||'export')+'.pdf');
                    } catch(e){
                        console.error(e);
                        alert('Failed to build structured PDF: '+e.message);
                    } finally {
                        btn.disabled=false; btn.classList.remove('opacity-50'); btn.textContent='Download Structured PDF';
                    }
                });
            }
            if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', buildStructuredPdf); } else { buildStructuredPdf(); }
        })();
    </script>

    <!-- Load SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
</x-app-layout>