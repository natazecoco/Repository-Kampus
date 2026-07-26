<?php

namespace Tests\Unit;

use App\Models\PublicationFile;
use App\Models\User;
use Tests\TestCase;

class PublicationFileTest extends TestCase
{
    public function test_visibility_label_defaults()
    {
        $file = new PublicationFile();

        // default visibility when not set is handled in attribute accessor
        $file->visibility = null;

        $label = $file->getVisibilityLabelAttribute();

        $this->assertIsString($label);
        $this->assertStringContainsString('Mahasiswa', $label);
    }

    public function test_can_be_viewed_by_admin()
    {
        $file = new PublicationFile();
        $file->visibility = 'admin';

        $admin = $this->createUser('admin');

        $this->assertTrue($file->canBeViewedBy($admin));
    }

    public function test_can_be_viewed_by_authenticated_user_when_authenticated_visibility()
    {
        $file = new PublicationFile();
        $file->visibility = 'authenticated';

        $user = $this->createUser('student');

        $this->assertTrue($file->canBeViewedBy($user));
    }

    public function test_cannot_be_viewed_by_guest_when_authenticated_visibility()
    {
        $file = new PublicationFile();
        $file->visibility = 'authenticated';

        $this->assertFalse($file->canBeViewedBy(null));
    }

    public function test_can_be_downloaded_only_when_allow_download_true_and_viewable()
    {
        $file = new PublicationFile();
        $file->visibility = 'public';
        $file->allow_download = true;

        $this->assertTrue($file->canBeDownloadedBy(null));

        // if not allowed to download
        $file2 = new PublicationFile();
        $file2->visibility = 'public';
        $file2->allow_download = false;

        $this->assertFalse($file2->canBeDownloadedBy(null));
    }

    public function test_visibility_helpers_return_expected_values()
    {
        $publicFile = new PublicationFile();
        $publicFile->visibility = 'public';

        $this->assertTrue($publicFile->isPublic());
        $this->assertFalse($publicFile->isRestricted());
        $this->assertSame('Publik', $publicFile->visibility_label);

        $restrictedFile = new PublicationFile();
        $restrictedFile->visibility = 'authenticated';

        $this->assertFalse($restrictedFile->isPublic());
        $this->assertTrue($restrictedFile->isRestricted());
        $this->assertSame('Mahasiswa internal', $restrictedFile->visibility_label);
    }

    private function createUser(string $role): User
    {
        return User::factory()->make([
            'role' => $role,
        ]);
    }
}
