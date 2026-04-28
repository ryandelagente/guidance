<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">📖 Help & User Guide</h2>
    </x-slot>

    @php
        $role = auth()->user()->role;
        $isStaff = in_array($role, ['guidance_counselor','guidance_director','super_admin']);
        $isStudent = $role === 'student';
        $isFaculty = $role === 'faculty';
        $isAdmin = $role === 'super_admin';

        // All modules with role-aware metadata
        $modules = [
            [
                'id' => 'getting-started',
                'icon' => '🚀',
                'title' => 'Getting Started',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'Signing in',
                        'p' => 'Use your CHMSU email and password on the Sign In page. If you forgot your password, click "Forgot password?" on the login form to receive a reset link.',
                    ],
                    [
                        'h' => 'Navigating the system',
                        'p' => 'The red sidebar on the left contains every module you have access to. Your role determines which links appear. The bell icon in the top bar shows pending items that need your attention. Your name is at the bottom of the sidebar — click "Profile" to update your account or "Log Out" to end your session.',
                    ],
                    [
                        'h' => 'Notifications',
                        'p' => 'The bell icon in the top-right shows a red badge when you have pending appointments, unanswered referrals, pending clearance requests, or unread messages. Click it for a quick summary with direct links to each item.',
                    ],
                    [
                        'h' => 'Global Search (⌘K / Ctrl+K)',
                        'p' => 'Use the search bar in the top header to find anything fast. Type at least 2 characters — results group by module (Students, Appointments, Referrals, Certificates, Announcements, Resources, Workshops). Use the keyboard: ⌘K (Mac) or Ctrl+K (Windows) to focus the search bar from any page; arrow keys to navigate results; Enter to open; Esc to close. What you can search depends on your role — counselors see only their cases, students see only their own appointments.',
                    ],
                    [
                        'h' => '🔐 Single Sign-On (Google / Microsoft 365)',
                        'p' => 'If your administrator has configured SSO, you can sign in with Google or Microsoft 365 instead of typing a password. Only @chmsu.edu.ph addresses are accepted. SSO works alongside 2FA. If the SSO buttons don\'t appear on the login page, your campus hasn\'t enabled them yet — use email + password as usual.',
                    ],
                    [
                        'h' => '🛡️ Two-Factor Authentication',
                        'p' => 'Add a second layer of security on top of your password. Click the 🛡️ icon next to your Profile button at the bottom of the sidebar (or visit My Account → 🛡️) to set up. You\'ll scan a QR code with Google Authenticator, Microsoft Authenticator, or Authy. After that, every login asks for a 6-digit code from your phone. The setup gives you 8 single-use recovery codes — save them in a password manager so you can still log in if you lose your phone. Strongly recommended for staff handling confidential student data.',
                    ],
                    [
                        'h' => '🔔 Notification preferences',
                        'p' => 'Visit the 🔔 Notifications link at the bottom of the sidebar to control how the GMS reaches you: email, SMS (if your phone number is set), and in-app. You can toggle each event individually — appointment reminders, message alerts, urgent announcements, etc. In-app notifications are always on. SMS requires your phone number and that your campus has configured an SMS provider (Semaphore for PH, Twilio for international).',
                    ],
                    [
                        'h' => '🌗 Dark mode',
                        'p' => 'Click the moon/sun icon in the top header to toggle between light and dark mode. Your choice is remembered in your browser. Useful for late-evening sessions when reading screens.',
                    ],
                    [
                        'h' => '📱 Install as an app (PWA)',
                        'p' => 'On Android Chrome / iPhone Safari, you can "Install" or "Add to Home Screen" — the GMS becomes a standalone app icon on your phone, opens fullscreen without the browser bar. Look for the install prompt in your browser\'s menu.',
                    ],
                ],
            ],
            [
                'id' => 'student-profiles',
                'icon' => '👥',
                'title' => 'Student Profiles',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'A digital cumulative record for every CHMSU student — personal info, family background, academic standing, emergency contacts, and uploaded documents.',
                    ],
                    [
                        'h' => 'Adding a student',
                        'p' => 'Go to "Student Profiles" → "+ New Student". Fill in their academic and personal information. The system auto-creates a login account for them using their student ID number @chmsu.edu.ph and a default password they can reset on first login.',
                    ],
                    [
                        'h' => 'Viewing the activity timeline',
                        'p' => 'Click the purple "📋 Timeline" button on any student page to see every event involving them — appointments, sessions, referrals, disciplinary records, test results, certificates — in one chronological view.',
                    ],
                    [
                        'h' => 'Uploading documents',
                        'p' => 'On the student\'s profile page, expand "+ Upload Document" in the Documents section. Choose a type (Birth Certificate, Medical, etc.) and upload PDF/image/doc up to 10MB.',
                    ],
                ],
            ],
            [
                'id' => 'caseload',
                'icon' => '📋',
                'title' => 'My Caseload',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Your active student list with auto-computed risk indicators. Counselors see only their assigned students; Directors can switch counselors via the dropdown.',
                    ],
                    [
                        'h' => 'Reading the risk levels',
                        'p' => 'High risk = academic_status is "at_risk" OR 2+ active referrals. Medium = "probation" OR 1 active referral. Low = good standing with no active concerns.',
                    ],
                    [
                        'h' => 'Spotting students who need attention',
                        'p' => 'The "No Contact 30+ Days" stat at the top shows students you haven\'t had a session with in over a month. Click any student to open their profile or timeline.',
                    ],
                ],
            ],
            [
                'id' => 'appointments',
                'icon' => '📅',
                'title' => 'Appointments',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'For students — booking',
                        'p' => 'Go to Appointments → "+ Book Appointment". Pick a counselor and date — available slots will appear. Select a time, choose your concern type, and submit. The counselor will confirm via email.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For staff — managing',
                        'p' => 'The Appointments page has both a List view (filterable table) and a Calendar view (monthly grid). Click any appointment to confirm/cancel/complete it. After a session is marked "completed", a "Add Notes" button appears so you can attach a Counseling Session record.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Calendar view',
                        'p' => 'Toggle to Calendar mode in the upper-left of the page. Color-coded pills show appointments per day (yellow = pending, blue = confirmed, green = completed). Click prev/next arrows to navigate months.',
                    ],
                    [
                        'h' => 'Automatic reminders (email + SMS)',
                        'p' => 'Confirmed appointments automatically generate reminder emails to both the student and counselor at 8:00 AM Manila time the day before. If a user has SMS notifications enabled and a phone number on file, they also get a text reminder. Reminders fire only once per appointment. Cancelled or pending ones don\'t trigger reminders. For this to work in production, the OS cron must run "php artisan schedule:run" every minute, and SMS_DRIVER must be configured.',
                    ],
                    [
                        'h' => 'Auto-generated meeting links (virtual)',
                        'p' => 'When booking a virtual appointment, the system auto-generates a unique Jitsi Meet room link (free, public, no API key needed). The link is included in the appointment record, the confirmation email, and the reminder. Counselors can replace it with a Zoom or Google Meet link if preferred — just edit the appointment and paste the new URL.',
                    ],
                ],
            ],
            [
                'id' => 'sessions',
                'icon' => '📝',
                'title' => 'Counseling Sessions / Case Notes',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Confidential session notes for each counseling encounter. Notes are AES-256 encrypted at rest — even database admins can\'t read them in plain text without the application key.',
                    ],
                    [
                        'h' => '🔐 Case-Note PIN (required)',
                        'p' => 'The first time you open Case Notes, you\'ll be asked to set up a personal 4–6 digit PIN. This is required in addition to your account password by CHMSU policy and the Data Privacy Act. You confirm setup with your account password. After that, you enter your PIN once per browsing session and it stays unlocked for 15 minutes — you\'ll see a "🔓 Unlocked" badge with the remaining time on the Case Notes page, plus a "🔒 Lock now" button if you step away from your desk. Failed PIN attempts are rate-limited (5 tries, then a 60-second cooldown) and recorded in the audit log.',
                    ],
                    [
                        'h' => 'Forgot or changing your PIN',
                        'p' => 'Click "Forgot or want to change your PIN?" on the verify screen. You\'ll need to confirm with your account password before setting a new PIN. If you can\'t remember either, ask the Super Admin — note that any administrative reset is logged.',
                    ],
                    [
                        'h' => 'Creating a case note',
                        'p' => 'After completing an appointment, click "Add Notes" on the appointments list, or go to "Case Notes" → "+ New Session". Record the presenting concern, your notes, recommendations, and an optional follow-up date.',
                    ],
                    [
                        'h' => 'Follow-up date',
                        'p' => 'Setting a follow-up date makes the student appear in your "Pending Follow-ups" caseload counter. Use this to track who you should reach out to next.',
                    ],
                ],
            ],
            [
                'id' => 'group-sessions',
                'icon' => '👥',
                'title' => 'Group Counseling Sessions',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Schedule and run sessions with multiple students at once — peer support groups, anti-bullying circles, anxiety/grief/depression groups, social skills workshops. Each session has a focus area, a venue, capacity, and a participant roster.',
                    ],
                    [
                        'h' => 'Creating a group session',
                        'p' => 'Group Sessions → "+ New Group Session". Set focus, date, capacity, venue. Add initial participants from the multi-select (Ctrl/Cmd-click for multiple). You can add more later from the session detail page.',
                    ],
                    [
                        'h' => 'During / after the session',
                        'p' => 'On the session detail page, switch each participant\'s attendance status (Registered → Attended / No-show / Withdrew) directly from the table. Add Group Notes describing themes discussed and key takeaways — but NOT individual disclosures (those belong in the participant\'s individual case notes).',
                    ],
                    [
                        'h' => 'Confidentiality reminder',
                        'p' => 'Remind participants at the start of each group that what\'s shared in the room stays in the room. Group notes you write here are visible to other staff with case-note access — keep them about themes, not specific personal disclosures.',
                    ],
                ],
            ],
            [
                'id' => 'screening',
                'icon' => '📋',
                'title' => 'Mental Health Screening (PHQ-9 / GAD-7 / K-10)',
                'roles' => ['student','staff'],
                'sections' => [
                    [
                        'h' => 'What these are',
                        'p' => 'Three industry-standard, validated mental-health screening instruments used by clinicians worldwide: PHQ-9 (depression, 9 items), GAD-7 (anxiety, 7 items), K-10 (general psychological distress, 10 items). All are public-domain, widely used in PH mental health settings.',
                    ],
                    [
                        'h' => 'For students — taking a screening',
                        'p' => 'Sidebar → Mental Health Screening. Pick the test that fits — depression, anxiety, or general distress. Each takes 1-2 minutes. After submitting, you see your score and severity level. Your assigned counselor is notified automatically if your score is severe or if you indicate self-harm thoughts.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For staff — reviewing results',
                        'p' => 'Sidebar → Screening shows the dashboard with stats: severe results, self-harm flagged, unreviewed, last 30 days. Each entry can be opened and marked Reviewed with internal notes. Critical results (severe severity OR PHQ-9 Q9 self-harm flag) auto-email the assigned counselor + director.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Score interpretation',
                        'p' => 'PHQ-9: 0-4 minimal, 5-9 mild, 10-14 moderate, 15-19 moderately severe, 20+ severe. GAD-7: 0-4 minimal, 5-9 mild, 10-14 moderate, 15+ severe. K-10: under 20 low, 20-24 mild, 25-29 moderate, 30+ severe distress. These are published cutoffs from the original validation studies.',
                    ],
                    [
                        'h' => 'Important caveat',
                        'p' => 'Screening tools are NOT diagnostic. A high score signals "let\'s have a conversation," not "you have X disorder." Pair every screening with a counseling session. Never use a screening result as the sole basis for clinical decisions.',
                    ],
                ],
            ],
            [
                'id' => 'crisis-escalation',
                'icon' => '🚨',
                'title' => 'Crisis Escalation (Auto)',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What triggers it',
                        'p' => 'Two automatic systems flag students who may need immediate help: (1) wellness check-ins where mood ≤2, stress ≥4, AND the student ticked "wants counselor" — OR notes contain self-harm language; (2) any PHQ-9, GAD-7, or K-10 result with severe severity, OR PHQ-9 Q9 (self-harm thoughts) > 0.',
                    ],
                    [
                        'h' => 'What happens',
                        'p' => 'When triggered, the system immediately emails the student\'s assigned counselor and ALL active directors/admins with subject "🚨 CRISIS ALERT — IMMEDIATE REVIEW". The email contains the trigger, scores, the student\'s notes (if any), and a link to the wellness check-in or screening result. The alert is also recorded in the audit log for compliance review.',
                    ],
                    [
                        'h' => 'Best practices',
                        'p' => 'Treat every crisis alert as urgent. Reach out to the student within 1-2 hours during office hours, same evening if outside hours. If you can\'t reach the student promptly, escalate to NCMH 1553 — better to over-respond than miss a critical case.',
                    ],
                ],
            ],
            [
                'id' => 'messages',
                'icon' => '💬',
                'title' => 'Secure Messaging',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Direct counselor↔student messaging that stays inside the GMS. Message bodies are AES-256 encrypted at rest — even database admins can\'t read them in plain text. This is the recommended channel for confidential follow-ups so nothing leaks through personal email or SMS.',
                    ],
                    [
                        'h' => 'Starting a conversation (counselor)',
                        'p' => 'Messages → "+ New Message". Pick a student from the dropdown, optionally add a subject, type your message, and send. Each counselor↔student pair has one persistent thread — sending again to the same student adds to the existing conversation.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Replying',
                        'p' => 'Click any conversation in your inbox to open the thread. Type in the box at the bottom and click "Send" — or press Ctrl/⌘ + Enter to send without lifting your hands from the keyboard. Messages from the other person are auto-marked as read when you open the conversation.',
                    ],
                    [
                        'h' => 'Read receipts',
                        'p' => 'Once you send a message, you\'ll see "✓ Sent" until the other person opens the conversation, at which point it changes to "✓✓ Read" with a hover tooltip showing exactly when. This helps counselors know whether a follow-up is needed.',
                    ],
                    [
                        'h' => 'Unread badge',
                        'p' => 'The bell icon in the top header shows your total unread count across all conversations. Conversations with unread messages also have a blue tint and an unread counter in your inbox.',
                    ],
                ],
            ],
            [
                'id' => 'referrals',
                'icon' => '🚩',
                'title' => 'Referrals',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'For faculty — submitting',
                        'p' => 'Use Referrals → "Submit Referral" if you notice a student exhibiting concerning behavior (excessive absences, failing grades, signs of distress). Faculty can track the status of their submitted referrals on the same page.',
                        'roles' => ['faculty'],
                    ],
                    [
                        'h' => 'For counselors — handling',
                        'p' => 'New referrals appear at the top of the Referrals list. Acknowledge → assign yourself → take action → resolve. Add interventions as you work the case so the referring faculty member knows progress is being made.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Urgency levels',
                        'p' => 'Critical urgency requires action within 24 hours. High = within 3 days. Medium = within a week. Low = no fixed deadline.',
                    ],
                ],
            ],
            [
                'id' => 'walk-in',
                'icon' => '🚪',
                'title' => 'Walk-in Queue',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Real-time tracker of walk-in visitors at the Guidance Office today. Reception checks people in; counselors call them when ready.',
                    ],
                    [
                        'h' => 'Checking someone in',
                        'p' => 'Click "+ Check In" → search for them in the dropdown if they\'re a registered student, or type their name as a walk-in guest. Set the priority (normal / urgent / 🚨 crisis) and reason for visit.',
                    ],
                    [
                        'h' => 'Calling the next person',
                        'p' => 'Click "📞 Call" on a row in the Waiting Queue — they move to the "Currently Being Seen" section. When done, click "✓ Complete". Crisis rows are highlighted red and should always be seen first.',
                    ],
                ],
            ],
            [
                'id' => 'wellness',
                'icon' => '💭',
                'title' => 'Wellness Check-ins',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'For students',
                        'p' => 'Quick 30-second mood check-in: rate your mood (😢→😄), stress, sleep, and academic load. Optional note. Tick the "I\'d like to speak with a counselor" box to flag yourself for outreach. One check-in per day.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For staff — monitoring',
                        'p' => 'The staff Wellness page shows a board of recent check-ins with auto-computed risk levels. High-risk + counselor-requested rows are highlighted amber so you can prioritize outreach. Click "Mark Reviewed" once you\'ve acted.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Risk score',
                        'p' => 'Calculated from inverted mood + stress + inverted sleep + academic stress. Anything ≥4 (or any check-in flagged "wants counselor") is automatically high-risk.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'action-plans',
                'icon' => '🎯',
                'title' => 'Action Plans',
                'roles' => ['staff','student'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Goal-tracking plans agreed between counselor and student. Like an IEP, each plan has milestones the student works toward, with target dates and progress tracking.',
                    ],
                    [
                        'h' => 'Creating a plan (counselor)',
                        'p' => 'Action Plans → "+ New Plan" → pick the student → write title, focus area, dates → add as many milestones as needed. Each milestone can have its own target date.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Tracking progress',
                        'p' => 'On the plan detail page, click the circle next to a milestone to mark it complete. The progress bar at the top updates automatically. Once everything\'s done, change the plan\'s status to "Completed" and add Outcome Notes describing the result.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Viewing your plans (student)',
                        'p' => 'You\'ll see plans your counselor has created with you. Watch the milestones — you can\'t edit them, but they show what you and your counselor agreed to work on together.',
                        'roles' => ['student'],
                    ],
                ],
            ],
            [
                'id' => 'workshops',
                'icon' => '🎓',
                'title' => 'Workshops & Events',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'Browsing & RSVPing',
                        'p' => 'The Workshops page shows upcoming events as cards. Click "RSVP" on any card you want to attend. Once registered, the card shows "✓ You\'re registered" — click "Cancel RSVP" if your plans change.',
                    ],
                    [
                        'h' => 'Workshop modes',
                        'p' => 'In-person events have a venue. Virtual events show their meeting link only after you RSVP. Hybrid events have both.',
                    ],
                    [
                        'h' => 'For staff — creating',
                        'p' => 'Workshops → "+ New Workshop". Set capacity, RSVP deadline, and category. Once it starts, the card shows a "LIVE" indicator. After completion, mark attendees as "Attended" from the workshop\'s detail page.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'announcements',
                'icon' => '📢',
                'title' => 'Announcements',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'Reading announcements',
                        'p' => 'Important office-wide updates appear here. Pinned announcements stay at the top. Urgent (red) and warning (yellow) ones use color-coded borders so you don\'t miss them.',
                    ],
                    [
                        'h' => 'Posting (staff)',
                        'p' => 'Announcements → "+ New Announcement". Pick the audience (Everyone / Students / Staff / Faculty), priority, and an optional expiry date. Pin urgent items so they\'re seen first.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'resources',
                'icon' => '📚',
                'title' => 'Resource Library',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'Crisis hotlines',
                        'p' => 'The top of the Resources page always shows PH crisis hotlines (NCMH 1553, In Touch, Hopeline) — bookmark this page if you ever need quick access.',
                    ],
                    [
                        'h' => 'Browsing articles',
                        'p' => 'Articles, videos, and guides organized by category: mental health, academic, career, financial, self-care, relationships. Use the search bar to find something specific.',
                    ],
                    [
                        'h' => 'Adding resources (staff)',
                        'p' => 'Click "+ Add Resource". Pick a type (article / video / hotline / PDF / link). Mark "Emergency" to pin a hotline at the top of the page for everyone.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'concerns',
                'icon' => '🤝',
                'title' => 'Anonymous Concerns',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'Submitting anonymously',
                        'p' => 'Anyone — including non-students — can use the public form at /concerns to report bullying, mental health concerns about someone, abuse, threats, etc. No login is required. After submitting, you receive a reference code (TIP-XXXXXXXX) to track the status without revealing your identity.',
                    ],
                    [
                        'h' => 'Tracking your report',
                        'p' => 'Save your reference code from the confirmation page. Visit /concerns/track and enter the code to see whether the report is being reviewed, action has been taken, or it\'s been resolved.',
                    ],
                    [
                        'h' => 'For staff — triaging',
                        'p' => 'Anonymous Tips → review each concern. Update the status (Reviewing → Action Taken → Resolved) and add private staff notes. Critical-urgency entries are auto-highlighted at the top.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'testing',
                'icon' => '🧪',
                'title' => 'Psychological Testing',
                'roles' => ['staff','student'],
                'sections' => [
                    [
                        'h' => 'Test Inventory (staff)',
                        'p' => 'Catalog of psychological tests (IQ, personality, career aptitude, etc.). Add new tests with their administration time, scoring method, and category.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Testing Sessions (staff)',
                        'p' => 'Schedule batch testing for specific colleges or year levels. Track who showed up and who completed.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Test Results',
                        'p' => 'Counselors record raw scores, percentiles, and interpretations. Students only see results that have been "released" — counselors hold release until they\'ve interpreted them.',
                    ],
                ],
            ],
            [
                'id' => 'riasec',
                'icon' => '🎯',
                'title' => 'Career Interest Test (RIASEC)',
                'roles' => ['student','staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'A 60-question Holland Code (RIASEC) self-assessment that helps students understand the kinds of work they naturally enjoy. After taking the test, students get a 3-letter top code (e.g. SAI = Social-Artistic-Investigative) and a list of 12 career paths that match their interests.',
                    ],
                    [
                        'h' => 'Taking the test (student)',
                        'p' => 'Career Test → "✨ Take the Test". For each of the 60 statements, click 👍 Like if it sounds appealing or 👎 Dislike if it doesn\'t. There are no right or wrong answers — go with your gut. Takes about 10 minutes. The progress bar at the top tracks how many you\'ve answered. Submit only enables once all 60 are answered.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'Understanding your results',
                        'p' => 'The 6 RIASEC types are: Realistic (Doer), Investigative (Thinker), Artistic (Creator), Social (Helper), Enterprising (Persuader), Conventional (Organizer). Your top 3 codes form your Holland Code. The score breakdown shows how many "likes" you scored per type out of 10. Career suggestions are derived from real workforce data linked to those codes.',
                    ],
                    [
                        'h' => 'Retaking the test',
                        'p' => 'Students can retake the test anytime — past results are kept in your history so you can see how your interests evolve. Counselors should encourage retaking after major experiences (internship, immersion, big life event).',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For counselors — reviewing results',
                        'p' => 'The staff Career Test page lists every completed assessment with a score grid (R/I/A/S/E/C columns) and the student\'s top code. Filter by top code to find students with similar profiles. Click "View Profile" to see the full breakdown and career suggestions, then schedule a meeting to discuss what the results mean for their major or post-graduation plans.',
                        'roles' => ['staff'],
                    ],
                    [
                        'h' => 'Important caveat',
                        'p' => 'RIASEC is a starting point for career exploration, not a verdict. The system explicitly tells students this on their results page. Always pair the test with a counseling conversation so the student understands their results in context, not as a fixed prediction.',
                    ],
                ],
            ],
            [
                'id' => 'disciplinary',
                'icon' => '⚠️',
                'title' => 'Disciplinary Records',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'Recording offenses',
                        'p' => 'Disciplinary → "+ New Record". Track minor and major offenses with the date, description, action taken, and sanction. Linked to the student\'s cumulative record for full history.',
                    ],
                    [
                        'h' => 'Status workflow',
                        'p' => 'Pending → Under Review → Resolved/Dismissed. A sanction can have an end date that auto-tracks when it expires.',
                    ],
                ],
            ],
            [
                'id' => 'clearance',
                'icon' => '✅',
                'title' => 'Clearance & Exit Interviews',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'For students — requesting',
                        'p' => 'Clearance → "+ Request Clearance". For graduating students, you must complete the Exit Interview survey before clearance can be marked Approved. The survey has 10 questions about your CHMSU experience.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For staff — processing',
                        'p' => 'Review pending clearance requests, verify any outstanding records (disciplinary, library, fees), and approve/reject. Add reasons when rejecting so the student knows what\'s blocking.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'certificates',
                'icon' => '🎓',
                'title' => 'Good Moral Certificates',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'Issuing a certificate',
                        'p' => 'Certificates → "+ Issue Certificate". Select the student, purpose (employment, scholarship, etc.), validity period. The system generates a unique certificate number automatically.',
                    ],
                    [
                        'h' => 'Printing & PDF',
                        'p' => 'On the certificate detail page, click "🖨 Print" for browser-native print or "⬇ Download PDF" for a properly formatted A4 PDF generated by the system.',
                    ],
                    [
                        'h' => 'Revoking',
                        'p' => 'If a certificate needs to be invalidated (e.g. issued in error, student lost good standing), click Revoke and provide a reason. The certificate stays in the database for audit purposes but is marked as revoked.',
                    ],
                ],
            ],
            [
                'id' => 'session-feedback',
                'icon' => '⭐',
                'title' => 'Service Feedback',
                'roles' => ['all'],
                'sections' => [
                    [
                        'h' => 'For students',
                        'p' => 'After a counseling session is marked complete, you\'ll get a link to rate your experience. Star-rate four areas (overall, helpfulness, listening, comfort) and add optional comments. Anonymous to your counselor — only aggregate stats are shown.',
                        'roles' => ['student'],
                    ],
                    [
                        'h' => 'For staff — reviewing aggregate',
                        'p' => 'Service Feedback dashboard shows averages by counselor + recommend % + concern-resolved %. Director can filter by counselor for QA reviews. Useful evidence for CHED accreditation.',
                        'roles' => ['staff'],
                    ],
                ],
            ],
            [
                'id' => 'analytics',
                'icon' => '📊',
                'title' => 'Analytics & Reports',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'Dashboard',
                        'p' => 'Real-time charts of student demographics, counseling volume, common concerns, referral categories. All filterable by year/month.',
                    ],
                    [
                        'h' => 'CHED-ready accomplishment report',
                        'p' => 'Click "📄 Print Report" on the analytics page. Generates a Republic-of-Philippines-letterhead formal report with KPIs, percentage tables, monthly trends, and a signature block (Prepared by / Reviewed by / Noted by).',
                    ],
                    [
                        'h' => 'CSV exports',
                        'p' => 'Export buttons let you download students, appointments, referrals, or disciplinary records as CSV with UTF-8 BOM (opens directly in Excel without garbled characters).',
                    ],
                ],
            ],
            [
                'id' => 'schedule-matrix',
                'icon' => '📊',
                'title' => 'Schedule Matrix',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'A Director\'s-eye view of every counselor\'s availability for any given week, all on one screen. Rows = counselors, columns = days. Each cell shows the schedule range, free slots, and a color-coded fill bar.',
                    ],
                    [
                        'h' => 'Reading the colors',
                        'p' => '🟢 Green = fully open. 🟡 Yellow = partially booked. 🟠 Orange = busy (≥75% booked). 🔴 Red = fully booked. ⚪ Gray = day off / no schedule. Today\'s column is outlined in blue.',
                    ],
                    [
                        'h' => 'Use cases',
                        'p' => 'A walk-in arrives needing urgent help — at a glance, see who has the next free slot. Faculty refers a student — see which counselor has the most capacity to take it. Director plans staffing — spot weeks where the office is overloaded.',
                    ],
                    [
                        'h' => 'Navigation',
                        'p' => 'Use the « and » arrows to move between weeks, or "This week" to jump back to the current one. The Utilization stat at the top right shows what % of all available slots across the office are booked — useful for spotting overload trends.',
                    ],
                ],
            ],
            [
                'id' => 'cumulative-record',
                'icon' => '📄',
                'title' => 'Cumulative Record (PDF)',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'A printable, multi-page PDF that bundles a student\'s entire guidance history into one official-looking document — the digital equivalent of the physical "brown envelope". Includes profile, family background, emergency contacts, all appointments, all sessions logged (count only — case notes themselves stay confidential), referrals, disciplinary records, test results, certificates, and uploaded documents.',
                    ],
                    [
                        'h' => 'How to print',
                        'p' => 'Open any student profile and click the "📄 Print Record" button at the top right. The PDF downloads instantly, A4 portrait, ready for printing or archival.',
                    ],
                    [
                        'h' => 'Who can use it',
                        'p' => 'Counselors and admin only. Each export is logged in the audit trail — including who exported, when, and which student.',
                    ],
                    [
                        'h' => 'When to use',
                        'p' => 'When a student transfers schools and you need to send the complete guidance record. For year-end archival to physical filing. As an attachment to formal correspondence about a student. For CHED accreditation review when a sample student record is requested.',
                    ],
                    [
                        'h' => 'What\'s NOT included',
                        'p' => 'Confidential case-note bodies are intentionally excluded — only the count and metadata (date, concern, status) appear. To attach actual case notes, the receiving guidance office must send a written formal request to CHMSU\'s Guidance Office.',
                    ],
                ],
            ],
            [
                'id' => 'performance',
                'icon' => '📈',
                'title' => 'Counselor Performance',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Per-counselor metrics dashboard for the Director\'s QA work and 1:1 management conversations. Shows active caseload, appointment completion rate, no-show rate, sessions logged, referrals handled, resolution rate, average response time, and student feedback ratings.',
                    ],
                    [
                        'h' => 'Reading the cards',
                        'p' => 'Each counselor gets a card with a color-coded left border: green = strong performance (high completion + rating + resolution), blue = solid, gray = mostly missing data or needs attention. The numbers behind the score are completion rate ≥80%, no-show ≤10%, rating ≥4.5, resolution ≥70%, and response time ≤24h.',
                    ],
                    [
                        'h' => 'Comparison table',
                        'p' => 'Below the cards, a side-by-side table shows every counselor in one row each — useful for spotting outliers (e.g. one counselor with a very low rating or a very long response time).',
                    ],
                    [
                        'h' => 'Year + Month filter',
                        'p' => 'Use the Year and Month dropdowns at the top to scope the metrics. Useful for monthly reporting and trend comparisons.',
                    ],
                    [
                        'h' => 'For CHED accreditation',
                        'p' => 'Combined with Service Feedback aggregates and the printable analytics report, the Performance dashboard gives the office concrete evidence of staff productivity and student satisfaction — both required for CHED institutional reviews.',
                    ],
                ],
            ],
            [
                'id' => 'calendar-sync',
                'icon' => '📆',
                'title' => 'Calendar Sync (iCal)',
                'roles' => ['staff'],
                'sections' => [
                    [
                        'h' => 'What it does',
                        'p' => 'Subscribe your work calendar app (Google Calendar, Outlook, Apple Calendar) to a personal feed URL so your CHMSU appointments and workshop RSVPs appear automatically alongside your other commitments. Read-only — your calendar app refreshes every few hours.',
                    ],
                    [
                        'h' => 'Setting it up',
                        'p' => 'Calendar Sync → copy your Personal Feed URL → in your calendar app, choose "Subscribe from URL" and paste it. Step-by-step instructions for Google, Outlook, and Apple are inside the page. Once subscribed, no further action is needed — new appointments and workshops auto-appear.',
                    ],
                    [
                        'h' => 'What\'s included',
                        'p' => 'Counseling appointments assigned to you (excluding cancelled/no-shows), plus workshops you organized or RSVPed to. Date range is past 30 days + next 90 days, rolling. Includes meeting links, locations, student concern summaries.',
                    ],
                    [
                        'h' => 'Keep your URL secret',
                        'p' => 'The feed URL is private — anyone with it can read your calendar (no login required). Don\'t share it. If it ever leaks, click "🔄 Regenerate Token" to instantly invalidate the old URL — anyone using it gets a 404. You\'ll then need to update your calendar apps with the new URL.',
                    ],
                ],
            ],
            [
                'id' => 'my-profile',
                'icon' => '👤',
                'title' => 'My Profile (Students)',
                'roles' => ['student'],
                'sections' => [
                    [
                        'h' => 'What you can update yourself',
                        'p' => 'Sidebar → My Account → My Profile. You can update: contact number, home address, civil status, religion, profile photo, family background (parents\' names, occupations, contacts), guardian info, and your own emergency contacts.',
                    ],
                    [
                        'h' => 'What you can\'t change yourself',
                        'p' => 'Your name, student ID number, program, year level, academic status, and assigned counselor are read-only here — those need to go through the Guidance Office (visit in person or message your counselor) to ensure verification.',
                    ],
                    [
                        'h' => 'Profile photo upload',
                        'p' => 'Upload a clear photo of yourself (PNG/JPG, max 5MB) so your counselor can recognize you when you walk in. The photo replaces the initials avatar in the system.',
                    ],
                ],
            ],
            [
                'id' => 'data-privacy',
                'icon' => '🔏',
                'title' => 'My Data & Privacy (RA 10173)',
                'roles' => ['student'],
                'sections' => [
                    [
                        'h' => 'Your rights under the Data Privacy Act',
                        'p' => 'The Philippine Data Privacy Act (RA 10173) gives you specific rights over your personal data: to be informed, to access, to data portability, to rectification, to erasure, to object, and to file complaints with the National Privacy Commission. Sidebar → My Account → My Data & Privacy lets you exercise these rights without paperwork.',
                    ],
                    [
                        'h' => 'See what we hold',
                        'p' => 'The page summarizes how many records of each type are linked to you — appointments, counseling notes, referrals, wellness check-ins, documents, contacts, certificates. This is "Right to Access" in action.',
                    ],
                    [
                        'h' => 'Download your data (Right to Portability)',
                        'p' => 'Click "📥 Download My Data (JSON)" to get a complete machine-readable copy of all your personal info in this system. Useful if you transfer to another school, want to keep a personal record, or need to share with a guardian. Note: confidential counseling case-note bodies are NOT included (they need an in-person request to the Guidance Office for legal reasons).',
                    ],
                    [
                        'h' => 'Request a correction (Right to Rectification)',
                        'p' => 'If something is wrong (name spelling, birth date, contact info), submit a correction request through the form. The system emails your counselor and logs the request. You\'ll get a response within 7 working days.',
                    ],
                    [
                        'h' => 'See who accessed your record',
                        'p' => 'The "Who has accessed my record" section shows the last 30 audit log entries involving your data — counselor name, action taken, exact timestamp. This is required by RA 10173 (right to be informed).',
                    ],
                    [
                        'h' => 'Right to erasure',
                        'p' => 'For deletion of records, contact CHMSU\'s Data Protection Officer through the Guidance Office. Some records (academic, disciplinary, certificates) cannot be deleted while you\'re an enrolled student due to legal retention requirements.',
                    ],
                ],
            ],
            [
                'id' => 'admin',
                'icon' => '🔐',
                'title' => 'User Accounts & Audit Log',
                'roles' => ['admin'],
                'sections' => [
                    [
                        'h' => 'Managing user accounts',
                        'p' => 'Administration → User Accounts. Create staff accounts, assign roles, deactivate users. You cannot deactivate or delete other Super Admins or your own account.',
                    ],
                    [
                        'h' => 'Audit Log',
                        'p' => 'Administration → Audit Log. Tracks every login, logout, failed login attempt, and CRUD operation on sensitive records (students, sessions, referrals, disciplinary, certificates). Required by the Philippine Data Privacy Act for systems handling counseling data.',
                    ],
                    [
                        'h' => 'Reading the diff',
                        'p' => 'Click "Details" on any "updated" entry to see exactly which fields changed, with old and new values shown side-by-side.',
                    ],
                ],
            ],
        ];

        // Filter modules by current user's role
        $visibleModules = collect($modules)->filter(function ($mod) use ($isStaff, $isStudent, $isFaculty, $isAdmin) {
            $roles = $mod['roles'];
            if (in_array('all', $roles)) return true;
            if (in_array('staff', $roles) && $isStaff) return true;
            if (in_array('student', $roles) && $isStudent) return true;
            if (in_array('faculty', $roles) && $isFaculty) return true;
            if (in_array('admin', $roles) && $isAdmin) return true;
            return false;
        });
    @endphp

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                {{-- ── Sidebar TOC ── --}}
                <aside class="lg:col-span-1">
                    <div class="bg-white shadow-sm rounded-lg p-4 lg:sticky lg:top-4">
                        <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-3">In This Guide</h3>
                        <nav class="space-y-1 text-sm">
                            @foreach($visibleModules as $mod)
                                <a href="#{{ $mod['id'] }}" class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 text-gray-600 hover:text-gray-900 transition">
                                    <span>{{ $mod['icon'] }}</span>
                                    <span>{{ $mod['title'] }}</span>
                                </a>
                            @endforeach
                        </nav>

                        <div class="border-t border-gray-100 mt-4 pt-4">
                            <h4 class="font-semibold text-gray-700 text-xs uppercase tracking-wide mb-2">Need More Help?</h4>
                            <p class="text-xs text-gray-500 leading-relaxed">
                                Visit the <strong>Guidance Office</strong> at the main campus, Mon–Fri 8 AM – 5 PM, or
                                call <a href="tel:0344600511" class="text-blue-600 hover:underline">(034) 460-0511</a>.
                            </p>
                        </div>
                    </div>
                </aside>

                {{-- ── Content ── --}}
                <main class="lg:col-span-3 space-y-5">

                    {{-- Hero --}}
                    <div class="bg-gradient-to-br from-red-900 to-red-700 rounded-xl text-white p-7 shadow-md">
                        <h1 class="text-2xl font-bold mb-2">Welcome to the GMS User Guide 👋</h1>
                        <p class="text-red-100 text-sm leading-relaxed">
                            This guide explains every feature available to your role
                            (<strong>{{ auth()->user()->getRoleDisplayName() }}</strong>).
                            Use the table of contents on the left to jump to any topic, or scroll through to learn what the system can do.
                        </p>
                    </div>

                    {{-- Quick links cards --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <a href="{{ route('dashboard') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-lg flex-shrink-0">🏠</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Go to Dashboard</p>
                                <p class="text-xs text-gray-500">Your starting point</p>
                            </div>
                        </a>
                        @if($isStudent)
                        <a href="{{ route('appointments.create') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-lg flex-shrink-0">📅</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Book Appointment</p>
                                <p class="text-xs text-gray-500">See a counselor</p>
                            </div>
                        </a>
                        <a href="{{ route('riasec.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 text-lg flex-shrink-0">🎯</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Career Test</p>
                                <p class="text-xs text-gray-500">Find your fit</p>
                            </div>
                        </a>
                        @elseif($isStaff)
                        <a href="{{ route('caseload.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-lg flex-shrink-0">📋</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">My Caseload</p>
                                <p class="text-xs text-gray-500">Active students</p>
                            </div>
                        </a>
                        <a href="{{ route('walk-in.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-lg flex-shrink-0">🚪</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Walk-in Queue</p>
                                <p class="text-xs text-gray-500">Today's visitors</p>
                            </div>
                        </a>
                        @elseif($isFaculty)
                        <a href="{{ route('referrals.create') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 text-lg flex-shrink-0">🚩</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">Submit Referral</p>
                                <p class="text-xs text-gray-500">Report a concern</p>
                            </div>
                        </a>
                        <a href="{{ route('referrals.index') }}" class="bg-white shadow-sm rounded-lg p-4 hover:shadow-md transition flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-lg flex-shrink-0">📂</div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">My Referrals</p>
                                <p class="text-xs text-gray-500">Track status</p>
                            </div>
                        </a>
                        @endif
                    </div>

                    {{-- Module sections --}}
                    @foreach($visibleModules as $mod)
                    <section id="{{ $mod['id'] }}" class="bg-white shadow-sm rounded-lg p-6 scroll-mt-4">
                        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-gray-100">
                            <div class="text-3xl">{{ $mod['icon'] }}</div>
                            <h2 class="font-bold text-gray-900 text-xl">{{ $mod['title'] }}</h2>
                        </div>

                        <div class="space-y-4">
                            @foreach($mod['sections'] as $section)
                                @php
                                    // Section-level role filtering (some sections show only for student / staff / etc.)
                                    if (isset($section['roles'])) {
                                        $matches = false;
                                        foreach ($section['roles'] as $r) {
                                            if ($r === 'staff' && $isStaff) $matches = true;
                                            if ($r === 'student' && $isStudent) $matches = true;
                                            if ($r === 'faculty' && $isFaculty) $matches = true;
                                        }
                                        if (!$matches) continue;
                                    }
                                @endphp
                                <div>
                                    <h3 class="font-semibold text-gray-800 mb-1.5">{{ $section['h'] }}</h3>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $section['p'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'});return false;" class="text-xs text-gray-400 hover:text-gray-600">↑ Back to top</a>
                        </div>
                    </section>
                    @endforeach

                    {{-- Tips footer --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-5">
                        <h3 class="font-semibold text-blue-900 mb-2">💡 Quick Tips</h3>
                        <ul class="text-sm text-blue-800 space-y-1.5">
                            <li>• <strong>Press <kbd class="bg-white px-1.5 py-0.5 rounded text-xs border">⌘K</kbd> or <kbd class="bg-white px-1.5 py-0.5 rounded text-xs border">Ctrl+K</kbd></strong> from any page to instantly search students, appointments, referrals, and more.</li>
                            <li>• <strong>Press <kbd class="bg-white px-1.5 py-0.5 rounded text-xs border">Ctrl/⌘ + Enter</kbd></strong> inside a message reply box to send without clicking.</li>
                            <li>• Your role determines which sidebar items you see — if something is missing, you may not have permission for it.</li>
                            <li>• The 🚨 Crisis priority on walk-ins, the red badge on the bell, and amber-highlighted wellness rows are all designed to grab your attention. Don't ignore them.</li>
                            <li>• Anonymous concerns at <code class="bg-white px-1.5 py-0.5 rounded text-xs">/concerns</code> work without an account — share that link with anyone who might benefit.</li>
                            <li>• Use <strong>Secure Messages</strong> instead of personal email/SMS for student follow-ups. Bodies are AES-256 encrypted and stay inside the GMS.</li>
                            <li>• Counselors: your <strong>case-note PIN</strong> auto-locks after 15 minutes — use the "🔒 Lock now" button if you step away from a shared desk.</li>
                            <li>• Subscribe to your <strong>Calendar Sync</strong> URL so CHMSU appointments appear in Google Calendar / Outlook automatically — eliminates double-booking.</li>
                            <li>• Staff: enable <strong>🛡️ Two-Factor Authentication</strong> via the icon at the bottom of the sidebar — protects against stolen-password attacks.</li>
                            <li>• Confirmed appointments auto-send reminder emails the day before — make sure to confirm appointments instead of leaving them pending so reminders fire.</li>
                            <li>• Use the <strong>Schedule Matrix</strong> view to see who's free across the whole counseling team at a glance — useful for triaging walk-ins and urgent referrals.</li>
                            <li>• Students: visit <strong>My Data & Privacy</strong> anytime to download a full copy of your data or see who has accessed your record.</li>
                            <li>• 🚨 <strong>Crisis alerts auto-email counselors</strong> when a wellness check-in or screening indicates immediate concern — don't ignore them.</li>
                            <li>• 🌗 Toggle <strong>dark mode</strong> via the moon/sun icon in the header — saved per browser.</li>
                            <li>• 📱 Install GMS as a phone app via your browser's "Add to Home Screen" / "Install app" menu.</li>
                            <li>• When in doubt, log it. Counseling sessions, action plan milestones, and case notes are how the office demonstrates impact for CHED accreditation.</li>
                        </ul>
                    </div>

                    {{-- Crisis hotlines --}}
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-5">
                        <h3 class="font-bold text-red-900 mb-2">🚨 Crisis Hotlines (24/7)</h3>
                        <p class="text-sm text-red-800 mb-3">If you or someone you know is in immediate distress:</p>
                        <ul class="text-sm text-red-900 space-y-1">
                            <li>• <strong>NCMH Crisis Hotline</strong> — <a href="tel:1553" class="underline font-mono">1553</a> (toll-free) or <a href="tel:09178998727" class="underline font-mono">0917-899-USAP</a></li>
                            <li>• <strong>In Touch Community Services</strong> — <a href="tel:0288937603" class="underline font-mono">(02) 8893-7603</a></li>
                            <li>• <strong>Hopeline Philippines</strong> — <a href="tel:09175584673" class="underline font-mono">0917-558-4673</a></li>
                            <li>• <strong>CHMSU Guidance Office</strong> — <a href="tel:0344600511" class="underline font-mono">(034) 460-0511</a> (Mon–Fri, 8 AM – 5 PM)</li>
                        </ul>
                    </div>

                </main>

            </div>
        </div>
    </div>
</x-app-layout>
