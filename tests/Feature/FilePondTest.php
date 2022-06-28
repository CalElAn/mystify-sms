<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\Models\User;

class FilePondTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_file_is_processed_after_uploading()
    {
        Storage::fake('public');

        $testImage = UploadedFile::fake()->image('testImage.jpg');

        $response = $this->post('/filepond/process', ['filepond' => $testImage]);

        $locationID = $response->content();
        /**to assert that file exists */
        Storage::disk('public')->assertExists('filepond/tmp/'.$locationID); 
        /**to assert that file location ID was returned */
        $response->assertSee($locationID);
    }

    /** @test */
    public function a_file_is_deleted_after_reverting_it()
    {
        $this->withoutExceptionHandling();

        Storage::fake('public');

        $testImage1 = UploadedFile::fake()->image('testImage1.jpg');
        $testImage2 = UploadedFile::fake()->image('testImage2.jpg');

        $response1 = $this->post('/filepond/process', ['filepond' => $testImage1]);
        $response2 = $this->post('/filepond/process', ['filepond' => $testImage2]);

        $locationID1 = $response1->content();
        $locationID2 = $response2->content();
        
        Storage::disk('public')->assertExists('filepond/tmp/'.$locationID1); 
        Storage::disk('public')->assertExists('filepond/tmp/'.$locationID2); 

        $this->call('delete', '/filepond/revert', [], [], [], [], $locationID1);
        $this->call('delete', '/filepond/revert', [], [], [], [], $locationID2);

        Storage::disk('public')->assertMissing('filepond/tmp/'.$locationID1); 
        Storage::disk('public')->assertMissing('filepond/tmp/'.$locationID2); 
    }

    /** @test */
    public function the_requested_user_profile_picture_file_in_storage_is_returned_after_loading_it()
    {    
        $userProfilePictureAndFile = $this->createAndReturnUserPropertyAndFile();

        $user = $userProfilePictureAndFile['user'];

        $response = $this->actingAs($user)->get('/filepond/load/User/'.$user->id);

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'inline');
        $response->assertHeader('filename', $userProfilePictureAndFile['storagePublicDisk']->path($user->profile_picture_path));
    }

    /** @test */
    public function a_user_profile_picture_file_in_storage_is_deleted_after_removing_it()
    {
        $userProfilePictureAndFile = $this->createAndReturnUserPropertyAndFile();
        $user = $userProfilePictureAndFile['user'];

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $userWhoDidIsNotAuthenticated = User::factory()->create();

        $this->actingAs($userWhoDidIsNotAuthenticated)->delete('/filepond/remove/User/'.$user->id)->assertStatus(403);

        $response = $this->actingAs($userProfilePictureAndFile['user'])->delete('/filepond/remove/User/'.$user->id);

        $response->assertStatus(200);
        $this->assertEquals($response->content(), 1);
        $userProfilePictureAndFile['storagePublicDisk']->assertMissing($userProfilePictureAndFile['userProfilePictureHashName']);
    }

    public function createAndReturnUserPropertyAndFile()
    {
        /** @var \Illuminate\Filesystem\Filesystem */
        $storagePublicDisk = Storage::fake('public'); 

        $testImage1 = UploadedFile::fake()->image('testImage1.jpg');
        $testImage1HashName = $testImage1->hashName();
        $storagePublicDisk->put('/', $testImage1);
        $storagePublicDisk->assertExists($testImage1HashName);

        $userProfilePicture = UploadedFile::fake()->image('user_profile_pic.jpg');
        $userProfilePictureHashName = $userProfilePicture->hashName();
        $storagePublicDisk->put('/', $userProfilePicture);
        $storagePublicDisk->assertExists($userProfilePictureHashName);

        /** @var \Illuminate\Contracts\Auth\Authenticatable */
        $user = User::factory()->create(['profile_picture_path' => $userProfilePictureHashName]);

        return [
            'user' => $user,
            'storagePublicDisk' => $storagePublicDisk,
            'testImage1HashName' => $testImage1HashName,
            'userProfilePictureHashName' => $userProfilePictureHashName,
        ];
    }
}
