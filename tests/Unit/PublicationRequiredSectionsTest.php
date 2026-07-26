<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Publication;

class PublicationRequiredSectionsTest extends TestCase
{
    public function test_required_sections_for_thesis_contains_expected_keys()
    {
        $required = Publication::requiredSectionsForType('thesis');

        $this->assertContains('cover', $required);
        $this->assertContains('originality_statement', $required);
        $this->assertContains('approval_sheet', $required);
        $this->assertContains('abstract_id', $required);
        $this->assertContains('chapter_1', $required);
        $this->assertContains('bibliography', $required);
    }

    public function test_missing_required_sections_from_array_detects_missing()
    {
        $files = [
            ['section' => 'cover'],
            ['section' => 'abstract_id'],
            ['section' => 'chapter_1'],
        ];

        $missing = Publication::missingRequiredSectionsFromArray('thesis', $files);

        $this->assertIsArray($missing);
        $this->assertContains('originality_statement', $missing);
        $this->assertContains('approval_sheet', $missing);
        $this->assertContains('chapter_2', $missing);
    }

    public function test_required_for_article_is_full_document()
    {
        $required = Publication::requiredSectionsForType('article');
        $this->assertContains('full_document', $required);
        $this->assertContains('bibliography', $required);
    }
}
