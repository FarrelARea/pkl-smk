<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('school_admin','teacher','student','company_supervisor','superadmin') DEFAULT 'student'");

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::transaction(function () {
                DB::statement("CREATE TABLE users_temp (id integer primary key autoincrement not null, name varchar not null, email varchar not null, email_verified_at datetime, password varchar not null, role varchar check (role in ('school_admin', 'teacher', 'student', 'company_supervisor', 'superadmin')) not null default 'student', school_id integer, company_id integer, remember_token varchar, created_at datetime, updated_at datetime)");
                DB::statement("INSERT INTO users_temp (id, name, email, email_verified_at, password, role, school_id, company_id, remember_token, created_at, updated_at) SELECT id, name, email, email_verified_at, password, role, school_id, company_id, remember_token, created_at, updated_at FROM users");
                DB::statement("DROP TABLE users");
                DB::statement("ALTER TABLE users_temp RENAME TO users");
                DB::statement("CREATE UNIQUE INDEX users_email_unique ON users (email)");
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('school_admin','teacher','student','company_supervisor') DEFAULT 'student'");

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::transaction(function () {
                DB::statement("CREATE TABLE users_temp (id integer primary key autoincrement not null, name varchar not null, email varchar not null, email_verified_at datetime, password varchar not null, role varchar check (role in ('school_admin', 'teacher', 'student', 'company_supervisor')) not null default 'student', school_id integer, company_id integer, remember_token varchar, created_at datetime, updated_at datetime)");
                DB::statement("INSERT INTO users_temp (id, name, email, email_verified_at, password, role, school_id, company_id, remember_token, created_at, updated_at) SELECT id, name, email, email_verified_at, password, role, school_id, company_id, remember_token, created_at, updated_at FROM users WHERE role != 'superadmin'");
                DB::statement("DROP TABLE users");
                DB::statement("ALTER TABLE users_temp RENAME TO users");
                DB::statement("CREATE UNIQUE INDEX users_email_unique ON users (email)");
            });
        }
    }
};
