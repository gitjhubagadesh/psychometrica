# Database Migrations for Psychometrica

This document describes all database migrations for the Psychometrica application.

## Migration Files Overview

All migrations are located in `app/Database/Migrations/` directory.

### Migration 1: Core Tables (2025-01-01-000001_CreateCoreTables.php)
**Tables Created:**
- `ci_sessions` - CodeIgniter session storage
- `psy_languages` - Language options for questionnaires
- `psy_countries` - Country reference data
- `psy_user_type` - User type/section definitions

### Migration 2: Admin and Company Tables (2025-01-01-000002_CreateAdminAndCompanyTables.php)
**Tables Created:**
- `psy_admin_users` - Administrator accounts
- `psy_companies` - Company/organization information

### Migration 3: Test Tables (2025-01-01-000003_CreateTestTables.php)
**Tables Created:**
- `psy_test_name` - Test name definitions
- `psy_tests` - Test configurations
- `psy_test_factor` - Test factors/dimensions
- `psy_master_tests` - Master test templates

### Migration 4: Question Tables (2025-01-01-000004_CreateQuestionTables.php)
**Tables Created:**
- `psy_questions` - Question bank
- `psy_question_options` - Answer options for questions
- `psy_memory_main_image` - Memory test main images
- `psy_paragraph_questions` - Paragraph-based questions

### Migration 5: User Tables (2025-01-01-000005_CreateUserTables.php)
**Tables Created:**
- `psy_users` - Quiz users/test takers
- `psy_user_registration` - User registration details
- `psy_user_groups` - User group management
- `psy_user_answers` - User responses to questions
- `psy_user_test_progress` - User test completion tracking

### Migration 6: Report Tables (2025-01-01-000006_CreateReportTables.php)
**Tables Created:**
- `psy_test_reports` - Test report configurations
- `psy_report_top_skills` - Top skills display content
- `psy_report_metadata_definitions` - Report metadata templates

### Migration 8: Quiz Tracking Tables (2025-01-01-000008_CreateQuizTrackingTables.php)
**Tables Created:**
- `psy_quiz_attempts` - Quiz attempt tracking
- `psy_quiz_user_timer` - Real-time quiz timer tracking

### Migration 10: Reference Tables (2025-01-01-000010_CreateReferenceTables.php)
**Tables Created:**
- `psy_factor_mapping` - Factor-to-report mapping
- `psy_skill_statements` - Skill level statements for reports

## Total Tables Coverage

**Total Database Tables:** 27
**Tables with Migrations:** 26 (excluding `migrations` table which is auto-created by CodeIgniter)

## Removed Tables (2026-01-03)

The following 11 unused tables were removed from both database and migrations:

**PDF-Related Tables (4):**
- `psy_pdf_experience_levels` - ❌ Removed (unused)
- `psy_pdf_percentile_bands` - ❌ Removed (unused)
- `psy_pdf_score_percentiles` - ❌ Removed (unused)
- `psy_pdf_skill_categories` - ❌ Removed (unused)

**Report Content Tables (4):**
- `psy_report_authenticity_metadata` - ❌ Removed (unused)
- `psy_report_band_ranges` - ❌ Removed (unused)
- `psy_report_factors` - ❌ Removed (unused)
- `psy_report_intro_content` - ❌ Removed (unused)

**Structure Tables (3):**
- `psy_roles` - ❌ Removed (unused)
- `psy_sub_sections` - ❌ Removed (unused)
- `psy_test_sections` - ❌ Removed (unused)

**Deleted Migration Files:**
- `2025-01-01-000007_CreatePdfTables.php` - ❌ Deleted
- `2025-01-01-000009_CreateTestStructureTables.php` - ❌ Deleted

**Backup Location:**
All removed table data was backed up to: `backups/unused_tables_backup_20260103_142243.sql`

## Running Migrations

To run all migrations:
```bash
php spark migrate
```

To rollback all migrations:
```bash
php spark migrate:rollback
```

To refresh all migrations (rollback and re-run):
```bash
php spark migrate:refresh
```

To check migration status:
```bash
php spark migrate:status
```

## Notes

- The `migrations` table is automatically created by CodeIgniter to track migration status
- All foreign keys are properly defined where necessary
- Unique constraints are added where needed (e.g., `psy_admin_users` has unique username and email)
- Proper cascade delete is configured for related tables
- Default values are set appropriately (e.g., status fields default to 1 or 0)
- Timestamp fields use CodeIgniter's default timestamp handling
- Foreign key constraint `fk_role` was removed from `psy_admin_users` when `psy_roles` table was dropped
- The `role_id` field remains in `psy_admin_users` for backward compatibility but is no longer constrained

## Database Schema Features

- **Streamlined Coverage:** All 27 active tables in the psychometrica_db database
- **Proper Relationships:** Foreign keys and constraints maintain data integrity
- **Flexible Design:** ENUM fields for predefined options, TEXT fields for variable content
- **Audit Trail:** created_at timestamps on relevant tables
- **Performance:** Indexed columns on frequently queried fields
- **Cleanup Complete:** Removed 11 unused tables identified in code usage analysis
