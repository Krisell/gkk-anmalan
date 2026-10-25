-- How much is written to the production database, to pick a quiet time for the cutover. Read-only.
--
-- Five independent queries, without variables, so they also work in phpMyAdmin (run them one at a time there, with
-- the database selected) and in the mysql client:
--
--   mysql -t -h <DB_HOST> -u <DB_USERNAME> -p <DB_DATABASE> < bin/db-write-activity.sql
--
-- Counts inserts (created_at) and updates (updated_at later than created_at) over the last 90 days in the app's tables
-- that have both columns (with the gkk_ prefix used on One.com). updated_at only keeps a row's latest change, so
-- repeated updates of one row count once, and deletes aren't visible: the numbers are a lower bound. users updates
-- include logged-in visits (last_visited_at is set at most every 30 minutes per member). Times are UTC
-- (APP_TIMEZONE); Swedish time is UTC+1 in winter and UTC+2 in summer.

-- 1. Writes per table, last 90 days
SELECT tbl, kind, COUNT(*) AS writes
FROM (
    SELECT 'activity_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_activity_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'activity_logs', 'update', updated_at FROM gkk_activity_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'audit_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_audit_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'audit_logs', 'update', updated_at FROM gkk_audit_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_admin_tasks' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_admin_tasks WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_admin_tasks', 'update', updated_at FROM gkk_competition_admin_tasks WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_registrations', 'update', updated_at FROM gkk_competition_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competitions' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competitions WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competitions', 'update', updated_at FROM gkk_competitions WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'document_folders' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_document_folders WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'document_folders', 'update', updated_at FROM gkk_document_folders WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'documents' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_documents WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'documents', 'update', updated_at FROM gkk_documents WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'email_sends' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_email_sends WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'email_sends', 'update', updated_at FROM gkk_email_sends WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'event_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_event_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'event_registrations', 'update', updated_at FROM gkk_event_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'events' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_events WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'events', 'update', updated_at FROM gkk_events WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'integration_tokens' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_integration_tokens WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'integration_tokens', 'update', updated_at FROM gkk_integration_tokens WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'news_items' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_news_items WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'news_items', 'update', updated_at FROM gkk_news_items WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'payments' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_payments WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'payments', 'update', updated_at FROM gkk_payments WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'results' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_results WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'results', 'update', updated_at FROM gkk_results WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_fields' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_fields WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_fields', 'update', updated_at FROM gkk_signature_request_fields WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_signers' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_signers WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_signers', 'update', updated_at FROM gkk_signature_request_signers WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_requests' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_requests WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_requests', 'update', updated_at FROM gkk_signature_requests WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'slides' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_slides WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'slides', 'update', updated_at FROM gkk_slides WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'users' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_users WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'users', 'update', updated_at FROM gkk_users WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
) w
GROUP BY tbl, kind
ORDER BY writes DESC;

-- 2. Writes per day, last 30 days
SELECT DATE(at) AS day, DAYNAME(at) AS weekday, COUNT(*) AS writes
FROM (
    SELECT 'activity_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_activity_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'activity_logs', 'update', updated_at FROM gkk_activity_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'audit_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_audit_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'audit_logs', 'update', updated_at FROM gkk_audit_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_admin_tasks' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_admin_tasks WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_admin_tasks', 'update', updated_at FROM gkk_competition_admin_tasks WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_registrations', 'update', updated_at FROM gkk_competition_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competitions' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competitions WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competitions', 'update', updated_at FROM gkk_competitions WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'document_folders' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_document_folders WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'document_folders', 'update', updated_at FROM gkk_document_folders WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'documents' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_documents WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'documents', 'update', updated_at FROM gkk_documents WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'email_sends' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_email_sends WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'email_sends', 'update', updated_at FROM gkk_email_sends WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'event_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_event_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'event_registrations', 'update', updated_at FROM gkk_event_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'events' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_events WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'events', 'update', updated_at FROM gkk_events WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'integration_tokens' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_integration_tokens WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'integration_tokens', 'update', updated_at FROM gkk_integration_tokens WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'news_items' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_news_items WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'news_items', 'update', updated_at FROM gkk_news_items WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'payments' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_payments WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'payments', 'update', updated_at FROM gkk_payments WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'results' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_results WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'results', 'update', updated_at FROM gkk_results WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_fields' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_fields WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_fields', 'update', updated_at FROM gkk_signature_request_fields WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_signers' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_signers WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_signers', 'update', updated_at FROM gkk_signature_request_signers WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_requests' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_requests WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_requests', 'update', updated_at FROM gkk_signature_requests WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'slides' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_slides WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'slides', 'update', updated_at FROM gkk_slides WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'users' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_users WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'users', 'update', updated_at FROM gkk_users WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
) w
WHERE at >= UTC_TIMESTAMP() - INTERVAL 30 DAY
GROUP BY day, weekday
ORDER BY day;

