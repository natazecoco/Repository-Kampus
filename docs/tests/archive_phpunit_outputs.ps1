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
  'debug_run.php',
  'output.txt',
  'failing_docs.txt',
  'konteks_publications.txt',
  'konteksapp_repo.txt'
)

New-Item -ItemType Directory -Force -Path docs/tests/archives | Out-Null

foreach ($f in $files) {
    if (Test-Path $f) {
        git mv $f docs/tests/archives/
        Write-Output "moved $f"
    } else {
        Write-Output "missing $f"
    }
}

git add docs/tests/phpunit-summary-2026-08-13.md

# Commit if there are staged changes
$staged = git diff --cached --name-only
if ($staged) {
    git commit -m "docs(tests): add phpunit summary and archive raw outputs (2026-08-13)"
    Write-Output "Committed archive and summary"
} else {
    Write-Output "No changes to commit"
}
