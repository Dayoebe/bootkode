# Continuous integration

.github/workflows/verify.yml runs on pushes, pull requests and manual dispatch. Actions are pinned to verified commit SHAs, repository permission is read-only, checkout credentials are not persisted and no production secrets are required.

The application job uses PHP 8.4, Node 22 and an isolated MariaDB 10.11 service. Its account can access only bootkode_test. It installs Composer/npm lockfiles, creates an ephemeral testing key, checks dependency metadata/platform requirements and PHP syntax, builds assets, runs the guarded tests, compiles Blade and checks route caching. Mail/cache/session use array drivers and queues are synchronous. Neither delivery schedule is enabled. PHPUnit JUnit output is retained for seven days; no working database, private diagnostics or environment file is uploaded.

The separate dependency release gate runs composer audit --locked and npm audit. Both audits run even when one fails, and their JSON reports are retained for seven days. No continue-on-error, advisory suppression, ignore policy or forced major update is used. An application-job pass does not override a failed dependency release gate.

Initial real runs exposed 5,908 committed vendor files and generated bootstrap package/service manifests. The committed vendor snapshot lacked framework files required by its own installed metadata. Consequently composer install could accept the incomplete installed package and fail during Artisan package discovery. A first git-archive reproduction included the same tracked dependencies and was not a clean installation. After confirming the exact locked upstream revision, dependency files and bootstrap manifests were removed from Git tracking while local copies were retained. The existing ignore rules now apply. Normal locked dist installation is retained; the temporary source preference was removed. No vendor patch or framework version change is part of the final implementation.

Fresh checkout installation and the application CI job now pass. The latest exact feature-commit result and local test/browser evidence are recorded in verification.md. Developer machines must still install dependencies with composer install and npm ci; vendor/node_modules are generated local state.

The npm fix scopes @tailwindcss/typography's parser dependency to compatible postcss-selector-parser 6.1.5 or later in major 6. The upstream [advisory](https://github.com/postcss/postcss-selector-parser/security/advisories/GHSA-rj75-hqrm-r3gf) provides that backport. The lockfile deduplicates the parser and current npm audit reports zero findings. No audit fix --force, major framework change or suppression was used.

The reviewed dependency migration moves the lock to Laravel 12.70.2, Socialite 5.31.0 and `firebase/php-jwt` 7.2.1. It clears the previously recorded Laravel/JWT advisories without ignores, policy changes or forced unrelated upgrades. Local `composer audit --locked`, both npm audits, platform checks and the full application suite pass. Keep the release gate mandatory: a future advisory must fail CI rather than being converted to an ignore, and the delivered commit still needs a remote CI run.

CI provides automated regression evidence. It does not prove real mail/payment/OAuth providers, deployment workers, backup restoration, browser accessibility or editorial/legal readiness.