-- 3. Writes per hour of the day (UTC), last 90 days
SELECT HOUR(at) AS hour_utc, COUNT(*) AS writes, ROUND(COUNT(*) / 90, 1) AS per_day
FROM (
    SELECT 'activity_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_activity_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'activity_logs', 'update', updated_at FROM gkk_activity_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'audit_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_audit_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'audit_logs', 'update', updated_at FROM gkk_audit_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_admin_tasks' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_admin_tasks WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_admin_tasks', 'update', updated_at FROM gkk_competition_admin_tasks WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_registrations', 'update', updated_at FROM gkk_competition_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competitions' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competitions WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competitions', 'update', updated_at FROM gkk_competitions WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'document_folders' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_document_folders WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'document_folders', 'update', updated_at FROM gkk_document_folders WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'documents' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_documents WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'documents', 'update', updated_at FROM gkk_documents WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'email_sends' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_email_sends WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'email_sends', 'update', updated_at FROM gkk_email_sends WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'event_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_event_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'event_registrations', 'update', updated_at FROM gkk_event_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'events' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_events WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'events', 'update', updated_at FROM gkk_events WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'integration_tokens' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_integration_tokens WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'integration_tokens', 'update', updated_at FROM gkk_integration_tokens WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'news_items' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_news_items WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'news_items', 'update', updated_at FROM gkk_news_items WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'payments' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_payments WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'payments', 'update', updated_at FROM gkk_payments WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'results' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_results WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'results', 'update', updated_at FROM gkk_results WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_fields' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_fields WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_fields', 'update', updated_at FROM gkk_signature_request_fields WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_signers' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_signers WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_signers', 'update', updated_at FROM gkk_signature_request_signers WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_requests' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_requests WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_requests', 'update', updated_at FROM gkk_signature_requests WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'slides' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_slides WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'slides', 'update', updated_at FROM gkk_slides WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'users' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_users WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'users', 'update', updated_at FROM gkk_users WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
) w
GROUP BY hour_utc
ORDER BY hour_utc;

-- 4. Writes per weekday and evening hour (UTC 16-22), last 90 days
SELECT WEEKDAY(at) AS day_no, DAYNAME(at) AS weekday, HOUR(at) AS hour_utc, COUNT(*) AS writes
FROM (
    SELECT 'activity_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_activity_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'activity_logs', 'update', updated_at FROM gkk_activity_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'audit_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_audit_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'audit_logs', 'update', updated_at FROM gkk_audit_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_admin_tasks' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_admin_tasks WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_admin_tasks', 'update', updated_at FROM gkk_competition_admin_tasks WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_registrations', 'update', updated_at FROM gkk_competition_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competitions' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competitions WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competitions', 'update', updated_at FROM gkk_competitions WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'document_folders' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_document_folders WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'document_folders', 'update', updated_at FROM gkk_document_folders WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'documents' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_documents WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'documents', 'update', updated_at FROM gkk_documents WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'email_sends' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_email_sends WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'email_sends', 'update', updated_at FROM gkk_email_sends WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'event_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_event_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'event_registrations', 'update', updated_at FROM gkk_event_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'events' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_events WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'events', 'update', updated_at FROM gkk_events WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'integration_tokens' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_integration_tokens WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'integration_tokens', 'update', updated_at FROM gkk_integration_tokens WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'news_items' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_news_items WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'news_items', 'update', updated_at FROM gkk_news_items WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'payments' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_payments WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'payments', 'update', updated_at FROM gkk_payments WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'results' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_results WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'results', 'update', updated_at FROM gkk_results WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_fields' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_fields WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_fields', 'update', updated_at FROM gkk_signature_request_fields WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_signers' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_signers WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_signers', 'update', updated_at FROM gkk_signature_request_signers WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_requests' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_requests WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_requests', 'update', updated_at FROM gkk_signature_requests WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'slides' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_slides WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'slides', 'update', updated_at FROM gkk_slides WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'users' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_users WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'users', 'update', updated_at FROM gkk_users WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
) w
WHERE HOUR(at) BETWEEN 16 AND 22
GROUP BY day_no, weekday, hour_utc
ORDER BY day_no, hour_utc;

-- 5. Busiest single hours, last 90 days
SELECT DATE_FORMAT(at, '%Y-%m-%d %H:00') AS hour_utc, COUNT(*) AS writes
FROM (
    SELECT 'activity_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_activity_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'activity_logs', 'update', updated_at FROM gkk_activity_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'audit_logs' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_audit_logs WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'audit_logs', 'update', updated_at FROM gkk_audit_logs WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_admin_tasks' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_admin_tasks WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_admin_tasks', 'update', updated_at FROM gkk_competition_admin_tasks WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competition_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competition_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competition_registrations', 'update', updated_at FROM gkk_competition_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'competitions' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_competitions WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'competitions', 'update', updated_at FROM gkk_competitions WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'document_folders' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_document_folders WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'document_folders', 'update', updated_at FROM gkk_document_folders WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'documents' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_documents WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'documents', 'update', updated_at FROM gkk_documents WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'email_sends' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_email_sends WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'email_sends', 'update', updated_at FROM gkk_email_sends WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'event_registrations' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_event_registrations WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'event_registrations', 'update', updated_at FROM gkk_event_registrations WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'events' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_events WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'events', 'update', updated_at FROM gkk_events WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'integration_tokens' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_integration_tokens WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'integration_tokens', 'update', updated_at FROM gkk_integration_tokens WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'news_items' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_news_items WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'news_items', 'update', updated_at FROM gkk_news_items WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'payments' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_payments WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'payments', 'update', updated_at FROM gkk_payments WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'results' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_results WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'results', 'update', updated_at FROM gkk_results WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_fields' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_fields WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_fields', 'update', updated_at FROM gkk_signature_request_fields WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_request_signers' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_request_signers WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_request_signers', 'update', updated_at FROM gkk_signature_request_signers WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'signature_requests' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_signature_requests WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'signature_requests', 'update', updated_at FROM gkk_signature_requests WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'slides' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_slides WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'slides', 'update', updated_at FROM gkk_slides WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
    UNION ALL
    SELECT 'users' AS tbl, 'insert' AS kind, created_at AS at FROM gkk_users WHERE created_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY
    UNION ALL
    SELECT 'users', 'update', updated_at FROM gkk_users WHERE updated_at >= UTC_TIMESTAMP() - INTERVAL 90 DAY AND updated_at > created_at
) w
GROUP BY hour_utc
ORDER BY writes DESC
LIMIT 10;
