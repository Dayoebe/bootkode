# Course content import and generation

BootKode's Course Content Studio is an authoring aid, not a publishing workflow. It creates records in the existing `courses`, `sections`, `lessons`, `assessments`, and `questions` tables. Every result remains unpublished and unapproved and must be reviewed in the existing course builder and approval flow.

## Access and processing

Open **Course Management → Course Content Studio**, or the Studio link in **My Courses** / **All Courses**. The dashboard route is restricted to super administrators, academy administrators, and instructors. `CourseAuthoringAccess` is checked for Livewire requests; the services also verify the acting editor before creating a course. Instructors own their generated/imported courses. Platform administrators can inspect all content runs; instructors see their own runs.

Long-running work is dispatched to the `course-content` queue. Run records in `course_content_runs` retain status, source identity, revision, course link, stage progress, attribution or review flags, and a safe failure message. AI stages persist after each completed module; retry skips saved stages. Import jobs re-fetch and validate the source, then use a database transaction for course records and source metadata. The source identity includes the editor and selected lesson limit; prior recorded identities are reused for compatibility only when they do not point to another editor's course. Completed and partial imports share duplicate detection. Processing uses cache locks and the database source/revision unique key. A conflict state prevent implicit replacement of a prior course or editor changes. An editor must explicitly request a separate draft for a changed source revision.

Run a worker for the queue:

```sh
php artisan queue:work course-content --queue=course-content --timeout=600 --tries=2
```

The dedicated database queue connection reserves each job for 960 seconds; jobs time out after 600 seconds and processing locks expire after 900 seconds. Keep the normal default-queue worker separately. Use a shared atomic cache store (database or Redis) when workers run on multiple hosts. The local file cache supports workers on one host.

The synchronous Artisan commands are intended for bounded manual operation and dry-run review; the dashboard uses queued jobs.

## Supported licensed source

Only the built-in `freecodecamp` GitHub source is registered. Its first reviewed path is **`curriculum/challenges/english/blocks/algorithms`**. Select 1–12 direct Markdown files; the block currently has 10. Files are selected deterministically by filename, and this bounded subset is explicitly labelled as challenge descriptions requiring editorial expansion. Other blocks are refused pending their own licence/format review; the Studio provides an external repository link. No source-management database or arbitrary repository URL field is needed for this fixed adapter.

The adapter checks the complete root BSD-3-Clause licence against a whitespace-normalized fingerprint (`3006a6f8636a327498f20182fa54a7b39a92f6c8bf814002c7ebf902611b50c0`). A changed notice fails closed and requires an explicit code/licence review. Nonrecursive Git trees inspect all ancestor directories and the selected block for additional notices, avoiding the Contents API's 1,000-entry truncation. Truncated/malformed trees, symlinks and unreviewed per-file rights/source declarations are refused or excluded. freeCodeCamp's separate `curriculum/licenses/the-odin-project.txt` is outside this reviewed block; this adapter grants no permission for that content or other third-party material.

The selected challenge's `title` and `--description--` content become sanitized text lessons. Tests, hints, starter code, solutions, images, downloads, videos, dependencies and scripts are excluded. The full copyright notice, licence conditions and disclaimer are appended to **every imported lesson**, including separately exported lessons, and retained in run metadata. Original challenge identifiers, immutable source URLs, author, repository/path, revision, importer version and import time are retained. Unsupported selected files produce a partial result; unselected files do not. An invalid licence or entirely unsupported subset produces no course.

Source fetching only accepts HTTPS on the two fixed GitHub hosts. It rejects redirects and user credentials/ports, validates all resolved addresses, pins a verified public address through cURL, disables proxy inheritance and bounds connection/request time, response bytes and files. User input cannot control the destination host or local filesystem. No retrieved code is executed. Downloads stop at their byte limit rather than checking only after an unlimited download. Ordinary tests use deterministic HTTP and DNS fixtures.

