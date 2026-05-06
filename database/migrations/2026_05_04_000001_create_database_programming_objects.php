<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->dropObjects();

        DB::unprepared(<<<'SQL'
CREATE FUNCTION fn_event_status(
    p_start_date DATE,
    p_end_date DATE,
    p_start_time TIME,
    p_end_time TIME
) RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE v_now DATETIME;
    DECLARE v_start DATETIME;
    DECLARE v_end DATETIME;

    SET v_now = CONVERT_TZ(NOW(), @@session.time_zone, '+08:00');
    SET v_start = TIMESTAMP(p_start_date, COALESCE(p_start_time, '00:00:00'));
    SET v_end = TIMESTAMP(p_end_date, COALESCE(p_end_time, '23:59:59'));

    IF v_now < v_start THEN
        RETURN 'upcoming';
    ELSEIF v_now > v_end THEN
        RETURN 'finished';
    END IF;

    RETURN 'ongoing';
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE FUNCTION fn_session_availability(
    p_attendance_date DATE,
    p_time_in_start TIME,
    p_time_out_end TIME,
    p_event_start_date DATE,
    p_event_end_date DATE,
    p_event_start_time TIME,
    p_event_end_time TIME
) RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE v_now DATETIME;
    DECLARE v_open DATETIME;
    DECLARE v_close DATETIME;
    DECLARE v_base_date DATE;

    SET v_now = CONVERT_TZ(NOW(), @@session.time_zone, '+08:00');
    SET v_base_date = COALESCE(p_attendance_date, p_event_start_date, CURRENT_DATE());
    SET v_open = TIMESTAMP(v_base_date, COALESCE(p_time_in_start, p_event_start_time, '00:00:00'));
    SET v_close = TIMESTAMP(v_base_date, COALESCE(p_time_out_end, p_event_end_time, '23:59:59'));

    IF v_now < v_open THEN
        RETURN 'upcoming';
    ELSEIF v_now > v_close THEN
        RETURN 'closed';
    END IF;

    RETURN 'open';
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE VIEW vw_members_full AS
SELECT
    m.member_id,
    m.member_fname,
    m.member_mname,
    m.member_lname,
    m.gender,
    m.birth_date,
    m.email,
    m.phone_number,
    m.province,
    m.city,
    m.street,
    m.is_archived,
    m.archived_at,
    m.created_at,
    m.updated_at,
    mm.members_ministry_id,
    mm.ministry_id,
    mm.role_in_ministry,
    mm.date_joined,
    mm.status AS ministry_status,
    mn.ministry_name
FROM members m
LEFT JOIN members_ministries mm ON mm.member_id = m.member_id
LEFT JOIN ministries mn ON mn.ministry_id = mm.ministry_id
SQL);

        DB::unprepared(<<<'SQL'
CREATE VIEW vw_events_full AS
SELECT
    e.event_id,
    e.event_name,
    e.start_date,
    e.end_date,
    e.start_time,
    e.end_time,
    e.description,
    e.status AS stored_status,
    fn_event_status(e.start_date, e.end_date, e.start_time, e.end_time) AS computed_status,
    e.type_id,
    t.type_name,
    e.administrator_id,
    a.username AS administrator_username,
    a.role AS administrator_role,
    CASE
        WHEN a.role = 'super_admin' THEN 'Church Administrator'
        WHEN a.role = 'admin' THEN 'Attendance Coordinator'
        ELSE 'Administrator'
    END AS administrator_role_label,
    e.created_at,
    e.updated_at
FROM events e
LEFT JOIN types t ON t.type_id = e.type_id
LEFT JOIN administrators a ON a.administrator_id = e.administrator_id
SQL);

        DB::unprepared(<<<'SQL'
CREATE VIEW vw_attendance_session_summary AS
SELECT
    s.attendance_session_id,
    s.event_id,
    s.administrator_id,
    s.attendance_name,
    s.attendance_date,
    s.time_in_start,
    s.time_out_end,
    s.created_at,
    s.updated_at,
    e.event_name,
    e.start_date,
    e.end_date,
    e.start_time,
    e.end_time,
    e.type_id,
    t.type_name,
    fn_session_availability(
        s.attendance_date,
        s.time_in_start,
        s.time_out_end,
        e.start_date,
        e.end_date,
        e.start_time,
        e.end_time
    ) AS availability_status,
    SUM(CASE WHEN atd.status = 'Present' THEN 1 ELSE 0 END) AS approved_attendance_count,
    SUM(CASE WHEN atd.status = 'Pending' THEN 1 ELSE 0 END) AS pending_attendance_count,
    COUNT(atd.attendance_id) AS total_attendance_count
FROM attendance_sessions s
JOIN events e ON e.event_id = s.event_id
LEFT JOIN types t ON t.type_id = e.type_id
LEFT JOIN attendances atd ON atd.attendance_session_id = s.attendance_session_id
GROUP BY
    s.attendance_session_id,
    s.event_id,
    s.administrator_id,
    s.attendance_name,
    s.attendance_date,
    s.time_in_start,
    s.time_out_end,
    s.created_at,
    s.updated_at,
    e.event_name,
    e.start_date,
    e.end_date,
    e.start_time,
    e.end_time,
    e.type_id,
    t.type_name
SQL);

        DB::unprepared(<<<'SQL'
CREATE VIEW vw_attendance_records_full AS
SELECT
    a.attendance_id,
    a.member_id,
    a.event_id,
    a.administrator_id,
    a.attendance_session_id,
    a.attended_at,
    a.time_in,
    a.time_out,
    a.status,
    a.created_at,
    a.updated_at,
    m.member_fname,
    m.member_mname,
    m.member_lname,
    m.email,
    m.phone_number,
    e.event_name,
    ad.username AS administrator_username,
    s.attendance_name,
    s.attendance_date
FROM attendances a
JOIN members m ON m.member_id = a.member_id
JOIN events e ON e.event_id = a.event_id
LEFT JOIN administrators ad ON ad.administrator_id = a.administrator_id
LEFT JOIN attendance_sessions s ON s.attendance_session_id = a.attendance_session_id
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_create_member(
    IN p_member_fname VARCHAR(255),
    IN p_member_mname VARCHAR(255),
    IN p_member_lname VARCHAR(255),
    IN p_gender VARCHAR(20),
    IN p_birth_date DATE,
    IN p_email VARCHAR(255),
    IN p_phone_number VARCHAR(50),
    IN p_street VARCHAR(500),
    IN p_city VARCHAR(255),
    IN p_province VARCHAR(255),
    IN p_ministry_id BIGINT,
    IN p_ministry_status VARCHAR(20)
)
BEGIN
    INSERT INTO members (
        member_fname, member_mname, member_lname, gender, birth_date,
        email, phone_number, street, city, province, created_at, updated_at
    )
    VALUES (
        p_member_fname, NULLIF(p_member_mname, ''), p_member_lname, p_gender, p_birth_date,
        p_email, p_phone_number, NULLIF(p_street, ''), NULLIF(p_city, ''), NULLIF(p_province, ''),
        NOW(), NOW()
    );

    IF p_ministry_id IS NOT NULL AND p_ministry_id > 0 THEN
        INSERT INTO members_ministries (
            member_id, ministry_id, date_joined, status
        )
        VALUES (
            LAST_INSERT_ID(), p_ministry_id, CURRENT_DATE(), COALESCE(NULLIF(p_ministry_status, ''), 'active')
        );
    END IF;

    SELECT LAST_INSERT_ID() AS member_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_update_member(
    IN p_member_id BIGINT,
    IN p_member_fname VARCHAR(255),
    IN p_member_mname VARCHAR(255),
    IN p_member_lname VARCHAR(255),
    IN p_gender VARCHAR(20),
    IN p_birth_date DATE,
    IN p_email VARCHAR(255),
    IN p_phone_number VARCHAR(50),
    IN p_street VARCHAR(500),
    IN p_city VARCHAR(255),
    IN p_province VARCHAR(255),
    IN p_ministry_id BIGINT,
    IN p_ministry_status VARCHAR(20)
)
BEGIN
    DECLARE v_date_joined DATE;

    SELECT date_joined
    INTO v_date_joined
    FROM members_ministries
    WHERE member_id = p_member_id
    ORDER BY members_ministry_id
    LIMIT 1;

    UPDATE members
    SET
        member_fname = p_member_fname,
        member_mname = NULLIF(p_member_mname, ''),
        member_lname = p_member_lname,
        gender = p_gender,
        birth_date = p_birth_date,
        email = p_email,
        phone_number = p_phone_number,
        street = NULLIF(p_street, ''),
        city = NULLIF(p_city, ''),
        province = NULLIF(p_province, ''),
        updated_at = NOW()
    WHERE member_id = p_member_id;

    DELETE FROM members_ministries WHERE member_id = p_member_id;

    IF p_ministry_id IS NOT NULL AND p_ministry_id > 0 THEN
        INSERT INTO members_ministries (
            member_id, ministry_id, date_joined, status
        )
        VALUES (
            p_member_id, p_ministry_id, COALESCE(v_date_joined, CURRENT_DATE()), COALESCE(NULLIF(p_ministry_status, ''), 'active')
        );
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_set_member_archive(
    IN p_member_id BIGINT,
    IN p_is_archived TINYINT(1)
)
BEGIN
    UPDATE members
    SET
        is_archived = p_is_archived,
        archived_at = CASE WHEN p_is_archived = 1 THEN NOW() ELSE NULL END,
        updated_at = NOW()
    WHERE member_id = p_member_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_create_event(
    IN p_event_name VARCHAR(255),
    IN p_type_id BIGINT,
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_start_time TIME,
    IN p_end_time TIME,
    IN p_description TEXT,
    IN p_administrator_id BIGINT
)
BEGIN
    INSERT INTO events (
        event_name, type_id, start_date, end_date, start_time, end_time,
        description, status, administrator_id, created_at, updated_at
    )
    VALUES (
        p_event_name, p_type_id, p_start_date, p_end_date, p_start_time, p_end_time,
        NULLIF(p_description, ''), fn_event_status(p_start_date, p_end_date, p_start_time, p_end_time), p_administrator_id, NOW(), NOW()
    );

    SELECT LAST_INSERT_ID() AS event_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_update_event(
    IN p_event_id BIGINT,
    IN p_event_name VARCHAR(255),
    IN p_type_id BIGINT,
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_start_time TIME,
    IN p_end_time TIME,
    IN p_description TEXT
)
BEGIN
    UPDATE events
    SET
        event_name = p_event_name,
        type_id = p_type_id,
        start_date = p_start_date,
        end_date = p_end_date,
        start_time = p_start_time,
        end_time = p_end_time,
        description = NULLIF(p_description, ''),
        status = fn_event_status(p_start_date, p_end_date, p_start_time, p_end_time),
        updated_at = NOW()
    WHERE event_id = p_event_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_finish_event(IN p_event_id BIGINT)
BEGIN
    UPDATE events
    SET
        status = 'finished',
        updated_at = NOW()
    WHERE event_id = p_event_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_create_attendance_session(
    IN p_event_id BIGINT,
    IN p_administrator_id BIGINT,
    IN p_attendance_name VARCHAR(255),
    IN p_attendance_date DATE,
    IN p_time_in_start TIME,
    IN p_time_out_end TIME
)
BEGIN
    INSERT INTO attendance_sessions (
        event_id, administrator_id, attendance_name, attendance_date, time_in_start, time_out_end, created_at, updated_at
    )
    VALUES (
        p_event_id, p_administrator_id, p_attendance_name, p_attendance_date, p_time_in_start, p_time_out_end, NOW(), NOW()
    );

    SELECT LAST_INSERT_ID() AS attendance_session_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_update_attendance_session(
    IN p_attendance_session_id BIGINT,
    IN p_attendance_name VARCHAR(255),
    IN p_attendance_date DATE,
    IN p_time_in_start TIME,
    IN p_time_out_end TIME
)
BEGIN
    UPDATE attendance_sessions
    SET
        attendance_name = p_attendance_name,
        attendance_date = p_attendance_date,
        time_in_start = p_time_in_start,
        time_out_end = p_time_out_end,
        updated_at = NOW()
    WHERE attendance_session_id = p_attendance_session_id;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_save_attendance_record(
    IN p_attendance_session_id BIGINT,
    IN p_member_id BIGINT,
    IN p_event_id BIGINT,
    IN p_administrator_id BIGINT,
    IN p_status VARCHAR(20),
    IN p_mark_time_in TINYINT(1)
)
BEGIN
    DECLARE v_existing_id BIGINT;

    SELECT attendance_id
    INTO v_existing_id
    FROM attendances
    WHERE attendance_session_id = p_attendance_session_id
      AND member_id = p_member_id
    LIMIT 1;

    IF v_existing_id IS NULL THEN
        INSERT INTO attendances (
            member_id, event_id, administrator_id, attendance_session_id,
            attended_at, time_in, status, created_at, updated_at
        )
        VALUES (
            p_member_id, p_event_id, p_administrator_id, p_attendance_session_id,
            NOW(), CASE WHEN p_mark_time_in = 1 THEN NOW() ELSE NULL END,
            p_status, NOW(), NOW()
        );

        SELECT LAST_INSERT_ID() AS attendance_id;
    ELSE
        UPDATE attendances
        SET
            event_id = p_event_id,
            administrator_id = p_administrator_id,
            attended_at = NOW(),
            time_in = CASE WHEN p_mark_time_in = 1 THEN NOW() ELSE time_in END,
            status = p_status,
            updated_at = NOW()
        WHERE attendance_id = v_existing_id;

        SELECT v_existing_id AS attendance_id;
    END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_delete_attendance_record(IN p_attendance_id BIGINT)
BEGIN
    DELETE FROM attendances WHERE attendance_id = p_attendance_id;
END
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $this->dropObjects();
    }

    private function dropObjects(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_delete_attendance_record');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_save_attendance_record');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_update_attendance_session');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_attendance_session');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_finish_event');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_update_event');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_event');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_set_member_archive');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_update_member');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_member');

        DB::unprepared('DROP VIEW IF EXISTS vw_attendance_records_full');
        DB::unprepared('DROP VIEW IF EXISTS vw_attendance_session_summary');
        DB::unprepared('DROP VIEW IF EXISTS vw_events_full');
        DB::unprepared('DROP VIEW IF EXISTS vw_members_full');

        DB::unprepared('DROP FUNCTION IF EXISTS fn_session_availability');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_event_status');
    }
};
