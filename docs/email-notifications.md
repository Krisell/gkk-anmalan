# Email Notifications for Events and Competitions

## Overview

This feature allows administrators to send email notifications to all registered members when creating or updating events (funktionärsanmälan) and competitions (tävlingsanmälan).

## Features

### For Administrators

1. **Send Notifications**: Click the mail icon next to any event or competition in the admin interface to send an email notification to all granted members.

2. **Confirmation Dialog**: Before sending, a confirmation dialog appears showing:
   - A warning that emails will be sent to all members
   - History of previous notifications (date, sender, recipient count)

3. **Result Feedback**: After sending, a modal shows:
   - Success: Number of recipients who received the email
   - Error: If something went wrong during the send process

4. **Notification History**: View when notifications were sent, by whom, and to how many recipients.

### Email Content

The notification email includes:

**For Events:**
- Event name
- Date (and end date if applicable)
- Time
- Location
- Last registration date
- Description
- Link to registration page

**For Competitions:**
- Competition name
- Date (and end date if applicable)
- Time
- Location
- Last registration date
- Event classes (KSL, KBP, SL, BP)
- Description
- Link to registration page

### Recipients

- Only **granted members** (users with granted_by > 0) receive notifications
- Only users with valid email addresses are included

## Technical Implementation

### Database

A new `notification_logs` table tracks all sent notifications:
- `notifiable_type`: Event or Competition class name
- `notifiable_id`: ID of the event/competition
- `sent_by`: ID of admin who sent the notification
- `recipients_count`: Number of emails sent
- `created_at`: Timestamp of notification

### API Endpoints

**Events:**
- `POST /admin/events/{event}/send-notification` - Send notification
- `GET /admin/events/{event}/notification-status` - Get notification history

**Competitions:**
- `POST /admin/competitions/{competition}/send-notification` - Send notification
- `GET /admin/competitions/{competition}/notification-status` - Get notification history

### Email Template

Located at: `resources/views/emails/event-competition-notification.blade.php`

Uses Laravel's Markdown email components for consistent styling.

## Usage Guide

### Sending a Notification

1. Navigate to Admin → Events or Admin → Competitions
2. Find the event/competition you want to notify members about
3. Click the mail icon (envelope) in the Actions column
4. Review the confirmation dialog (check for previous notifications)
5. Click "Skicka mail" to confirm
6. Wait for the success confirmation showing recipient count

### Best Practices

1. **Avoid Duplicate Notifications**: Check the notification history before sending to avoid sending multiple emails for the same event.

2. **Timing**: Send notifications when:
   - A new event/competition is created
   - Important details change (date, location, last registration date)
   - Approaching last registration date (reminder)

3. **Content**: Ensure all event/competition details are filled in before sending the notification.

4. **Testing**: Consider testing the notification with a single user account first if you're unsure about the content.

## Error Handling

- If an email fails to send to a specific user, the error is logged but doesn't stop emails to other users
- The final recipient count reflects only successful sends
- Errors are logged in the application log for debugging

## Security

- Only admins can send notifications (enforced by AdminMiddleware)
- Confirmation is required before sending (confirmed=true parameter)
- All actions are logged with admin user ID

## Future Enhancements

Potential improvements for future versions:

- Option to preview the email before sending
- Send to specific user groups (e.g., only those who haven't responded)
- Schedule notifications for later sending
- User preference to opt-out of certain notification types
- Reminder notifications X days before last registration date
- Notification on event/competition updates