The root [licence](https://github.com/freeCodeCamp/freeCodeCamp/blob/d6d7c30c60750ad0118635ddfd0cec37810d2b92/LICENSE.md), ancestor listings and representative [Algorithms files](https://github.com/freeCodeCamp/freeCodeCamp/tree/d6d7c30c60750ad0118635ddfd0cec37810d2b92/curriculum/challenges/english/blocks/algorithms) were inspected live on 26 October 2026. A live queued import persisted two challenge descriptions in the isolated browser test database and its rerun was skipped. This is support for the bounded reviewed text subset, not the entire repository or its assets.

## AI generation

AI generation uses the existing `AiManager` and configured OpenAI-compatible provider from `config/bootkode_ai.php`. It does not select or expose credentials in Livewire. It validates a blueprint, then generates bounded lesson-content batches per module. Existing `CourseGenerationService`, `CourseGenerationPayload`, `CourseGenerationBlueprintBuilder`, `CourseValidationService`, and assessment models persist sanitized lesson content and supported multiple-choice quizzes/project rubrics.

AI output is treated as untrusted content. Malformed structures, unexpected lesson titles, insufficient safe teaching text, and inconsistent quiz answer indexes are rejected. Server whitelists discard AI ownership, IDs, prices, publication fields, assessment types, score weights and unsupported media; Markdown becomes sanitized HTML. Requested projects create a real `project_criteria` written-response question using the existing attempt/manual-review workflow. Factual correctness and the educational quality of examples still require human review. Completed module stages persist for retry. Usage is recorded in `ai_usage_records` without prompts or responses. The course-generation quota is separate from learner mentor usage:

```dotenv
BOOTKODE_AI_ENABLED=true
BOOTKODE_AI_PROVIDER=groq
GROQ_API_KEY=server-side-key
GROQ_MODEL=current-supported-model
GROQ_BASE_URL=https://api.groq.com/openai/v1
BOOTKODE_AI_COURSE_GENERATION_DAILY_REQUESTS=12
```

The default is disabled. PHP cURL/DNS support is required for the source adapter; no GitHub token is required for the bounded public API requests, which remain subject to GitHub rate limits. Configure a real provider/model and server-side key, then clear configuration cache. Do not put credentials in Vite variables, Livewire properties, course content, or logs. Generation flags factual content, references and answer keys for human verification; no generated source citation is represented as verified.

## Commands

List sources:

```sh
php artisan bootkode:courses-import --list-sources
```

Inspect a bounded source without writing to the database:

```sh
php artisan bootkode:courses-import \
  --source=freecodecamp \
  --path=curriculum/challenges/english/blocks/algorithms \
  --limit=2 \
  --dry-run
```

Import after checking the preview. Select an existing category and an authorised editor account:

```sh
php artisan bootkode:courses-import \
  --source=freecodecamp \
  --path=curriculum/challenges/english/blocks/algorithms \
  --category-id=1 \
  --instructor-id=1 \
  --limit=2
```

Pass `--new-revision` only after reviewing the existing imported draft and wanting a separate course for a changed source revision.

Generate with the configured provider:

```sh
php artisan bootkode:courses-generate \
  --topic="Introduction to Digital Marketing" \
  --category-id=1 \
  --instructor-id=1 \
  --audience="New small-business owners" \
  --level=beginner \
  --objectives="Plan a campaign;Measure campaign results" \
  --modules=3 \
  --lessons-per-module=3 \
  --duration=180 \
  --with-assessments
```

Both Studio and CLI generation are idempotent for the same editor and validated input, including across reloads. A run key cannot be reused with changed input. Supply an explicit new `--request-id` to intentionally create another draft with the same inputs. Optional CLI inputs include `--teaching-style`, `--practical-requirements`, `--assessment-requirements`, `--instructions`, and `--language`. Check `php artisan bootkode:courses-generate --help` for the complete command contract.

## Review, retries, and limitations

The Studio shows queued, processing, completed, partial, conflict, skipped, and failed runs. Successful runs link to the actual course builder. Failed AI retries retain complete blueprint/module stages; source retries re-fetch and reassess the current revision. A changed source does not overwrite an existing course. Each AI run is capped at 5 modules and 4 lessons per module, and provider calls share the explicit per-editor daily generation quota.

Generated and imported courses may still need outcomes, examples, practical exercises, contextual adaptation, assessment review, accessibility checks, references, thumbnails, and editorial/quality checks. Imported freeCodeCamp challenge text is only a subset, not a complete self-contained course. This initial release does not import packages, videos, images, downloadable assets, interactive challenge execution, arbitrary GitHub repositories, or upload packages. AI is synchronous inside each queued job; no live provider or repository success is claimed by mocked tests. The existing additive `course_content_runs` migration is necessary because course tables cannot store pre-course failures or resumable provider stages. It creates only run tracking and foreign keys with null-on-delete; core course, learning, wallet and credential schemas remain unchanged. It was already applied locally when this continuation began. Back up and review forward migrations for each deployment, and deploy the dedicated worker; this continuation made no working-database schema or fixture changes.

## Verification

See [verification.md](verification.md) for exact final suite/build/browser results and current dependency gates. Tests exercise real persistence against a guarded `_test` database, mocked source/provider failure and retry paths, importer licence/attribution/sanitization, owner/subset/revision identity, concurrent processing locks, preserved edits, draft availability, AI quota/stage recovery, server-owned grading fields, CLI runs and Livewire queueing. Network tests cover hostile URLs, private DNS answers, DNS pinning, redirects and byte bounds.

The live source path was exercised separately through browser preview → database queue → worker → actual existing draft editor → duplicate-safe rerun in `bootkode_content_browser_test`. Live AI generation was not called; AI assertions use deterministic HTTP/provider fixtures. Existing generated/imported drafts always require human subject review and the normal quality/approval flow.

## Implementation map

- Existing persistence: `CourseGenerationService`, `CourseGenerationPayload`, `CourseGenerationBlueprintBuilder`, `CourseValidationService`, `Course`, `Section`, `Lesson`, `Assessment` and `Question`. Normal approval continues through `CourseQualityService` and the current editor/approval screens.
- Existing AI integration: `AiManager`, `OpenAiCompatibleProvider`, `AiUsageRecord`, `config/bootkode_ai.php`; optional response-format support preserves the provider's existing contract.
- Acquisition: `CourseContentSource`, `CourseContentSourceRegistry`, `FreeCodeCampSource`, `GitHubContentClient`, `CourseImportService`, `AiCourseGenerationService`, and safe `CourseContentException`.
- Processing/state: `CourseContentRun`, the existing additive `2026_10_22_180000_create_course_content_runs_table` migration, `ImportCourseContent`, `GenerateCourseWithAi`, and the isolated connection in `config/queue.php`.
- Entry points: `CourseContentStudio` and its Blade view; `ImportCourseContentCommand` / `GenerateCourseWithAiCommand`; the named route, role-filtered menu and existing My Courses / All Courses links.
- Verification: `CourseContentPipelineTest`, `CourseSourceNetworkTest`, extended `OpenAiCompatibleProviderTest`, and the public BSD licence fixture in `tests/Fixtures/course-content`. No new package dependency was introduced.
