# PHPUnit Summary — 2026-08-13

- Date: 2026-08-13
- Status: All tests passed after fix (33 tests, 101 assertions)

## Failure before fix
- Failing test: `Tests\Feature\ExampleTest::test_publication_detail_page_shows_related_recommendations_when_none_are_precomputed`
- Error: `ParseError: syntax error, unexpected identifier "git"` in compiled Blade view
- Affected view: `resources/views/show.blade.php` (stray token `git` inside `@foreach`)

## Fix applied
- Removed stray `git` token from `@foreach($complementaryRecommendations->take(3)git as $item)` → `@foreach($complementaryRecommendations->take(3) as $item)` in `resources/views/show.blade.php`.
- After the change, re-ran PHPUnit: all tests passed.

## Raw outputs moved to `docs/tests/archives/`
- phpunit_run_output.txt
- phpunit_testdox.txt
- phpunit_testdox_utf8.txt
- phpunit_testdox2.txt
- phpunit_testdox2_utf8.txt
- phpunit_failures_parsed.txt
- phpunit_failures.txt
- phpunit_feature_output.txt
- feature_testdox.txt
- repo_test_output.txt
- test_failures.txt
- test-result.txt
- debug_register_output.txt
- debug_run.php
- output.txt
- failing_docs.txt
- konteks_publications.txt
- konteksapp_repo.txt

(Note: listed files were moved if present in the repo root.)

## Notes & Recommendations
- Keep detailed raw outputs as CI artifacts when possible to avoid increasing repo size.
- The summary file is safe to commit and useful for PRs and reviewers.
- If you prefer compressed archives to reduce repo size, we can compress instead of keeping individual files.
