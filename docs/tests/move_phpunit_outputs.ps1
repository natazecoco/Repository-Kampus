$files = @(
  'phpunit_run_output.txt',
  'phpunit_testdox.txt',
  'phpunit_testdox_utf8.txt',
  'phpunit_testdox2.txt',
  'phpunit_testdox2_utf8.txt',
  'phpunit_failures_parsed.txt',
  'phpunit_failures.txt',
  'phpunit_feature_output.txt',
  'feature_testdox.txt',
  'repo_test_output.txt',
  'test_failures.txt',
  'test-result.txt',
  'debug_register_output.txt',
  'output.txt',
  'failing_docs.txt',
  'konteks_publications.txt',
  'konteksapp_repo.txt'
)

foreach ($f in $files) {
    if (Test-Path $f) {
        Move-Item -Path $f -Destination docs/tests/archives/ -Force
        Write-Output "moved $f"
    } else {
        Write-Output "missing $f"
    }
}

# stage and commit
git add docs/tests/archives/* docs/tests/phpunit-summary-2026-08-13.md
$staged = git diff --cached --name-only
if ($staged) {
    git commit -m "docs(tests): archive phpunit outputs (2026-08-13)"
    Write-Output "Committed moved files"
} else {
    Write-Output "No changes to commit"
}
