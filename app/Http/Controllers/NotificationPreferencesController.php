<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $prefs = $user->getNotificationPrefsResolved();
        return view('notification-preferences.edit', compact('prefs'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'channels'        => 'array',
            'channels.email'  => 'sometimes|boolean',
            'channels.sms'    => 'sometimes|boolean',
            'channels.inapp'  => 'sometimes|boolean',
            'events'          => 'array',
            'phone_number'    => 'nullable|string|max:30',
        ]);

        $events = collect(\App\Models\User::defaultNotificationPreferences()['events'])
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => (bool) ($data['events'][$key] ?? false)])
            ->toArray();

        $prefs = [
            'channels' => [
                'email' => (bool) ($data['channels']['email'] ?? false),
                'sms'   => (bool) ($data['channels']['sms'] ?? false),
                'inapp' => (bool) ($data['channels']['inapp'] ?? true),
            ],
            'events' => $events,
        ];

        $user->forceFill([
            'notification_preferences' => $prefs,
            'phone_number'             => $data['phone_number'] ?? null,
        ])->save();

        return back()->with('success', 'Notification preferences saved.');
    }
}
