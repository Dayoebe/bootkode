# Course content audit — 28 October 2026

Status: editorial payload prepared; working-database application and current browser acceptance are pending database recovery. Do not read this report as confirmation that the saved courses have changed.

## Scope and decisions

Read the actual 10-course graph: 30 sections, 81 lessons, 40 assessments and 136 question definitions (126 multiple-choice questions and 10 project criteria). Review covered topic alignment, explanations, worked examples, practice/feedback, assessment keys, rubric totals, numerical/code contracts, local materials and imported prose. Keep substantive original teaching that is already correct.

| ID | Course | Lessons | Editorial decision |
|---|---|---|---|
| 1 | Practical Algorithms with JavaScript | 11 | Remove imported appendices; clarify comparison traces and labelled permutation counts. |
| 2 | JavaScript Data Structures: Arrays and Objects | 13 | Remove imported appendices; retain practical catalogue examples and reference checks. |
| 4 | Build Your First Responsive Website | 9 | Attach printable/editable practice workbook and reference implementation. |
| 5 | Spreadsheet Skills for Better Business Decisions | 9 | Correct S002/C3 and S004/E5 cleaning references; attach dataset and workbook. |
| 6 | Digital Marketing: Plan, Create and Measure Your First Campaign | 9 | Add independent review alternatives and printable/editable workbook. |
| 7 | Workplace Communication: Listen, Write and Speak Clearly | 6 | Review all teaching and attach existing workbooks to lesson resources. |
| 8 | Time Management: Plan a Realistic Week | 6 | Review capacity arithmetic and attach existing workbooks to lesson resources. |
| 9 | Teamwork and Collaboration: Build Trust and Handle Disagreement | 6 | Review decision arithmetic and attach existing workbooks to lesson resources. |
| 10 | Customer Service Essentials: Understand, Respond and Follow Through | 6 | Review case calculations and attach existing workbooks to lesson resources. |
| 11 | Critical Thinking and Practical Problem Solving | 6 | Review weighted options and attach existing workbooks to lesson resources. |

## Prepared changes

- Change 32 reading bodies. Remove imported challenge/licence appendices from 24 lessons in courses 1 and 2, leaving original teaching and worked implementations intact.
- Explain binary-search index versus value traces and labelled-position versus distinct-string permutation counts. Preserve the implemented APIs rather than silently change the problem being taught.
- Fix the spreadsheet cleaning exercise: with the header in row 1, S002 is row 3 / C3 (WhatsApp) and S004 is row 5 / E5 (Quantity 4). The source CSV itself is valid and is preserved.
- Give independent learners specific alternatives to partner review in four lessons.
- Rewrite ten course descriptions around actual activities, align syllabus summaries to saved section titles, improve practice FAQs and remove irrelevant source-certificate/credential language.
- Normalize all 81 plain-text search/context fields from their HTML teaching content.
- Attach the actual downloadable reference kits, CSV and workbooks to relevant lesson resources. Add three original practice workbooks in PDF and editable Markdown, covering lessons and capstones.
- Move complete source credits and licence notices into two clearly named downloadable source-notice files. Existing reference archives retain their notices. No full legal notice is inserted into the cleaned reading/search fields. Explicit offline packs include required notices even when optional documents are deselected; ordinary authenticated course HTML remains uncached.

## Preservation and application boundary

Private, permission-restricted original/current course exports and the proposed payload are under `storage/app/course-audit-20261028`. The guard compares source records again before applying and refuses to overwrite concurrent teaching edits. User approval/review updates made during inspection were inspected and captured in `current-before.json`; these flags and original review timestamps are preserved. No courses are published or unapproved by the editorial script.

The intended transaction preserves course/lesson IDs and slugs, section order, assessment/question definitions, original files, enrollments, recorded answers, wallets/transactions, progress, certificates and import provenance. It verifies local material existence and assessment scoring before completion. Its dedicated rehearsal database ends in `_test`. Do not rerun the initial exporter or earlier creation scripts over existing courses.

## Evidence and outstanding checks

- Algorithms reference verification: 15,428 checks passed; data structures: 392 checks passed.
- New practice PDFs: three valid five-page A4 documents; extracted text contains exercises and capstones.
- Focused offline-notice tests: 3 passed, 9 assertions before the outage.
- Initial full suite: 432 passed, 1 failed (2,084 assertions); failure was a missing Vite manifest during a concurrent build. The manifest/build have been restored; the repeat was interrupted by the database outage. No all-green full-suite claim is made.
- `npm run build` passed with the existing PostCSS `from` warning. Changed PHP syntax checks, Blade compilation and whitespace checks passed.
- Pending: successful transactional rehearsal, application to the working courses, final preservation hashes, current authenticated lesson/resource checks and responsive browser acceptance at 390/768/1024/1440px.

## Database interruption

MariaDB stopped during verification. A permitted normal start failed: `ibdata1` system-space page 0 reports a checksum mismatch and InnoDB registration aborts. No editorial transaction was applied. Preserve original database files and use a separate physical recovery copy; an empty migrated schema would not recover user records. Details and any subsequent verified recovery/application results must be appended here.

This is an editorial audit with explicit technical checks, not a guarantee of perfect teaching or a new credential claim. Publication and human instructional review remain separate.

### Preservation and isolated recovery result

A complete physical preservation copy is at `/opt/lampp/var/mysql-preserved-course-audit-20261028`; the independent diagnostic copy is `/opt/lampp/var/mysql-course-audit-recovery-20261028`. All 6,143 original regular-file paths/sizes were checked against both copies. Stale sockets are not database content. Both copies are private; original files were not replaced, removed or manually repaired.

Recovery modes 1 through 6 were tested only against the isolated copy, with separate data/log directories, a loopback-only port and read-only server setting. Every attempt stopped with the same system-space page-0 checksum error. The checksum utility independently reports page 0 invalid. Recovery mode is for salvage and does not repair damaged data; see [MariaDB's recovery guidance](https://mariadb.com/docs/server/server-usage/storage-engines/innodb/innodb-troubleshooting/innodb-recovery-modes). A validated full data restore or specialist recovery is required before normal application writes. No checksum suppression, system-tablespace deletion, empty-schema substitution or manual page rewriting was attempted.

The pending payload changes 32 lesson bodies and preserves all 36 retained teaching code blocks byte-for-byte. The new spreadsheet workbook's first page was visually inspected; all three PDFs have five A4 pages with extractable activities/capstones. Current authenticated browser verification was prepared but not executed after the outage. No `applied.json` or successful rehearsal proof exists, so the working updater refuses to proceed.
