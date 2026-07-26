<?php

namespace Tests\Feature;

use App\Filament\Resources\Publications\Schemas\PublicationForm;
use App\Models\Publication;
use App\Models\Topic;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicationFormCreateOptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_publication_form_topics_select_has_create_option_form_schema(): void
    {
        $schema = Schema::make()->model(Publication::class);
        PublicationForm::configure($schema);

        $topicsField = collect($schema->getComponents())
            ->first(fn ($component) => $component instanceof Select && $component->getName() === 'topics');

        $this->assertNotNull($topicsField, 'Publication form should contain a topics Select field.');
        $this->assertInstanceOf(Select::class, $topicsField);
        $this->assertTrue($topicsField->hasCreateOptionActionFormSchema());

        $createOptionForm = $topicsField->getCreateOptionActionForm(Schema::make()->model(Publication::class));
        $this->assertIsArray($createOptionForm);
        $this->assertSame(['name', 'slug'], array_map(fn ($component) => $component->getName(), $createOptionForm));
    }

    public function test_publication_form_topics_create_option_can_save_a_new_topic(): void
    {
        $schema = Schema::make()->model(Publication::class);
        PublicationForm::configure($schema);

        /** @var Select|null $topicsField */
        $topicsField = collect($schema->getComponents())
            ->first(fn ($component) => $component instanceof Select && $component->getName() === 'topics');

        $this->assertInstanceOf(Select::class, $topicsField);
        $this->assertNotNull($topicsField->getCreateOptionUsing());

        $createdTopicId = $topicsField->getCreateOptionUsing()( 
            $topicsField,
            ['name' => 'Sistem Informasi', 'slug' => 'sistem-informasi'],
            Schema::make()->model(Publication::class),
        );

        $this->assertDatabaseHas('topics', [
            'id' => $createdTopicId,
            'name' => 'Sistem Informasi',
            'slug' => 'sistem-informasi',
        ]);

        $topic = Topic::find($createdTopicId);
        $this->assertNotNull($topic);
        $this->assertSame('Sistem Informasi', $topic->name);
    }
}
