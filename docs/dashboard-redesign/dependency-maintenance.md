# Compatible dependency security maintenance

23 October 2026. This continues the release audit recorded in the redesign report.

The Composer audit fell from **62 advisory entries across 19 packages to 5 across 2 packages**. Twenty-four locked packages were updated within the existing root constraints, using a targeted update with `--minimal-changes --with-dependencies`. No package was added, no root requirement changed, and no advisory was ignored. Laravel remains 11.46.1; Livewire moves from 3.6.4 to 3.8.11. Tailwind remains 3.4.19. Updated packages retain PHP requirements compatible with the declared PHP 8.2 minimum; execution and platform checks used PHP 8.4.27.

Updates cover Livewire, Dompdf, Guzzle/PSR-7, CommonMark, Flysystem, phpseclib, PHPUnit, PsySH, FPDI and affected Symfony components, plus their necessary dependencies. The complete versions are in composer.lock. Full working-tree regression acceptance after the updates and workspace repairs: **274 tests passed, 1,417 assertions**. This includes the user's in-progress course-content tests; see the fixture correction below. No real provider credentials or production migrations were needed.

Git already tracks part of vendor. Existing tracked packages were refreshed, including new upstream files in those packages, and Composer metadata was generated from an isolated checkout of committed application source. This prevents uncommitted course-content classes entering the release classmap. Newly untracked dependency packages remain managed by Composer. Two upstream documentation whitespace defects were normalized without changing licence text. A fresh isolated download encountered a GitHub DNS timeout; using the installed dependency tree, Composer's isolated install dry run confirmed that the lock and installed versions agree. This is not a claim that a fresh network installation passed.

## Remaining release work

The five remaining Composer entries affect Laravel 11 and firebase/php-jwt 6. Laravel's [temporary signed URL advisory](https://github.com/laravel/framework/security/advisories/GHSA-crmm-hgp2-wgrp) lists fixes in later framework majors. The existing Socialite 5.23.1 requires JWT ^6.4; a separate Socialite update dry run introduces JWT 7, phpseclib 4 and another polyfill. These major dependency changes were not forced into this maintenance step. The exact remaining audit is retained in [Composer evidence](evidence/composer-audit-after.json).

The npm audit remains **8 vulnerabilities: 5 high, 3 moderate**, involving the Tailwind 3 build dependency graph. The registry still reports Tailwind 3.4.19 as its latest 3.x release and braces 3.0.3 as latest; npm suggests Tailwind 4 or incompatible dependency changes for part of the graph. Tailwind 4, forced overrides, advisory suppression and `npm audit fix --force` were not used. [npm evidence](evidence/npm-audit-after.json) records the limits. These are unresolved release findings, even where dependencies execute only during builds.

## Independent course-content test fixture

The import revision regression previously expected `conflict` but received `skipped` because repeated `Http::fake()` calls retain earlier callbacks. Its helper now swaps in a fresh HTTP factory and prevents stray requests before configuring each revision fixture. The original revision/conflict/draft assertions remain intact; importer behavior was not changed. This five-line correction remains with the user's untracked CourseContentPipelineTest.php rather than committing their unfinished feature. The full working-tree suite is green; branch-only acceptance is recorded separately in verification.md.
