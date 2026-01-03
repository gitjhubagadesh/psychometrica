# Unused Database Tables Report

**Generated:** 2026-01-03
**Database:** psychometrica_db
**Total Tables:** 38 (37 psy_ tables + 1 system table)

## Summary

Out of 36 application tables (excluding `ci_sessions` and `migrations`), **11 tables** are currently **unused in the application code** (controllers, models, views, JavaScript).

However, **ALL 11 tables contain data**, which indicates they are prepared for future features or PDF report generation that hasn't been fully implemented yet.

---

## 🔴 Completely Unused Tables (11 tables)

These tables exist in the database with data but have **NO references** in PHP or JavaScript code (excluding migrations):

### PDF-Related Tables (4 tables)
Used for PDF report generation - data exists but functionality not implemented in UI

| Table Name | Row Count | Purpose |
|-----------|-----------|---------|
| `psy_pdf_experience_levels` | 2 | Experience level definitions for PDF reports |
| `psy_pdf_percentile_bands` | 3 | Percentile band categories (Low/Medium/High) |
| `psy_pdf_score_percentiles` | 363 | Score-to-percentile mapping for reports |
| `psy_pdf_skill_categories` | 12 | Skill category definitions for PDF |

### Report Content Tables (4 tables)
Report generation templates and content - data populated but not used in code

| Table Name | Row Count | Purpose |
|-----------|-----------|---------|
| `psy_report_authenticity_metadata` | 1 | Authenticity check metadata for reports |
| `psy_report_band_ranges` | 6 | Score band range definitions |
| `psy_report_factors` | 12 | Factor descriptions for reports |
| `psy_report_intro_content` | 2 | Report introduction text templates |

### Structure Tables (3 tables)
Test organization tables - prepared but not implemented

| Table Name | Row Count | Purpose |
|-----------|-----------|---------|
| `psy_roles` | 2 | User role definitions |
| `psy_sub_sections` | 3 | Test sub-section definitions |
| `psy_test_sections` | 1 | Test section definitions |

---

## 🟡 Minimally Used Tables (5 tables)

These tables are referenced in code but only in **1 location** (AdminModel.php), suggesting limited implementation:

| Table Name | References | Row Count | Used In |
|-----------|------------|-----------|---------|
| `psy_factor_mapping` | 1 | 28 | AdminModel.php only |
| `psy_languages` | 1 | 2 | AdminModel.php only |
| `psy_report_metadata_definitions` | 1 | 3 | AdminModel.php only |
| `psy_report_top_skills` | 1 | 12 | AdminModel.php only |
| `psy_skill_statements` | 1 | 76 | AdminModel.php only |

---

## 🟢 Actively Used Tables (20 tables)

These tables have **5+ references** in the codebase and are actively used:

| Table Name | References | Primary Use |
|-----------|------------|-------------|
| `psy_users` | 58 | Core user management |
| `psy_tests` | 41 | Test configurations |
| `psy_questions` | 35 | Question bank |
| `psy_test_factor` | 25 | Test factors/dimensions |
| `psy_master_tests` | 21 | Master test templates |
| `psy_companies` | 21 | Company management |
| `psy_user_registration` | 19 | User registration |
| `psy_user_answers` | 13 | Quiz responses |
| `psy_question_options` | 13 | Question options |
| `psy_quiz_attempts` | 12 | Quiz attempt tracking |
| `psy_user_groups` | 11 | User group management |
| `psy_user_type` | 10 | User type definitions |
| `psy_paragraph_questions` | 9 | Paragraph questionnaires |
| `psy_admin_users` | 9 | Admin authentication |
| `psy_test_name` | 6 | Test names |
| `psy_memory_main_image` | 6 | Memory test images |
| `psy_countries` | 6 | Country reference |
| `psy_user_test_progress` | 5 | Test progress tracking |
| `psy_test_reports` | 5 | Test report configs |
| `psy_quiz_user_timer` | 5 | Quiz timer tracking |

---

## Recommendations

### Option 1: Keep for Future Development ✅ (Recommended)
- All unused tables contain data and appear to be part of planned features
- PDF report generation functionality seems to be prepared but not implemented
- Keep tables for when PDF/advanced reporting features are developed

### Option 2: Document as "Planned Features"
- Create documentation for these tables explaining their intended purpose
- Add TODO comments in code where these features should be implemented

### Option 3: Remove if Not Needed
If these features are not planned for implementation:
- Back up the table data first
- Drop unused tables to simplify the schema
- This would free up ~400 rows of data across 11 tables

---

## Implementation Status by Feature

### ✅ Fully Implemented
- User Management (users, registration, groups)
- Question Management (questions, options, paragraph, memory)
- Test Management (tests, factors, master tests)
- Company Management
- Quiz Taking (attempts, answers, timer, progress)
- Basic Admin Functions

### ⚠️ Partially Implemented
- Report Generation (tables exist with data but minimal code usage)
- Language Support (table exists but limited integration)

### ❌ Not Implemented
- PDF Report Generation (4 tables ready, no UI/controller code)
- Advanced Reporting Features (4 report content tables)
- Test Section/Subsection Management (2 tables)
- Role-based Access Control (roles table exists but unused)

---

## Notes

- The `ci_sessions` table is used by CodeIgniter framework (not counted as unused)
- The `migrations` table is used by CodeIgniter migration system (not counted)
- All unused tables were created via migrations and contain seeded/populated data
- This suggests a larger reporting/PDF generation feature was planned but not completed
