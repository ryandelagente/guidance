<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">🔔 Notification Preferences</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('notification-preferences.update') }}" class="space-y-4">
                @csrf @method('PATCH')

                {{-- Channels --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">How should we reach you?</h3>
                    <div class="space-y-3">
                        @foreach(['email' => ['Email', '📧 Sent to your account email — best for non-urgent items.'], 'sms' => ['SMS', '📱 Phone text messages — best for urgent reminders. Requires phone number.'], 'inapp' => ['In-app', '🔔 Bell icon notifications inside the GMS — always on.']] as $key => $info)
                        <label class="flex items-start gap-3 p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-300">
                            <input type="checkbox" name="channels[{{ $key }}]" value="1"
                                   @checked($prefs['channels'][$key])
                                   {{ $key === 'inapp' ? 'disabled checked' : '' }}
                                   class="rounded border-gray-300 mt-0.5">
                            <div class="flex-1">
                                <div class="font-medium text-gray-800 text-sm">{{ $info[0] }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $info[1] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400 font-normal">(for SMS)</span></label>
                        <input type="text" name="phone_number" value="{{ auth()->user()->phone_number }}"
                               placeholder="+63 9XX XXX XXXX"
                               class="w-full border-gray-300 rounded-md text-sm">
                        <p class="text-xs text-gray-400 mt-1">Standard PH carrier rates may apply if SMS is enabled by your guidance office.</p>
                    </div>
                </div>

                {{-- Events --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Which events should notify me?</h3>
                    @php
                        $eventLabels = [
                            'appointment_reminder'   => ['Appointment reminders', 'Day-before reminder for confirmed appointments'],
                            'appointment_confirmed'  => ['Appointment confirmation', 'When your counselor confirms an appointment'],
                            'referral_assigned'      => ['Referral assigned', 'When a faculty referral is assigned to me'],
                            'message_received'       => ['New secure messages', 'When someone sends you a message in the GMS'],
                            'announcement_urgent'    => ['Urgent announcements', '🚨 Critical office-wide announcements (recommended)'],
                            'announcement_normal'    => ['Regular announcements', 'Routine info & event announcements'],
                            'wellness_crisis_alert'  => ['Crisis alerts (staff)', 'When a student\'s check-in indicates immediate concern'],
                            'data_correction_response' => ['Data correction updates', 'Response to your privacy/correction requests'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($eventLabels as $key => [$label, $desc])
                        <label class="flex items-start gap-3 p-2.5 hover:bg-gray-50 rounded-lg cursor-pointer">
                            <input type="checkbox" name="events[{{ $key }}]" value="1"
                                   @checked($prefs['events'][$key] ?? false)
                                   class="rounded border-gray-300 mt-0.5">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-800">{{ $label }}</div>
                                <div class="text-xs text-gray-500">{{ $desc }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2.5 rounded-md">
                        Save Preferences
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
