<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\PublicationFile;
use Illuminate\Contracts\Auth\Authenticatable;

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

        $admin = $this->createUserMock('admin');

        $this->assertTrue($file->canBeViewedBy($admin));
    }

    public function test_can_be_viewed_by_authenticated_user_when_authenticated_visibility()
    {
        $file = new PublicationFile();
        $file->visibility = 'authenticated';

        $user = $this->createUserMock('student');

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

    private function createUserMock(string $role): Authenticatable
    {
        $mock = $this->getMockBuilder(Authenticatable::class)
            ->disableOriginalConstructor()
            ->getMock();

        // add dynamic role property
        $mock->role = $role;

        return $mock;
    }
}
